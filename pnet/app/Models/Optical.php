<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 02/10/2017
 * Time: 10:08
 */

namespace App\Models;

use Core\BaseModelEloquent;


class Optical extends BaseModelEloquent
{

    public $table = 'opticals';
    public $timestamps = false;

    protected $fillable = ['rx', 'tx', 'gpons_id'];

}