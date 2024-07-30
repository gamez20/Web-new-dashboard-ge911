<?php

class ReportespdfController extends \Phalcon\Mvc\Controller
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

        $direction = $this->request->getPost('name');
        $location=$this->request->getPost('location');
        $emergency = $this->request->getPost('emergency');
        $polares=$this->request->getPost('polares');
        $date = $this->request->getPost('date');
        $hour= $this->request->getPost('detail');
        $status=$this->request->getPost('status');
        $idadmin=$this->request->getPost('iddmin');
        $auxAdmin=$this->request->getPost('auxAdmin');

        //die($name);
        //TODO validar datos
        if ($this->request->isPost()) {
            $util = new UtilitiesController();
            if($util->isNotEmptyValue($direction) == "" ||
                $util->isNotEmptyValue($location) == "" ||
                $util->isNotEmptyValue($emergency) == "" ||
                $util->isNotEmptyValue($polares) == ""||
                $util->isNotEmptyValue($date) == "" ||
                $util->isNotEmptyValue($hour) == "" ||
                $util->isNotEmptyValue($status) == "" ||
                $util->isNotEmptyValue($idadmin) == "" ||
                $util->isNotEmptyValue($auxAdmin) == ""

            ){
                $response['status'] = false;
                $response['message'] = "Alguno de los datos esta vacio";
                return json_encode($response);
            }
            $this->view->dataSearch = $this->searchEmergency($direction,$location,$emergency,$polares,$date,$hour,$status,$idadmin,$auxAdmin);
        }else{
            $this->view->dataSearch =$this->searchEmergency($direction,$location,$emergency,$polares,$date,$hour,$status,$idadmin,$auxAdmin);
        }

        $this->view->setTemplateAfter("layout");
        $this->view->direction   =$direction;
        $this->view->location    =$location   ;
        $this->view->emergency   =$emergency   ;
        $this->view->polares     =$polares   ;
        $this->view->date        =$date   ;
        $this->view->hour        =$hour   ;
        $this->view->status      =$status   ;
        $this->view->idadmin      =$idadmin   ;
        $this->view->auxAdmin     =$auxAdmin   ;

    }


    public function searchEmergency(){

        $data = TbEmergency::query()
            ->columns([
                'count(id_emergency) as total',
                'idadmin',

            ] )
            ->groupBy("idadmin")
            ->execute()->toArray();


        return $data;

    }

    public function createAction()
    {
        $name = $this->request->getPost('name');
        $phone = $this->request->getPost('phone');
        $location = $this->request->getPost('location');
        $emergency = $this->request->getPost('emergency');
        $date =$this->request->getPost('date');
        $hour =$this->request->getPost('hour');
        $placa=$this->request->getPost('placa');
        $polares=$this->request->getPost('polares');


        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $libs = $this->config->application->libraryDir;
        include($libs."mpdf60/mpdf.php");
        $mpdf=new mPDF('');
        $dt =date("d_m_Y");


        $html =
            ' 
        <div style="font-size: 12px;font-family: helvetica;">
            <div style="width:48%;float:left;"> 
                <div style="width:80%; margin-top:100px;border:0.5px solid #434343;">
                    <div style="width: 100%;padding:15px;background:#434343;color:white;"><h3>Reporte de Emergencias</h3></div>
                    <div style="padding:10px;border-bottom:0.5px solid #434343;"><b>Nombre:</b> ' . $name . '</div>
                    <div style="padding:10px;border-bottom:0.5px solid #434343;"><b>Teléfono:</b> ' . $phone . '</div>
                    <div style="padding:10px;border-bottom:0.5px solid #434343;"><b>Ubicación:</b> ' . $location . '</div>
                    <div style="padding:10px;border-bottom:0.5px solid #434343;"><b>Emergencia:</b>' . $emergency . '</div>
                    <div style="padding:10px;border-bottom:0.5px solid #434343;"><b>Date:</b>' . $date . '</div>
                    <div style="padding:10px;border-bottom:0.5px solid #434343;"><b>Hora:</b>' . $hour. '</div>
                    <div style="padding:10px;border-bottom:0.5px solid #434343;"><b>Placa:</b>' . $placa . '</div>
                    <div style="padding:10px;border-bottom:0.5px solid #434343;"><b>Polares:</b>' . $polares . '</div>
                </div>
            </div>
            <div style="width:50%;margin-top:100px; float:left;">
                <div style="width:100%;padding:15px; float: left;">
                    <div style="width: 100%;padding:20px;border:0.5px solid #434343">
                    <div style="text-align: center;font-size: 10px;width:10%;float:left;">50</div> 
                    <div style="text-align: center;font-size: 10px;width:15%;float:left;">100</div> 
                    <div style="text-align: center;font-size: 10px;width:15%;float:left;">150</div> 
                    <div style="text-align: center;font-size: 10px;width:15%;float:left;">200</div>
                    <div style="text-align: center;font-size: 10px;width:15%;float:left;">250</div>
                    <div style="text-align: center;font-size: 10px;width:15%;float:left;">300</div>
                        <div style="width: 20%;padding:25px;margin-top: 5%;background: #f1c40f;color:white;text-align: center;">
                            148
                        </div>
                        <div style="width: 50%;padding:25px;margin-top: 5%;background: #e67e22;color:white;text-align: center;">
                            148
                        </div>
                        <div style="width: 10%;padding:25px;margin-top: 5%;background: #8e44ad;color:white;text-align: center;">
                            148
                        </div>
                    </div>
                </div>
                <div style="width:100%;border-radius:5px;border:0.6px solid #434343;padding:15px; float: left;">
                
                    <div style="width:25%;float: left;height:20px;padding:5px;background: #f1c40f;border-radius:100%;color:white;font-size: 12px;text-align: center;margin-left:5px;"> 
                        Estadística 
                    </div> 
                    
                    <div style="width:25%;float: left;height:20px;padding:5px;background: #e67e22;border-radius:100%;color:white;font-size: 12px;text-align: center;margin-left:5px;"> 
                        Estadística 
                    </div> 
                    
                    <div style="width:25%;float: left;height:20px;padding:5px;background: #8e44ad;border-radius:100%;color:white;font-size: 12px;text-align: center;margin-left:5px;"> 
                        Estadística
                    </div>  
                    
                </div>
            </div>
            </div>';

        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        // OUTPUT

        $mpdf->Output("reportes/reporte_" . $dt.".pdf","F");
        $this->view->setTemplateAfter("layout");
    }


    ////////////////////////////
    //    FILTRO POR FUERZAS  //
   ////////////////////////////


    public function searchAction()
    {
        $fullName = $this->request->getPost('fullname');
        $email=$this->request->getPost('email');
        $phone = $this->request->getPost('phone');
        $placa=$this->request->getPost('placa');
        $city = $this->request->getPost('city');
        $password= $this->request->getPost('password');
        $ocupation=$this->request->getPost('ocupation');


        //die($name);
        //TODO validar datos
        if ($this->request->isPost()) {
            $util = new UtilitiesController();
            if($util->isNotEmptyValue($fullName) == "" ||
                $util->isNotEmptyValue($email) == "" ||
                $util->isNotEmptyValue($phone) == "" ||
                $util->isNotEmptyValue($placa) == ""||
                $util->isNotEmptyValue($city) == "" ||
                $util->isNotEmptyValue($password) == "" ||
                $util->isNotEmptyValue($ocupation) == ""

            ){
                $response['status'] = false;
                $response['message'] = "Alguno de los datos esta vacio";
                return json_encode($response);
            }
            $this->view->dataSearch = $this->searchEmergencyForce($fullName,$email,$phone,$placa,$city,$password,$ocupation);
        }else{
            $this->view->dataSearch =TbEmergency::find()->toArray();
        }

        $this->view->setTemplateAfter("layout");
        $this->view->direction   =$fullName;
        $this->view->location    =$email   ;
        $this->view->emergency   =$phone   ;
        $this->view->polares     =$placa  ;
        $this->view->date        =$city   ;
        $this->view->hour        =$password   ;
        $this->view->status      =$ocupation   ;


    }


    public function searchEmergencyForce(){

        $data = TbEmergency::query()
            ->orderBy("idadmin asc")
            ->execute()->toArray();
        return $data;

    }


     public function fechaAction()
     {
         $direction = $this->request->getPost('direction');
         $location = $this->request->getPost('location');
         $emergency = $this->request->getPost('emergency');
         $polares=$this->request->getPost('polares');
         $date =$this->request->getPost('date');
         $hour =$this->request->getPost('hour');
         $status=$this->request->getPost('status');
         $auxAdmin=$this->request->getPost('polares');

         $this->response->setHeader('Access-Control-Allow-Origin', '*');
         $libs = $this->config->application->libraryDir;
         include($libs."mpdf60/mpdf.php");
         $mpdf=new mPDF('');
         $dt =date("d_m_Y");


         $html =
             'Reporte de emergencias atendidas por fecha<br>
             Dirección: ' . $direction . '<br>
             Ubicación: ' . $location . '<br>
             Emergencia:' . $emergency . '<br>
             Polares:' . $polares . '<br>
             Date:' . $date . '<br>
             Hora:' . $hour. '<br>
             Status:' . $status . '<br>
             Aux :' . $auxAdmin . '<br>
             ';


         $mpdf->SetDisplayMode('fullpage');
         $mpdf->WriteHTML($html);

         // OUTPUT

         $mpdf->Output("/var/www/html/GE911-Final/public/reportesfechas/Informe_por_Fecha_" . $dt.".pdf","F");
     }

    public function reportForcesAction(){

        $dataForces = TbAdmin::query()
            ->columns(['*'])
            ->execute()->toArray();
        $rows_forces = "";
        foreach ($dataForces as $data){
            $status = ($data['activeStatus'])?'Activo':'Inactivo';
            $rows_forces .= '<tr>' .
                '<td>'.$data['fullName'].'</td>' .
                '<td>'.$data['email'].'</td>' .
                '<td>'.$data['phone'].'</td>' .
                '<td>'.$data['placa'].'</td>' .
                '<td>'.$data['city'].'</td>' .
                '<td>'.$data['ocupation'].'</td>' .
                '<td>'.$status.'</td>' .
                '</tr>' ;
        }
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $libs = $this->config->application->libraryDir;
        include($libs."mpdf60/mpdf.php");
        $mpdf=new mPDF('');
        $dt =date("His");
        $mes = date("MY");

        $html = ' <div style="font-size: 12px;font-family: helvetica;">
         <table class="tablence">
            <thead>
            <tr class="bg-teal-light text-white">
                <th>fullName </th>
                <th>email </th>
                <th>phone </th>
                <th>placa </th>
                <th>city  </th>
                <th>ocupation  </th>
                <th>activeStatus </th> 
            </tr>
            </thead>
            <tbody> 
                
                    '.$rows_forces.' 
                 
            </tbody>
            <div class="col-md-2 center">
            </div>
        </table>
    </div>';

        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        // OUTPUT
        mkdir("reportes/" . $mes, 0777);
        $mpdf->Output("reportes/" . $mes . "/repForce_" . $dt.".pdf","F");
        echo ' <div class="bg-black animated fadeInDown">
                    <table class="tablence-ge">
                        <thead>
                        <tr class="bg-teal-light text-white">
                            <th>fullName </th>
                            <th>email </th>
                            <th>phone </th>
                            <th>placa </th>
                            <th>city  </th>
                            <th>ocupation  </th>
                            <th>activeStatus </th> 
                        </tr>
                        </thead>
                        <tbody> 
                
                    '.$rows_forces.' 
                 
            </tbody> 
        </table>
    </div>
    <a href="reportes/' . $mes . '/repForce_' . $dt.'.pdf" target="_blank"> 
        <div class="font-s36 text-black text-shadow animated fadeIn" id="btn_see_pdf">
            <p>Ver en pdf</p>
        </div>
    </a>';
    }

    public function reportUserActiveAction(){

        $dataForces = TbUser::query()
            ->columns(['*'])
            ->where('statusActive=0',
                array())
            ->execute()->toArray();
        $rows_forces = "";

        foreach ($dataForces as $data){

            $rows_forces .= '<tr>' .
                '<td>'.$data['fullName'].'</td>' .
                '<td>'.$data['email'].'</td>' .
                '<td>'.$data['phone'].'</td>' .
                '<td>'.$data['gender'].'</td>' .
                '</tr>' ;
        }
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $libs = $this->config->application->libraryDir;
        include($libs."mpdf60/mpdf.php");
        $mpdf=new mPDF('');
        $dt =date("His");
        $mes = date("MY");

        $html = ' <div style="font-size: 12px;font-family: helvetica;">
         <table class="tablence">
            <thead>
            <tr class="bg-teal-light text-white">
                <th>fullName </th>
                <th>email </th>
                <th>phone </th>
                <th>gender </th> 
            </tr>
            </thead>
            <tbody> 
                
                    '.$rows_forces.' 
                 
            </tbody> 
        </table>
    </div>';

        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        // OUTPUT
        mkdir("reportes/" . $mes, 0777);
        $mpdf->Output("reportes/" . $mes . "/repUsersActive_" . $dt.".pdf","F");

        echo ' <div class="bg-white">
         <table class="tablence">
            <thead>
            <tr class="bg-teal-light text-white">
                <th>fullName </th>
                <th>email </th>
                <th>phone </th>
                <th>gender </th> 
            </tr>
            </thead>
            <tbody>  
                    '.$rows_forces.'  
            </tbody> 
        </table>
    </div>
    <a href="reportes/' . $mes . '/repUsersActive_' . $dt.'.pdf" target="_blank">
        <div class="font-s36 text-black text-shadow animated fadeIn" id="btn_see_pdf">
            <p>Ver en pdf</p>
        </div>
    </a>';
    }

    public function reportUserBlockAction(){

        $dataForces = TbUser::query()
            ->columns(['*'])
            ->where('statusActive=1',
                array())
            ->execute()->toArray();
        $rows_forces = "";

        foreach ($dataForces as $data){

            $rows_forces .= '<tr>' .
                '<td>'.$data['fullName'].'</td>' .
                '<td>'.$data['email'].'</td>' .
                '<td>'.$data['phone'].'</td>' .
                '<td>'.$data['gender'].'</td>' .
                '</tr>' ;
        }
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $libs = $this->config->application->libraryDir;
        include($libs."mpdf60/mpdf.php");
        $mpdf=new mPDF('');
        $dt =date("His");
        $mes = date("MY");

        $html = ' <div style="font-size: 12px;font-family: helvetica;">
         <table class="tablence">
            <thead>
            <tr class="bg-teal-light text-white">
                <th>fullName </th>
                <th>email </th>
                <th>phone </th>
                <th>gender </th> 
            </tr>
            </thead>
            <tbody> 
                
                    '.$rows_forces.' 
                 
            </tbody> 
        </table>
    </div>';

        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        // OUTPUT
        mkdir("reportes/" . $mes, 0777);
        $mpdf->Output("reportes/" . $mes . "/repUsersBlock_" . $dt.".pdf","F");

        echo ' <div class="bg-black animated fadeInDown">
         <table class="tablence-ge">
            <thead>
            <tr class="bg-teal-light text-white">
                <th>fullName </th>
                <th>email </th>
                <th>phone </th>
                <th>gender </th> 
            </tr>
            </thead>
            <tbody> 
                
                    '.$rows_forces.' 
                 
            </tbody> 
        </table>
    </div>
    <a href="reportes/' . $mes . '/repUsersBlock_' . $dt.'.pdf" target="_blank">
        <div class="font-s36 text-black text-shadow animated fadeIn" id="btn_see_pdf">
            <p>Ver en pdf</p>
        </div>
    </a>';
    }

    public function reportEmergencyByDayAction(){

        $dataEmer = TbEmergency::query()
            ->columns([
                'count(id_emergency) as total',
                'date',
            ] )
            ->groupBy("date")
            ->execute()->toArray();

        $rows_emer = "";
        foreach ($dataEmer as $data){
            $rows_emer .= '<tr>' .
                '<td>'.$data['date'].'</td>' .
                '<td>'.$data['total'].'</td>' .
                '</tr>' ;
        }
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $libs = $this->config->application->libraryDir;
        include($libs."mpdf60/mpdf.php");
        $mpdf=new mPDF('');
        $dt =date("His");
        $mes = date("MY");
        $html = ' <div style="font-size: 12px;font-family: helvetica;">
         <table class="tablence">
            <thead>
            <tr class="bg-teal-light text-white">
                <th>date </th>
                <th>total </th> 
            </tr>
            </thead>
            <tbody> 
                
                    '.$rows_emer.' 
                 
            </tbody> 
        </table>
    </div>';

        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        // OUTPUT
        mkdir("reportes/" . $mes, 0777);
        $mpdf->Output("reportes/" . $mes . "/repEmerDay_" . $dt.".pdf","F");
        echo' <div class="bg-black animated fadeInDown">
         <table class="tablence-ge">
            <thead>
            <tr class="bg-teal-light text-white">
                <th>date </th>
                <th>total </th> 
            </tr>
            </thead>
            <tbody> 
                
                    '.$rows_emer.' 
                 
            </tbody> 
        </table>
    </div>
    <a href="reportes/' . $mes . '/repEmerDay_' . $dt.'.pdf" target="_blank">
        <div class="font-s36 text-black text-shadow animated fadeIn" id="btn_see_pdf">
            <p>Ver en pdf</p>
        </div>
    </a>';
    }

    public function reportEmergencyByAdminAction(){

        $dataEmer = TbEmergency::query()
            ->columns([
                'count(id_emergency) as total',
                'idadmin',
                'ad.placa',
                'ad.fullName',
            ] )
            ->leftJoin("TbAdmin","ad.id_admin = TbEmergency.idadmin", "ad")
            ->groupBy("idadmin")
            ->execute()->toArray();

        $rows_emer = "";
        foreach ($dataEmer as $data){
            $rows_emer .= '<tr>' .
                '<td>'.$data['total'].'</td>' .
                '<td>'.$data['placa'].'</td>' .
                '<td>'.$data['fullName'].'</td>' .
                '</tr>' ;
        }
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $libs = $this->config->application->libraryDir;
        include($libs."mpdf60/mpdf.php");
        $mpdf=new mPDF('');
        $dt =date("His");
        $mes = date("MY");

        $html = '<div style="font-size: 12px;font-family: helvetica;">
         <table class="tablence">
            <thead>
            <tr class="bg-teal-light text-white">
                <th>total</th>
                <th>placa </th> 
                <th>fullName </th> 
            </tr>
            </thead>
            <tbody>  
                    '.$rows_emer.'  
            </tbody> 
        </table>
    </div>';

        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        // OUTPUT
        mkdir("reportes/" . $mes, 0777);
        $mpdf->Output("reportes/" . $mes . "/repEmerAdmin_" . $dt.".pdf","F");

        echo '<div class="bg-black animated fadeInDown">
         <table class="tablence-ge">
            <thead>
            <tr class="bg-teal-light text-white">
                <th>total</th>
                <th>placa </th> 
                <th>fullName </th> 
            </tr>
            </thead>
            <tbody>  
                    '.$rows_emer.'  
            </tbody> 
        </table>
    </div>
    <a href="reportes/' . $mes . '/repEmerAdmin_' . $dt.'.pdf" target="_blank">
        <div class="font-s36 text-black text-shadow animated fadeIn" id="btn_see_pdf">
            <p>Ver en pdf</p>
        </div>
    </a>';
    }


}




