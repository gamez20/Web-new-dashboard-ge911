<?php

class AppForceController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
    }



    /**
     * Funcion que realiza el registro de un usuario a la base de datos
     * @param $fullName
     * @param $email
     * @param $phone
     * @param $placa
     * @param $city
     * @param $password
     * @param $ocupation
     * @param $location
     * @return string JSON con el resultado de la operacion
     */
    public function registerAction($fullName,$email,$phone,$placa,$city,$password,$ocupation,$location,$token)
    {

        $util = new UtilitiesController();
        if($util->isNotEmptyValue($fullName) == "" ||
            $util->isNotEmptyValue($email) == "" ||
            $util->isNotEmptyValue($phone) == "" ||
            $util->isNotEmptyValue($placa) == "" ||
            $util->isNotEmptyValue($city) == "" ||
            $util->isNotEmptyValue($password) == ""||
            $util->isNotEmptyValue($ocupation) == ""||
            $util->isNotEmptyValue($location) == ""||
            $util->isNotEmptyValue($token) == ""){
            $response['status'] = false;
            $response['message'] = "Error - Alguno de los datos esta vacio";
            return json_encode($response);
        }
        $exist = $this->existuser($phone,$email);
        if (!$exist){
            $user = new TbAdmin();
            $user->fullName= $fullName;
            $user->email= $email;
            $user->phone= $phone;
            $user->placa= $placa;
            $user->city= $city;
            $user->ocupation= $ocupation;
            $user->location= $location;
            $user->token= $token;

            $salt = '1234567890abcdefghijklmnopqrstuvwxyz!#$%&/=?¿@';
            $passEnd = md5($salt . $password);
            $user->password = $passEnd;
            if ($user->save() == false) {
                $response['status'] = false;
                $response['message'] = "No se puedo registrar el administrador";
                return json_encode($response);
            }else{
                $response['status'] = true;
                $response['message'] = "Administrador registrado";
                return json_encode($response);
            }
        }else{
            $response['status'] = false;
            $response['message'] = "Administrador ya existe";
            return json_encode($response);
        }

    }

    /**
     * Funcion que verifica la existencia de un usuario en el sistema
     * @param $phone El telefono del usuario.
     * @param $email Correo electronico
     * @return bool true si existe, false en caso contrario
     */
    private function existuser($phone,$email){
        $phone_exist = TbAdmin::query()
            ->where('phone = :phone: AND email=:email:',
                array('phone' => $phone,'email'=>$email))
            ->execute()->toArray();
        if (count($phone_exist) == 0) {
            return false;
        }else {
            return true;
        }
    }


    /**
     * Funcion que realiza el login de un usuario a la base de datos
     * @param $email El email del usuario
     * @param $password El password de cuenta
     * @return string JSON cone l resultado de la operacion
     */
    public function loginAction($email,$password)
    {
        $exist = $this->existuserLogin($email,$password);
        if ($exist!= false){
            $response['status'] = true;
            $response['data'] =$exist;
            return json_encode($response);
        }else{
            $response['status'] = false;
            $response['data'] = false;
            return json_encode($response);
        }
    }

    /**
     * Funcion que verifica la existencia de un usuario en el sistema
     * @param $email El email del usuario
     * @param $password El password de cuenta
     * @return bool true si existe, false en caso contrario
     */
    private function existuserLogin($email,$password){
        $salt = '1234567890abcdefghijklmnopqrstuvwxyz!#$%&/=?¿@';
        $passEnd = md5($salt . $password);
        $phone_exist = TbAdmin::query()
            ->columns([
                'fullName',
                'email',
                'phone',
                'placa',
                'city',
                'ocupation'
            ] )
            ->where('password = :password: AND email=:email:',
                array('password' => $passEnd,'email'=>$email))
            ->execute()->ToArray();
        if (count($phone_exist)==0) {
            return false;
        }else {
            return $phone_exist[0];
        }
    }


    /**
     * Funcion que realiza el login de un usuario a la base de datos
     * @param $email El email del usuario
     * @return string JSON cone l resultado de la operacion
     */
    public function updateTokenAction($email,$token)
    {
        $exist = TbAdmin::query()
            ->where('email=:email:',
                array('email'=>$email))
            ->execute()->getFirst();
        if ($exist!= false){
            $exist->token=$token;
            if ($exist->save() == false) {
                $response['status'] = false;
                $response['message'] = "No se puedo actualizar el token del administrador";
                return json_encode($response);
            }else{
                $response['status'] = true;
                $response['message'] = "Administrador actualizado";
                return json_encode($response);
            }
        }else{
            $response['status'] = false;
            $response['message'] = "Administrador no encontrado";
            return json_encode($response);
        }
    }

    /**
     * Funcion que actualiza el estado del administrador/fuerza
     * @param $email email de la fuerza
     * @param $activeStatus  el estatus a ser asignado a la fuerza
     * @return string mensaje de respuesta de la operacion
     */
    public function updateStatusAction($email,$activeStatus){
        $exist = TbAdmin::query()
            ->where('email=:email:',
                array('email'=>$email))
            ->execute()->getFirst();
        if ($exist!= false){
            if ($activeStatus=="true"){
                $activeStatus=0;
            }else{
                $activeStatus=1;
            }
            $exist->activeStatus=$activeStatus;
            if ($exist->save() == false) {
                $response['status'] = false;
                $response['message'] = "No se puedo actualizar el status del administrador";
                return json_encode($response);
            }else{
                $response['status'] = true;
                $response['message'] = "Administrador status  actualizado";
                return json_encode($response);
            }
        }else{
            $response['status'] = false;
            $response['message'] = "Administrador no encontrado";
            return json_encode($response);
        }
    }

    /**
     * Funcion que actualiza de la locacion de la fuerza
     * @param $placa placa de la fuerza
     * @param $locationAdmin ubicacion
     * @return string respuesta de la operacion
     */
    public function updateLocationAdminAction($placa,$locationAdmin)
    {
        $admin = TbAdmin::query()
            ->where('placa=:placa:',
                array('placa' => $placa))
            ->execute()->getFirst();
        if ($admin != null) {

        //se valida la actualizacion cada x tiempo
            $atime = date('H:i:s');
            $ptime =  $admin->timeLog;
            $minutos = (strtotime($ptime)-strtotime($atime))/60;
            $minutos = abs($minutos); $minutos = floor($minutos);
            if($minutos >= 5){
                $loc_adm = new LocationAdmin();
                $loc_adm->location = $locationAdmin;
                $loc_adm->date = date('Y-m-d');
                $loc_adm->time = $atime;
                $loc_adm->placa = $placa;
                $loc_adm->save();
                $admin->timeLog = $atime;
            }
            $admin->location = $locationAdmin;
            $admin->save();
            $response['status'] = true;
            $response['message'] = "ubicacion Actualizada";
            return json_encode($response);
        }else{
            $response['status'] = false;
            $response['message'] = "Administrador no encontrado";
            return json_encode($response);
        }
    }

    /**
     * Funcion que revisa el estatud del administrador
     * @param $email correo a ser verificado
     * @return string respuesta de la operacion
     */
    public function checkStatusAction($email){
        $exist = TbAdmin::query()
            ->where('email=:email:',
                array('email'=>$email))
            ->execute()->getFirst();
        if ($exist!= false){
            $response['status'] = true;
            $response['message'] = "Administrador encontrado";
            if ($exist->activeStatus==0){
                $dataRet = true;
            }else{
                $dataRet = false;
            }
            $response['dataStatus'] = $dataRet;
            return json_encode($response);
        }else{
            $response['status'] = false;
            $response['message'] = "Administrador no encontrado";
            return json_encode($response);
        }
    }

    /**
     * Funcionq ue guarda la locacion historica de la fuerza
     * @return bool
     */
    public function addLocationAdminAction(){

        $update         = $this->request->getPost('update');
        $id             = $this->request->getPost('id');
        $full_name      = $this->request->getPost('full_name');
        $phone          = $this->request->getPost('phone');
        $location       = $this->request->getPost('location');
        $date           = $this->request->getPost('date');
        $time           = $this->request->getPost('time');
        $placa          = $this->request->getPost('placa');
        $estatus_service= $this->request->getPost('estatus_service');
        $direction      = $this->request->getPost('direction');
        $polares        = $this->request->getPost('polares');

        if($this->request->isPost()) {
            $util = new UtilitiesController();
            if ($util->isNotEmptyValue($update) == "" ||
                $util->isNotEmptyValue($id) == "" ||
                $util->isNotEmptyValue($full_name) == "" ||
                $util->isNotEmptyValue($phone) == "" ||
                $util->isNotEmptyValue($location) == "" ||
                $util->isNotEmptyValue($date) == "" ||
                $util->isNotEmptyValue($time) == "" ||
                $util->isNotEmptyValue($placa) == "" ||
                $util->isNotEmptyValue($estatus_service) == "" ||
                $util->isNotEmptyValue($direction) == "" ||
                $util->isNotEmptyValue($polares) == "") {
                $response['status'] = false;
                $response['message'] = "Error - Alguno de los datos esta vacio";
                return json_encode($response);
            }
        }

        if($update){
            $locationAdmin= $this->searchLocationAdminById($id);
            $locationAdmin->full_name        = $full_name;
            $locationAdmin->phone            = $phone;
            $locationAdmin->location         = $location;
            $locationAdmin->date             = $date;
            $locationAdmin->time             = $time;
            $locationAdmin->placa            = $placa;
            $locationAdmin->estatus_service  = $estatus_service;
            $locationAdmin->direction        = $direction;
            $locationAdmin->polares          = $polares;
        }else{
            $locationAdmin= new LocationAdmin();
            $locationAdmin->full_name        = $full_name;
            $locationAdmin->phone            = $phone;
            $locationAdmin->location         = $location;
            $locationAdmin->date             = $date;
            $locationAdmin->time             = $time;
            $locationAdmin->placa            = $placa;
            $locationAdmin->estatus_service  = $estatus_service;
            $locationAdmin->direction        = $direction;
            $locationAdmin->polares          = $polares;
        }

        if ($locationAdmin->save() == false) {
            $id = false;
        }else{
            $id = $locationAdmin->getWriteConnection()->lastInsertId();
        }
        return $id;
    }

    /**
     * Funcionq ue retorna la lista de las emergencias segun la placa
     * @param $placa
     * @return string
     * TbForceCall.status 0=con respuesta , 1:sin respuesta
     */
    public function searchEmergencyPlacaAction($placa){
        $calls = TbForceCall::query()
            ->columns([
                'em.id_emergency',
                'em.location',
                'em.emergency',
                'em.direction',
                'em.date',
                'em.hour',
                'us.phone',
                'us.fullName',
                'TbForceCall.status as statusCall',
            ] )
            ->leftJoin("TbEmergency","em.id_emergency = TbForceCall.idemergency", "em")
            ->leftJoin("TbUser","em.iduser = us.id_user", "us")
            ->where('placa=:placa: AND (TbForceCall.status = 1 OR TbForceCall.status = 0) AND em.status=1',
                array('placa' => $placa))
            ->execute()->ToArray();
        $response['status'] = true;
        $response['data'] = $calls;
        return json_encode($response);
    }

    /**
     * Funcionq eu actualiza el estatus de la llamana
     * @param $idCall identificador de llamada
     * @param $response respuesta dada por la fuerza
     * @return string resoltado de la operacion
     */
    public function updateCallStatusPlacaAction($idCall,$response){
        $calls = TbForceCall::query()
            ->where('id_force_call  = :idcall: ',
                array('idcall' => $idCall))
            ->execute()->getFirst();
        if ($calls != null) {
            $calls->status = $response;
            $calls->save();
        }

        $response['status'] = true;
        $response['message'] = "llamada actualizada";
        return json_encode($response);
    }


}
 