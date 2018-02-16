<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 02/10/2017
 * Time: 15:54
 */

namespace App\Models;


use Core\BaseModelEloquent;

class Chassi extends BaseModelEloquent
{
    public $table = 'chassis';
    public $timestamps = false;

    protected $fillable = ['name', 'address', 'actives_olt'];


    public function olt()
    {
         return $this->hasMany(Olt::class);
    }



}