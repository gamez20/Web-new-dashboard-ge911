<?php

class AppUserController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
    }



    /**
     * Funcion que realiza el registro de un usuario a la base de datos
     * @param $fullName
     * @param $email
     * @param $phone
     * @param $photo
     * @param $location
     * @return string JSON con el resultado de la operacion
     */
    public function registerAction()
    {
        //TODO validar datos
        $fullName = $this->request->getPost('fullName');
        $location = $this->request->getPost('location');
        $phone = $this->request->getPost('phone');
        $photo = $this->request->getPost('photo');
        $email = $this->request->getPost('email');
        $gender = $this->request->getPost('gender');

        if($this->request->isPost()) {
            $util = new UtilitiesController();
            if ($util->isNotEmptyValue($fullName) == "" ||
                $util->isNotEmptyValue($location) == "" ||
                $util->isNotEmptyValue($phone) == "" ||
                $util->isNotEmptyValue($photo) == "" ||
                $util->isNotEmptyValue($email) == "" ||
                $util->isNotEmptyValue($gender) == "") {
                $response['status'] = false;
                $response['message'] = "Error - Alguno de los datos esta vacio";
                return json_encode($response);
            }
        }
        return $this->registerUserApp($fullName,$location,$phone,$photo,$email,$gender);
    }

    /**
     * Funcion que registra un usuario de en la app de emergencias
     * @param $fullName
     * @param $location
     * @param $phone
     * @param $photo
     * @param $email
     * @param $gender
     * @return string
     */
    public function registerUserApp($fullName,$location,$phone,$photo,$email,$gender)
    {
        $exist = $this->existUser($phone,$email);
        if ($exist==0){
            $user = new TbUser();
            $user->fullName= $fullName;
            $user->location= $location;
            $user->phone= $phone;
            $user->photo= $photo;
            $user->statusActive= 0;
            $user->email= $email;
            $user->gender= $gender;

            if ($user->save() == false) {
                $response['status'] = false;
                $response['exist'] = false;
                $response['reg'] = 0;
                $response['message'] = "No se puedo registrar el usuario";
                return json_encode($response);
            }else{
                $response['status'] = true;
                $response['exist'] = true;
                $response['reg'] = $user->getWriteConnection()->lastInsertId();
                $response['message'] = "Usuario registrado";
                return json_encode($response);
            }
        }else{
            $response['status'] = false;
            $response['exist'] = true;
            $response['reg'] = $exist;
            $response['message'] = "Usuario ya existe";
            return json_encode($response);
        }

    }

    /**
     * Funcionq ue registra un usuario en el panel para su visualizacion
     * @param $fullName
     * @param $location
     * @param $phone
     * @param $gender
     * @return bool
     */
    public function registerUserPanel($fullName,$location,$phone,$gender)
    {

            $user = new TbUser();
            $user->fullName= $fullName;
            $user->location= $location;
            $user->phone= $phone;
            $user->photo= '';
            $user->statusActive= 0;
            $user->email= '';
            $user->gender= $gender;

            if ($user->save() == false) {

                return false;
            }else{
                return $user->getWriteConnection()->lastInsertId();
            }
    }

    /**
     * Funcion que verifica la existencia de un usuario en el sistema
     * @param $phone El telefono del usuario.
     * @param $email Correo electronico
     * @return bool true si existe, false en caso contrario
     */
    private function existUser($phone,$email){

        $exist = TbUser::query()
            ->where('email=:email:',
                array('email'=>$email))
            ->execute()->getfirst();
        if ($exist!=false) {
            if($phone != $exist->phone){
                $exist->phone = $phone;
                $exist->save();
            }
            return $exist->id_user;
        }else {
            return 0;
        }  
    }

    /**
     * Funcionq ue verifica el estado de un usuario
     * @param $email email del usuario
     * @return string respuesta de la operacion
     */
    public function checkStatusAction($email){
        $exist = TbUser::query()
            ->where('email=:email:',
                array('email'=>$email))
            ->execute()->getFirst();
        if ($exist!= false){
            $response['status'] = true;
            $response['message'] = "Administrador encontrado";
            if ($exist->statusActive==0){
                $dataRet = true;
            }else{
                $dataRet = false;
            }
            $response['dataStatus'] = $dataRet;
            return json_encode($response);
        }else{
            $response['status'] = false;
            $response['message'] = "Administrador no encontrado";
            return json_encode($response);
        }
    }

    /**
     * Funcion que obtienen la ubicacion de un usuario par aubicar su emergencia
     * @param $idUser el identificador del usuario
     * @param $locationAdmin la ubicacion del usuario
     * @return string
     */
    public function locationUserAction($idUser,$locationAdmin)
    {
        $user = TbUser::query()
            ->where('id_user=:user:',
                array('user' => $idUser))
            ->execute()->getFirst();
        if ($user != null) {
            $loc_user = new LocationUser();
            $loc_user->location = $locationAdmin;
            $loc_user->date = date('Y-m-d');
            $loc_user->hour = date('H:i:s');
            $loc_user->id_user = $idUser;
            $loc_user->save();
            $response['status'] = true;
            $response['message'] = "ubicacion Actualizada";
            return json_encode($response);
        }else{
            $response['status'] = false;
            $response['message'] = "Usuario no encontrado";
            return json_encode($response);
        }
    }

    /**
     * Actualiza el token del usuario par alos mensajes push
     * @param $email correo del usuario
     * @param $token el token del usuario a actualizar
     * @return string resutlado de la operacion
     */
    public function updateTokenAction($email,$token)
    {
        $exist = TbUser::query()
            ->where('email=:email:',
                array('email'=>$email))
            ->execute()->getFirst();
        if ($exist!= false){
            $exist->token=$token;
            if ($exist->save() == false) {
                $response['status'] = false;
                $response['message'] = "No se puedo actualizar el token del usuario";
                return json_encode($response);
            }else{
                $response['status'] = true;
                $response['message'] = "Usuario actualizado";
                return json_encode($response);
            }
        }else{
            $response['status'] = false;
            $response['message'] = "Usuario no encontrado";
            return json_encode($response);
        }
    }


}

