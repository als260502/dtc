<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 21/08/2017
 * Time: 13:53
 */

namespace Core;

use App\Models\Log as Logging;

class Log
{


    public static function storeLog($AccessedPage, $activityDescription)
    {

        $data =[
                'user' => self::getUser()
                ,'page'=>$AccessedPage
                ,'description'=>$activityDescription
                ,'date'=>self::getDate()
             ];

        Logging::create($data);

    }


    private function getUser(){
        return Session::get('user');
    }

    private function getDate()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $date = date('Y-m-d H:i:s');
        return $date;

    }



}