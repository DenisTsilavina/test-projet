<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        if(Auth::check() && Auth::user()->user_type=='user'){
            return view('dashboard');
        }
        else if (Auth::check() && Auth::user()->user_type=='admin'){
            return view('admin.vente.dashboard');
        }
    }
}
