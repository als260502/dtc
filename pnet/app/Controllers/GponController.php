<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 02/10/2017
 * Time: 12:41
 */

namespace App\Controllers;


use App\Models\Chassi;
use App\Models\Ethernet;
use App\Models\Gpon;
use App\Models\Olt;
use Core\BaseController;
use Core\Log;
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

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Acessando Pagina Inicial");

        return $this->renderView('/mon_dtc/main', 'layout');
    }

    public function config()
    {
        $this->setPageTitle("Configurar ONU");

        $this->view->chassi = Chassi::all();

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Acessando Pagina de configuração de ONU");

        $this->renderView('/onu/config', 'layout');

    }

    public function findSerial($request)
    {

        if(!isset($request->post))return Redirect::routeRedirect(MY_HOST.'/config');

        $this->setPageTitle("Configurar ONU");
        $this->view->chassi = Chassi::all();

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Buscando Serial de ONU no chassi {$request->post->chassi[0]}");

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
                    $this->view->oltNumber = (isset($olt->id)) ? $olt->index : 1;

                    Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Dump da busca de ONU\n {$tn->getContentData()}\n");

                    //var_dump($this->view->oltNumber,$this->view->chassiNumber ) ;


                } else {
                    $this->view->mac = "Nenhum gpon encontrado!";
                    $this->view->class = 'warning';
                    Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Dump da busca de ONU\n {$tn->getError()}\n");
                }

            }
        }

        $this->renderView('/onu/config', 'layout');

    }

    public function configOnu($request)
    {

        if(!isset($request->post))return Redirect::routeRedirect(MY_HOST.'/config');

        $this->setPageTitle("Configurar ONU");
        $this->view->chassi = Chassi::all();

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Confgurando ONU\n".implode('|', (array)$request->post));

        $gpon = new Gpon();
        $ethernet = new Ethernet();
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
            return Redirect::routeRedirect(MY_HOST.'/config', [
                'Error' => "campos obrigatorio estão em branco"]);
        }

        //$index = (isset($olt->id)) ? Gpon::where('olt_id', $olt->id)->orderBy('service_port', 'desc')->first()->onu_index + 1: $this->post->onu_index;
        //$onuIndex = (isset($index)) ? $index : $this->post->onu_index;

        for ($i = 0; $i < count($request->post->porta); $i++) {
            $data = ['onu_index' => $request->post->onu_index
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

            $ethData = [
                'eth' => $request->post->porta_id[$i]
                ,'technology' => $tec[$i]
                ,'active' => 1
                ,'gpon_id' => $gponId[0]
            ];

            $porta = $ethernet->create($ethData);
            $ethId[] = $porta->id;

        }


        $tecnologia = (count($tec) > 1) ? implode('/', $tec) : $tec[0];
        $newGpon = Gpon::find($gponId[0]);


        $tn->configOnu($newGpon, $tecnologia);

        //var_dump($tn->getError());

        if ($tn->getError()) {
            $this->view->error = $tn->getError();
            Gpon::destroy($gponId);
            Ethernet::destroy($ethId);

            Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Erro ao configurar ONU\n {$tn->getError()}\n");
        }

        $this->view->content = $tn->getContentData();
        $this->view->kompressor = $tn->getKompressorData();
        $this->view->sapo = $tn->getSapoData();
        $this->view->complete = $tn->getResult();

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Dump da configuração de ONU \n {$tn->getContentData()}\n");

        $this->renderView('/onu/config', 'layout');
    }

    public function change()
    {
        $this->setPageTitle("Troca de  ONU");

        $this->view->onu = Gpon::select()->groupBy('onu_name')->get();

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Acessando Area de troca de serial da ONU\n");

        $this->renderView('/onu/change', 'layout');
    }

    public function reset()
    {
        $this->setPageTitle("Reboot na ONU");
        //$this->view->onu = Gpon::all()->sortBy('onu_name');
        $this->view->onu = Gpon::select(['id','onu_name'])->groupBy('onu_name')->get();
        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Acessando Area de reset de ONU\n");
        $this->renderView('/onu/reset', 'layout');
    }

    public function mac()
    {
        //$this->view->onu = Gpon::all()->sortBy('onu_name');
        $this->setPageTitle("Verificar MACs por traz da onu");
        $this->view->onu = Gpon::select(['id','onu_name'])->groupBy('onu_name')->get();

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Acessando Area de verificar mac por traz da ONU\n");
        $this->renderView('/onu/mac', 'layout');

    }

    public function activate()
    {
        //$this->view->onu = Gpon::all()->sortBy('onu_name');
        $this->setPageTitle("Ativar/Desativar Portas");
        $this->view->onu = Gpon::select(['id','onu_name'])->groupBy('onu_name')->get();

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Acessando Area de ativar/desativar portas da ONU\n");

        $this->renderView('/onu/ativar', 'layout');

    }

    public function manager()
    {
        $this->setPageTitle("Gerenciar BD");

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Acessando Area em construção da ferramenta ONU\n");
        $this->renderView('/onu/manager', 'layout');

    }

    public function changeOnu($request)
    {

        if (!isset($request->post)) return Redirect::routeRedirect(MY_HOST.'/change');

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Alterando serial da  ONU\n".implode('|', (array)$request->post));
        //$this->view->onu = Gpon::all()->sortBy('onu_name');
        $this->view->onu = Gpon::select(['id','onu_name'])->groupBy('onu_name')->get();
        $gpon = Gpon::find($request->post->onuName);
        $olt = Olt::find($gpon->olt_id);
        $chassi = Chassi::find($olt->chassi_id);
        $oldSerial = $gpon->serial_number;

        $dataToValidate = ['id' => $request->post->onuName, 'serial_number' => $request->post->serialNumber];
        $data = ['serial_number' => $request->post->serialNumber];

        $tn = new Telnet($chassi->address);

        if (Validator::make($dataToValidate, $gpon->validateChange())) {
            return Redirect::routeRedirect(MY_HOST.'/change', [
                'Error' => "algo que nao esta certo esta errado!!!"]);
        }

        $gpon->update($data);
        if (!$tn->changeOnu($gpon)) {
            $this->view->error = $tn->getError();
            //var_dump($tn->getError());
            $gpon->update(['serial_number' => $oldSerial]);

            Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Erro ao alterar serial ONU\n {$tn->getError()}");
        }

        $this->view->content = $tn->getContentData();
        $this->view->complete = $tn->getResult();

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Dump da alteração  de n/s da ONU\n {$tn->getContentData()}");

        $this->renderView('/onu/change', 'layout');

    }

    public function resetOnu($request)
    {
        if (!isset($request->post)) return Redirect::routeRedirect(MY_HOST.'/reset');

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Rebootando  ONU\n".implode('|', (array)$request->post));

        $this->view->onu = Gpon::select(['id','onu_name'])->groupBy('onu_name')->get();
        $gpon = Gpon::find($request->post->onuName);
        $olt = Olt::find($gpon->olt_id);
        $chassi = Chassi::find($olt->chassi_id);

        $tn = new Telnet($chassi->address);

        if (!$tn->resetOnu($gpon)) {
            $this->view->error = $tn->getError();
            Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Erro ao rebootar ONU\n {$tn->getError()}");
        }

        $this->view->content = $tn->getContentData();
        $this->view->complete = 'complete';


        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Dump do reboot da ONU\n {$tn->getContentData()}");
        $this->renderView('/onu/reset', 'layout');


    }

    public function getMac($request)
    {


        if (!isset($request->post)) return Redirect::routeRedirect(MY_HOST.'/mac');
        $this->setPageTitle("Verificar MACs por traz da onu");

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Buscando mac por traz da  ONU\n".implode('|', (array)$request->post));

        //$this->view->onu = Gpon::all()->sortBy('onu_name');
        $this->view->onu = Gpon::select(['id','onu_name'])->groupBy('onu_name')->get();
        $gpon = Gpon::find($request->post->onuName);
        $olt = Olt::find($gpon->olt_id);
        $chassi = Chassi::find($olt->chassi_id);

        $tn = new Telnet($chassi->address);

        $nGpon = $gpon->where(['onu_index' => $gpon->onu_index, 'olt_id' => $olt->id])->get();

        $totalMac = 0;
        $mac = array();
        for ($i = 0; $i < count($nGpon); $i++) {

            $tn->getMac($nGpon[$i]);

            if($tn->getMacData()) {
                $mac[$i] = $tn->getMacData();
                $mCount[] = count($tn->getMacData());

                $totalMac += $mCount[$i];

                $nMac[0] = $mac[$i];
                Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Erro ao buscar mac na ONU\n {$tn->getError()}");
            }
        }

        $this->view->mac = (isset($nMac[0]))?$nMac[0]: ['Error'=>'nenhum mac encontrado'];
        $this->view->macCount = (isset($nMac[0]))?$totalMac: 0;

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Dump da varredura de mac da ONU\n {$tn->getContentData()}");

        $this->renderView('/onu/mac', 'layout');

    }


    public function getPorts($request)
    {

        if (!isset($request->post)) return Redirect::routeRedirect(MY_HOST.'/activate');

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Buscando portas ativa na ONU\n");

        //$this->view->onu = Gpon::all()->sortBy('onu_name');
        $this->view->onu = Gpon::select(['id','onu_name'])->groupBy('onu_name')->get();
        $this->setPageTitle("Ativar/Desativar Portas");

        $this->view->gpon = Gpon::find($request->post->onuName);

        $this->view->eth = $this->view->gpon->ethernet;

        $this->view->onuID = $this->view->gpon->id;

        $this->renderView('/onu/ativar', 'layout');
    }

    public function active($request)
    {

        $eth = Ethernet::find($request->post->id);

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Iniciando ativação ou desativação da portada ONU\n");
        $nGpon = $eth->gpon()->first();
        $olt = $nGpon->olt()->first();
        $ch = Chassi::find($olt->chassi_id);

        if($request->post->action == 'enable')
            $eth->update(['active' => 1]);

        if($request->post->action == 'disable')
            $eth->update(['active' => 0 ]);

        $tn = new Telnet($ch->address);
        $tn->managePort($nGpon, $eth);
        if($tn->getError()){
            print json_encode([
                'result' => 'error'
                ,'msg' => $tn->getError()
            ]);

            if($request->post->action == 'enable')
                $eth->update(['active' => 0]);

            if($request->post->action == 'disable')
                $eth->update(['active' => 1 ]);

            Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Erro ao ativar ou desativar porta da ONU\n {$tn->getError()}");

        }
        else
        {
            print json_encode([
                'result' => 'success'
            ]);
        }

        Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Dump da ativação ou desativação das portas da ONU\n {$tn->getContentData()}");
    }

}