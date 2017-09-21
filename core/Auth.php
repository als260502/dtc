<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 22/08/2017
 * Time: 10:45
 */

namespace Core;


class Auth
{
    private static $id = null;
    private static $name = null;
    private static $email = null;
    private static $user = null;
    private static $pnetuname = null;

    public function __construct()
    {
        if(Session::get('user'))
        {
            $user = Session::get('user');

            self::$id = $user['id'];
            self::$name = $user['name'];
            self::$email = $user['email'];
            self::$user = $user['user'];
            self::$pnetuname = $user['pnetuname'];


        }
    }

    public static function id(){
        return self::$id;
    }

    public static function name(){
        return self::$name;
    }

    public static function email(){
        return  self::$email;
    }

    public static function user(){
        return self::$user;
    }

    public static function check()
    {
        if(self::$pnetuname == null ||  self::$user == null)
            return false;

        return true;
    }

}