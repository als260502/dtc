<?php

namespace Core;


use App\Models\User;

trait Authenticate
{
    public function login()
    {
        $this->setPageTitle("Login");

        return $this->renderView('/user/login');
    }

    public function auth($request)
    {
        Log::storeLog("view: /user/" . __FUNCTION__ . " Function: " . __METHOD__, "Tentativa de login usuario {$request->post->user}", 'Sistema');


        $result = User::where('user', $request->post->user)->first();
        if ($result && password_verify($request->post->password, $result->password)) {
            $user = array(
                'id' => $result->id,
                'name' => $result->name,
                'email' => $result->email,
                'user' => $result->user,
                'status' => $result->status,
                'pnetuname' => 'seg' . md5(
                        'seg' . $_SESSION[$result->user] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'])

            );

            Session::set('user', $user);


            Log::storeLog("view: /user/" . __FUNCTION__ . " Function: " . __METHOD__
                , "Usuario {$result->name} Logado no sistema", $result->user);

            return Redirect::routeRedirect(MY_HOST . '/main');

        }


        Log::storeLog("view: /user/" . __FUNCTION__ . " Function: " . __METHOD__
            , "Erro na checagem de usuario e senha\nERRO: Usuário ou senha estão incorretos!", $request->post->user);

        return Redirect::routeRedirect(MY_HOST, [
            'error' => ['Usuário ou senha estão incorretos!'],
            'inputs' => ['user' => $request->post->user]
        ]);


    }

    public function logout()
    {

        Log::storeLog("view: /user/" . __FUNCTION__ . " Function: " . __METHOD__, "Usuario desogado do sistema");
        Session::destroy('user');
        return Redirect::routeRedirect(MY_HOST . '/login');
    }

}