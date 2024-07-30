<?php
class ArborController extends ControllerBase
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
        $this->view->setTemplateAfter("layout");
    }
}