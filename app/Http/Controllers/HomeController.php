<?php
namespace App\Http\Controllers;

use Careminate\Http\Validations\Validate;

class HomeController extends Controller
{
    public function index()
    {  
        //requests, rules, attributes
        $validation = Validate::make(
        ['user_id' => $_GET['user_id'] ?? '',],
        ['user_id' => 'required','integer','unique:users,id,1',],
        ['user_id'=> trans('main.user_id'),]
    );
        echo"<pre>";
        return var_dump($validation->validated());
    }

}