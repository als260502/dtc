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

    protected $fillable = ['index', 'name', 'serial', 'service_port'];

    public function olt(){
        $this->belongsToMany(Olt::class);
    }

    public function port(){
        $this->hasMany(Port::class);
    }


}