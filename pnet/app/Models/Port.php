<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 26/09/2017
 * Time: 12:17
 */

namespace App\Models;


use Core\BaseModelEloquent;

class Port extends BaseModelEloquent
{
    public $table = 'ports';
    public $timestamps = false;

    protected $fillable = ['port_number', 'vlan', 'gpons_id'];


    public function gpon(){
        $this->belongsTo(Gpon::class);
    }

}