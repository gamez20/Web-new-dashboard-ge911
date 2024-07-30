<?php

class UserAdmController extends \Phalcon\Mvc\Controller
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
        //TODO validar datos
        if($this->request->isPost()){
            $util = new UtilitiesController();
            $idUser       = $util->isNotEmptyValue($this->request->getPost('idUser'));
            $name       = $util->isNotEmptyValue($this->request->getPost('name'));
            $location   = $util->isNotEmptyValue($this->request->getPost('location'));
            $phone      = $util->isNotEmptyValue($this->request->getPost('phone'));
            $email      = $util->isNotEmptyValue($this->request->getPost('email'));
            $status     = (integer)$this->request->getPost('status');

            $this->view->dataSearch = $this->searchUser($idUser,$name,$location,$phone,$email,$status);
        }else{
            $this->view->dataSearch = $this->searchUser("","","","","",0);
        }
        $this->view->setTemplateAfter("layout");
        $this->view->idUser       =$idUser    ;
        $this->view->name       =$name    ;
        $this->view->location   =$location;
        $this->view->phone      =$phone   ;
        $this->view->email      =$email   ;
        $this->view->status     =$status  ;
    }

    public function searchUser($idUser,$name,$location,$phone,$email,$status){

        //die($name ." / ". $location   ." / ".$phone      ." / ".$email      ." / ".$status);
        $filterIdUser       = "";
        $filterName       = "";
        $filterLocation   = "";
        $filterPhone      = "";
        $filterEmail      = "";
        $filterStatus     = $status;

        if($idUser    != null || $idUser    !=''){
            $filterIdUser     =" AND id_user = ".$idUser;
        }
        if($name    != null || $name    !=''){
            $filterName     =" AND fullName like '%".$name."%'";
        }
        if($location!= null || $location!=''){
            $filterLocation =" AND  location like '%".$location."%'";
        }
        if($phone   != null || $phone   !=''){
            $filterPhone    =" AND  phone like '%".$phone."%'";
        }
        if($email   != null || $email   !=''){
            $filterEmail    =" AND  email like '%".$email."%'";
        }
        if($status  == null || $status ==''){
            $filterStatus   = 0;
        }

        $data = TbUser::query()
            ->where("statusActive =".$filterStatus.$filterIdUser.$filterName.$filterLocation.$filterPhone.$filterEmail,
                array( ))
            ->execute()->toArray();

        return $data;
    }

    public function updateStatusAction(){
        $idUser=$this->request->getPost('id_user');
        $activeStatus=$this->request->getPost('statusActive');
        $exist = TbUser::query()
            ->where('id_user=:idUser:',
                array('idUser'=>$idUser))
            ->execute()->getFirst();
        if ($exist!= false){
            if ($activeStatus==0){
                $activeStatus=1;
            }elseif($activeStatus==1){
                $activeStatus=0;
            }
            $exist->statusActive=$activeStatus;
            if ($exist->save() == false) {
                $response['status'] = false;
                $response['message'] = "No se puedo actualizar el status del usuario";
                return json_encode($response);
            }else{
                $response['status'] = true;
                $response['message'] = "Usuario status  actualizado";
                return json_encode($response);
            }
        }else{
            $response['status'] = false;
            $response['message'] = "Usuario no encontrado";
            return json_encode($response);
        }
    }


}

