<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 26/09/2017
 * Time: 12:12
 */

namespace App\Models;


use Core\BaseModelEloquent;

class Gpon extends BaseModelEloquent
{

    public $table = 'gpons';
    public $timestamps = false;

    protected $fillable = ['onu_index', 'onu_name', 'serial_number', 'port_number', 'vlan', 'service_port', 'olt_id'];

    public function validateFind(){
        return [
            'onu_name' => 'require|min:2',
            'serial_number'=> 'require',
            'chassi' => 'require',
            'olt' => 'require',
            'selectionPorts' => 'require'
        ];
    }

}