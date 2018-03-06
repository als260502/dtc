<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 02/10/2017
 * Time: 12:41
 */

namespace App\Controllers;


use App\Models\Chassi;
use App\Models\Gpon;
use Core\BaseController;
use Core\Telnet;
use Core\Validator;
use Core\Redirect;

class GponController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->chassi = new Chassi();

    }

    public function index()
    {
        $this->setPageTitle("Gerência de ONU");
        return $this->renderView('/mon_dtc/main', 'layout');
    }

    public function config($request)
    {
        $this->setPageTitle("Configurar ONU");

        $this->view->chassi = Chassi::all();
        $this->renderView('/onu/config', 'layout');

    }

    public function findSerial($request)    {

        $this->setPageTitle("Configurar ONU");
        $this->view->chassi = Chassi::all();

        if (isset($request->post->chassi[0])) {

            if ($request->post->chassi[0] != '0') {
                preg_match_all("/[0-9]/", $request->post->chassi[0], $olt);
                $ch = Chassi::where('id', $olt[0])->first();

                $tn = new Telnet($ch->address);
                $mac = $tn->getDiscoveredOnu($request->post->olt);

                if (isset($mac)) {
                    $this->view->mac = $mac;
                    $this->view->class = 'info';

                    $olt = $ch->olt()->where('index', $request->post->olt)->first();
                    $gpon = (isset($olt->id)) ? Gpon::where('olt_id', $olt->id)->orderBy('service_port', 'desc')->first() : null;
                    $this->view->onuIndex = (isset($gpon->onu_index)) ? $gpon->onu_index + 1 : 0;
                    $this->view->servicePort = (isset($gpon->service_port)) ? $gpon->service_port + 1 : 0;
                    $this->view->vlan = (isset($gpon->vlan)) ? $gpon->vlan + 1 : 0;

                    $this->view->chassiNumber = $request->post->chassi;
                    $this->view->oltNumber = (isset($olt->id))?$olt->id:1;

                    //var_dump($this->view->oltNumber,$this->view->chassiNumber ) ;


                } else {
                    $this->view->mac = "Nenhum gpon encontrado!";
                    $this->view->class = 'warning';
                }

            }
        }

        $this->renderView('/onu/config', 'layout');

    }

    public function configOnu($request)
    {

        $gpon = new Gpon();
        $chassiNumber = substr($request->post->chassi[0], 1,1);
        $ch = $ch = Chassi::where('id', $chassiNumber)->first();

        $olt = $ch->olt()->where('index', $request->post->olt)->first();
        $tn = new Telnet($ch->address);

        var_dump($request);die;

        $validate = ['onu_name' => $request->post->name
            ,'serial_number' => $request->post->serial
            ,'chassi' => $chassiNumber
            ,'olt' => $request->post->olt
            ,'selectionPorts' => $request->post->selectionPorts

        ];

        if(Validator::make($validate, $gpon->validate()))
        {
            return Redirect::routeRedirect('/dtc/config',[
                'Error' => "campos obrigatorio estão em branco"]);
        }

        for ($i = 0; $i < $request->post->selectionPorts; $i++){
            $data = ['onu_index' => $request->post->onu_index
                ,'onu_name' => $request->post->name
                ,'serial_number' => $request->post->serial
                ,'port_number' => $i+1
                ,'vlan' => $request->post->vlan+$i
                ,'service_port' => $request->post->service_port+$i
                ,'olt_id' => $olt->id
            ];

            $gp = $gpon->create($data);
            $gponId[] = $gp->id;

        }


        $this->renderView('/onu/config', 'layout');
    }

}