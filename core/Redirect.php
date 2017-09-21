<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 17/08/2017
 * Time: 09:25
 */

namespace Core;


class Redirect
{
    public static function routeRedirect(string $urlDestination, $with = array())
    {
        //var_dump($with);
        if(count($with) > 0)
            foreach ($with as $key => $value)
                Session::set($key, $value);

        return header("Location:{$urlDestination}");
    }
}