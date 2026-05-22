<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Commande;
use App\Models\Description;
use App\Models\SousCategory;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\Unite;
use App\Models\LigneVente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VenteController extends Controller
{
    // ══════════════════════════════════════════════════════════════
    // INDEX — Liste toutes les ventes avec leurs lignes (produits)
    // ══════════════════════════════════════════════════════════════
    public function index()
    {
        $varotra = Vente::with(['lignes', 'vendeur', 'clientAnon'])->latest()->get();

        $unites = Unite::all();

        return view('admin.vente.index', compact('varotra', 'unites'));
    }

    // ══════════════════════════════════════════════════════════════
    // UPDATE STATUS (Commande)
    // ══════════════════════════════════════════════════════════════
    public function updateStatus(Request $request, Commande $commande)
    {
        $request->validate([
            'status'       => 'required|in:pending,approved,cancelled',
            'montant_paye' => 'nullable|numeric|min:0',
        ]);

        $commande->status = $request->status;

        if ($request->filled('montant_paye')) {
            $commande->montant_paye = $request->montant_paye;
        }

        $commande->save();
        $commande->client->user->notify(new \App\Notifications\CommandeStatusNotification($commande));

        return back()->with('success', 'Commande mise à jour et client notifié par email.');
    }

    // ══════════════════════════════════════════════════════════════
    // CREATE
    // ══════════════════════════════════════════════════════════════
    public function create()
    {
        $stocks = Stock::all();
        $descriptions = Description::where('effectif', '>', 0)->get();
        $categories = SousCategory::all();
        $unites = Unite::all();

        return view('admin.vente.create', compact('stocks', 'descriptions', 'categories', 'unites'));
    }

    // ══════════════════════════════════════════════════════════════
    // STORE
    // ══════════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        // ── 1. Validation de base ─────────────────────────────────
        $request->validate([
            'ventes' => ['required', 'array', 'min:1'],
            'ventes.*.stock_id' => ['required', 'integer', 'exists:stocks,id'],
            'ventes.*.description_id' => ['required', 'integer', 'exists:descriptions,id'],
            'ventes.*.categorie_id' => ['required', 'integer', 'exists:sous_categories,id'],
            'ventes.*.quantite' => ['required', 'numeric', 'min:0.01'],
            'ventes.*.unite_id' => ['required', 'integer', 'exists:unites,id'],
            'mode_paiement' => ['required', 'in:espece,mvola,airtel_money,virement'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:100'],
        ], [
            'ventes.required' => 'Le panier est vide.',
            'ventes.min' => 'Ajoutez au moins un produit.',
            'mode_paiement.required' => 'Le mode de paiement est obligatoire.',
        ]);

        // ── 2. Résolution Client / Vendeur ────────────────────────
        $vendeurId = null;
        $clientAnonId = null;

        if (Auth::guard('client')->check()) {
            $clientAnonId = Auth::guard('client')->id();

        } elseif (Auth::check()) {
            $vendeurId = Auth::id();

            $request->validate([
                'phone' => ['required', 'string', 'max:20'],
                'ville' => ['required', 'string', 'max:100'],
            ]);

            $client = Client::firstOrCreate(
                ['telephone' => $request->phone],
                ['adresse' => $request->address ?? '', 'ville' => $request->ville]
            );

            $clientAnonId = $client->id;

        } else {
            return redirect()->route('login');
        }

        // ── 3. Transaction ────────────────────────────────────────
        DB::beginTransaction();

        try {
            $totalGeneral = 0;
            $lignes = [];

            foreach ($request->ventes as $vente) {
                $description   = Description::with('unite')->findOrFail($vente['description_id']);
                $sousCategorie = SousCategory::findOrFail($vente['categorie_id']);
                $uniteChoisie  = Unite::findOrFail($vente['unite_id']);

                $quantite = (float) $vente['quantite'];
                $prixStock = (float) $sousCategorie->prix_vente;
                $effectif = (float) $description->effectif;
                $factorStock = (float) $description->unite->factor;
                $factorAchat = (float) $uniteChoisie->factor;

                // Conversion en unité de base
                $stockBase = $effectif * $factorStock;
                $achatBase = $quantite * $factorAchat;
                $resteBase = $stockBase - $achatBase;

                if ($resteBase < 0) {
                    throw new \Exception(
                        "Stock insuffisant pour « {$description->description} » "
                        . "(demandé : {$achatBase}, disponible : {$stockBase} en unité de base)."
                    );
                }

                // Calcul du prix
                $prixParBase = $stockBase > 0 ? $prixStock / $factorStock : 0;
                $prixUnitaire = $prixParBase * $factorAchat;
                $total = $quantite * $prixUnitaire;

                // Mise à jour du stock
                $description->effectif = $factorStock > 0
                    ? $resteBase / $factorStock
                    : $effectif - $quantite;
                $description->save();

                $totalGeneral += $total;

                $lignes[] = [
                    'stock_id' => $vente['stock_id'],
                    'description_id' => $vente['description_id'],
                    'categorie_id' => $vente['categorie_id'],
                    'unite_id' => $uniteChoisie->id,
                    'quantite' => $quantite,
                    'unite_symbol' => $uniteChoisie->symbol,
                    'prix_unitaire' => round($prixUnitaire, 2),
                    'total' => round($total, 2),
                    'produit_nom' => $description->description,
                    'sous_categorie' => $sousCategorie->stock_categorie,
                ];
            }

            // Vente principale
            $venteRecord = Vente::create([
                'vendeur_id' => $vendeurId,
                'client_anon_id'=> $clientAnonId,
                'mode_paiement' => $request->mode_paiement,
                'total_general' => round($totalGeneral, 2),
            ]);

            // Lignes de vente
            foreach ($lignes as $ligne) {
                LigneVente::create(array_merge($ligne, ['vente_id' => $venteRecord->id]));
            }
            DB::commit();

            $route = Auth::guard('client')->check()
                ? 'client.vente.recu'
                : 'admin.vente.recu';

            return redirect()
                ->route($route, $venteRecord->id)
                ->with('success', 'Vente enregistrée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['stock' => $e->getMessage()])
                ->withInput();
        }
    }

    // ══════════════════════════════════════════════════════════════
    // REÇU
    // ══════════════════════════════════════════════════════════════
    public function recu(Vente $vente)
    {
        $vente->load(['lignes', 'vendeur', 'clientAnon']);
        return view('recu.vente', compact('vente'));
    }

    // ══════════════════════════════════════════════════════════════
    // DASHBOARD
    // ══════════════════════════════════════════════════════════════
    public function dashboard()
    {
        $totalVentes = Vente::count();
        $totalRevenu = LigneVente::sum('total');

        $ventesRecentes = Vente::with(['lignes', 'vendeur', 'clientAnon'])
            ->latest()->take(10)->get();

        $revenusParJour = LigneVente::selectRaw('DATE(created_at) as jour, SUM(total) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('jour')->orderBy('jour')->get();

        $topProduits = LigneVente::selectRaw('produit_nom, SUM(quantite) as qte_vendue, SUM(total) as revenu')
            ->groupBy('produit_nom')->orderByDesc('revenu')->take(5)->get();

        $parModePaiement = Vente::selectRaw('mode_paiement, COUNT(*) as nb, SUM(total_general) as total')
            ->groupBy('mode_paiement')->get();

        return view('admin.vente.dashboard', compact(
            'totalVentes', 'totalRevenu', 'ventesRecentes',
            'revenusParJour', 'topProduits', 'parModePaiement'
        ));
    }

    // ══════════════════════════════════════════════════════════════
    // DESTROY — Supprime la vente (les lignes en cascade via FK)
    // ══════════════════════════════════════════════════════════════
    public function destroy(Vente $vente)
    {
        // Restaurer le stock pour chaque ligne
        foreach ($vente->lignes as $ligne) {
            $description = Description::find($ligne->description_id);
            if ($description && $description->unite) {
                $factorStock = (float) $description->unite->factor;
                $achatBase   = (float) $ligne->quantite * (float) ($ligne->unite->factor ?? 1);
                $description->effectif += $factorStock > 0
                    ? $achatBase / $factorStock
                    : $ligne->quantite;
                $description->save();
            }
        }

        $vente->delete(); // lignes supprimées en cascade si FK configurée

        return redirect()->route('admin.vente.index')
            ->with('success', 'Vente supprimée avec succès.');
    }
}
