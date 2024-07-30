<?php

class EmergencyController extends \Phalcon\Mvc\Controller
{

    public function initialize()
    {
        if ($this->session->has("user-name")) {
        } else {
            return $this->response->redirect('');
        }
    }

    public function indexAction()
    {
        //$this->view->setTemplateAfter("layout");
    }

    /**
     * Listado de las emergencias
     */
    public function allEmergencyAction(){

        if($this->request->isPost()) {
            //die("IS POST");
            $util = new UtilitiesController();
            $direccion = $util->isNotEmptyValue($this->request->getPost('direccion'));
            $emergencia = $util->isNotEmptyValue($this->request->getPost('emergencia'));
            $fecha = $util->isNotEmptyValue($this->request->getPost('fecha.phtml'));
            $id_admin = $util->isNotEmptyValue($this->request->getPost('id_admin'));

            $this->view->allEmergency = $this->searchEmergency($direccion,$emergencia,$fecha,$id_admin);
        }else{
            $data = $this->allEmergencyData();
            $this->view->allEmergency = $data;
        }
       //$data = $this->allEmergencyData();
       //$this->view->allEmergency = $data;
        $this->view->setTemplateAfter("layout");
    }

    /**
     * Funcionq ue retorna el listado de las emergencias
     * @return bool
     */
    public function allEmergencyAjaxAction(){
        $this->view->disable();
        if ($this->request->isGet()) {
            // Check whether the request was made with Ajax
            if ($this->request->isAjax()) {
                $data = $this->allEmergencyData();
                echo json_encode(array('res' => "done","data"=>$data));
                return false;
            }
        }
    }

    /**
     * Funcionq ue tiene toda la informacion relacionada con la emergencia
     * @return mixed
     */
    public function allEmergencyData(){
        $data = TbEmergency::query()
            ->columns([
                'direction',
                'TbEmergency.location',
                'emergency',
                'polares',
                'date',
                'hour',
                'status',
                'iduser',
                'idadmin',
                'auxAdmin',
                //user
                'us.fullName as userName',
                'us.phone',
                'us.photo',
                'us.email',
                //admin
                'ad.fullName as adminName',
                'ad.placa',
                'ad.ocupation',
            ] )
            ->leftJoin("TbUser",
                "TbEmergency.iduser = us.id_user", "us")
            ->leftJoin("TbAdmin",
                "TbEmergency.idadmin = ad.id_admin", "ad")
            ->execute()->toArray();
        return $data;
    }

    /**
     * Funcion que adiciona a una emergencia  una fuerza adicional a la principal
     * @return string
     */
    public function addForceAuxAction(){
        $forceId        = $this->request->getPost('forceId');
        $emergencyId    = $this->request->getPost('emergencyId');
        $data = TbEmergency::query()
            ->where('id_emergency=:emergency:',
                array('emergency'=>$emergencyId))
            ->execute()->getFirst();
        if ($data!= false){
            $aux = json_decode($data->auxAdmin);
            $auxArray = (array)($aux);
            $number = count($auxArray);
            $auxArray["adm".$number] = $forceId;
            $data->auxAdmin = json_encode($auxArray);
            $data->save();
            $response['status'] = true;
            $response['message'] = "Fuerza auxiliar adicionada";
            return json_encode($response);
        }else{
            $response['status'] = false;
            $response['message'] = "Emergencia no encontrada";
            return json_encode($response);
        }

    }

    /**
     * Funcionq ue registra una emergencia manualmente desde el panel
     */
    public function registerEmergencyAction(){
        $this->view->setTemplateAfter("layout");
        if ($this->request->isPost()){
            $util = new UtilitiesController();
            $name       = $util->isNotEmptyValue($this->request->getPost('name'));
            $phone      = $util->isNotEmptyValue($this->request->getPost('phone'));
            $email      = $util->isNotEmptyValue($this->request->getPost('email'));
            $status     = (integer)$this->request->getPost('status');
            $userAdm = new UserAdmController();
            $this->view->dataSearch = $userAdm->searchUser('',$name,$phone,$email,$status);
        }
    }

    /**
     * Funcion que ayuda al registro de la emergencia en la aplicacion
     * @return mixed
     */
    public function addEmergencyAction(){
        $location = $this->request->getPost('location');
        $location = str_replace("(", "", $location);
        $location = str_replace(")", "", $location);
        //$location = str_replace(",", "", $location);
        //se valida si viene usuario
        $idUser = $this->request->getPost('idUser');
        if($idUser==""){
            $fullName = $this->request->getPost('name');
            $gender = $this->request->getPost('gender');
            $phone = $this->request->getPost('phone');

            $user = new AppUserController();
            $idUser = $user->registerUserPanel($fullName,$location,$phone,$gender);
        }
        //TODO validar datos
        $post = $this->request->getPost();
        $direction = $post['direction'];
        $emergency = $post['emergency'];
        $polares = $post['polares'];
        $date = date('Y-m-d');
        $hour = date('H:i:s');
        $status = '1';
        $iduser = $idUser;
        $idadmin = 1;
        $auxAdmin = '{}';


        $emer = new TbEmergency();
        $emer->direction    = $direction;
        $emer->location     = $location;
        $emer->emergency    = $emergency;
        $emer->polares      = $polares;
        $emer->date         = $date;
        $emer->hour         = $hour;
        $emer->status       = $status;
        $emer->iduser       = $iduser;
        $emer->idadmin      = $idadmin;
        $emer->auxAdmin     = $auxAdmin;
        $emer->save();
        return $this->response->redirect('principal/activeForce');
    }

    /**
     * Funcion que busca o filtra las emergencias para su visualizacion
     * @param $direccion
     * @param $emergencia
     * @param $fecha
     * @param $id_admin
     * @return mixed
     */
    public function searchEmergency($direccion,$emergencia,$fecha,$id_admin){
        //die($name ." / ". $location   ." / ".$phone      ." / ".$email      ." / ".$status);
        $filterDireccion       = "";
        $filterEmergencia   = "";
        $filterFecha      = "";
        $filterId_admin      = "";


        if($direccion    != null || $direccion    !=''){
            $filterDireccion     =" AND direction like '%".$direccion."%'";
        }
        if($emergencia!= null || $emergencia!=''){
            $filterEmergencia =" AND  emergency like '%".$emergencia."%'";
        }
        if($fecha   != null || $fecha   !=''){
            $filterFecha    =" AND  date like '%".$fecha."%'";
        }
        if($id_admin   != null || $id_admin   !=''){
            $filterId_admin    =" AND  idadmin like '%".$id_admin."%'";
        }

        $data = TbEmergency::query()
            ->where("status < 2 ".$filterDireccion.$filterEmergencia.$filterFecha.$filterId_admin,
                array( ))
            ->execute()->toArray();

        return $data;
    }

    /**
     * Vista de graficos
     */
    public function graphicDataAction(){
        $this->view->setTemplateAfter("layout");
    }

    /**
     * Funcionq ue extrae la informacion de una emergencia para ser visualizada
     * @return string
     */
    public function emergencyGraphicDataAction(){
 
        $data = TbEmergency::query()
             ->columns([
                'direction',
                'TbEmergency.location',
                'emergency',
                'polares',
                'date',
                'hour',
                'status',
                'iduser',
                'idadmin',
                'auxAdmin',
                //user
                'us.fullName as userName',
                'us.phone',
                'us.photo',
                'us.email',
                'us.id_user',
                //admin
                'ad.fullName as adminName',
                'ad.placa',
                'ad.ocupation'
            ] )
            ->leftJoin("TbUser",
                "TbEmergency.iduser = us.id_user", "us")
            ->leftJoin("TbAdmin",
                "TbEmergency.idadmin = ad.id_admin", "ad")
            ->execute()->toArray();

        return json_encode($data);
    }
}