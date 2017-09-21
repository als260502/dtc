<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 15/08/2017
 * Time: 09:19
 */

namespace Core;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class Container
{
    public static function newController(string $controller)
    {

        $controllerInstalce = "\\App\\Controllers\\".$controller;
        return new $controllerInstalce();

    }

    public static function getModel(string $modelName)
    {
        $objModel = "\\App\\Models\\".$modelName;
        return new $objModel(Database::getDatabase());
    }

    public static function pageNotFound()
    {

        $path = __DIR__ . "/../pnet/app/Views/error/404.phtml";

        if(file_exists($path))
        {
            return require_once $path;
        }
        else
        {
            return require_once $path;
            echo "ERRO 404 - Página não encontrada!";
        }

    }

}