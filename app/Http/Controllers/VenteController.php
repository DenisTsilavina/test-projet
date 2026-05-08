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

    public function index()
    {
        $varotra = Vente::with(['description', 'categorie', 'user'])->get();
        return view('admin.vente.index', compact('varotra'));

    }


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

        // booted() recalcule automatiquement reste_a_payer + payment_status
        $commande->save();

        $commande->client->user->notify(new CommandeStatusNotification($commande));

        return back()->with('success', 'Commande mise à jour et client notifié par email.');
    }

    public function create()
    {
        $stocks= Stock::all();
        $descriptions = Description::where('effectif', '>', 0)->get();
        $categories  = SousCategory::all();
        $unites = Unite::all();
        return view('admin.vente.create', compact('stocks', 'descriptions', 'categories','unites'));
            //passe en deuxieme view les donner
           // ->with('client.achat', 'stocks', 'descriptions', 'categories'));
    }

    public function store(Request $request)
    {
        // ══════════════════════════════════════════
        // 1. VALIDATION
        // ══════════════════════════════════════════
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

        // ══════════════════════════════════════════
        // 2. RÉSOLUTION DU CLIENT
        //    Connecté  → users       (client_id)
        //    Anonyme   → clients     (client_anon_id)
        // ══════════════════════════════════════════
        $clientId   = null;
        $clientType = null;

        if (Auth::check()) {
            $clientId   = Auth::id();
            $clientType = 'user';
        } else {
            $request->validate([
                'phone' => ['required', 'string', 'max:20'],
                'ville' => ['required', 'string', 'max:100'],
            ]);

            $client = Client::firstOrCreate(
                ['telephone' => $request->phone],
                [
                    'adresse' => $request->address,
                    'ville'   => $request->ville,
                ]
            );

            $clientId   = $client->id;
            $clientType = 'client';
        }

        // ══════════════════════════════════════════
        // 3. TRANSACTION
        // ══════════════════════════════════════════
        DB::beginTransaction();

        try {
            $totalGeneral = 0;
            $lignes = [];

            foreach ($request->ventes as $vente) {

                $description = Description::with('unite')->findOrFail($vente['description_id']);
                $sousCategorie = SousCategory::findOrFail($vente['categorie_id']);
               // $uniteChoisie = Unite::findOrFail($vente['unite_id']);
                // Si unite_id absent, utiliser l'unité de la description
                $uniteChoisie = isset($vente['unite_id']) && $vente['unite_id']
                    ? Unite::findOrFail($vente['unite_id'])
                    : $description->unite;

                $quantite  = (float) $vente['quantite'];
                $prixStock = (float) $sousCategorie->prix_vente;
                $effectif  = (float) $description->effectif;
                $type = $description->unite->type; // 'masse' | 'volume' | 'unit'

                // ──────────────────────────────────────────
                // CAS A — type = 'unit' (pcs)
                // Pas de conversion, calcul direct
                //
                //   reste         = effectif - quantite
                //   prix_unitaire = prix_stock / effectif
                //   total         = quantite × prix_unitaire
                // ──────────────────────────────────────────
                if ($type === 'unit') {

                    $reste = $effectif - $quantite;

                    if ($reste < 0) {
                        throw new \Exception(
                            "Stock insuffisant pour « {$description->description} » "
                            . "(demandé : {$quantite} pcs, disponible : {$effectif} pcs)."
                        );
                    }

                    $prixUnitaire = $prixStock / $effectif;
                    $total = $quantite * $prixUnitaire;

                    $description->effectif = $reste;
                    $description->save();

                } else {
                    // ──────────────────────────────────────────
                    // CAS B — type = 'masse' ou 'volume'
                    // Tout convertir vers l'unité de BASE
                    //
                    //   stock_base        = effectif × factor_stock
                    //   achat_base        = quantite × factor_unite_choisie
                    //   reste_base        = stock_base - achat_base
                    //
                    //   prix_unitaire_base = prix_stock / stock_base
                    //   total              = achat_base × prix_unitaire_base
                    //
                    // Exemples :
                    //   3 sacs×25kg → stock_base = 75 kg
                    //   achat 12 kg → achat_base = 12 kg  → prix = 12 × (150000/75) = 24 000 Ar
                    //   achat 500g  → achat_base = 0.5 kg → prix = 0.5 × 2000 = 1 000 Ar
                    // ──────────────────────────────────────────
                    $factorStock = (float) $description->unite->factor;
                    $factorAchat = (float) $uniteChoisie->factor;

                    $stockBase = $effectif  * $factorStock;  // ex: 3 × 25 = 75 kg
                    $achatBase = $quantite  * $factorAchat;  // ex: 12 × 1 = 12 kg

                    $resteBase = $stockBase - $achatBase;

                    if ($resteBase < 0) {
                        throw new \Exception(
                            "Stock insuffisant pour « {$description->description} » "
                            . "(demandé : {$achatBase}, disponible : {$stockBase} en unité de base)."
                        );
                    }

                    $prixUnitaireBase = $prixStock / $stockBase; // Ar par unité de base
                    $total = $achatBase * $prixUnitaireBase;
                    $prixUnitaire = $prixUnitaireBase;

                    // Recalcul effectif restant dans l'unité d'origine
                    $description->effectif = $resteBase / $factorStock;
                    $description->save();
                }

                $totalGeneral += $total;

                $lignes[] = [
                    'stock_id' => $vente['stock_id'],
                    'description_id' => $vente['description_id'],
                    'categorie_id' => $vente['categorie_id'],
                    'unite_id' => $vente['unite_id'],
                    'quantite' => $quantite,
                    'unite_symbol' => $uniteChoisie->symbol,
                    'prix_unitaire' => round($prixUnitaire, 2),
                    'total' => round($total, 2),
                    'produit_nom' => $description->description,
                    'sous_categorie' => $sousCategorie->stock_categorie,
                ];
            }

            // ── Vente principale ──
            $venteRecord = Vente::create([
                'vendeur_id' => Auth::id(),
                'client_id' => $clientType === 'user'   ? $clientId : null,
                'client_anon_id' => $clientType === 'client' ? $clientId : null,
                'mode_paiement' => $request->mode_paiement,
                'total_general' => round($totalGeneral, 2),
            ]);

            // ── Lignes ──
            foreach ($lignes as $ligne) {
                LigneVente::create(array_merge($ligne, ['vente_id' => $venteRecord->id]));
            }

            DB::commit();

            return redirect()
                ->route('admin.vente.recu', $venteRecord->id)
                ->with('success', 'Vente enregistrée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['stock' => $e->getMessage()])
                ->withInput();
        }
    }

    // ──────────────────────────────────────────────────
    // 5. REÇU PDF (vue blade imprimable)
    // ──────────────────────────────────────────────────
    public function recu(Vente $vente)
    {
        // ✅ Enlever 'client' si la relation n'existe pas
        $vente->load(['lignes', 'vendeur', 'clientAnon']);
        return view('recu.vente', compact('vente'));
    }



// ── Calcul du montant d'une ligne (statique, réutilisable) ───────────────
    //
    //   isPcs = 1  →  montant = qteClient × prix
    //   isPcs = 0  →  stockBase = effectif_stock × factor
    //                 montant   = prix × qteClient ÷ stockBase
    //
    private static function calculerMontantLigne(array $ligne): float
    {
        $qte       = (float) $ligne['effectif'];
        $prix      = (float) $ligne['prix'];
        $factor    = (float) $ligne['factor'];
        $stockBase = (float) $ligne['stock_base'];
        $isPcs     = (int)   $ligne['is_pcs'] === 1;

        if ($isPcs || $factor <= 1) {
            return $qte * $prix;
        }

        if ($stockBase <= 0) return 0;

        return ($prix * $qte) / $stockBase;
    }

    public function getTotalRevenue(): float
    {
        return Vente::with('categorie')->get()->sum(function ($vente) {
            $prixAchat = (int) ($vente->categorie->prix_achat ?? 0);
            return ($vente->prix - $prixAchat) * $vente->effectif;
        });
    }

    public function getRevenue(Vente $vente): float
    {
        $prixAchat = (int) ($vente->categorie->prix_achat ?? 0);
        return ($vente->prix - $prixAchat) * $vente->effectif;
    }

    public function dashboard()
    {
        $totalRevenue  = (float)$this->getTotalRevenue();
        $totalVente = (int) Vente::count();
        $venteRecentes = Vente::with(['description', 'categorie'])
            ->latest()
            ->take(10)
            ->get();
        return view('admin.vente.dashboard', compact('totalRevenue', 'totalVente', 'venteRecentes'));
    }

    public function destroy(Vente $vente)
    {
        // Restaurer le stock dans Description (cohérent avec store())
        $description = Description::find($vente->description_id);
        if ($description) {
            $description->increment('effectif', $vente->effectif);
        }

        $vente->delete();

        return redirect()->route('admin.vente.index')
            ->with('success', 'Vente supprimée avec succès.');
    }
}
