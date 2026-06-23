<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('client.dashboard');
    }

    /**
     * Dashboard client
     */
    public function clientDashboard()
    {
        $user = Auth::user();

        return view('client.dashboard', compact('user'));
    }

    /**
     * Dashboard admin
     */
    public function adminDashboard()
    {
        $totalUsers  = \App\Models\User::count();
        $totalVentes = \App\Models\Vente::count();

        return view('admin.dashboard', compact('totalUsers', 'totalVentes'));
    }
}
