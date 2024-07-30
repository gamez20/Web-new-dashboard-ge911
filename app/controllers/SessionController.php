<?php

class SessionController extends \Phalcon\Mvc\Controller
{
    public function indexAction()
    {

    }

    public function startAction()
    {
        if ($this->request->isPost()) {
            // Get the data from the user
            $email = $this->request->getPost('email');
            $password  = $this->request->getPost('password');
            // Find the user in the database
            $force = new AdminController();
            $exist = $force->loginDashboard($email,$password);
            if ($exist) {
                $this->initSession($email);
                return $this->response->redirect('principal');
            }else{
                return $this->response->redirect('');
            }
        }
    }

    //Funciones de session
    public function initSession($userName){
        // Set a session variable
        $this->session->set("user-name", $userName);
        //send  email
        /*$dataEmail = [
            "admin"=> $userName,
            "fecha1"=> date('d F Y'),
            "fecha2"=> date('d/m/Y'),
            "hora"=> date('h:i:s'),
        ];
        $this->getDI()
            ->getMail()
            ->send('jmla196@gmail.com', 'Inicio de sesión', 'login', $dataEmail);*/
    }
    public function closeSessionAction(){
        $this->session->remove('user-name');
        $this->session->destroy(true);
        return $this->response->redirect('');
    }

    public function setVar($var,$value){
        $this->session->set($var, $value);
    }
}