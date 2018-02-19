<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 02/10/2017
 * Time: 12:41
 */

namespace App\Controllers;


use App\Models\Chassi;
use Core\BaseController;

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

    public function config()
    {
        $this->setPageTitle("Configurar ONU");

        $this->view->chassi = Chassi::all();


        $this->renderView('/onu/config', 'layout');
    }

    public function findSerial($id, $request){

    }

}