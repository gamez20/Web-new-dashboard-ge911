<?php

/**
 * Created by PhpStorm.
 * User: paycore
 * Date: 10/02/17
 * Time: 11:50 AM
 */ 
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Filter;
class UtilitiesController extends ControllerBase
{

    /*
   * Funcion que retorna los ultimos caracteres de una cadena
   * segun el valor del value, iniciendo de derecha a izquierda
   * */
    public static function extractLastChars($string,$value )
    {
        return substr($string, -(intval($value)));
    }


    /**
     * Funcionq ue valida si un elemento del arreglo es vacio o nulo
     * @param $data El arreglo con el dato a validar
     * @param $value La key de validaciond el dato
     * @return mixed Arreglo que contiene el resultado de la operacion
     */
    public function isNotEmptyValueArray($data,$value){
        $response['status']=true;
        if (empty($data[$value])  ){//&& isset($data[$value])) {
            $response['status']=false;
            $response['Message']= $value." es requerido";

        }
        return $response;
    }

    /**
     * Funcion que valida si un elemento del arreglo es vacio o nulo
     * @param $data El valor con el dato a validar
     * @return mixed Arreglo que contiene el resultado de la operacion
     */
    public function isNotEmptyValue($data){
        $response = $data;
        if (empty($data)){//&& isset($data[$value])) {
            /*$response['status'] = false;
            $response['message'] = "Dato no existe es valido" ;*/
            $response = "";
        }
        return $response;
    }


    /**
     * Funcion que obtiene la ip de donde se realiza la peticion a alguno de los servicios
     * @return mixed La ip de donde se realiza la peticion
     */
    public function getUserIP()
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }
        return $ip;
    }

    /**
     * Funcion que valida el formato de una fecha.phtml
     * @param $data El valor de fecha.phtml a ser validado
     * @param $format El formato que debe tener la cadena ingresada
     * @return bool true si es valida , false en caso contrario
     */
    public function validateDateFormat($data,$format){
        $d = DateTime::createFromFormat($format, $data);
        if ($d && $d->format($format) == $data){ //fecha.phtml es valida
            return true;
        }
        return false;
    }

    /**
     * FUncionq ue valida si es una cadena con longitud deseasa
     * @param $string La cadena a validad
     * @param $long La longitud de la cadena
     * @return bool|string true, si es correcto, en caso contrario el mensaje de error
     */
    public function PayCoreString($key, $string,$long){
        if (!empty($string) && $string != null)
            if (strlen($string)<=$long)
                return "";
            else
                return "El parametro $key con el valor \"$string\" supera la longitud permitida";
        else
            return "StringVal:El parametro $key no puede ser vacio";
    }

    /**
     * FUncion que valida si es una cadena es numero con longitud deseasa
     * @param $string La cadena a validad
     * @param $long La longitud de la cadena
     * @return bool|string true, si es correcto, en caso contrario el mensaje de error
     */
    public function PayCoreNumber($key, $number, $long){
        $number = trim($number);
        if ($number != "")
            if (strlen($number)<=$long)
                if (is_numeric ($number))
                    return "";
                else
                    return "El parametro $key no es numerico";
            else
                return "El parametro $key con el valor \"$number\"  supera la longitud permitida";
        else
            return "integerVal:El parametro $key no puede ser vacio";
    }

    public function sanitizeDataType($data,$type){
        $filter = new Filter();
        return $filter->sanitize($data,$type);
    }

    public function pointToPoint($point1_lat, $point1_long, $point2_lat, $point2_long, $unit = 'km', $decimals = 2) {
        // Cálculo de la distancia en grados
        $degrees = rad2deg(acos((sin(deg2rad($point1_lat))*sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat))*cos(deg2rad($point2_lat))*cos(deg2rad($point1_long-$point2_long)))));

        // Conversión de la distancia en grados a la unidad escogida (kilómetros, millas o millas naúticas)
        switch($unit) {
            case 'km':
                $distance = $degrees * 111.13384; // 1 grado = 111.13384 km, basándose en el diametro promedio de la Tierra (12.735 km)
                break;
            case 'mt':
                $distance = ($degrees * 111.13384)*1000; //
                break;
            case 'mi':
                $distance = $degrees * 69.05482; // 1 grado = 69.05482 millas, basándose en el diametro promedio de la Tierra (7.913,1 millas)
                break;
            case 'nmi':
                $distance =  $degrees * 59.97662; // 1 grado = 59.97662 millas naúticas, basándose en el diametro promedio de la Tierra (6,876.3 millas naúticas)
        }
        return round($distance, $decimals);
    }
}