<?php


namespace App\Config;


class RouteConfig
{

    public function getRoutes(){
        $routes[] =['/', 'HomeController@index'];

        $routes[] =['/login', 'UserController@login'];
        $routes[] =['/login/auth', 'UserController@auth'];
        $routes[] =['/logout', 'UserController@logout'];

        $routes[] =['/user/create', 'UserController@create'];
        $routes[] =['/user/store', 'UserController@store'];


        return $routes;
    }

}