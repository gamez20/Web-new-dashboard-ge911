<?php

class ButtonController extends \Phalcon\Mvc\Controller
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
        $name = $this->request->getPost('name');
        $resume = $this->request->getPost('resume');
        $detail = $this->request->getPost('detail');
        //TODO validar datos
        if ($this->request->isPost()) {
            $util = new UtilitiesController();
            if($util->isNotEmptyValue($name) == "" ||
                $util->isNotEmptyValue($resume) == "" ||
                $util->isNotEmptyValue($detail) == "" ){
                $response['status'] = false;
                $response['message'] = "Alguno de los datos esta vacio";
                return json_encode($response);
            }
            $this->view->dataSearch = $this->searchTip($name,$resume,$detail);
        }else{
                $this->view->dataSearch = Tip::find()->toArray();
            }

        $this->view->setTemplateAfter("layout");
        $this->view->name        =$name;
        $this->view->resume      =$resume   ;
        $this->view->detail      =$detail   ;

    }

    public function searchTip($name,$resume,$detail){

        $filterName   = "";
        $filterResume      = "";
        $filterDetail     = "";

        if($name!= null || $name!=''){
            $filterName =" AND  location like '%".$name."%'";
        }
        if($resume   != null || $resume   !=''){
            $filterResume    =" AND  phone like '%".$resume."%'";
        }
        if($detail   != null || $detail   !=''){
            $filterDetail    =" AND  email like '%".$detail."%'";
        }

        $data = Tip::query()
            ->where("name =".$filterName.$filterResume.$filterDetail,
                array( ))
            ->execute()->toArray();

        return $data;
    }

    public function botonesAction()
    {
        $data = UserAdmin::find();
        die(var_dump($data));
    }

    //***********************************************************************
    //Servicios botones
    //***********************************************************************

    public function allButtonAction(){
        $data = ButtonFree::find()->toArray();
        return $data;
    }

    public function searchButtonByIdAction(){
        $id   = $this->request->getPost('id_button');
        return $this->searchButtonById($id);
    }

    public function searchButtonById($id){
        $data = ButtonFree::query()
            ->where('id_button = :id: ',
                array('id' =>$id))
            ->execute()->getFirst();
        return $data;
    }

    public function addButtonAction(){

        $update = $this->request->getPost('update');
        $id     = $this->request->getPost('id');
        $url    = "";//$this->request->getPost('url');
        $name   = $this->request->getPost('name');

        $logs = $this->config["logosDir"];
        $folderUser = $logs["logos"] ;
        if ($this->request->hasFiles() == true) {
            // Print the real file names and their sizes
            foreach ($this->request->getUploadedFiles() as $file) {
                //Move the file into the application
                //se extrae la extencion
                $extension = $file->getExtension();
                $file->moveTo($folderUser."/". $name.".".$extension);
                $url=$folderUser."/". $name.".".$extension;
            }
        }

        if($update == true){
            $button= $this->searchButtonById($id);
            $button->url     = $url;
            $button->name    = $name;
        }else{
            $button= new ButtonFree();
            $button->url     = $url;
            $button->name    = $name;
        }

        if ($button->save() == false) {
            $id = false;
        }else{
            $id = $button->getWriteConnection()->lastInsertId();
        }
        return $this->response->redirect('button');
    }

    public function deleteButtonByIdAction(){
        $id   = $this->request->getPost('id_button');
        $button= $this->searchButtonById($id);
        if ($button!= false){
            if ($button->delete() === false) {
                $retorno = false;
            } else {
                $retorno = true;
            }
        }
        return $retorno;
    }

   /**public function noticiaAction()
    {
        $phql='select *  from Tip';
        $id_and_post=$this->modelsManager->executeQuery($phql);

        foreach ($id_and_post as $up){
            echo "Id"," ",$up->idtip,"<br>";
            /**echo "Imagen"," ",$up->url_image,"<br>";
            echo "name"," ",$up->name,"<br>";
            echo "resume"," ",$up->resume,"<br>";
            echo "detail"," ",$up->detail,"<br>";
            echo "<br>";

        }

    }**/





}