<?php

class ConfigController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {

        $this->view->setTemplateAfter("layout");
        $this->view->allDataAdminPanel = $this->allAdminPanel();
    }

    public function initialize(){
        $name_user = $this->session->get("user-name");
        if($name_user == "root@root.co"){
            $this->view->ROOTExist = $name_user;
        }
    }

    /**
     * Fucion que lista todos los administradores/fuerzas del panel
     * @return mixed
     */
    public function allAdminPanel(){
        $allAdmin = TbAdminPanel::query()
            ->execute()->toArray();
        return $allAdmin;

    }

    /**
     * Elimina un administrador del panel
     * @return mixed
     */
    public function deleteAdminPanelAction(){


        if($this->request->isPost()){
            $id_admin = (integer)$this->request->getPost('id_codigo');
            $admin = TbAdminPanel::query()->where('id_codigo = :id: ',
            array('id' =>$id_admin))
                ->execute()->getFirst();

            if($admin != false){
                $admin->delete();
            }
        }
        return $this->response->redirect('config');
    }

    /**
     * Agregar un administrador para el panel
     * @return mixed
     */
    public function addAdminPanelAction(){

        if($this->request->isPost()){

            $adminPanel = new TbAdminPanel();
            $util = new UtilitiesController();
            $id_codigo= $util->isNotEmptyValue($this->request->getPost('id_codigo'));
            $username= $util->isNotEmptyValue($this->request->getPost('username'));
            $password= $util->isNotEmptyValue($this->request->getPost('password'));
            $name= $util->isNotEmptyValue($this->request->getPost('name'));
            $permisos= $util->isNotEmptyValue($this->request->getPost('permisos'));
            $email= $util->isNotEmptyValue($this->request->getPost('email'));

            $salt = '1234567890abcdefghijklmnopqrstuvwxyz!#$%&/=?¿@';
            $passEnd = md5($salt . $password);
            $adminPanel->password = $passEnd;
            $adminPanel->id_codigo = $id_codigo;
            $adminPanel->username = $username;
            $adminPanel->name = $name;
            $adminPanel->permisos = $permisos;
            $adminPanel->email = $email;

            if ($adminPanel->save() == false) {
                $response['status'] = false;
                $response['message'] = "No se puedo registrar el administrador";
                //return json_encode($response);
            }else{
                $response['status'] = true;
                $response['message'] = "Administrador registrado";
                //return json_encode($response);
            }
        }else{
            $response['status'] = false;
            $response['message'] = "Administrador ya existe";
            //return json_encode($response);
        }

        return $this->response->redirect('config');
    }
}

