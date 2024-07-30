<?php

class TipController extends \Phalcon\Mvc\Controller
{

    public function initialize()
    {
        if ($this->session->has("user-name")) {
        } else {
            return $this->response->redirect('');
        }
    }

    public function indexAction()
    {

    }
    //***********************************************************************
    //Servicios Tips.
    //***********************************************************************

    public function allTipAction(){
        $data = Tip::find()->toArray();
        return $data;
    }

    public function searchTipByIdAction(){
        $id   = $this->request->getPost('idtip');
        return $this->searchTipById($id);
    }

    public function searchTipById($id){
        $data = Tip::query()
            ->where('idtip = :id: ',
                array('id' =>$id))
            ->execute()->getFirst();
        return $data;
    }

    public function addTipAction(){
        if($this->request->isPost()){
            $util = new UtilitiesController();
            if($util->isNotEmptyValue($this->request->getPost('name')) != ""
                || $util->isNotEmptyValue($this->request->getPost('resume'))){
        $update         = $this->request->getPost('update');
        $id             = $this->request->getPost('id');
        $url_Image  = "";//$this->request->getPost('url_Image');
        $name       = $this->request->getPost('name');
        $resume     = $this->request->getPost('resume');
        $detail     = $this->request->getPost('resume');

        $logs = $this->config["logosDir"];
        $folderUser = $logs["tips"] ;
        if ($this->request->hasFiles() == true) {
            // Print the real file names and their sizes
            foreach ($this->request->getUploadedFiles() as $file) {
                //Move the file into the application
                //se extrae la extencion
                $extension = $file->getExtension();
                $file->moveTo($folderUser."/". $name.".".$extension);
                $url_Image=$folderUser."/". $name.".".$extension;
            }
        }

        if($update==true){
            $tip= $this->searchTipById($id);
            $tip->url_Image    = $url_Image;
            $tip->name         = $name;
            $tip->resume       = $resume;
            $tip->detail       = $detail;
        }else{
            $tip= new Tip();
            $tip->url_Image    = $url_Image;
            $tip->name         = $name;
            $tip->resume       = $resume;
            $tip->detail       = $detail;
        }

        if ($tip->save() == false) {
            $id = false;
        }else{
            $id = $tip->getWriteConnection()->lastInsertId();
        }
            }
            return $this->response->redirect('button');
        }
        return $this->response->redirect('button');
    }

    public function deleteTipByIdAction(){

        if($this->request->isPost()) {
            $id = $this->request->getPost('idtip');
            $tip = $this->searchTipById($id);
            if ($tip != false) {
                    $tip->delete();
                }
            return $this->response->redirect('button');
        }
    }
}

