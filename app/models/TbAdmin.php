<?php

class TbAdmin extends \Phalcon\Mvc\Model
{

    public $fullName;
    public $email;
    public $phone;
    public $placa;
    public $city;
    public $password;
    public $ocupation;
    public $location;
    public $token;
    public $activeStatus;
    public $timeLog;

    public function initialize()
    {
        $this->setSchema("admin");
    }
    public function getSource()
    {
        return 'tb_admin';
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