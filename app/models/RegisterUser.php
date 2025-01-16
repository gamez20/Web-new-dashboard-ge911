<?php

class RegisterUser extends \Phalcon\Mvc\Model
{
     public $id_register_user;
     public $photo_url;         //usuariourifoto;
     public $facebook_name;     //nombreUsuarioFacebook;
     public $phone;             //telefonoUsuario;
     public $location;          //ubicacion;
     public $date;              //fecha;

    public function initialize()
    {
        $this->setSchema("admin");
    }
    public function getSource(): string
    {
        return 'register_user';
    }
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

 }