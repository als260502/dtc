<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 02/10/2017
 * Time: 11:06
 */

namespace App\Models;


use Core\BaseModelEloquent;

class TimeOut extends BaseModelEloquent
{

    public $table = 'timeoutstatus';
    public $timestamps = false;

    protected $fillable = ['time', 'description', 'gpons_id'];

}