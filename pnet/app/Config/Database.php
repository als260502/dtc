<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 16/08/2017
 * Time: 11:27
 */

namespace App\Config;


class Database
{

    public static function databaseConfig(){

        return
            [
             /** Opções de banco de dados (sqlite, mysql)*/
            'driver' => 'mysql',
            'sqlite' => [
                    'database' =>'database.db',

                ],
            'mysql'=> [
                'host' => 'localhost',
                'database' => 'predialfone',
                'user' => 'root',
                'pass' => '',
                'charset' => 'utf8',
                'collation'=>'utf8_unicode_ci'
                ]

            ];
    }

}