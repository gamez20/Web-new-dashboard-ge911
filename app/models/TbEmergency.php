<?php

class TbEmergency extends \Phalcon\Mvc\Model
{

    public $direction;
    public $location;
    public $emergency;
    public $polares;
    public $date;
    public $hour;
    public $status;
    public $iduser;
    public $idadmin;
    public $auxAdmin;


    public function initialize()
    {
        $this->setSchema("admin");
    }
    public function getSource()
    {
        return 'tb_emergency';
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