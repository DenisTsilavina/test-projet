<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Vente;
use Illuminate\Http\Request;
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
        // CHANGEMENT : Stock/Description -> Produit directement, avec
        // les infos nécessaires pour afficher la dispo (stock ou compositions).
        $produits = Produit::where('statut', 'actif')
            ->with(['produitStock.stock', 'produitFini.compositions.stock'])
            ->get();

        return view('admin.vente.create', compact('produits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ventes'                  => 'required|array|min:1',
            'ventes.*.produit_id'     => 'required|exists:produits,id',
            'ventes.*.prix'           => 'required|numeric|min:0',
            'ventes.*.effectif'       => 'required|integer|min:1',
        ]);

        $lignes = collect($request->ventes)->map(fn ($v) => [
            'produit_id' => $v['produit_id'],
            'quantite'   => $v['effectif'],
        ])->toArray();

        // Réutilise la même vérification de dispo que les commandes.
        $errors = \App\Models\Commande::verifierDisponibiliteLignes($lignes);

        if (!empty($errors)) {
            return back()->withInput()->withErrors($errors);
        }

        DB::transaction(function () use ($request) {
            foreach ($request->ventes as $v) {
                $produit  = Produit::find($v['produit_id']);
                $prix     = (int) $v['prix'];
                $effectif = (int) $v['effectif'];

                $vente = Vente::create([
                    'user_id'      => auth()->id(),
                    'produit_id'   => $produit->id,
                    'type_produit' => $produit->type,
                    'prix'         => $prix,
                    'effectif'     => $effectif,
                    'prix_total'   => $prix * $effectif,
                ]);

                $vente->appliquerSurStock();
            }
        });

        $count = count($request->ventes);

        return redirect()->route('admin.vente.index')
            ->with('success', "$count vente(s) enregistrée(s) avec succès !");
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
}
