<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 21/08/2017
 * Time: 13:53
 */

namespace App\Controllers;


use App\Models\User;
use Core\BaseController;
use Core\Log;
use Core\Redirect;
use Core\Session;
use Core\Validator;
use Core\Authenticate;

class UserController extends BaseController
{

    use Authenticate;
    private $users;

    public function __construct()
    {
        parent::__construct();
        $this->users = new User();
    }

    public function index()
    {

        $this->setPageTitle("Login");
        return $this->renderView('/user/login');

    }


    public function create()
    {

        $this->setPageTitle("New User");
        return $this->renderView('/user/create', 'layout');

        Log::storeLog("view: /user/" . __FUNCTION__ . " Function: " . __METHOD__, "Acessando pagina de criação de usuario");

    }

    public function store($request)
    {

        //var_dump($request->post, implode("|", (array)$request->post));die;

        $data =
            [
                'name' => $request->post->name,
                'email' => $request->post->email,
                'password' => $request->post->password,
                'user' => $request->post->user,
                'status' => 0
            ];


        if (Validator::make($data, $this->users->validateInsert())) {
            return Redirect::routeRedirect(MY_HOST . '/user/create');
        }


        $data['password'] = password_hash($request->post->password, PASSWORD_BCRYPT);

        try {

            $this->users->create($data);

            Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__, "Criado Usuario {$data['user']} Senha {$request->post->password} Nome {$data['name']}");

            return Redirect::routeRedirect(MY_HOST . "/user/create", [
                'success' => ["Usuário criado com sucesso"]
            ]);

        } catch (\Exception $e) {

            Log::storeLog("view: /user/".__FUNCTION__." Function: ".__METHOD__
                , "Erro na tentativa de criar usuario {$data['user']} {$data['name']}
                 Erro: {$e->getCode()}\n{$e->getMessage()}\n{$e->getTraceAsString()}");

            return Redirect::routeRedirect(MY_HOST . "/user/create", [
                'error' => ["Erro: {$e->getMessage()}"]
            ]);
        }

    }

}