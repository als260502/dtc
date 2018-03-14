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
use App\Models\Olt;
use App\Models\Port;
use Core\BaseController;
use Core\Telnet;
use Core\Validator;
use Core\Redirect;

class GponController extends BaseController
{

    private $gpon;
    public function __construct()
    {
        parent::__construct();
        $this->chassi = new Chassi();
        $this->gpon = new Gpon();

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
        $this->view->ports = Port::all();
        $this->renderView('/onu/config', 'layout');

    }

    public function findSerial($request)
    {

        $this->setPageTitle("Configurar ONU");
        $this->view->chassi = Chassi::all();
        $this->view->ports = Port::all();

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
                    $this->view->oltNumber = (isset($olt->id)) ? $olt->id : 1;

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

        $this->setPageTitle("Configurar ONU");
        $this->view->chassi = Chassi::all();
        $this->view->ports = Port::all();

        $gpon = new Gpon();
        $chassiNumber = substr($request->post->chassi[0], 1, 1);
        $ch = $ch = Chassi::where('id', $chassiNumber)->first();

        $olt = $ch->olt()->where('index', $request->post->olt)->first();
        $tn = new Telnet($ch->address);

        //var_dump($request,$request->post->porta[0]);die;

        $validate = ['onu_name' => $request->post->name
            , 'serial_number' => $request->post->serial
            , 'chassi' => $chassiNumber
            , 'olt' => $request->post->olt

        ];

        if (Validator::make($validate, $gpon->validate())) {
            return Redirect::routeRedirect('/dtc/config', [
                'Error' => "campos obrigatorio estão em branco"]);
        }

        $onuIndex = (isset($olt->id)) ? Gpon::where('olt_id', $olt->id)->orderBy('service_port', 'desc')->first()->onu_index + 1 : 0;

        for ($i = 0; $i < count($request->post->porta); $i++) {
            $data = ['onu_index' => $onuIndex
                , 'onu_name' => $request->post->name
                , 'serial_number' => $request->post->serial
                , 'port_number' => $i + 1
                , 'vlan' => $request->post->vlan + $i
                , 'service_port' => $request->post->service_port + $i
                , 'olt_id' => $olt->id
            ];

            $gp = $gpon->create($data);
            $gponId[] = $gp->id;
            $tec[] = $ports = (isset($request->post->porta)) ? $request->post->porta[$i] : 'UTP';
        }


        $tecnologia = (count($tec) > 1) ? implode('/', $tec) : $tec[0];
        $newGpon = Gpon::find($gponId[0]);
        $newGpon->ports()->attach($request->post->porta_id);

        $tn->configOnu($newGpon, $tecnologia);

        //var_dump($tn->getError());

        if ($tn->getError()) {
            $this->view->error = $tn->getError();
            Gpon::destroy($gponId);
            $newGpon->ports()->detach();
            //var_dump($gponId);
        }

        $this->view->content = $tn->getContentData();
        $this->view->kompressor = $tn->getKompressorData();
        $this->view->complete = $tn->getResult();


        $this->renderView('/onu/config', 'layout');
    }

    public function change()
    {
        $this->setPageTitle("Troca de  ONU");

        $this->view->onu = Gpon::all()->sortBy('onu_name');


        $this->renderView('/onu/change', 'layout');
    }

    public function reset()
    {
        $this->setPageTitle("Reboot na ONU");
        $this->view->onu = Gpon::all()->sortBy('onu_name');

        $this->renderView('/onu/reset', 'layout');
    }

    public function mac()
    {
        $this->view->onu = Gpon::all()->sortBy('onu_name');
        $this->setPageTitle("Verificar MACs por traz da onu");

        $this->renderView('/onu/mac', 'layout');

    }

    public function activate()
    {
        $this->view->onu = Gpon::all()->sortBy('onu_name');
        $this->setPageTitle("Ativar/Desativar Portas");

        $this->renderView('/onu/ativar', 'layout');

    }

    public function manage()
    {
        $this->setPageTitle("Troca de  ONU");


        $this->renderView('/onu/change', 'layout');

    }

    public function changeOnu($request)
    {

        $this->view->onu = Gpon::all()->sortBy('onu_name');
        $gpon = Gpon::find($request->post->onuName);
        $olt = Olt::find($gpon->olt_id);
        $chassi = Chassi::find($olt->chassi_id);
        $oldSerial = $gpon->serial_number;

        $dataToValidate = ['id' => $request->post->onuName, 'serial_number' => $request->post->serialNumber];
        $data = ['serial_number' => $request->post->serialNumber];

        $tn = new Telnet($chassi->address);

        if (Validator::make($dataToValidate, $gpon->validateChange())) {
            return Redirect::routeRedirect('/dtc/change', [
                'Error' => "algo que nao esta certo esta errado!!!"]);
        }

        $gpon->update($data);
        if (!$tn->changeOnu($gpon)) {
            $this->view->error = $tn->getError();
            //var_dump($tn->getError());
            $gpon->update(['serial_number' => $oldSerial]);
        }

        $this->view->content = $tn->getContentData();
        $this->view->complete = $tn->getResult();

        $this->renderView('/onu/change', 'layout');

    }

    public function resetOnu($request)
    {

        $this->view->onu = Gpon::all()->sortBy('onu_name');
        $gpon = Gpon::find($request->post->onuName);
        $olt = Olt::find($gpon->olt_id);
        $chassi = Chassi::find($olt->chassi_id);

        $tn = new Telnet($chassi->address);

        if (!$tn->resetOnu($gpon)) {
            $this->view->error = $tn->getError();
        }

        $this->view->content = $tn->getContentData();
        $this->view->complete = 'complete';


        $this->renderView('/onu/reset', 'layout');

    }

    public function getMac($request)
    {

        $this->view->onu = Gpon::all()->sortBy('onu_name');
        $gpon = Gpon::find($request->post->onuName);
        $olt = Olt::find($gpon->olt_id);
        $chassi = Chassi::find($olt->chassi_id);

        $tn = new Telnet($chassi->address);

        $nGpon = $gpon->where(['onu_index' => $gpon->onu_index, 'olt_id' => $olt->id])->get();

        $totalMac = 0;
        $mac = array();
        for ($i = 0; $i < count($nGpon); $i++) {

            $tn->getMac($nGpon[$i]);
            $mac[$i] = $tn->getMacData();
            $mCount[] = count($tn->getMacData());

            $totalMac += $mCount[$i];

            $nMac[0] = $mac[$i];

        }

        $this->view->mac = $nMac[0];
        $this->view->macCount = $totalMac;

        $this->renderView('/onu/mac', 'layout');

    }


    public function getPorts($request)
    {

        $gpon = Gpon::find($request->post->id);
        $olt = Olt::find($gpon->olt_id);
        $nGpon = $gpon->distinct()->select('port_number')->where(['onu_index' => $gpon->onu_index, 'olt_id' => $olt->id])->get();

       //var_dump(count($nGpon));
        print json_encode(array('id' => count($nGpon)));


    }


}