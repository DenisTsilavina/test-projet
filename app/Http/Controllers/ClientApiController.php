<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Commande;

class ClientApiController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        $commandes = Commande::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total_commandes'    => Commande::where('user_id', $user->id)->count(),
            'commandes_en_cours' => Commande::where('user_id', $user->id)->where('status', 'en_cours')->count(),
            'commandes_livrees'  => Commande::where('user_id', $user->id)->where('status', 'livre')->count(),
            'total_depense'      => Commande::where('user_id', $user->id)->where('status', 'livre')->sum('montant_total'),
        ];

        return response()->json([
            'user'      => $user,
            'stats'     => $stats,
            'commandes' => $commandes,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Déconnecté']);
    }
}
