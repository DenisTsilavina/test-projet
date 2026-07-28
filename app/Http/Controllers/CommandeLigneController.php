<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\CommandeLigne;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommandeLigneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Ajouter une ligne (Ingrédient ou Main d'œuvre) à la commande.
     */
    public function store(Request $request, Commande $commande)
    {
        abort_if($commande->user_id !== Auth::id(), 403);

        // On ne peut modifier les lignes que si la commande est encore "en attente"
        if ($commande->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Vous ne pouvez plus modifier une commande en cours de traitement.');
        }

        $request->validate([
            'type' => 'required|in:ingredient,main_oeuvre',
            'quantite' => 'required|integer|min:1',
            // Si c'est un ingrédient, on valide l'ID du stock
            'stock_id' => 'required_if:type,ingredient|exists:stocks,id',
            // Si c'est de la main d'œuvre, le client saisit lui-même le libellé et le prix estimé
            'libelle' => 'required_if:type,main_oeuvre|nullable|string|max:255',
            'prix_unitaire' => 'required_if:type,main_oeuvre|nullable|numeric|min:0',
        ]);

        if ($request->type === 'ingredient') {
            $stockItem = Stock::findOrFail($request->stock_id);

            // Vérification basique du stock disponible (optionnel mais recommandé)
            if ($stockItem->quantite < $request->quantite) {
                return redirect()->back()->with('error', "Stock insuffisant pour {$stockItem->nom}. (Disponible: {$stockItem->quantite})");
            }

            $commande->lignes()->create([
                'type' => 'ingredient',
                'libelle' => $stockItem->nom, // ou $stockItem->designation selon ton modèle Stock
                'quantite' => $request->quantite,
                'prix_unitaire' => $stockItem->prix, // assure-toi que ton modèle Stock possède cet attribut
            ]);
        } else {
            // Ajout d'une ligne de main d'œuvre
            $commande->lignes()->create([
                'type' => 'main_oeuvre',
                'libelle' => $request->libelle,
                'quantite' => $request->quantite,
                'prix_unitaire' => $request->prix_unitaire,
            ]);
        }

        return redirect()->back()->with('success', 'Ligne ajoutée avec succès !');
    }

    /**
     * Supprimer une ligne.
     */
    public function destroy(Commande $commande, CommandeLigne $ligne)
    {
        abort_if($commande->user_id !== Auth::id(), 403);
        abort_if($ligne->commande_id !== $commande->id, 404);

        if ($commande->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Impossible de modifier cette commande.');
        }

        $ligne->delete();

        return redirect()->back()->with('success', 'Ligne supprimée.');
    }
}
