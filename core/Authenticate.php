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
        $result = User::where('user', $request->post->user)->first();

        if($result && password_verify($request->post->password, $result->password))
        {
            $user = [
                'id' => $result->id,
                'name' => $result->name,
                'email' => $result->email,
                'user' => $result->user,
                'pnetuname' => 'seg'.md5(
                        'seg'.$_SESSION[$result->user].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])

            ];

            Session::set('user', $user);

            return Redirect::routeRedirect('/dtc');

    }

        return Redirect::routeRedirect('/', [
           'error' => ['Usuário ou senha estão incorretos!'],
            'inputs' => ['user'=> $request->post->user]
        ]);

    }

    public function logout ()
    {
        Session::destroy('user');
        return Redirect::routeRedirect('/login');
    }

}