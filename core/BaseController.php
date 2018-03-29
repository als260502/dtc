<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 15/08/2017
 * Time: 12:41
 */

namespace Core;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

abstract class BaseController
{
    protected $view;
    protected $error;
    protected $inputs;
    protected $success;
    protected $auth;

    private $layoutPath;
    private $viewPath;
    private $pageTitle = null;



    public function __construct()
    {
        $this->view = new \stdClass();
        $this->auth = new Auth();

        if(Session::get('success'))
        {
            $this->success = Session::get('success');
            Session::destroy('success');
        }

        if(Session::get('error'))
        {
            $this->error = Session::get('error');
            Session::destroy('error');
        }

        if(Session::get('inputs'))
        {
            $this->inputs = Session::get('inputs');
            Session::destroy('inputs');
        }


    }

    /**
     * Renderiza os arquivos phtml com o conteudo das paginas
     *
     * caminho do controller e da action ex: home/index
     * @param string $viewPath
     *
     * pode chamar uma pagina com layout padrao ou nao
     * @param string|null $layoutPath
     * @return mixed
     */
    protected function renderView(string $viewPath, string $layoutPath = null)
    {
        $this->viewPath = $viewPath;
        $this->layoutPath = $layoutPath;

        if ($this->layoutPath)
            return $this->layout();
        else
            return $this->viewContent();
    }

    protected function viewContent()
    {
        $path = __DIR__ . "/../pnet/app/Views/{$this->viewPath}.phtml";
        //var_dump($path);
        if (file_exists($path)) {
           return require_once($path);
        }/* else {
            echo ("Arquivo de  View não encontrado");
        }*/
    }

    protected function layout()
    {
        $path = __DIR__ . "/../pnet/app/Views/layout/{$this->layoutPath}.phtml";
       // var_dump($path);
        if (file_exists($path)) {
           return require_once($path);
        } else {
           echo ("Arquivo de  View não encontrado");
        }
    }

    protected function setPageTitle(string $pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    protected function getPageTitle(string $separator = null)
    {
        if ($separator) {
            return $this->pageTitle . " " . $separator . " ";
        } else {
            return $this->pageTitle;
        }
    }

    public function forbiden(){
        return Redirect::routeRedirect(MY_HOST.'/login');
    }

}