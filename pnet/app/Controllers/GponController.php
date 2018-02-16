<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 02/10/2017
 * Time: 12:41
 */

namespace App\Controllers;


use Core\BaseController;

class GponController extends BaseController
{

    public function index()
    {
        $this->setPageTitle("Gerência de ONU");



        return $this->renderView('/mon_dtc/main', 'layout');
    }

}