<?php
/* Iniciando a sessão */
if(!session_id()){
    session_start();
}
const MY_HOST = '/dtc';
/* iniciando as rotas */
$routeConfig = new \App\Config\RouteConfig();
$route = new Core\Route($routeConfig->getRoutes());



