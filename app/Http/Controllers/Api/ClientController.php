<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $user = Auth::user();

        // 1. Statistiques du client
        $stats = [
            'total_commandes'    => Vente::where('user_id', $user->id)->count(),
            'commandes_en_cours' => Vente::where('user_id', $user->id)
                ->whereIn('status', ['en_attente', 'en_cours', 'expedie'])
                ->count(),
            'commandes_livrees'  => Vente::where('user_id', $user->id)
                ->where('status', 'livre')
                ->count(),
            'total_depense'      => Vente::where('user_id', $user->id)
                ->where('status', '!=', 'annule')
                ->sum('montant_total'),
        ];

        // 2. Derniers achats / commandes
        $commandes = Vente::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($vente) {
                return [
                    'id'            => $vente->id,
                    'reference'     => $vente->reference ?? 'CMD-' . str_pad($vente->id, 5, '0', STR_PAD_LEFT),
                    'montant_total' => $vente->montant_total,
                    'status'        => $vente->status ?? 'en_attente',
                    'mode_paiement' => $vente->mode_paiement ?? 'Espèces',
                    'created_at'    => $vente->created_at ? $vente->created_at->format('d/m/Y H:i') : null,
                ];
            });

        // 3. Réponse JSON unique
        return response()->json([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'stats'     => $stats,
            'commandes' => $commandes,
        ]);
    }

    /**
     * Déconnexion sécurisée
     */
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
