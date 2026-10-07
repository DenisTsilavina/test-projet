<?php

namespace App\Http\Controllers;

use App\Models\Description;
use App\Models\Stock;
use App\Models\SousCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DescriptionController extends Controller
{
    public function index()
    {
        $stocks = Stock::with(['descriptions.sousCategorie', 'unite'])->get();

        return view('stock.index', compact('stocks'));
    }

    // ==========================================
    // GESTION DES DESCRIPTIONS
    // ==========================================

    public function create($stock_id)
    {
        $stock = Stock::findOrFail($stock_id);

        return view('stock.description-create', compact('stock'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_id'    => 'required|exists:stocks,id',
            'description' => 'required|string|max:255',
            'effectif'    => 'nullable|integer|min:0',
            'region'      => 'required|string|max:100',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Gestion du téléversement de l'image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('descriptions', 'public');
            $validated['image'] = $path;
        }

        Description::create($validated);

        return redirect()
            ->route('stock.index')
            ->with('success', 'Description ajoutée avec succès !');
    }

    public function edit(Description $description)
    {
        $description->load('stock');

        return view('stock.description-edit', compact('description'));
    }

    public function update(Request $request, Description $description)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'effectif'    => 'nullable|integer|min:0',
            'region'      => 'required|string|max:100',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Remplacement de l'image si un nouveau fichier est envoyé
        if ($request->hasFile('image')) {
            if ($description->image && Storage::disk('public')->exists($description->image)) {
                Storage::disk('public')->delete($description->image);
            }

            $path = $request->file('image')->store('descriptions', 'public');
            $validated['image'] = $path;
        }

        $description->update($validated);

        return redirect()
            ->route('stock.index')
            ->with('success', 'Description mise à jour avec succès !');
    }

    public function destroy(Description $description)
    {
        // Suppression du fichier image sur le disque s'il existe
        if ($description->image && Storage::disk('public')->exists($description->image)) {
            Storage::disk('public')->delete($description->image);
        }

        $description->delete();

        return redirect()
            ->back()
            ->with('success', 'Description supprimée avec succès !');
    }

    // ==========================================
    // GESTION DES SOUS-CATÉGORIES
    // ==========================================

    public function createSousCategorie($description_id)
    {
        $description = Description::with('stock')->findOrFail($description_id);

        return view('stock.souscategorie-create', compact('description'));
    }

    public function storeSousCategorie(Request $request)
    {
        $validated = $request->validate([
            'description_id' => 'required|exists:descriptions,id',
            'prix_achat'     => 'nullable|numeric|min:0',
            'prix_vente'     => 'nullable|numeric|min:0',
        ]);

        SousCategory::create($validated);

        return redirect()
            ->route('stock.index')
            ->with('success', 'Sous-catégorie ajoutée avec succès !');
    }

    public function editSousCategorie(SousCategory $sousCategory)
    {
        $sousCategory->load('description.stock');

        return view('stock.souscategorie-edit', compact('sousCategory'));
    }

    public function updateSousCategorie(Request $request, SousCategory $sousCategory)
    {
        $validated = $request->validate([
            'prix_achat' => 'nullable|numeric|min:0',
            'prix_vente' => 'nullable|numeric|min:0',
        ]);

        $sousCategory->update($validated);

        return redirect()
            ->route('stock.index')
            ->with('success', 'Sous-catégorie mise à jour avec succès !');
    }

    public function destroySousCategorie(SousCategory $sousCategory)
    {
        $sousCategory->delete();

        return redirect()
            ->back()
            ->with('success', 'Sous-catégorie supprimée avec succès !');
    }
}
