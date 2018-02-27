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

    protected $fillable = ['index', 'qtd_onu', 'chassis_id'];

    public function gpon(){
        $this->hasMany(Gpon::class);
    }

}