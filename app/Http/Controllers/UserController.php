<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();

        if (Auth::check() && Auth::user()->user_type=='admin'){
            return view('admin.vente.dashboard');
        }

        if(Auth::check() && Auth::user()->user_type=='user'){
            return view('client.dashboard');
        }

        abort(403,'Accès non autorisé');
    }
}
