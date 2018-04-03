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


    public static function storeLog($AccessedPage, $activityDescription, $user = null)
    {

        $data = array();

        if($user == null) {
            $data = [
                  'user' => Auth::user()
                , 'page' => $AccessedPage
                , 'description' => $activityDescription
                , 'date' => self::getDate()
            ];
        }else{
            $data = [
                'user' => $user
                , 'page' => $AccessedPage
                , 'description' => $activityDescription
                , 'date' => self::getDate()
            ];
        }
    //var_dump($data);
        Logging::create($data);

    }


    private  static function getUser(){
        return Session::get('user')['user'];
    }

    private static function getDate()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $date = date('Y-m-d H:i:s');
        return $date;

    }



}