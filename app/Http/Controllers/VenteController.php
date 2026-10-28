<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\Vente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VenteController extends Controller
{
    public function index()
    {
        $varotra = Vente::with(['produit', 'user'])->latest()->get();

        return view('admin.vente.index', compact('varotra'));
    }

    public function create()
    {
        $produits = Produit::where('statut', 'actif')
            ->with(['produitStock.stock', 'produitFini.compositions.stock'])
            ->get();

        // Données pour l'affichage de la disponibilité (magasin / réserve) en temps réel
        [$catalogue, $stocksData] = $this->preparerCatalogue($produits);

        return view('admin.vente.create', compact('produits', 'catalogue', 'stocksData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ventes' => 'required|array|min:1',
            'ventes.*.produit_id' => 'required|exists:produits,id',
            'ventes.*.prix' => 'required|numeric|min:0',
            'ventes.*.effectif' => 'required|integer|min:1',
        ]);

        $lignes = collect($request->ventes)->map(fn ($v) => [
            'produit_id' => $v['produit_id'],
            'quantite' => $v['effectif'],
        ])->toArray();

        // Vérification de disponibilité (magasin + réserve)
        $errors = \App\Models\Commande::verifierDisponibiliteLignes($lignes);

        if (!empty($errors)) {
            return back()->withInput()->withErrors($errors);
        }

        DB::transaction(function () use ($request) {
            foreach ($request->ventes as $v) {
                $produit  = Produit::find($v['produit_id']);
                $prix = (int) $v['prix'];
                $effectif = (int) $v['effectif'];

                $vente = Vente::create([
                    'user_id' => auth()->id(),
                    'produit_id' => $produit->id,
                    'type_produit' => $produit->type,
                    'prix' => $prix,
                    'effectif' => $effectif,
                    'prix_total' => $prix * $effectif,
                ]);

                $vente->appliquerSurStock();
            }
        });

        $count = count($request->ventes);

        return redirect()->route('admin.vente.index')
            ->with('success', "$count vente(s) enregistrée(s) avec succès !");
    }

    /**
     * UN CLIC : transfère de la réserve vers le magasin ce qui manque
     * pour couvrir les lignes de vente en cours (appelé en AJAX par la page de vente).
     *
     * Reçoit : lignes[] = { produit_id, effectif }
     * Retourne du JSON : message, transferts[], stocks[] (quantités à jour).
     */
    public function chargerMagasin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lignes' => 'required|array|min:1',
            'lignes.*.produit_id' => 'required|exists:produits,id',
            'lignes.*.effectif' => 'required|integer|min:1',
        ]);

        $produits = Produit::with(['produitStock', 'produitFini.compositions'])
            ->whereIn('id', collect($validated['lignes'])->pluck('produit_id')->unique())
            ->get()
            ->keyBy('id');

        // Besoins cumulés par stock : stock_id => quantité nécessaire
        $besoins = [];

        foreach ($validated['lignes'] as $ligne) {
            $produit  = $produits->get($ligne['produit_id']);
            $quantite = (int) $ligne['effectif'];

            if (!$produit) {
                continue;
            }

            if ($produit->type === 'stock') {
                $stockId = $produit->produitStock?->stock_id;

                if ($stockId) {
                    $besoins[$stockId] = ($besoins[$stockId] ?? 0) + $quantite;
                }
            } else {
                foreach ($produit->produitFini?->compositions ?? [] as $composition) {
                    $besoins[$composition->stock_id] = ($besoins[$composition->stock_id] ?? 0)
                        + (float) $composition->quantite_necessaire * $quantite;
                }
            }
        }

        if (empty($besoins)) {
            return response()->json([
                'message' => 'Ces produits ne sont reliés à aucun stock.',
            ], 422);
        }

        $transferts = [];
        $insuffisants = [];
        $stocksMaj = [];

        try {
            DB::transaction(function () use ($besoins, &$transferts, &$insuffisants, &$stocksMaj) {
                // Verrou en ordre d'id : évite les blocages entre deux chargements simultanés
                $stocks = Stock::whereIn('id', array_keys($besoins))
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                foreach ($stocks as $stock) {
                    $manque = round($besoins[$stock->id] - (float) $stock->quantite_magasin, 2);

                    if ($manque > 0) {
                        $aTransferer = round(min($manque, (float) $stock->quantite), 2);

                        if ($aTransferer > 0) {
                            MouvementStock::transferer(
                                $stock,
                                $aTransferer,
                                'Chargement du magasin depuis la page de vente'
                            );

                            $transferts[] = ['stock' => $stock->name_stock, 'quantite' => $aTransferer];
                        }

                        // La réserve ne suffit pas à couvrir le besoin
                        if (round($manque - $aTransferer, 2) > 0) {
                            $insuffisants[] = $stock->name_stock;
                        }
                    }

                    $stock->refresh();

                    $stocksMaj[] = [
                        'id' => $stock->id,
                        'magasin' => (float) $stock->quantite_magasin,
                        'reserve' => (float) $stock->quantite,
                    ];
                }
            });
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => $this->messageChargement($transferts, $insuffisants),
            'transferts' => $transferts,
            'stocks' => $stocksMaj,
        ]);
    }

    public function getTotalRevenue(): float
    {
        return Vente::with('produit')->get()->sum(fn ($v) => $v->revenu_net);
    }

    public function getRevenue(Vente $vente): float
    {
        $vente->loadMissing('produit');
        return $vente->revenu_net;
    }

    public function dashboard()
    {
        $totalRevenue = $this->getTotalRevenue();
        $totalVente = Vente::count();
        $venteRecentes = Vente::with('produit')->latest()->take(10)->get();

        return view('admin.vente.dashboard', compact('totalRevenue', 'totalVente', 'venteRecentes'));
    }

    public function destroy(Vente $vente)
    {
        $vente->annulerSurStock();
        $vente->delete();

        return redirect()->route('admin.vente.index')
            ->with('success', 'Vente supprimée avec succès.');
    }

    /**
     * Prépare, pour la page de vente : le catalogue des produits (avec leurs besoins
     * en stock par unité vendue) et l'état des stocks (magasin / réserve).
     *
     * @return array{0: array, 1: array}
     */
    private function preparerCatalogue(Collection $produits): array
    {
        $stocks = [];

        $catalogue = $produits->map(function (Produit $produit) use (&$stocks) {
            $besoins = [];

            if ($produit->type === 'stock') {
                $stock = $produit->produitStock?->stock;

                if ($stock) {
                    $stocks[$stock->id] = $stock;
                    $besoins[] = ['stock_id' => $stock->id, 'qte' => 1.0];
                }
            } else {
                foreach ($produit->produitFini?->compositions ?? [] as $composition) {
                    if ($composition->stock) {
                        $stocks[$composition->stock->id] = $composition->stock;
                        $besoins[] = [
                            'stock_id' => $composition->stock->id,
                            'qte' => (float) $composition->quantite_necessaire,
                        ];
                    }
                }
            }

            return [
                'id' => $produit->id,
                'nom' => $produit->nom,
                'type' => $produit->type,
                'prix' => (int) $produit->prix_vente,
                'besoins' => $besoins,
            ];
        })->values()->all();

        $stocksData = collect($stocks)->map(fn (Stock $stock) => [
            'id' => $stock->id,
            'nom' => $stock->name_stock,
            'magasin' => (float) $stock->quantite_magasin,
            'reserve' => (float) $stock->quantite,
        ])->values()->all();

        return [$catalogue, $stocksData];
    }

    private function messageChargement(array $transferts, array $insuffisants): string
    {
        $format = fn (float $q) => rtrim(rtrim(number_format($q, 2, ',', ' '), '0'), ',');

        if (empty($transferts) && empty($insuffisants)) {
            return 'Le magasin couvre déjà ces ventes, rien à transférer.';
        }

        $message = '';

        if (!empty($transferts)) {
            $liste = collect($transferts)
                ->map(fn ($t) => "{$t['stock']} (+" . $format($t['quantite']) . ')')
                ->implode(', ');

            $message .= "Transféré vers le magasin : {$liste}.";
        }

        if (!empty($insuffisants)) {
            $message .= ' Stock total insuffisant pour : ' . implode(', ', $insuffisants) . '.';
        }

        return trim($message);
    }
}
