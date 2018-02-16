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

    public function index(){

        $this->setPageTitle("Login");

        return $this->renderView('/user/login');

    }


    public function create(){

        $this->setPageTitle("New User");
        return $this->renderView('/user/create', 'layout');

    }

    public function store($request){

        $data =
            [
                'name' => $request->post->name,
                'email' => $request->post->email,
                'password' => $request->post->password,
                'user' => $request->post->user
            ];



        if(Validator::make($data, $this->users->validateInsert()))
        {
            return Redirect::routeRedirect('/user/create');
        }


        $data['password'] = password_hash($request->post->password, PASSWORD_BCRYPT);

        try {

            $this->users->create($data);

            return Redirect::routeRedirect("/user/create", [
                'success' => ["Usuário criado com sucesso"]
            ]);

        }catch (\Exception $e){

            return Redirect::routeRedirect("/user/create", [
                'error' => ["Erro: {$e->getMessage()}"]
            ]);
        }


    }

}