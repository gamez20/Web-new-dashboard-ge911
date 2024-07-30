<?php

class PrincipalController extends \Phalcon\Mvc\Controller
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
        $data = TbEmergency::query()
            ->columns([
                'direction',
                'TbEmergency.location',
                'emergency',
                'polares',
                'date',
                'hour',
                'status',
                'iduser',
                'idadmin',
                'auxAdmin',
                //user
                'us.fullName as userName',
                'us.phone',
                'us.photo',
                'us.email',
                //admin
                'ad.fullName as adminName',
                'ad.placa',
                'ad.ocupation',
            ] )
            ->leftJoin("TbUser",
                "TbEmergency.iduser = us.id_user", "us")
            ->leftJoin("TbAdmin",
                "TbEmergency.idadmin = ad.id_admin", "ad")
            ->execute()->toArray();
        $this->view->allEmergency = $data;
    }

    public function forcesAction()
    {
        if ($this->request->isPost()) {
            $util = new UtilitiesController();
            $idAdmin = $util->isNotEmptyValue($this->request->getPost('idAdmin'));
            $name = $util->isNotEmptyValue($this->request->getPost('name'));
            $city = $util->isNotEmptyValue($this->request->getPost('city'));
            $phone = $util->isNotEmptyValue($this->request->getPost('phone'));
            $email = $util->isNotEmptyValue($this->request->getPost('email'));
            $ocupation = $util->isNotEmptyValue($this->request->getPost('ocupation'));

            $this->view->allDataForce = $this->searchUser($idAdmin,$name, $city, $phone, $email, $ocupation);
        } else {
            $this->view->allDataForce = $this->searchUser("","", "", "", "", "");
        }
        $this->view->setTemplateAfter("layout");
        $this->view->idAdmin = $idAdmin;
        $this->view->name = $name;
        $this->view->city = $city;
        $this->view->phone = $phone;
        $this->view->email = $email;
        $this->view->ocupation = $ocupation;
    }

    public function principalAction()
    {
        if ($this->request->isPost()) {
            $val = $this->request->getPost("active");
            if ($val == 0) {
                $data = "activeStatus = 0 ";
            } elseif ($val == 1) {
                $data = "activeStatus = 0 ";
            } else {
                $data = "activeStatus = 0 OR activeStatus = 1 ";
            }
            $dataForce = TbAdmin::query()
                ->columns([
                    'fullName',
                    'email',
                    'phone',
                    'placa',
                    'city',
                    'ocupation',
                    'location'
                ])
                ->where($data,
                    array())
                ->execute()->ToArray();
            $dataEmergency = TbEmergency::query()
                ->where("status = 1 ",
                    array())
                ->execute()->toArray();

            $dataUser = TbUser::query()->execute()->toArray();

            $response['status'] = true;
            $response['data'] = $dataForce;
            $response['dataa'] = $dataEmergency;
            $response['dataUser'] = $dataUser;
            return json_encode($response);
        } else {
            $response['status'] = false;
            $response['message'] = "No service avaliable";
            return json_encode($response);
        }
    }

    public function activeForceAction()
    {
        $this->view->setTemplateAfter("layout");
    }

    public function allForce()
    {
        $allForce = TbAdmin::query()
            ->columns([
                'fullName',
                'email',
                'phone',
                'placa',
                'city',
                'ocupation'
            ])
            ->execute()->toArray();

        return $allForce;

    }

    public function searchUser($idAdmin,$name, $city, $phone, $email, $ocupation)
    {

        $filterAdmin = "";
        $filterName = "";
        $filterCity = "";
        $filterPhone = "";
        $filterEmail = "";
        $filterOcupation = "";


        if ($idAdmin != null || $idAdmin != '') {
            $filterAdmin = " AND id_admin = ".$idAdmin;
        }
        if ($name != null || $name != '') {
            $filterName = " AND fullName like '%" . $name . "%'";
        }
        if ($city != null || $city != '') {
            $filterCity = " AND  city like '%" . $city . "%'";
        }
        if ($phone != null || $phone != '') {
            $filterPhone = " AND  phone like '%" . $phone . "%'";
        }
        if ($email != null || $email != '') {
            $filterEmail = " AND  email like '%" . $email . "%'";
        }
        if ($ocupation != null || $ocupation != '') {
            $filterOcupation = " AND  ocupation like '%" . $ocupation . "%'";
        }
        $data = TbAdmin::query()
            ->where("activeStatus < 2 " .$filterAdmin. $filterName . $filterCity . $filterPhone . $filterEmail . $filterOcupation,
                array())
            ->execute()->toArray();

        return $data;
    }

    public function deleteAdminAction()
    {
        if ($this->request->isPost()) {
            $id_admin = $this->request->getPost('id_admin');
            $admin = TbAdmin::query()
                ->where('id_admin = :id_admin: ',
                    array('id_admin' => $id_admin))
                ->execute()->getFirst();
            if ($admin != false) {
                $admin->delete();
            }
            return $this->response->redirect('principal');
        }
    }

    public function reloadMapAction()
    {
        $data = TbEmergency::query()
            ->where("status = 1 ",
                array())
            ->execute()->toArray();
        $response['status'] = true;
        $response['data'] = $data;
        return json_encode($response);
    }
}

