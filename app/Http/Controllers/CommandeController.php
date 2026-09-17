<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Produit;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $userRole = auth()->user()->roleService()->role();

            if (!in_array($userRole, [UserRole::ADMINS, UserRole::SUPER_ADMIN, UserRole::VENDEUR])) {
                abort(403, "Action non autorisée.");
            }

            return $next($request);
        })->only(['approuver', 'refuser', 'destroy']);
    }

    public function index()
    {
        $user = auth()->user();
        $userRole = $user->roleService()->role();

        $query = Commande::with(['client', 'lignes.produit']);

        if ($userRole === UserRole::CLIENT) {
            $query->where('client_id', $user->id);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_besoin'                 => 'nullable|date',
            'date_livraison'               => 'nullable|date',
            'remarque'                     => 'nullable|string',
            'lignes'                       => 'required|array|min:1',
            'lignes.*.produit_id'          => 'required|exists:produits,id',
            'lignes.*.quantite'            => 'required|integer|min:1',
            'lignes.*.prix_unitaire'       => 'required|numeric|min:0',
        ]);

        // CHANGEMENT : appel de la version centralisée dans Commande.php,
        // partagée avec CommandeAdminController, au lieu d'une copie locale.
        $errors = Commande::verifierDisponibiliteLignes($validated['lignes']);

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        $commande = DB::transaction(function () use ($validated) {
            $total = collect($validated['lignes'])
                ->sum(fn ($l) => $l['quantite'] * $l['prix_unitaire']);

            $commande = Commande::create([
                'client_id'      => auth()->id(),
                'date_commande'  => now(),
                'date_besoin'    => $validated['date_besoin'] ?? null,
                'date_livraison' => $validated['date_livraison'] ?? null,
                'statut'         => 'en_attente',
                'remarque'       => $validated['remarque'] ?? null,
                'total'          => $total,
            ]);

            foreach ($validated['lignes'] as $ligne) {
                $produit = Produit::find($ligne['produit_id']);

                LigneCommande::create([
                    'commande_id'   => $commande->id,
                    'produit_id'    => $produit->id,
                    'type_produit'  => $produit->type,
                    'quantite'      => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'total_ligne'   => $ligne['quantite'] * $ligne['prix_unitaire'],
                ]);
            }

            return $commande;
        });

        return response()->json(
            $commande->load('lignes.produit'),
            201
        );
    }

    public function show(Commande $commande)
    {
        $this->autoriserAccesCommande($commande);

        $commande->load(['client', 'lignes.produit', 'paiements', 'transports']);

        return response()->json($commande);
    }

    public function update(Request $request, Commande $commande)
    {
        $this->autoriserAccesCommande($commande);

        if ($commande->statut !== 'en_attente') {
            return response()->json([
                'message' => 'Cette commande a déjà été traitée, elle ne peut plus être modifiée.',
            ], 422);
        }

        $validated = $request->validate([
            'date_besoin'    => 'nullable|date',
            'date_livraison' => 'nullable|date',
            'remarque'       => 'nullable|string',
        ]);

        $commande->update($validated);

        return response()->json($commande);
    }

    /**
     * L'équivalent API de CommandeAdminController::confirmer().
     */
    public function approuver(Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return response()->json(['message' => 'Cette commande a déjà été traitée.'], 422);
        }

        // CHANGEMENT : appel de la version centralisée (instance) du modèle.
        $errors = $commande->verifierDisponibilite();

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        DB::transaction(function () use ($commande) {
            foreach ($commande->lignes as $ligne) {
                $ligne->appliquerSurStock();
            }

            $commande->update(['statut' => 'en_cours']);
        });

        return response()->json($commande->fresh('lignes.produit'));
    }

    public function refuser(Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return response()->json(['message' => 'Cette commande a déjà été traitée.'], 422);
        }

        $commande->update(['statut' => 'annule']);

        return response()->json($commande);
    }

    public function destroy(Commande $commande)
    {
        DB::transaction(function () use ($commande) {
            if ($commande->statut === 'en_cours') {
                foreach ($commande->lignes as $ligne) {
                    $ligne->annulerSurStock();
                }
            }

            $commande->delete();
        });

        return response()->json(['message' => 'Commande supprimée.']);
    }

    private function autoriserAccesCommande(Commande $commande): void
    {
        $user = auth()->user();
        $userRole = $user->roleService()->role();

        if ($userRole === UserRole::CLIENT && $commande->client_id !== $user->id) {
            abort(403, "Cette commande ne vous appartient pas.");
        }
    }
}
