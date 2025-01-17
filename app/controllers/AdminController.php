<?php

class AdminController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {

    }

    /**
     * Funcion que realiza el login de un usuario a la base de datos
     * @param $email El email del usuario
     * @param $password El password de cuenta
     * @return string JSON cone l resultado de la operacion
     */
    public function loginDashboard($email,$password)
    {
        $exist = $this->existuserLogin($email,$password);
        if ($exist){
            return  true;
        }else{
            return  false;
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
        echo $passEnd;
        die();
        $phone_exist = TbAdminPanel::query()
            ->where('password = :password: AND email=:email:',
                array('password' => $passEnd,'email'=>$email))
            ->execute()->toArray();
        if (count($phone_exist) == 0) {
            return false;
        }else {
            return true;
        }
    }

}

