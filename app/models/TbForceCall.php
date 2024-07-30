<?php

class TbForceCall extends \Phalcon\Mvc\Model
{

    public $idemergency;
    public $placa;
    public $date;
    public $hour;
    public $status;


    public function initialize()
    {
        $this->setSchema("admin");
    }
    public function getSource()
    {
        return 'tb_force_call';
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