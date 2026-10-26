<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Composition;
use App\Models\Produit;
use App\Models\ProduitFini;
use App\Models\ProduitStock;
use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProduitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Seuls les administrateurs peuvent créer, modifier ou supprimer
        $this->middleware(function ($request, $next) {
            $userRole = $request->user()->roleService()->role();

            if (!in_array($userRole, [UserRole::ADMINS, UserRole::SUPER_ADMIN], true)) {
                abort(403, "Action non autorisée. Vous devez être administrateur.");
            }

            return $next($request);
        })->except(['index', 'show']);
    }

    /**
     * Liste paginée avec recherche (q) et filtres (type, statut).
     */
    public function index(Request $request): View
    {
        $filters = [
            'q' => $request->string('q')->trim()->toString(),
            'type' => $request->string('type')->toString(),
            'statut' => $request->string('statut')->toString(),
        ];

        $produits = Produit::with(['produitStock.stock', 'produitFini.compositions'])
            ->filtrer($filters)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.produit.index', compact('produits'));
    }

    public function create(): View
    {
        $stocks = Stock::orderBy('name_stock')->get();

        return view('admin.produit.create', compact('stocks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:stock,fini',
            'prix_vente' => 'required|numeric|min:0',
            'statut' => 'nullable|in:actif,inactif',

            // Requis seulement si type = stock
            'stock_id' => 'required_if:type,stock|nullable|exists:stocks,id',
            'seuil_alerte' => 'nullable|integer|min:0',

            // Requis seulement si type = fini
            'temps_preparation' => 'nullable|integer|min:0',
            ...$this->compositionRules(required: $request->input('type') === 'fini'),
        ]);

        $produit = DB::transaction(function () use ($validated) {
            $produit = Produit::create([
                'nom' => $validated['nom'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'prix_vente'  => $validated['prix_vente'],
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

                $this->syncCompositions($produitFini, $validated['compositions']);
            }

            return $produit;
        });

        return redirect()->route('produit.index')
            ->with('success', "Produit « {$produit->nom} » créé avec succès.");
    }

    public function show(Produit $produit): View
    {
        $produit->load(['produitStock.stock', 'produitFini.compositions.stock']);

        return view('admin.produit.show', compact('produit'));
    }

    public function edit(Produit $produit): View
    {
        $produit->load(['produitStock', 'produitFini.compositions']);
        $stocks = Stock::orderBy('name_stock')->get();

        return view('admin.produit.edit', compact('produit', 'stocks'));
    }

    /**
     * Le type (stock <-> fini) ne peut pas être modifié après coup, pour éviter
     * de migrer produits_stock <-> produits_finis. Pour changer de type,
     * il vaut mieux supprimer et recréer le produit.
     */
    public function update(Request $request, Produit $produit): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix_vente' => 'required|numeric|min:0',
            'statut' => 'required|in:actif,inactif',
            'seuil_alerte' => 'nullable|integer|min:0',
            'temps_preparation' => 'nullable|integer|min:0',
            ...$this->compositionRules(required: false),
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

            if ($produit->estFini() && $produit->produitFini) {
                $produit->produitFini->update([
                    'temps_preparation' => $validated['temps_preparation'] ?? null,
                    'description' => $validated['description'] ?? null,
                ]);

                // Si des compositions sont envoyées, la recette est remplacée entièrement
                // (plus simple et plus sûr qu'une correspondance ligne par ligne).
                if (!empty($validated['compositions'])) {
                    $this->syncCompositions($produit->produitFini, $validated['compositions']);
                }
            }
        });

        return redirect()->route('produit.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Produit $produit): RedirectResponse
    {
        // Empêche la suppression si le produit a déjà été commandé (historique préservé).
        if ($produit->lignesCommandes()->exists()) {
            return redirect()->route('produit.index')
                ->with('error', 'Impossible de supprimer ce produit : des commandes y font référence.');
        }

        // produits_stock / produits_finis (+ compositions) sont supprimés en cascade (cascadeOnDelete).
        $produit->delete();

        return redirect()->route('produit.index')
            ->with('success', 'Produit supprimé avec succès.');
    }

    /**
     * Règles de validation communes aux compositions (recette).
     * "distinct" empêche d'utiliser deux fois le même ingrédient.
     */
    private function compositionRules(bool $required): array
    {
        return [
            'compositions' => ($required ? 'required' : 'nullable') . '|array' . ($required ? '|min:1' : ''),
            'compositions.*.stock_id' => 'required_with:compositions|distinct|exists:stocks,id',
            'compositions.*.quantite_necessaire' => 'required_with:compositions|numeric|min:0.01',
            'compositions.*.unite' => 'nullable|string|max:20',
        ];
    }

    /**
     * Remplace toutes les compositions d'un produit fini par la liste fournie.
     */
    private function syncCompositions(ProduitFini $produitFini, array $compositions): void
    {
        $produitFini->compositions()->delete();

        foreach ($compositions as $composition) {
            Composition::create([
                'produit_fini_id' => $produitFini->id,
                'stock_id' => $composition['stock_id'],
                'quantite_necessaire' => $composition['quantite_necessaire'],
                'unite' => $composition['unite'] ?? null,
            ]);
        }
    }
}
