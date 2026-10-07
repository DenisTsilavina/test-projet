<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Stock;
use App\Models\Commande;
use App\Models\CommandeLigne;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PanierController extends Controller
{
    public function index()
    {
        $panier = session()->get('panier', []);
        $total = array_reduce($panier, fn($carry, $item) => $carry + ($item['prix'] * $item['quantite']), 0);

        return view('client.panier', compact('panier', 'total'));
    }

    public function ajouter(Request $request, $id)
    {
        $produit = Produit::findOrFail($id);
        $panier = session()->get('panier', []);

        if (isset($panier[$id])) {
            $panier[$id]['quantite'] += $request->input('quantite', 1);
        } else {
            $panier[$id] = [
                'id' => $produit->id,
                'nom' => $produit->nom,
                'prix' => $produit->prix_vente,
                'quantite' => (int)$request->input('quantite', 1),
            ];
        }

        session()->put('panier', $panier);
        return redirect()->back()->with('success', 'Produit ajouté au panier !');
    }

    public function modifier(Request $request, $id)
    {
        $panier = session()->get('panier', []);

        if (isset($panier[$id])) {
            $panier[$id]['quantite'] = max(1, (int)$request->quantite);
            session()->put('panier', $panier);
        }

        return redirect()->route('panier.index');
    }

    public function supprimer($id)
    {
        $panier = session()->get('panier', []);

        if (isset($panier[$id])) {
            unset($panier[$id]);
            session()->put('panier', $panier);
        }

        return redirect()->route('panier.index')->with('success', 'Article retiré du panier.');
    }

    public function passerCommande(Request $request)
    {
        $panier = session()->get('panier', []);

        if (empty($panier)) {
            return redirect()->route('boutique.index')->with('error', 'Votre panier est vide.');
        }

        $request->validate([
            'nom_client' => 'required|string|max:255',
            'telephone_client' => 'required|string|max:20',
            'adresse_livraison' => 'nullable|string',
        ]);

        $total = array_reduce($panier, fn($carry, $item) => $carry + ($item['prix'] * $item['quantite']), 0);

        $commande = Commande::create([
            'code_commande' => 'CMD-' . strtoupper(Str::random(6)),
            'nom_client' => $request->nom_client,
            'telephone_client' => $request->telephone_client,
            'adresse_livraison' => $request->adresse_livraison,
            'montant_total' => $total,
            'statut' => 'en_attente',
        ]);

        foreach ($panier as $item) {
            $produit = Produit::find($item['id']);

            CommandeLigne::create([
                'commande_id' => $commande->id,
                'produit_id' => $item['id'],
                'quantite' => $item['quantite'],
                'prix_unitaire' => $item['prix'],
                'sous_total' => $item['prix'] * $item['quantite'],
            ]);

            // Déduction du stock si c'est un produit lié à un stock direct
            if ($produit && $produit->type === 'stock' && $produit->stock_id) {
                $stock = Stock::find($produit->stock_id);
                if ($stock) {
                    $stock->decrement('quantite', $item['quantite']);
                }
            }
        }

        session()->forget('panier');

        return redirect()->route('boutique.index')->with('success', "Commande {$commande->code_commande} enregistrée avec succès !");
    }
}
