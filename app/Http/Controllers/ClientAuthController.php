<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ClientAuthController extends Controller
{
    public function register(Request $request)
    {
        // ✅ Forcer la redirection vers le dashboard en cas d'erreur de validation
        $validator = \Validator::make($request->all(), [
            'nom'     => 'required',
            'prenom'  => 'required',
            'email'   => 'required|email|unique:clients',
            'phone'   => 'required',
            'adresse' => 'required',
            'ville'   => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('client.dashboard')
                ->withErrors($validator)
                ->withInput()
                ->with('show_register_modal', true);
        }

        $validated = $validator->validated();
        $code = rand(100000, 999999);

        $client = Client::create([
            'nom'               => $validated['nom'],
            'prenom'            => $validated['prenom'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'],
            'adresse'           => $validated['adresse'],
            'ville'             => $validated['ville'],
            'password'          => Hash::make($code),
            'verification_code' => $code,
            'code_expires_at'   => now()->addMinutes(10),
            'is_verified'       => false,
        ]);

        // ✅ dd() supprimé
        Mail::raw(
            "Bonjour {$client->prenom},\n\nVotre code de vérification est : {$code}\n\nIl expire dans 10 minutes.",
            function ($message) use ($client) {
                $message->to($client->email)
                    ->subject('Votre code de vérification — Vohitsoa Tsena');
            }
        );

        session(['pending_client_email' => $client->email]);

        return redirect()->route('client.verify.form')
            ->with('success', "Un code a été envoyé à {$client->email}");
    }

// ✅ Corrigé
    public function showRegisterForm()
    {
        return redirect()->route('client.dashboard')
            ->with('show_register_modal', true);
    }
    /*public function register(Request $request)
    {
        $validated=$request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required|email|unique:clients',
            'phone' => 'required',
            'adresse' => 'required',
            'ville' => 'required',
        ]);
        $code=rand(100000,999999);

        $client= Client::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'adresse' => $validated['adresse'],
            'ville' => $validated['ville'],
            'password' => Hash::make($code),
            'verification_code' => $code,
            'code_expires_at' => now()->addMinutes(10),
            'is_verified' => false,
        ]);
        dd($client);
        Mail::raw("Bonjour {$client->prenom},\n\nVotre code de vérification est : {$code}\n\nIl expire dans 10 minutes.", function ($message) use ($client) {
            $message->to($client->email)
                ->subject('Votre code de vérification — Vohitsoa Tsena');
        });
        session(['pending_client_email' => $client->email]);

        return redirect()->route('client.verify.form')
            ->with('success', "Un code a été envoyé à {$client->email}"
        );

    }*/
    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:6',
        ]);
        $email=session('pending_client_email');
        $client=Client::where('email', $email)->first();
        if(!$client) {
            return back()->withErrors(['verification_code' => 'Session expirée, veuillez vous réinscrire.']);
        }
        if ($client->verification_code != $request->verification_code) {
            return back()->withErrors(['verification_code' => 'Code incorrect.']);
        }
        if (now()->gt($client->code_expires_at)) {
            return back()->WithErrors(['code' => 'Code expired. Réinscrivez-vous.']);
        }
        $client->update([
            'is_verified' => true,
            'verification_code' => null,
            'code_expires_at'=> null,
        ]);
        Auth::guard('client')->login($client);
        session()->forget('pending_client_email');
        return redirect()->route('client.achat')
            ->with('success', 'Compte vérifié ! Bienvenue ' . $client->prenom
        );
    }
   /** public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:6',
        ]);

        $email  = session('pending_client_email');
        $client = Client::where('email', $email)->first();

        dd([
            'email_session'    => $email,
            'code_saisi'       => $request->verification_code,
            'code_en_base'     => $client?->verification_code,
            'expires_at'       => $client?->code_expires_at,
            'expiré'           => $client ? now()->gt($client->code_expires_at) : 'client introuvable',
            'sont_egaux'       => $client ? ($client->verification_code == $request->verification_code) : 'client introuvable',
        ]);
    }*/
    public function showVerifyForm()
    {
        if (!session('pending_client_email')) {
            return redirect()->route('client.achat')
                ->with('login_error', 'Session expirée. Veuillez vous réinscrire.');
        }

        return view('client.verify');
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
             ->with('show_register_modal', true)
             ->with('login_error', 'Email ou mot de passe incorrect.'
         );
     }
    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login');
    }


}
