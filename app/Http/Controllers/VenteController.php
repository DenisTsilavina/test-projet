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
        // CHANGEMENT : la relation 'categorie' n'existe plus directement sur
        // Vente (colonne categorie_id supprimée). On passe par
        // description.sousCategorie pour retrouver le prix d'achat / de vente.
        $varotra = Vente::with(['description.sousCategorie', 'user'])->get();
        return view('admin.vente.index', compact('varotra'));
    }

    public function create()
    {
        $stocks = Stock::all();
        $descriptions = Description::where('effectif', '>', 0)
            ->with('sousCategorie')
            ->get();

        // CHANGEMENT : plus de liste globale de SousCategory à choisir
        // séparément — chaque description porte désormais sa propre
        // sous-catégorie (relation 1-1 via description_id).
        return view('admin.vente.create', compact('stocks', 'descriptions'));
    }

    public function store(Request $request)
    {
        // CHANGEMENT : stock_id et categorie_id retirés de la validation
        // (colonnes supprimées de `ventes`). Le stock et la sous-catégorie
        // se déduisent via description_id.
        $request->validate([
            'ventes'                   => 'required|array|min:1',
            'ventes.*.description_id'  => 'required|exists:descriptions,id',
            'ventes.*.prix'            => 'required|numeric|min:0',
            'ventes.*.effectif'        => 'required|integer|min:1',
        ]);

        $errors = [];

        foreach ($request->ventes as $i => $vente) {
            $description = Description::find($vente['description_id']);

            if (!$description) {
                $errors["ventes.$i.description_id"] = 'Article introuvable.';
                continue;
            }

            if ((int) $vente['effectif'] > (int) $description->effectif) {
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
                'prix'           => $prix,
                'effectif'       => $effectif,
                'prix_total'     => $prix * $effectif,
            ]);

            Description::find($vente['description_id'])
                ->decrement('effectif', $effectif);
        }

        $count = count($request->ventes);

        return redirect()->route('admin.vente.index')
            ->with('success', "$count vente(s) enregistrée(s) avec succès !");
    }

    public function getTotalRevenue(): float
    {
        // CHANGEMENT : accès au prix d'achat via description.sousCategorie
        // au lieu de vente.categorie (relation supprimée).
        return Vente::with('description.sousCategorie')->get()->sum(function ($vente) {
            $prixAchat = $vente->description->sousCategorie->prix_achat ?? 0;
            return ($vente->prix - $prixAchat) * $vente->effectif;
        });
    }

    public function getRevenue(Vente $vente): float
    {
        $vente->loadMissing('description.sousCategorie');
        $prixAchat = $vente->description->sousCategorie->prix_achat ?? 0;

        return ($vente->prix - $prixAchat) * $vente->effectif;
    }

    public function dashboard()
    {
        $totalRevenue  = $this->getTotalRevenue();
        $totalVente    = Vente::count();
        $venteRecentes = Vente::with(['description.sousCategorie'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.vente.dashboard', compact('totalRevenue', 'totalVente', 'venteRecentes'));
    }

    public function destroy(Vente $vente)
    {
        // CHANGEMENT : le stock disponible est suivi sur descriptions.effectif
        // (SousCategory n'a plus de colonne stock_categorie), donc on restaure
        // ici plutôt que sur la sous-catégorie.
        $vente->description()->increment('effectif', $vente->effectif);

        $vente->delete();

        return redirect()->route('admin.vente.index')
            ->with('success', 'Vente supprimée avec succès.');
    }
}
