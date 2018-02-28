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
                    $gpon = Gpon::where('olt_id', $olt->id)->orderBy('service_port', 'desc')->first();

                    $this->view->servicePort = (isset($gpon->service_port))? $gpon->service_port + 1: 0;
                    $this->view->vlan = (isset( $gpon->vlan))? $gpon->vlan + 1: 0;

                } else {
                    $this->view->mac = "Nenhum gpon encontrado!";
                    $this->view->class = 'warning';
                }

            }
        }

        $this->renderView('/onu/config', 'layout');
    }

    public function findSerial($request)
    {

        //$data

    }

}