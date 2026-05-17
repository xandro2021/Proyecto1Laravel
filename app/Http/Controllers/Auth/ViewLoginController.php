<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class ViewLoginController extends Controller
{
    public function home()
    {
        return view('index');
    }

    public function login()
    {
        return view('index');
    }

    public function register()
    {
        return view('register');
    }
}
