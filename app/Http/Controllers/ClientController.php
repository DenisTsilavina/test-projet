<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Commande;
use App\Models\Unite;
use App\Models\Vente;
use App\Notifications\CommandeStatusNotification;
use Illuminate\Http\Request;
use App\Models\SousCategory;
use App\Models\Description;
use App\Models\LigneVente;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index()
    {
         $client = Client::all();
         $unites = Unite::all();
         return view('client.dashboard', compact('client','unites'));
    }

    public function achat()
    {
        $stocks = Stock::all();
        $descriptions = Description::with('unite', 'sousCategories')
            ->where('effectif', '>', 0)
            ->get();
        $categories = SousCategory::all();
        $unites = Unite::all();

        return view('client.achat', compact('stocks', 'descriptions', 'categories', 'unites'));
    }


    public function createNewClient(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required',
            'ville' => 'required',
            'phone' => 'required',
        ]);
        Client::create([
            'user_id'=> auth()->id(),
            'address' => $request->address,
            'ville' => $request->ville,
            'phone' => $request->phone,
        ]);
        return redirect()->route('dashboard')->with('success', 'tu est authorise');
    }
     public function create()
     {
         return view('commande.lancer');
     }
    /*public function lanceCommande(Request $request)
    {
        $validated = $request->validate([
            'numero_commande' => 'required|string|unique:commandes,numero_commande',
            'date_commande' => 'required|date',
            'total_payements' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,cancelled',
            'payment_status' => 'required|in:payée,nonpayée',
            'address_livraison'=>'required|string|max:255',
            'notes'=>'required|string|max:255',
        ]);
        Commande::create([
            'client_id'=> auth()->id(),
            'numero_commande' => $validated['numero_commande'],
            'date_commande' => $validated['date_commande'],
            'total_payements' => $validated['total_payements'],
            'status' => $validated['status'],
            'payment_status' => $validated['payment_status'],
            'address_livraison' => $validated['address_livraison'],
            'notes' => $validated['notes'] ?? null,
        ]);
       /** Commande::create(array_merge($validated,[
            'client_id'=Auth()->id,
        ]));

        return redirect()
            ->route('commande.mecommande')
            ->with('success', 'Votre commande a bien été lancée !'
        );
    }*/
    public function lanceCommande(Request $request)
    {
        $validated = $request->validate([
            'nom_produit' => 'required|string|max:255',
            'numero_commande' => 'required|string|unique:commandes,numero_commande',
            'date_commande' => 'required|date',
            'total_payements' => 'required|numeric|min:0',
            'montant_paye' => 'required|numeric|min:0|lte:total_payements',
            'payment_method' => 'required|in:cash,mobile_money,virement,carte',
            'address_livraison' => 'required|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);

        $commande = Commande::create(array_merge($validated, [
            'client_id' => auth()->id(),
            'status' => Commande::STATUS_PENDING,
        ]));

       /* auth()->user()->notify(new CommandeStatusNotification($commande));

        return redirect()
            ->route('commande.mecommande')
            ->with('success', 'Votre commande a bien été lancée !'
        );*/
    }
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
    public function recu(Vente $vente)
    {
        // ✅ Enlever 'client' si la relation n'existe pas
        $vente->load(['lignes', 'clientAnon']);
        return view('recu.vente', compact('vente'));
    }

}
