<?php

class LocationUser extends \Phalcon\Mvc\Model
{


    public $id_location_user;
    public $location;       //ubicacion;
    public $date;           //fecha;
    public $hour;           //hora;
    public $id_user;           //usuario;

    public function initialize()
    {
        $this->setSchema("admin");
    }
    public function getSource()
    {
        return 'location_user';
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