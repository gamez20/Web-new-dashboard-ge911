<?php

class LocationController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        if($this->request->isPost()) {
            //die("IS POST");
            $util = new UtilitiesController();
            $name = $util->isNotEmptyValue($this->request->getPost('name'));
            $location = $util->isNotEmptyValue($this->request->getPost('location'));
            $phone = $util->isNotEmptyValue($this->request->getPost('phone'));
            $date = $util->isNotEmptyValue($this->request->getPost('date'));

            $this->view->allUserData = $this->searchUser($name,$location,$phone,$date);
            $this->view->setTemplateAfter("layout");
        }else{
        $this->view->allUserData = $this->allLocationUserAction();
        $this->view->setTemplateAfter("layout");
        }
    }

    public function initialize(){
        $name_user = $this->session->get("user-name");
        if($name_user == "root@root.co"){
            $this->view->ROOTExist = $name_user;
        }else{
            return $this->response->redirect('principal');
        }
    }

    public function adminDataAction(){

        if($this->request->isPost()) {
            //die("IS POST");
            $util = new UtilitiesController();
            $name = $util->isNotEmptyValue($this->request->getPost('name'));
            $location = $util->isNotEmptyValue($this->request->getPost('location'));
            $phone = $util->isNotEmptyValue($this->request->getPost('phone'));
            $date = $util->isNotEmptyValue($this->request->getPost('date'));

            $this->view->allDataAdmin = $this->searchAdmin($name,$location,$phone,$date);
            $this->view->setTemplateAfter("layout");
        }else{
        $this->view->allDataAdmin = $this->allLocationAdminAction();
        $this->view->setTemplateAfter("layout");
            }
            // die(var_dump( $this->searchAdmin("","","","","") ));
    }

    public  function  ubicacionAction()
    {

    }

    //**********************************************
    //Administrador
    //**********************************************

    public function allLocationAdminAction(){
        $data = LocationAdmin::query()//tr
        ->columns([
            "ad.fullName as full_name",
            "ad.phone as phone",
            "LocationAdmin.location as location",
            "LocationAdmin.date as date",
            "LocationAdmin.time as time",
            "LocationAdmin.placa as placa"
        ] )
            ->leftJoin("TbAdmin","ad.placa = LocationAdmin.placa", "ad")
            ->execute()->toArray();
        return $data;
    }

    public function searchLocationAdminByIdAction(){
        $id   = $this->request->getPost('id_location_admin');
        return $this->searchLocationAdminById($id);
    }

    public function searchLocationAdminById($id){
        $data = LocationAdmin::query()
            ->where('id_location_admin = :id: ',
                array('id' =>$id))
            ->execute()->getFirst();
        return $data;
    }

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

    public function deleteLocationAdminByIdAction(){
        $id   = $this->request->getPost('id_location_admin');
        $locationAdmin= $this->searchLocationAdminById($id);
        if ($locationAdmin!= false){
            if ($locationAdmin->delete() === false) {
                $retorno = false;
            } else {
                $retorno = true;
            }
        }
        return $retorno;
    }

    public function searchLocationAdminDateAction(){
        $date   = $this->request->getPost('date');
        $data = LocationAdmin::query()
            ->where(' date=:date:',
                array('date' =>$date))
            ->execute()->toArray();
        return $data;
    }

    //**********************************************
    //USER
    //**********************************************

    public function allLocationUserAction(){
        $data = LocationUser::query()
            ->columns([
            "us.fullName as full_name",
            "us.phone as phone",
            "LocationUser.location as location",
            "LocationUser.date as date",
            "LocationUser.hour as hour",
        ] )
            ->leftJoin("TbUser","us.id_user = LocationUser.id_user", "us")
            ->execute()->toArray();
        return $data;
    }

    public function searchLocationUserByIdAction(){
        $id   = $this->request->getPost('id_location_user');
        return $this->searchLocationUserById($id);
    }

    public function searchLocationUserById($id){
        $data = LocationUser::query()
            ->where('id_location_user = :id: ',
                array('id' =>$id))
            ->execute()->getFirst();
        return $data;
    }

    public function addLocationUserAction(){

        $update         = $this->request->getPost('update');
        $id             = $this->request->getPost('id');
        $facebook_name  = $this->request->getPost('facebook_name');
        $phone          = $this->request->getPost('phone');
        $location       = $this->request->getPost('location');
        $date           = $this->request->getPost('date');
        $hour           = $this->request->getPost('hour');
        $direction      = $this->request->getPost('direction');
        $polares        = $this->request->getPost('polares');

        if($this->request->isPost()) {
            $util = new UtilitiesController();
            if ($util->isNotEmptyValue($update) == "" ||
                $util->isNotEmptyValue($id) == "" ||
                $util->isNotEmptyValue($facebook_name) == "" ||
                $util->isNotEmptyValue($phone) == "" ||
                $util->isNotEmptyValue($location) == "" ||
                $util->isNotEmptyValue($date) == "" ||
                $util->isNotEmptyValue($hour) == "" ||
                $util->isNotEmptyValue($direction) == "" ||
                $util->isNotEmptyValue($polares) == "") {
                $response['status'] = false;
                $response['message'] = "Error - Alguno de los datos esta vacio";
                return json_encode($response);
            }
        }

        if($update){
            $locationUser= $this->searchUserById($id);
            $locationUser->facebook_name    = $facebook_name;
            $locationUser->phone            = $phone;
            $locationUser->location         = $location;
            $locationUser->date             = $date;
            $locationUser->hour             = $hour;
            $locationUser->direction        = $direction;
            $locationUser->polares          = $polares;
        }else{
            $locationUser= new LocationUser();
            $locationUser->facebook_name    = $facebook_name;
            $locationUser->phone            = $phone;
            $locationUser->location         = $location;
            $locationUser->date             = $date;
            $locationUser->hour             = $hour;
            $locationUser->direction        = $direction;
            $locationUser->polares          = $polares;
        }

        if ($locationUser->save() == false) {
            $id = false;
        }else{
            $id = $locationUser->getWriteConnection()->lastInsertId();
        }
        return $id;
    }

    public function deleteLocationUserByIdAction(){
        $id   = $this->request->getPost('id_location_user');
        $locationUser= $this->searchLocationUserById($id);
        if ($locationUser!= false){
            if ($locationUser->delete() === false) {
                $retorno = false;
            } else {
                $retorno = true;
            }
        }
        return $retorno;
    }

    public function searchLocationUserDateAction(){
        $date   = $this->request->getPost('date');
        $data = LocationUser::query()
            ->where(' date=:date:',
                array('date' =>$date))
            ->execute()->toArray();
        return $data;
    }

    public function searchAdmin($name,$location,$phone,$date){
        //die($name ." / ". $location   ." / ".$phone      ." / ".$email      ." / ".$status);
        $filterPhone       = "";
        $filterDate       = "";
        $filterLocation   = "";
        $filterName      = "";

        if($name    != null || $name    !=''){
            $filterName     =" AND ad.fullName like '%".$name."%'";
        }
        if($location!= null || $location!=''){
            $filterLocation =" AND LocationAdmin.location like '%".$location."%'";
        }
        if($phone   != null || $phone   !=''){
            $filterPhone    =" AND ad.phone like '%".$phone."%'";
        }
        if($date   != null || $date   !=''){
            $filterDate    =" AND LocationAdmin.date like '%".$date."%'";
        }

        $data = LocationAdmin::query()
            ->columns([
                "ad.fullName as full_name",
                "ad.phone as phone",
                "LocationAdmin.location as location",
                "LocationAdmin.date as date",
                "LocationAdmin.time as time",
                "LocationAdmin.placa as placa"
            ] )
            ->leftJoin("TbAdmin","ad.placa = LocationAdmin.placa", "ad")
            ->where("activeStatus < 2 ".$filterPhone.$filterDate.$filterLocation.$filterName,
                array( ))
            ->execute()->toArray();
         return $data;
    }

    public function searchUser($name,$location,$phone,$date){
        //die($name ." / ". $location   ." / ".$phone      ." / ".$email      ." / ".$status);
        $filterPhone        = "";
        $filterDate         = "";
        $filterLocation     = "";
        $filterName         = "";


        if($name    != null || $name    !=''){
            $filterName     =" AND fullName like '%".$name."%'";
        }
        if($location!= null || $location!=''){
            $filterLocation =" AND LocationUser.location like '%".$location."%'";
        }
        if($phone   != null || $phone   !=''){
            $filterPhone    =" AND phone like '%".$phone."%'";
        }
        if($date   != null || $date   !=''){
            $filterDate    =" AND date like '%".$date."%'";
        }

        $data = LocationUser::query()
            ->columns([
                "us.fullName as full_name",
                "us.phone as phone",
                "LocationUser.location as location",
                "LocationUser.date as date",
                "LocationUser.hour as hour",
            ] )
            ->leftJoin("TbUser","us.id_user = LocationUser.id_user", "us")
            ->where("id_location_user > 0 ".$filterPhone.$filterDate.$filterLocation.$filterName,
                array( ))
            ->execute()->toArray();
        return $data;
    }
}

