<?php
/**
 * Created by PhpStorm.
 * User: Andre
 * Date: 15/03/2018
 * Time: 14:02
 */

namespace App\Models;


use Core\BaseModelEloquent;

class Ethernet extends BaseModelEloquent
{

    public $table = 'ethernets';
    public $timestamps = false;

    protected $fillable = ['eth', 'technology', 'active', 'gpon_id'];

    public function gpon(){
        return $this->belongsTo(Gpon::class);
    }

}