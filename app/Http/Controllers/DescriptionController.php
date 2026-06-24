<?php

namespace App\Http\Controllers;

use App\Models\Description;
use App\Models\Stock;
use App\Models\SousCategory; // Ajout de l'import manquant pour la sous-catégorie
use Illuminate\Http\Request;

class DescriptionController extends Controller
{
    /**
     * Liste des stocks avec descriptions (vue index).
     */
    public function index()
    {
        $stocks = Stock::with(['descriptions.sousCategories', 'unites'])->get();

        return view('stock.index', compact('stocks'));
    }

    // ==========================================
    // GESTION DES DESCRIPTIONS
    // ==========================================

    /**
     * Affiche le formulaire de création d'une description.
     */
    public function create($stock_id)
    {
        $stock = Stock::findOrFail($stock_id);

        return view('stock.description-create', compact('stock'));
    }

    /**
     * Enregistre une nouvelle description.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_id'    => 'required|exists:stocks,id',
            'description' => 'required|string|max:255',
            'region'    => 'required|string|max:100',
        ]);

        Description::create($validated);

        return redirect()
            ->route('stock.index')
            ->with('success', 'Description ajoutée avec succès !');
    }

    /**
     * Affiche le formulaire d'édition d'une description.
     */
    public function edit(Description $description)
    {
        $description->load('stock');

        return view('stock.description-edit', compact('description'));
    }

    /**
     * Met à jour une description existante.
     */
    public function update(Request $request, Description $description)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'region'    => 'required|string|max:100',
        ]);

        $description->update($validated);

        return redirect()
            ->route('stock.index')
            ->with('success', 'Description mise à jour avec succès !');
    }

    /**
     * Supprime une description.
     */
    public function destroy(Description $description)
    {
        $description->delete();

        return redirect()
            ->back()
            ->with('success', 'Description supprimée avec succès !');
    }

    // ==========================================
    // GESTION DES SOUS-CATÉGORIES (Noms corrigés)
    // ==========================================

    /**
     * Affiche la page de création d'une sous-catégorie.
     */
    public function createSousCategorie($description_id)
    {
        $description = Description::with('stock')->findOrFail($description_id);

        return view('stock.souscategorie-create', compact('description'));
    }

    /**
     * Enregistre une nouvelle sous-catégorie.
     */
    public function storeSousCategorie(Request $request)
    {
        $validated = $request->validate([
            'description_id'  => 'required|exists:descriptions,id',
            'stock_categorie' => 'required|string|max:255',
            'prix_achat'      => 'nullable|numeric|min:0',
            'prix_vente'      => 'nullable|numeric|min:0',
        ]);

        SousCategory::create($validated);

        return redirect()
            ->route('stock.index')
            ->with('success', 'Sous-catégorie ajoutée avec succès !');
    }

    /**
     * Affiche la page d'édition d'une sous-catégorie.
     */
    public function editSousCategorie(SousCategory $sousCategory)
    {
        $sousCategory->load('description.stock');

        return view('stock.souscategorie-edit', compact('sousCategory'));
    }

    /**
     * Met à jour une sous-catégorie existante.
     */
    public function updateSousCategorie(Request $request, SousCategory $sousCategory)
    {
        $validated = $request->validate([
            'stock_categorie' => 'required|string|max:255',
            'prix_achat'      => 'nullable|numeric|min:0',
            'prix_vente'      => 'nullable|numeric|min:0',
        ]);

        $sousCategory->update($validated);

        return redirect()
            ->route('stock.index')
            ->with('success', 'Sous-catégorie mise à jour avec succès !');
    }

    /**
     * Supprime une sous-catégorie.
     */
    public function destroySousCategorie(SousCategory $sousCategory)
    {
        $sousCategory->delete();

        return redirect()
            ->back()
            ->with('success', 'Sous-catégorie supprimée avec succès !');
    }
}
