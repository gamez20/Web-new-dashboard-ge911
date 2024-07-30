<?php
include APP_PATH.'/library/firebase/Push.php';
include APP_PATH.'/library/firebase/firebase.php';
class PushController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
    }

    /**
     * Este metodo se usa para enviar un mensaje en broadcast a todos los dispositivos dependiendo
     * el topic seleccionado
     * @param $title - titulo del mensaje
     * @param $message - mensaje a ser enviado
     * @param $topic - topic elegido como el de despliegue o el de usuarios
     * @return string - respuesta del servicio
     */
    public function sendPushMessageAction($title,$message,$topic){

        $util = new UtilitiesController();
        if($util->isNotEmptyValue($title) == "" ||
            $util->isNotEmptyValue($message) == "" ||
            $util->isNotEmptyValue($topic) == ""){
            $response['status'] = false;
            $response['message'] = "Error - Alguno de los datos esta vacio";
            return json_encode($response);
        }

        $firebase = new firebase();
        $push = new Push();

        $push->setTitle($title);
        $push->setMessage($message);
        $push->setIsAlert(true);
        $push->setImage('');

        $response = $firebase->sendToTopic($topic, $push->getPushAlert());
        return json_encode($response);
    }

    /**
     * este metodo es usado para enviar noticias en broadcast a los usuarios
     * @param $idNoticia - id de la noticia para la consulta
     * @param $topic_type - el topic que se eligio en el combox o despliegue o usuario
     * @return string - respuesta del servicio
     */
    public function sendPushNoticeAction($idNoticia,$topic_type){

        $util = new UtilitiesController();
        if($util->isNotEmptyValue($idNoticia) == "" ||
            $util->isNotEmptyValue($topic_type) == "" ){
            $response['status'] = false;
            $response['message'] = "Error - Alguno de los datos esta vacio";
            return json_encode($response);
        }

        $id = (integer)$idNoticia;
        $data = Tip::query()->where('idtip = :id: ',
            array('id' =>$id))
            ->execute()->getFirst();
        $title = $data->name;
        $message=$data->detail;
        $topic ="";
        if($topic_type =="Fuerzas"){
            $topic="ge911AdminTest";
        }
        if ($topic_type =="Usuarios"){
            $topic="ge911Emergency";
        }
                $firebase = new firebase();
                $push = new Push();

                $push->setTitle($title);
                $push->setMessage($message);
                $push->setIsAlert(true);
                $push->setImage('');

        $response = $firebase->sendToTopic($topic, $push->getPushAlert());
        return json_encode($response);
    }
}

