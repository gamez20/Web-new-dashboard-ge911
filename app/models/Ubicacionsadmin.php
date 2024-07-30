<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;

class Ubicacionsadmin extends \Phalcon\Mvc\Model
{


     public $id;

     public $nombresApellidos;

     public $telefono;

     public $ubicacion;

     public $fecha;

     public $hora;

     public $placa;

     public $estadoServicio;

     public $direccion;

     public $polares;

     public $__v;



    public function initialize()
    {
        $this->setSchema("administrador");
        $this->setSource("ubicacions");
    }


    public function getSource()
    {
        return 'ubicacions';
    }


    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }
}