<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    //redirect register page
    public function registerPage(){
        return view('authentication.register');
    }


    //redirect login page
    public function loginPage(){
        return view('authentication.login');
    }
}
