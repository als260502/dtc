<?php
/* Iniciando a sessão */
if(!session_id()){
    session_start();
}

/* iniciando as rotas */
$routeConfig = new \App\Config\RouteConfig();
$route = new Core\Route($routeConfig->getRoutes());



