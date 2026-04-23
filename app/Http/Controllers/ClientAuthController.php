<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientAuthController extends Controller
{
    public function register(Request $request)
    {
        $validated=$request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required|email|unique:clients',
            'phone' => 'required',
            'adresse' => 'required',
            'ville' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        $client= Client::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'adresse' => $validated['adresse'],
            'ville' => $validated['ville'],
            'password' => Hash::make($validated['password']),
        ]);
        Auth::guard('client')->login($client);
        return redirect()->route('client.achat')
            ->with('success', 'Compte crée avec success!'
        );
    }
     public function login(Request $request)
     {
         $credentials = $request->validate([
             'email'=>'required|email',
             'password'=>'required'
         ]);
         if(Auth::guard('client')->attempt($credentials, $request->boolean('remember')))
         {
             $request->session()->regenerate();
             return redirect()->intended(route('client.achat'));
         }
         return back()
             ->withInput($request->only('email'))
             ->with('show_reistre_modal',true)
             ->with('login_error', 'Email ou mot de passe incorrect.'
         );
     }
     public function logout(Request $request)
     {
         Auth::guard('client')->logout();
         $request->session()->invalidate();
         $request->session()->regenerateToken();
         return redirect()->route('client.dashboard');
     }
}
