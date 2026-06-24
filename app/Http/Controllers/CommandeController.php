<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Commande;

use App\Models\Stock;
use Illuminate\Http\Request;

class CommandeController extends Controller
{


// 1. Pour afficher la page
    public function create() {
        //  $articles = Article::all(); Récupère les articles pour le Blade
        $stocks = Stock::all();     // Récupère le stock pour le Blade

        return view('client.commande.create-commande', compact( 'stocks'));
    }

// 2. Pour recevoir et traiter les données du formulaire
    public function store(Request $request) {
        // Validation des données de base
        $request->validate([
            'type_commande' => 'required|in:article,stock,autre',
            'effectif' => 'required|integer|min:1',
            'date_besoin' => 'required|date|after_or_equal:today',
            'commentaires' => 'nullable|string',
        ]);

        // Initialisation des variables polymorphiques
        $commandableType = null;
        $commandableId = null;
        $nomCommande = null;

        // Détermination du choix de l'utilisateur
        if ($request->type_commande === 'article') {
            $commandableType = Article::class;
            $commandableId = $request->article_id;
        } elseif ($request->type_commande === 'stock') {
            $commandableType = Stock::class;
            $commandableId = $request->stock_id;
        } else {
            $nomCommande = $request->nom_commande; // Cas "Autre"
        }

        // Sauvegarde en Base de données
        Commande::create([
            'commandable_type' => $commandableType,
            'commandable_id'   => $commandableId,
            'nom_commande'     => $nomCommande,
            'effectif'         => $request->effectif,
            'date_besoin'      => $request->date_besoin,
            'commentaires'     => $request->commentaires,
            'user_id'          => auth()->id() ?? 1, // Id de l'utilisateur connecté
        ]);

        return redirect()->back()->with('success', 'Commande enregistrée avec succès !');
    }
}
