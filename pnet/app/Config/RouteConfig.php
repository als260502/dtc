<?php


namespace App\Config;


class RouteConfig
{

    public function getRoutes(){
        $routes[] =[MY_HOST, 'UserController@index'];
        $routes[] =[MY_HOST.'/login', 'UserController@login'];
        $routes[] =[MY_HOST.'/login/auth', 'UserController@auth'];
        $routes[] =[MY_HOST.'/logout', 'UserController@logout'];

        $routes[] =[MY_HOST.'/user/create', 'UserController@create'];
        $routes[] =[MY_HOST.'/user/store', 'UserController@store'];

        $routes[] =[MY_HOST.'/main', 'GponController@index', 'auth'];
        $routes[] =[MY_HOST.'/config', 'GponController@config', 'auth'];
        $routes[] =[MY_HOST.'/change', 'GponController@change', 'auth'];
        $routes[] =[MY_HOST.'/reset', 'GponController@reset', 'auth'];
        $routes[] =[MY_HOST.'/mac', 'GponController@mac', 'auth'];
        $routes[] =[MY_HOST.'/activate', 'GponController@activate', 'auth'];
        $routes[] =[MY_HOST.'/manager', 'GponController@manager', 'auth'];

        $routes[] =[MY_HOST.'/find', 'GponController@findSerial', 'auth'];
        $routes[] =[MY_HOST.'/save', 'GponController@configOnu', 'auth'];
        $routes[] =[MY_HOST.'/updateonu', 'GponController@changeOnu', 'auth'];
        $routes[] =[MY_HOST.'/reboot', 'GponController@resetOnu', 'auth'];
        $routes[] =[MY_HOST.'/get', 'GponController@getMac', 'auth'];
        $routes[] =[MY_HOST.'/portas', 'GponController@getPorts', 'auth'];
        $routes[] =[MY_HOST.'/active', 'GponController@active', 'auth'];



        $routes[] =[MY_HOST.'/mon', 'GponController@mon', 'auth'];



        return $routes;
    }

}