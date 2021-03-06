<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 02/10/2017
 * Time: 15:57
 */

namespace App\Models;


use Core\BaseModelEloquent;

class Olt extends BaseModelEloquent
{

    public $table = 'olts';
    public $timestamps = false;

    protected $fillable = ['index','qnq', 'qtd_onu', 'chassis_id'];

    public function gpon(){
        return $this->hasMany(Gpon::class);
    }

    public function chassi(){
       return $this->belongsTo(Chassi::class);
    }

}