<?php

namespace App\Controllers\Auth;

use App\Core\Controller;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.index')->with('title', 'My Dashboard');
    }
}
