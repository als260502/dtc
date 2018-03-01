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

        //var_dump($request);
        $mGpon = new Gpon();

        $dataToValidate=[
            'onu_name' => $request->post->name,
            'serial_number'=> $request->post->serial,
            'chassi' => $request->post->chassi[0],
            'olt' => $request->post->olt,
            'selectionPorts' => $request->post->selectionPorts
            ];

        if(Validator::make($dataToValidate, $mGpon->validateFind() )) {
            return Redirect::routeRedirect("/dtc/config", [
                'error' => ["Erro: alguns camopos estao em branco"]
            ]);
        }

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
                    $this->view->servicePort = (isset($gpon->service_port)) ? $gpon->service_port + 1 : 0;
                    $this->view->vlan = (isset($gpon->vlan)) ? $gpon->vlan + 1 : 0;

                } else {
                    $this->view->mac = "Nenhum gpon encontrado!";
                    $this->view->class = 'warning';
                }

            }
        }

        $this->renderView('/onu/config', 'layout');

    }

    public function configOnu()
    {

        $this->renderView('/onu/config', 'layout');
    }

}