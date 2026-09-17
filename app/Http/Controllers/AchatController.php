<?php

namespace App\Http\Controllers;

use App\Models\Achat;
use App\Models\LigneAchat;
use App\Models\Fournisseur;
use App\Models\Stock;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AchatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $userRole = auth()->user()->roleService()->role();

            if (!in_array($userRole, [UserRole::ADMINS, UserRole::SUPER_ADMIN, UserRole::VENDEUR])) {
                abort(403, "Action non autorisée. Vous devez être administrateur ou vendeur.");
            }

            return $next($request);
        })->except(['index', 'show']);
    }

    public function index()
    {
        $achats = Achat::with('fournisseur')->latest()->get();

        return view('admin.achat.index', compact('achats'));
    }

    public function create()
    {
        $fournisseurs = Fournisseur::all();
        $stocks = Stock::all();

        return view('admin.achat.create', compact('fournisseurs', 'stocks'));
    }

    /**
     * Enregistre l'achat en statut 'en_attente'. Pas de mouvement de
     * stock ici — le stock n'entre réellement qu'à la validation
     * (méthode valider()), une fois que la marchandise est réceptionnée.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'remarque' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.stock_id' => 'required|exists:stocks,id',
            'lignes.*.quantite' => 'required|numeric|min:0.01',
            'lignes.*.prix_unitaire' => 'required|integer|min:0',
        ]);

        $achat = DB::transaction(function () use ($validated) {
            $total = collect($validated['lignes'])
                ->sum(fn ($l) => $l['quantite'] * $l['prix_unitaire']);

            $achat = Achat::create([
                'fournisseur_id' => $validated['fournisseur_id'],
                'date_achat' => now(),
                'statut' => 'en_attente',
                'remarque' => $validated['remarque'] ?? null,
                'total' => $total,
            ]);

            foreach ($validated['lignes'] as $ligne) {
                LigneAchat::create([
                    'achat_id' => $achat->id,
                    'stock_id' => $ligne['stock_id'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'total_ligne' => $ligne['quantite'] * $ligne['prix_unitaire'],
                ]);
            }

            return $achat;
        });

        return redirect()->route('achat.index')
            ->with('success', "Achat #{$achat->id} enregistré. En attente de validation (réception marchandise).");
    }

    public function show(Achat $achat)
    {
        $achat->load(['fournisseur', 'lignes.stock']);

        return view('admin.achat.show', compact('achat'));
    }

    public function edit(Achat $achat)
    {
        if ($achat->statut !== 'en_attente') {
            return redirect()->route('achat.index')
                ->with('error', 'Cet achat a déjà été validé, il ne peut plus être modifié.');
        }

        $achat->load('lignes.stock');
        $fournisseurs = Fournisseur::all();
        $stocks = Stock::all();

        return view('admin.achat.edit', compact('achat', 'fournisseurs', 'stocks'));
    }

    /**
     * Modifie uniquement les infos générales, tant que l'achat n'est
     * pas encore validé (le stock n'a pas encore bougé).
     */
    public function update(Request $request, Achat $achat)
    {
        if ($achat->statut !== 'en_attente') {
            return redirect()->route('achat.index')
                ->with('error', 'Cet achat a déjà été validé, il ne peut plus être modifié.');
        }

        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'remarque' => 'nullable|string',
        ]);

        $achat->update($validated);

        return redirect()->route('achat.index')
            ->with('success', 'Achat mis à jour avec succès.');
    }

    /**
     * Valide la réception de la marchandise : applique les entrées de
     * stock (via Achat::valider(), qui utilise MouvementStock::enregistrer()
     * pour chaque ligne) et passe l'achat au statut 'valide'.
     */
    public function valider(Achat $achat)
    {
        if ($achat->statut !== 'en_attente') {
            return redirect()->route('achat.index')
                ->with('error', 'Cet achat a déjà été traité.');
        }

        $achat->valider();

        return redirect()->route('achat.index')
            ->with('success', "Achat #{$achat->id} validé, stock mis à jour.");
    }

    /**
     * Supprime l'achat. Si déjà validé (stock déjà entré), on retire
     * ce qui avait été ajouté avant de supprimer, pour ne pas laisser
     * un stock fantôme.
     */
    public function destroy(Achat $achat)
    {
        DB::transaction(function () use ($achat) {
            if ($achat->statut === 'valide') {
                foreach ($achat->lignes as $ligne) {
                    \App\Models\MouvementStock::enregistrer(
                        stock: $ligne->stock,
                        typeMouvement: 'sortie',
                        referenceType: 'ajustement',
                        referenceId: $achat->id,
                        quantite: $ligne->quantite,
                        remarque: "Annulation achat #{$achat->id}"
                    );
                }
            }

            $achat->delete();
        });

        return redirect()->route('achat.index')
            ->with('success', 'Achat supprimé.');
    }
}
