<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientRegisterController extends Controller
{
    public function show()
    {
        return view('auth.register-client');
    }

    public function store(Request $request): RedirectResponse
    {
        // Le champ 'role' n'est volontairement PAS validé ni lu : 'role' est
        // dans le $fillable de User, donc accepter une valeur venant du
        // formulaire permettrait à n'importe qui de s'inscrire en Super Admin.
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => UserRole::CLIENT,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('client.dashboard');
    }
}
