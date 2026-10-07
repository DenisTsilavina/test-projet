<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\ProduitStock;
use App\Models\ProduitFini;
use App\Models\Composition;
use App\Models\Stock;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $userRole = auth()->user()->roleService()->role();

            if (!in_array($userRole, [UserRole::ADMINS, UserRole::SUPER_ADMIN ])) {
                abort(403, "Action non autorisée. Vous devez être administrateur.");
            }

            return $next($request);
        })->except(['index', 'show']);
    }

    public function index()
    {
        $produits = Produit::with(['produitStock.stock', 'produitFini'])->latest()->get();

        return view('admin.produit.index', compact('produits'));
    }

    public function create()
    {
        $stocks = Stock::all();

        return view('admin.produit.create', compact('stocks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:stock,fini',
            'prix_vente' => 'required|numeric|min:0',
            'statut' => 'nullable|string',

            // Requis seulement si type = stock
            'stock_id' => 'required_if:type,stock|exists:stocks,id',
            'seuil_alerte' => 'nullable|integer|min:0',

            // Requis seulement si type = fini
            'temps_preparation' => 'nullable|integer|min:0',
            'compositions' => 'required_if:type,fini|array|min:1',
            'compositions.*.stock_id' => 'required_with:compositions|exists:stocks,id',
            'compositions.*.quantite_necessaire' => 'required_with:compositions|numeric|min:0.01',
            'compositions.*.unite' => 'nullable|string',
        ]);

        $produit = DB::transaction(function () use ($validated) {
            $produit = Produit::create([
                'nom' => $validated['nom'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'prix_vente' => $validated['prix_vente'],
                'statut' => $validated['statut'] ?? 'actif',
            ]);

            if ($validated['type'] === 'stock') {
                ProduitStock::create([
                    'produit_id' => $produit->id,
                    'stock_id' => $validated['stock_id'],
                    'seuil_alerte' => $validated['seuil_alerte'] ?? 0,
                ]);
            } else {
                $produitFini = ProduitFini::create([
                    'produit_id' => $produit->id,
                    'temps_preparation' => $validated['temps_preparation'] ?? null,
                    'description' => $validated['description'] ?? null,
                ]);

                foreach ($validated['compositions'] as $composition) {
                    Composition::create([
                        'produit_fini_id' => $produitFini->id,
                        'stock_id' => $composition['stock_id'],
                        'quantite_necessaire'  => $composition['quantite_necessaire'],
                        'unite' => $composition['unite'] ?? null,
                    ]);
                }
            }

            return $produit;
        });

        return redirect()->route('produit.index')
            ->with('success', "Produit « {$produit->nom} » créé avec succès.");
    }

    public function show(Produit $produit)
    {
        $produit->load(['produitStock.stock', 'produitFini.compositions.stock']);

        return view('admin.produit.show', compact('produit'));
    }

    public function edit(Produit $produit)
    {
        $produit->load(['produitStock', 'produitFini.compositions']);
        $stocks = Stock::all();

        return view('admin.produit.edit', compact('produit', 'stocks'));
    }

    /**
     * Ici on ne permet PAS de changer le type (stock <-> fini) après
     * coup, pour éviter d'avoir à migrer produits_stock <-> produits_finis
     * en cours de route. Pour changer de type, il vaut mieux supprimer
     * et recréer le produit.
     */
    public function update(Request $request, Produit $produit)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix_vente' => 'required|numeric|min:0',
            'statut' => 'required|string',
            'seuil_alerte' => 'nullable|integer|min:0',
            'temps_preparation' => 'nullable|integer|min:0',
            'compositions' => 'nullable|array',
            'compositions.*.stock_id' => 'required_with:compositions|exists:stocks,id',
            'compositions.*.quantite_necessaire'  => 'required_with:compositions|numeric|min:0.01',
            'compositions.*.unite' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $produit) {
            $produit->update([
                'nom' => $validated['nom'],
                'description' => $validated['description'] ?? null,
                'prix_vente' => $validated['prix_vente'],
                'statut' => $validated['statut'],
            ]);

            if ($produit->estStock() && isset($validated['seuil_alerte'])) {
                $produit->produitStock?->update([
                    'seuil_alerte' => $validated['seuil_alerte'],
                ]);
            }

            if ($produit->estFini()) {
                $produit->produitFini?->update([
                    'temps_preparation' => $validated['temps_preparation'] ?? null,
                    'description' => $validated['description'] ?? null,
                ]);

                // Si des compositions sont envoyées, on remplace entièrement
                // l'ancienne recette par la nouvelle (plus simple et plus sûr
                // que d'essayer de faire correspondre ligne par ligne).
                if (!empty($validated['compositions']) && $produit->produitFini) {
                    $produit->produitFini->compositions()->delete();

                    foreach ($validated['compositions'] as $composition) {
                        Composition::create([
                            'produit_fini_id' => $produit->produitFini->id,
                            'stock_id' => $composition['stock_id'],
                            'quantite_necessaire' => $composition['quantite_necessaire'],
                            'unite' => $composition['unite'] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('produit.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Produit $produit)
    {
        // Empêche la suppression si le produit a déjà été commandé,
        // pour préserver l'historique des commandes passées.
        if ($produit->lignesCommandes()->exists()) {
            return redirect()->route('produit.index')
                ->with('error', 'Impossible de supprimer ce produit : des commandes y font référence.');
        }

        DB::transaction(function () use ($produit) {
            // produits_stock / produits_finis (+ compositions en cascade) sont liés par cascadeOnDelete() dans les migrations, donc pas besoin de les supprimer manuellement ici.
            $produit->delete();
        });

        return redirect()->route('produit.index')
            ->with('success', 'Produit supprimé avec succès.');
    }
}
