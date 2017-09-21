<?php
namespace Core;



class Route
{

    private $routes;

    public function __construct(array $routes)
    {
        $this->serRoutes($routes);
        $this->run();
    }

    private function serRoutes(array $routes)
    {
        foreach ($routes as $route)
        {
            $routeExplode = explode('@', $route[1]);



            if(isset($route[2]))
                $rt = [$route[0], $routeExplode[0], $routeExplode[1], $route[2]];
            else
                $rt = [$route[0], $routeExplode[0], $routeExplode[1]];

            $newRoutes[] = $rt;
        }

        $this->routes = $newRoutes;
    }

    private function getRequests()
    {
        $obj = new \stdClass();
        foreach ($_GET as $key => $value)
        {
            @$obj->get->$key = $value;
        }

        foreach ($_POST as $key => $value)
        {
            @$obj->post->$key = $value;
        }

        return $obj;

    }

    private function getUrl()
    {
        $url = parse_url(filter_input(INPUT_SERVER, 'REQUEST_URI'));
        $u = $url['path'];
        $urlString = substr($u, 1, strlen($u));
        return $u;
    }

    private function run()
    {
        $found = false;
        $param = [];
        $url = $this->getUrl();
        $urlArray = explode('/', $url);

        foreach ( $this->routes as $route)
        {
            $routeArray = explode('/', $route[0]);
            $param = [];

            for ($i = 0; $i < count($routeArray); $i++)
            {
                   if((strpos($routeArray[$i], "{") !== false) && (count($urlArray) == count($routeArray)))
                   {
                       $routeArray[$i] = $urlArray[$i];
                       $param[] = $urlArray[$i];
                   }
                   $route[0] = implode($routeArray, '/');
            }
            if($url == $route[0])
            {
               //echo sprintf("%s <br> %s <br> %s <br> %s",$route[0], $route[1], $route[2], $param[0]);
                $found = true;
                $controller = $route[1];
                $action = $route[2];
                $auth = new Auth();
                if(isset($route[3]) && !$auth->check())
                {
                    $action = 'forbiden';
                }
                break;
            }
        }
        if($found)
        {

            $controllerInstance = Container::newController($controller);

            switch(count($param)){
                case 1:
                    $controllerInstance->$action($param[0], $this->getRequests());
                    break;
                case 2:
                    $controllerInstance->$action($param[0], $param[1], $this->getRequests());
                    break;
                case 3:
                    $controllerInstance->$action($param[0], $param[1], $param[2], $this->getRequests());
                    break;
                default:
                    $controllerInstance->$action($this->getRequests());
                    break;

            }
        }
        else
        {
            Container::pageNotFound();
        }
    }

}