<?php

class ButtonFree extends \Phalcon\Mvc\Model
{

    public $id_button;
    public $url;
    public $name; //nombre;


    public function initialize()
    {
        $this->setSchema("admin");
    }
    public function getSource()
    {
        return 'button_free';
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