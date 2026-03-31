<?php

namespace App\Http\Controllers;

use App\Http\Requests\DescriptionRequest;
use App\Models\Description;
use App\Models\SousCategory;
use App\Models\Stock;
use Illuminate\Http\Request;
use DB;

class DescriptionController extends Controller
{
    public function index()
    {
        $stocks = Stock::with(['descriptions.sousCategories'])->get();
        $descriptions = Description::with(['sousCategories'])->get();
        $categories=SousCategory::all();

        return view('stock.index', compact('stocks', 'descriptions','categories'));
    }
    public function createdescription($id)
    {
        $stock = Stock::findOrFail($id);
        return view('stock.description-create', compact('stock'));
    }


    public function store (Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'effectif' => 'required|string',
            'stock_categorie' => 'nullable|string',
            'prix_achat'=>'nullable|integer',
            'prix_vente'=>'nullable|integer',
            'stock_id' => 'required',
        ]);
        try {
            DB::transaction(function () use ($validated) {
                $description= Description::create([
                    'description' => $validated['description'],
                    'effectif' => $validated['effectif'],
                    'stock_id' => $validated['stock_id'],
                    'stock_categorie' => $validated['stock_categorie'] ?? null,

                ]);

                if (!empty($validated['stock_categorie'])) {

                    SousCategory::create([
                        'description_id' => $description->id,
                        'stock_categorie' => $validated['stock_categorie'],
                        'prix_achat' => $validated['prix_achat'],
                        'prix_vente' => $validated['prix_vente'],
                    ]);
                    //dd($validated['name_categorie']);
                }
            });


            return redirect()->route('stock.index')->with('success', 'Description et sous_categorie enregistrées !');

        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de les enregistrements.');
        }
    }
    public function update(DescriptionRequest $descriptionRequest, $id)
    {
        $validated = $descriptionRequest->validated();
        try {
            DB::transaction(function () use ($validated, $id) {

                $description = Description::findOrFail($id);
                $description->update([
                    'description' => $validated['description'],
                    'effectif' => $validated['effectif'],
                    'stock_id' => $validated['stock_id'],
                    'stock_categorie' => $validated['stock_categorie'] ?? null,
                ]);

                if (!empty($validated['stock_categorie'])) {
                    SousCategory::updateOrCreate(
                        ['description_id' => $description->id],
                        [
                            'stock_categorie' => $validated['stock_categorie'],
                            'prix_achat' => $validated['prix_achat'],
                            'prix_vente' => $validated['prix_vente'],
                        ]
                    );
                } else {
                    SousCategory::where('description_id', $description->id)->delete();
                }
            });

            return redirect()->route('stock.index')->with('success', 'Description mise à jour avec succès !');

        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour.');
        }

    }
    public function destroy( Description $description){
        $description ->delete();
        return redirect()->back()->with('success', 'sous_category ajoutée avec succès !');
    }
}
