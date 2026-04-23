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
        $stocks= Stock::all();
        $descriptions = Description::where('effectif', '>', 0)->get();
        $categories  = SousCategory::all();

        return view('admin.vente.create', compact('stocks', 'descriptions', 'categories'));
            //passe en deuxieme view les donner
           // ->with('client.achat', 'stocks', 'descriptions', 'categories'));
    }

    function store(Request $request)
    {
        // ── 1. Validation ──
        $request->validate([
            // Ventes
            'ventes'                  => 'required|array|min:1',
            'ventes.*.stock_id'       => 'required|exists:stocks,id',
            'ventes.*.description_id' => 'required|exists:descriptions,id',
            'ventes.*.categorie_id'   => 'required|exists:sous_categories,id',
            'ventes.*.prix'           => 'required|numeric|min:0',
            'ventes.*.effectif'       => 'required|integer|min:1',
            // Client (correspond exactement aux champs fillable du modèle)
            'address'                 => 'required|string|max:255',
            'ville'                   => 'required|string|max:255',
            'phone'                   => 'required|string|max:20',
            'mode_paiement'           => 'required|string|in:espece,mvola,airtel_money,virement',
        ]);

        // ── 2. Vérification des stocks ──
        $errors = [];

        foreach ($request->ventes as $i => $vente) {
            $description = Description::find($vente['description_id']);

            if (!$description) {
                $errors["ventes.$i.description_id"] = 'Article introuvable.';
                continue;
            }

            if ((int)$vente['effectif'] > (int)$description->effectif) {
                $errors["ventes.$i.effectif"] =
                    "Stock insuffisant pour « {$description->description} ». " .
                    "Disponible : {$description->effectif} unité(s).";
            }
        }

        if (!empty($errors)) {
            return back()->withInput()->withErrors($errors);
        }


        $client = \App\Models\Client::create([
            'user_id' => auth()->id(),
            'address' => $request->address,
            'ville'   => $request->ville,
            'phone'   => $request->phone,
        ]);

        // ── 4. Enregistrer les ventes liées au client ──
        foreach ($request->ventes as $vente) {
            $prix     = (int) $vente['prix'];
            $effectif = (int) $vente['effectif'];

            Vente::create([
                'user_id'        => auth()->id(),
                'client_id'      => $client->id,
                'description_id' => (int) $vente['description_id'],
                'categorie_id'   => (int) $vente['categorie_id'],
                'prix'           => $prix,
                'effectif'       => $effectif,
                'prix_total'     => $prix * $effectif,
                'mode_paiement'  => $request->mode_paiement,
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
        return Vente::with('categorie')->get()->sum(function ($vente) {
            $prixAchat = (int) ($vente->categorie->prix_achat ?? 0);
            return ($vente->prix - $prixAchat) * $vente->effectif;
        });
    }

    public function getRevenue(Vente $vente): float
    {
        $prixAchat = (int) ($vente->categorie->prix_achat ?? 0);
        return ($vente->prix - $prixAchat) * $vente->effectif;
    }

    public function dashboard()
    {
        $totalRevenue  = (float)$this->getTotalRevenue();
        $totalVente = (int) Vente::count();
        $venteRecentes = Vente::with(['description', 'categorie'])
            ->latest()
            ->take(10)
            ->get();
        return view('admin.vente.dashboard', compact('totalRevenue', 'totalVente', 'venteRecentes'));
    }

    public function destroy(Vente $vente)
    {
        // Restaurer le stock dans Description (cohérent avec store())
        $description = Description::find($vente->description_id);
        if ($description) {
            $description->increment('effectif', $vente->effectif);
        }

        $vente->delete();

        return redirect()->route('admin.vente.index')
            ->with('success', 'Vente supprimée avec succès.');
    }
}
