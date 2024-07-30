<?php
/**
 * Created by PhpStorm.
 * User: paycores-05
 * Date: 19/01/18
 * Time: 11:15 AM
 */

/**
Add a comment to this line
- * @author Ravi Tamada
- * @link URL Tutorial link
- */
class Push {

    // push message title
    private $title;
    private $message;
    private $image;
    private $text;
    // push message payload
    private $data;
    // flag indicating whether to show the push
    // notification or not
    // this flag will be useful when perform some opertation
    // in background when push is recevied
    private $is_background;
    private $lat;
    private $long;
    private $isAlert;

    function __construct() {

           }

    public function setTitle($title) {
                $this->title = $title;
            }

    public function setIsAlert($isAlert) {
              $this->isAlert = $isAlert;
          }

   public function setMessage($message) {
                $this->message = $message;
            }

    public function setImage($imageUrl) {
                $this->image = $imageUrl;
            }

    public function setText($text) {
        $this->text = $text;
    }

    public function setPayload($data) {
                $this->data = $data;
            }

    public function setIsBackground($is_background) {
                $this->is_background = $is_background;
            }
    public function setLat($lat) {
                $this->lat = $lat;
            }
    public function setLong($long) {
                $this->long = $long;
            }

    public function getPushAlert() {
                $res = array();
                $res['data']['title'] = $this->title;
                //$res['data']['is_background'] = $this->is_background;
                $res['data']['message'] = $this->message;
                $res['data']['text'] = $this->text;
                $res['data']['isAlert'] = $this->isAlert;
                $res['data']['image'] = $this->image;
                //$res['data']['payload'] = $this->data;
                //$res['data']['lat'] = $this->lat;
                //$res['data']['long'] = $this->long;
                $res['data']['timestamp'] = date('Y-m-d G:i:s');
                return $res;
    }
    public function getPushEmergency() {
        $res = array();
        $res['data']['title'] = $this->title;
        $res['data']['is_background'] = $this->is_background;
        $res['data']['message'] = $this->message;
        $res['data']['text'] = $this->text;
        $res['data']['isAlert'] = $this->isAlert;
        $res['data']['image'] = $this->image;
        $res['data']['payload'] = $this->data;
        $res['data']['lat'] = $this->lat;
        $res['data']['long'] = $this->long;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');
        return $res;
    }

}