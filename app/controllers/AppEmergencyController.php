<?php
include APP_PATH.'/library/firebase/Push.php';
include APP_PATH.'/library/firebase/firebase.php';
class AppEmergencyController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
    }

    /**
     * Funcion que realiza el registro de una emergencia, desde la aplicacion por parte de un
     * usuario
     * ademas de asignarcela a una fuerza cercana para su atencion
     * @return string : retorno json para ser interpretado en la app
     */
    public function registerAction()
    {
        $direction = $this->request->getPost('direction');
        $lat = $this->request->getPost('lat');
        $long = $this->request->getPost('long');
        $emergency = $this->request->getPost('emergency');
        $polares = $this->request->getPost('polares');
        $iduser = $this->request->getPost('iduser');
        $message = $this->request->getPost('message');

        //TODO validar datos
        $util = new UtilitiesController();
        if($util->isNotEmptyValue($direction) == "" ||
            $util->isNotEmptyValue($lat) == "" ||
            $util->isNotEmptyValue($long) == "" ||
            $util->isNotEmptyValue($emergency) == "" ||
            $util->isNotEmptyValue($polares) == "" ||
            $util->isNotEmptyValue($iduser) == ""){
            $response['status'] = false;
            $response['message'] = "Alguno de los datos esta vacio";
            return json_encode($response);
        }
        //se valida si el usuario esta bloqueado o no
        $isBlocked = $this->userIsBaned($iduser);
        if($isBlocked){
            $response['status'] = false;
            $response['message'] = "Emergencia no registrada usuario baneado";
            return json_encode($response);
        }else{  
            // buscamos el admin mas cerca y lo asignamos
            $dataAdminDistance = $this->searchNearForce($lat,$long);
            /*$placa = $dataAdminDistance['placa'];
            $adm = TbAdmin::query()
                ->where('placa=:placa:',
                    array('placa'=>$placa))
                ->execute()->getFirst();*/
            $date = date('Y-m-d');
            $hour =date('H:m:s');
            $emer = new TbEmergency();
            $emer->direction    = $direction;
            $emer->location     = $lat." ".$long;
            $emer->emergency    = $emergency;
            $emer->polares      = $polares;
            $emer->date         = $date ;
            $emer->hour         = $hour ;
            $emer->status       = 1;
            $emer->iduser       = $iduser;
            $emer->idadmin      = 1;
            $emer->auxAdmin     = '{}';

            if ($emer->save() == false) {
                $response['status'] = false;
                $response['message'] = "No se pudo registrar la emergencia";
                return json_encode($response);
            }else{
                $id = $emer->getWriteConnection()->lastInsertId();
                // se notifica al admin sobre la emergencia
                foreach ($dataAdminDistance as $admin){
                    $adm = TbAdmin::query()
                        ->where('placa=:placa:',
                            array('placa'=>$admin['placa']))
                        ->execute()->getFirst();
                    //se guarda la referencia
                    $forceCall = new TbForceCall();
                    $forceCall->idemergency = $id;
                    $forceCall->placa = $admin['placa'];
                    $forceCall->date = $date ;
                    $forceCall->hour = $hour ;
                    $forceCall->status = 1;
                    $forceCall->save();
                    $this->sendForceEmergency($lat,$long,$adm,$message);
                }
                $response['status'] = true;
                $response['message'] = "Emergencia registrada";
                $response['idEmergency'] = $id;
                return json_encode($response);
            }
        }
    }

    /**
     * Verifica si un usuario esta baneado
     * @param $iduser El id del usuario
     */
    private function userIsBaned($iduser){
        $exist = TbUser::query()
            ->where('id_user=:iduser:',
                array('iduser'=>$iduser))
            ->execute()->getFirst();
        if ($exist!= false){
            if ($exist->statusActive==0){
                return  false;
            }else{
                return  true;
            }
        }else{
            return true;
        }
    }

    /**
     * el metodo busca la fuerza mas cercana a la emergencia generada y devulve la fuerza con la menor
     * distancia al punto donde se lanzo la emergencia
     *
     * @param $direccion - la ciudad buscada
     * @param $latEmergency - latitud de la emergencia
     * @param $longEmergency - longitud de la emergencia
     * @return string - Retorna un arreglo con la placa y el id del administrador mas cerca a la emergencia
     */
    private function searchNearForce($latEmergency,$longEmergency){

        $admins = TbAdmin::query()
            ->where('activeStatus=:status:',
                array('status'=>0))
            ->execute()->toArray();
        $util = new UtilitiesController();
        $datos = array();
        $distancias=array();
        foreach ($admins as $admin){
            $adminLocation = explode(",",$admin['location']);

            $km = $util->pointToPoint($latEmergency, $longEmergency, $adminLocation[0], $adminLocation[1]);
            $distancias[]=$km;
            $datosTem = array();
            $datosTem['placa'] = $admin['placa'];
            $datosTem['distance'] = $km;
            $datosTem['id_admin'] = $admin['id_admin'];
            $datos['admin_'.$admin['id_admin']]=$datosTem;
        }
        sort($distancias);
        $nearAdmin = array();
        $count = 0;
        $maxVal = 5;
        $min_value = $distancias[$count];
        while ($count<$maxVal){
            if(count($distancias) == $count){
                break;
            }else{
                $count++;
                foreach ($datos as $dato){
                    if($dato['distance'] == $min_value){
                        $nearAdmin[$count]= $dato;
                        if(count($distancias) > $count){
                            $min_value = $distancias[$count];
                            break;
                        }
                        break;
                    }
                }
            }
        }
       return $nearAdmin;
    }

    // envia el push de la emergencia a un adminsitrador
    public function sendForceEmergency($lat,$long,$admin,$message){
        $firebase = new Firebase();
        $push = new Push();
        $push->setImage('');
        $push->setIsBackground(FALSE);
        $push->setlat($lat);
        $push->setlong($long);

        if($message=="" || $message==NULL){
           $message ="Emergencia por Atender.";
        }
        $push->setTitle($message);
        $push->setMessage("Hay un emergencia en la ubicacion ".$lat.",".$long);
        $push->setText("Hay un emergencia en la ubicacion ".$lat.",".$long);
        $json = $push->getPushEmergency();
        $regId = $admin->token;
        $response = $firebase->send($regId, $json);
        return json_encode($response);
    }

    // envia el push del chat a la persona/administrador
    public function sendMessageChat($message,$token){
        $firebase = new Firebase();
        $push = new Push();
        $push->setImage('');
        $push->setIsBackground(FALSE);
        $push->setTitle("Chat message");
        $push->setMessage($message);
        $push->setText($message);
        $json = $push->getPushEmergency();
        $regId = $token;
        $response = $firebase->send($regId, $json);
        return json_encode($response);
    }

    /**
     * Funcion que facilita la asignacion de de emergencias tanto principal, como de auxiliares
     * @param $idEmergency identificador de la emergencia
     * @param $placaForce Fuerza que va a tomar la emergencia
     * @param $responseAdm  si acepta o rechaza la emergencia
     * @return string retorno de la operacion
     */
    public function acceptEmergencyAction($idEmergency,$placaForce,$responseAdm){


        $emer = TbEmergency::query()
            ->where('id_emergency=:emer:',
                array('emer' => $idEmergency))
            ->execute()->getFirst();
        $adm = TbAdmin::query()
            ->where('placa=:placa:',
                array('placa' => $placaForce))
            ->execute()->getFirst();
        if ($emer != null) {
            $callAdm = TbForceCall::query()
                ->where('idemergency=:idEmergencia: AND placa=:placa:',
                    array('idEmergencia'=>$idEmergency,'placa' => $placaForce))
                ->execute()->getFirst();
            if($responseAdm){
                $callAdm->status=0;
                $callAdm->save();
                //se valida si existe adm principal
                $statusAdm = $emer->idadmin;

                if($statusAdm==1){
                    $emer->idadmin = $adm->id_admin;
                    $message = "Emergencia asignada a principal";
                }else{
                    $aux = json_decode($emer->auxAdmin);
                    $auxArray = (array)($aux);
                    $number = count($auxArray);
                    $auxArray["adm".$number] = $adm->id_admin;
                    $emer->auxAdmin = json_encode($auxArray);
                    $message = "Emergencia asignada a auxiliar";
                }
                $emer->save();
                $response['status'] = true;
                $response['message'] = $message;
                return json_encode($response);
            }else{//rechazada
                $callAdm->status=2;
                $callAdm->save();
                $response['status'] = false;
                $response['message'] = "Emergencia rechazada";
                return json_encode($response);
            }
        }else{
            $response['status'] = false;
            $response['message'] = "Emergencia no encontrado";
            return json_encode($response);
        }

    }

    /**
     * Funcion que envia mensaje entre fuerza y usuario
     * @param $idEmergencia emergencia
     * @param $type el tipo de mensaje
     * @param $message el mensaje a enviar
     * @return string el retorno de la operacion
     */
    public function sendMessageAction($idEmergencia,$type,$message){

        //se obtiene la emergencia
        $emer = TbAdmin::query()
            ->where('id_emergency=:emerg:',
                array('emerg'=>$idEmergencia))
            ->execute()->getFirst();

        if ($emer){
            if ($type==0){
                $user = TbAdmin::query()
                    ->where('id_admin=:admin:',
                        array('admin'=>$emer->idadmin))
                    ->execute()->getFirst();
            }else{
                $user = TbUser::query()
                    ->where('id_user=:user:',
                        array('user'=>$emer->iduser))
                    ->execute()->getFirst();
            }

            $this->sendMessageChat($message,$user->token);
            $response['status'] = true;
            $response['message'] = "Mensaje chat enviado";
        }else{
            $response['status'] = true;
            $response['message'] = "Emergencia no encontrada";
        }
        return json_encode($response);
    }


    /**
     * Actualizacion de la emergencia su finalizacion u otro estado que pudiera tener
     * @param $idEmergency identificador de la emergencia
     * @param $placaForce fuerza que atendio la emergencia
     * @param $status el estatud de la emergencia a ser asignada
     * @return string respuesta de la actualizacion de la emergencia
     */
    public function updateEmergencyStatusAction($idEmergency,$placaForce,$status){



        $adm = TbAdmin::query()
            ->where('placa=:placa:',
                array('placa' => $placaForce))
            ->execute()->getFirst();

        $emer = TbEmergency::query()
            ->where('id_emergency=:emer: and idadmin=:admin:',
                array('emer' => $idEmergency,'admin' =>$adm->id_admin))
            ->execute()->getFirst();


        if ($emer != null) {
            $emer->status = $status;

            if ($emer->save() == false) {
                $response['status'] = false;
                $response['message'] = "No se pudo actualizar el estatus de la emergencia";
            }else{
                $response['status'] = true;
                $response['message'] = "Emergencia actualizada con el valor ".$status;
            }
            return json_encode($response);
        }else{
            $response['status'] = false;
            $response['message'] = "Emergencia no encontrado";
            return json_encode($response);
        }

    }

    /**
     * obtiene el estatus de la emergencia
     * @param $emergencyId el identificador de la emergencia
     * @return string resultad de la operacion
     */
    public function getStatusEmergencyAction($emergencyId)
    {

        $data = TbEmergency::query()
            ->where('id_emergency=:emergency:',
                array('emergency'=>$emergencyId))
            ->execute()->getFirst();
        if ($data!= false){
            $response['status'] = true;
            $response['statusEmergency'] = $data->status;
            $response['message'] = "Emergencia encontrada";
            return json_encode($response);
        }else{
            $response['status'] = false;
            $response['statusEmergency'] = false;
            $response['message'] = "Emergencia no encontrada";
            return json_encode($response);
        }
    }


     /*public function testAction(){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
         $firebase = new Firebase();
         $push = new Push();
         $push->setImage('');
         $push->setIsBackground(FALSE);
         $push->setlat(0);
         $push->setlong(0);
         $push->setTitle("Emergencia por Atender.");
         $push->setMessage("Hay un emergencia en la ubicacion ");
         $push->setText("Hay un emergencia en la ubicacion " );
         $json = $push->getPushEmergency();
         $regId = "dPbTfoGCfM8:APA91bEvRebX5-WBzLfCNpzF1jpR6DL_XSbv_Zn-030J4WfY_XAv4vI2A6IKXx_5-uEczvidhfujkOnVb8VM_lO4xjRYVngTHCyrE0R3j233O0FM5RhlNCsyXH89G0QpRvcAhTfV-HUE";
         $response = $firebase->send($regId, $json);
        die(var_dump($response));
    }*/

}

