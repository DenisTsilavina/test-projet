<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        // Si NON connecté → rediriger vers register
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->user_type === 'admin') {
            return view('admin.dashboard');
        }

        if ($user->user_type === 'user') {
            return view('client.achat');
        }

        abort(403, 'Accès non autorisé');
    }
}
