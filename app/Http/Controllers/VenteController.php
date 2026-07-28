<?php

namespace App\Http\Controllers;

use App\Models\Description;
use App\Models\SousCategory;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Models\Vente;

class VenteController extends Controller
{

    public function index()
    {
        $varotra = Vente::with(['description', 'categorie', 'user'])->get();
        return view('admin.vente.index', compact('varotra'));
    }


    public function create()
    {
        $stocks = Stock::all();
        $descriptions = Description::where('effectif', '>', 0)->get();
        $categories  = SousCategory::all();

        return view('admin.vente.create', compact('stocks', 'descriptions', 'categories')); // ✅
    }

   /**
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'stock_id'         => 'required|exists:stocks,id',
            'description_id'   => 'required|exists:descriptions,id',
            'sous_categorie_id'=> 'required|exists:sous_categories,id',
            'prix'             => 'required|numeric|min:0',
            'effectif'         => 'required|integer|min:1',
        ]);

        // Calcul automatique du prix total
        $validated['prix_total'] = $validated['prix'] * $validated['effectif'];

        // Récupérer la SousCategory (contient le stock disponible)
        $sousCategorie = SousCategory::find($validated['sous_categorie_id']);

        if (!$sousCategorie) {
            return back()
                ->withInput()
                ->withErrors(['sous_categorie_id' => 'Catégorie introuvable.']);
        }

        // Vérifier stock disponible
        if ($sousCategorie->stock_categorie < $validated['effectif']) {
            return back()
                ->withInput()
                ->withErrors(['effectif' => 'Stock insuffisant. Disponible : ' . $sousCategorie->stock_categorie]);
        }

        // Créer la vente
        Vente::create($validated);

        // Déduire du stock
        $sousCategorie->decrement('stock_categorie', $validated['effectif']);

        return redirect()->route('admin.vente.index')
            ->with('success', 'Vente enregistrée avec succès ! Merci !');
    }
*/
    public function store(Request $request)
    {
        $request->validate([
            'ventes'                   => 'required|array|min:1',
            'ventes.*.stock_id'        => 'required|exists:stocks,id',
            'ventes.*.description_id'  => 'required|exists:descriptions,id',
            'ventes.*.categorie_id'    => 'required|exists:sous_categories,id',
            'ventes.*.prix'            => 'required|numeric|min:0',
            'ventes.*.effectif'        => 'required|integer|min:1',
        ]);

        $errors = [];

        foreach ($request->ventes as $i => $vente) {

            $description = Description::find($vente['description_id']);

            if (!$description) {
                $errors["ventes.$i.description_id"] = 'article introuvable.';
                continue;
            }

            // ← comparer avec effectif de description (pas stock_categorie)
            if ((int)$vente['effectif'] > (int)$description->effectif) {
                $errors["ventes.$i.effectif"] =
                    "Stock insuffisant pour « {$description->description} ». " .
                    "Disponible : {$description->effectif} unité(s).";
            }
        }

        if (!empty($errors)) {
            return back()->withInput()->withErrors($errors);
        }

        foreach ($request->ventes as $vente) {

            $prix     = (int) $vente['prix'];
            $effectif = (int) $vente['effectif'];

            Vente::create([
                'user_id'        => auth()->id(),
                'description_id' => (int) $vente['description_id'],
                'categorie_id'   => (int) $vente['categorie_id'],
                'prix'           => $prix,
                'effectif'       => $effectif,
                'prix_total'     => $prix * $effectif,   // ← int * int, plus d'erreur
            ]);

            // ← décrémenter effectif dans descriptions (pas stock_categorie)
            Description::find($vente['description_id'])
                ->decrement('effectif', $effectif);
        }

        $count = count($request->ventes);

        return redirect()->route('admin.vente.index')
            ->with('success', "$count vente(s) enregistrée(s) avec succès !");
    }
    public function getTotalRevenue(): float
    {
        return Vente::with('categorie')->get()->sum(function ($vente) {
            return ($vente->prix - ($vente->sousCategorie->prix_achat ?? 0))
                * $vente->effectif;
        });
    }

    public function getRevenue(Vente $vente): float
    {
        return ($vente->prix - ($vente->sousCategorie->prix_achat ?? 0))
            * $vente->effectif;
    }

    public function dashboard()
    {
        $totalRevenue  = $this->getTotalRevenue();
        $totalVente    = Vente::count();
        $venteRecentes = Vente::with(['description', 'categorie'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.vente.dashboard', compact('totalRevenue', 'totalVente', 'venteRecentes'));
    }

    public function destroy(Vente $vente)
    {
        // Restaurer le stock dans SousCategory
        $sousCategorie = SousCategory::find($vente->sous_categorie_id);

        if ($sousCategorie) {
            $sousCategorie->increment('stock_categorie', $vente->effectif);
        }

        $vente->delete();

        return redirect()->route('admin.vente.index')
            ->with('success', 'Vente supprimée avec succès.');
    }
}
