<?php


class TbAdminPanel extends \Phalcon\Mvc\Model
{

    public $id_codigo;
    public $username;
    public $password;
    public $name;
    public $permisos;
    public $email;


    public function initialize()
    {
        $this->setSchema("admin");
    }
    public function getSource()
    {
        return 'tb_admin_panel';
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