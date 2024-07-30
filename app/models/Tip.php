<?php

class Tip extends \Phalcon\Mvc\Model
{

     public $idtip;
     public $url_Image;     //urlImagen;
     public $name;          //nombre;
     public $resume;        //resumen;
     public $detail;        //detalle;

    public function initialize()
    {
        $this->setSchema("admin");
    }
    public function getSource()
    {
        return 'tip';
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