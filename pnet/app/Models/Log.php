<?php
/**
 * Created by PhpStorm.
 * User: Andre
 * Date: 15/03/2018
 * Time: 14:02
 */

namespace App\Models;


use Core\BaseModelEloquent;

class Log extends BaseModelEloquent
{

    public $table = 'logs';
    public $timestamps = false;

    protected $fillable = ['user', 'page', 'description', 'date'];



}