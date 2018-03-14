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
        $routes[] =['/dtc/activate', 'GponController@activate', 'auth'];
        $routes[] =['/dtc/manager', 'GponController@manager', 'auth'];

        $routes[] =['/dtc/find', 'GponController@findSerial', 'auth'];
        $routes[] =['/dtc/save', 'GponController@configOnu', 'auth'];
        $routes[] =['/dtc/updateonu', 'GponController@changeOnu', 'auth'];
        $routes[] =['/dtc/reboot', 'GponController@resetOnu', 'auth'];
        $routes[] =['/dtc/get', 'GponController@getMac', 'auth'];
        $routes[] =['/dtc/portas', 'GponController@getPorts', 'auth'];



        $routes[] =['/dtc/mon', 'GponController@mon', 'auth'];



        return $routes;
    }

}