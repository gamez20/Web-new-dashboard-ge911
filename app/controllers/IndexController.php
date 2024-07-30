<?php
class IndexController extends ControllerBase
{
    public function indexAction()
    {
        if($this->session->get("user-name")){
            return $this->response->redirect('principal');
        }
        else{}
    }
}