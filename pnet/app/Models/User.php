<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 21/08/2017
 * Time: 13:49
 */

namespace App\Models;


use Core\BaseModelEloquent;


class User extends BaseModelEloquent
{

    public $table = 'users';
    public $timestamps = false;

    protected $fillable = ['name', 'email', 'password'];


    public function validateInsert(){

        return [
            'name' => 'min:2|max:255',
            'email'=> 'email|unique:User:email',
            'password' => 'min:6|max:16',
            'user' => 'require'
        ];

    }

    public function validateUpdate($id){

        return array
        (
            'name' => 'min:4|max:255',
            'email'=> "email|unique:User:email:$id",
            'password' => 'min:6|max:16'
        );

    }

    public function post()
    {
        /*
        *relacionamento 1-n
        * um usuario pode ter varios posts
        */
        return $this->hasMany(Post::class);
    }


}