<?php

class TbUser extends \Phalcon\Mvc\Model
{

    public $fullName;
    public $location;
    public $phone;
    public $photo;
    public $statusActive;
    public $email;
    public $gender;
    public $token;

    public function initialize()
    {
        $this->setSchema("admin");
    }
    public function getSource()
    {
        return 'tb_user';
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