<?php


namespace App\Config;


class RouteConfig
{

    public function getRoutes(){
        $routes[] =['/', 'UserController@index'];
        $routes[] =['/login', 'UserController@login'];
        $routes[] =['/login/auth', 'UserController@auth'];
        $routes[] =['/logout', 'UserController@logout'];

        $routes[] =['/user/create', 'UserController@create'];
        $routes[] =['/user/store', 'UserController@store'];

        $routes[] =['/dtc', 'GponController@index', 'auth'];
        $routes[] =['/dtc/config', 'GponController@config', 'auth'];
        $routes[] =['/dtc/change', 'GponController@change', 'auth'];
        $routes[] =['/dtc/reset', 'GponController@reset', 'auth'];
        $routes[] =['/dtc/mac', 'GponController@mac', 'auth'];

        $routes[] =['/dtc/find', 'GponController@findSerial', 'auth'];
        $routes[] =['/dtc/save', 'GponController@configOnu', 'auth'];


        $routes[] =['/dtc/mon', 'GponController@mon', 'auth'];



        return $routes;
    }

}