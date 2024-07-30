<?php

class LocationAdmin extends \Phalcon\Mvc\Model
{

    public $id_location_admin;
    public $location;           //ubicacion;
    public $date;               //fecha.phtml;
    public $time;               //hora;
    public $placa;              //placa;

    public function initialize()
    {
        $this->setSchema("admin");
    }
    public function getSource()
    {
        return 'location_admin';
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