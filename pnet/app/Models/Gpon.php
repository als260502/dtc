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

    public function validate(){
        return [
            'onu_name' => 'require|min:2',
            'serial_number'=> 'require',
            'chassi' => 'require',
            'olt' => 'require',

        ];
    }
    public function validateChange()
    {
        return['serrial_number' => 'require|min:12'];
    }

    public function olt(){
        return $this->belongsTo(Olt::class);
    }

    public function ports()
    {
        return $this->belongsToMany(Port::class);
    }



}