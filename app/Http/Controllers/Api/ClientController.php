<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $user = Auth::user();

        // CHANGEMENT : Vente -> Commande (le client voit SES commandes,
        // pas les ventes directes de l'admin). CHANGEMENT des noms de
        // colonnes : user_id -> client_id, status -> statut,
        // montant_total -> total (les vrais noms sur `commandes`).
        $stats = [
            'total_commandes' => Commande::where('client_id', $user->id)->count(),
            'commandes_en_cours' => Commande::where('client_id', $user->id)->whereIn('statut', ['en_attente', 'infos_demandees', 'en_cours'])->count(),
            'commandes_livrees' => Commande::where('client_id', $user->id)->where('statut', 'livre')->count(),
            'total_depense' => Commande::where('client_id', $user->id)->where('statut', '!=', 'annule')->sum('total'),
        ];

        $commandes = Commande::where('client_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($commande) {
                return [
                    'id' => $commande->id,
                    'reference'  => $commande->reference ?? 'CMD-' . str_pad($commande->id, 5, '0', STR_PAD_LEFT),
                    'total' => $commande->total,
                    'statut' => $commande->statut,
                    'created_at' => $commande->created_at?->format('d/m/Y H:i'),
                ];
            });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'stats' => $stats,
            'commandes' => $commandes,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Déconnexion réussie.'
        ]);
    }
}
