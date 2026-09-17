<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommandeAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $userRole = auth()->user()->roleService()->role();

            if (!in_array($userRole, [UserRole::ADMINS, UserRole::SUPER_ADMIN, UserRole::VENDEUR])) {
                abort(403, "Action non autorisée.");
            }

            return $next($request);
        });
    }

    public function index()
    {
        $commandes = Commande::with('client')
            ->latest()
            ->get();

        return view('admin.commandes.index', compact('commandes'));
    }

    public function show(Commande $commande)
    {
        $commande->load(['client', 'lignes.produit', 'paiements', 'transports']);

        return view('admin.commandes.show', compact('commande'));
    }

    /**
     * L'admin demande des précisions au client avant de trancher
     * (ex: le stock est juste, il veut confirmer les quantités).
     * Aucun mouvement de stock ici.
     */
    public function demanderInfos(Request $request, Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return back()->with('error', 'Cette commande a déjà été traitée.');
        }

        $validated = $request->validate([
            'remarque' => 'nullable|string',
        ]);

        $commande->update([
            'statut' => 'infos_demandees',
            'remarque' => $validated['remarque'] ?? $commande->remarque,
        ]);

        return back()->with('success', 'Demande de précisions envoyée au client.');
    }

    /**
     * Approuve la commande : revérifie la dispo (le stock a pu bouger
     * depuis la demande initiale du client), déduit réellement le stock,
     * passe au statut 'en_cours'.
     */
    public function confirmer(Commande $commande)
    {
        if (!in_array($commande->statut, ['en_attente', 'infos_demandees'])) {
            return back()->with('error', 'Cette commande a déjà été traitée.');
        }

        $errors = $commande->verifierDisponibilite();

        if (!empty($errors)) {
            return back()->withErrors($errors);
        }

        DB::transaction(function () use ($commande) {
            foreach ($commande->lignes as $ligne) {
                $ligne->appliquerSurStock();
            }

            $commande->update(['statut' => 'en_cours']);
        });

        return back()->with('success', "Commande #{$commande->id} confirmée, stock mis à jour.");
    }

    /**
     * Refuse la commande. Aucun mouvement de stock, puisqu'on n'avait
     * encore rien déduit à la demande initiale.
     */
    public function refuser(Request $request, Commande $commande)
    {
        if (!in_array($commande->statut, ['en_attente', 'infos_demandees'])) {
            return back()->with('error', 'Cette commande a déjà été traitée.');
        }

        $validated = $request->validate([
            'remarque' => 'nullable|string',
        ]);

        $commande->update([
            'statut' => 'annule',
            'remarque' => $validated['remarque'] ?? $commande->remarque,
        ]);

        return back()->with('success', "Commande #{$commande->id} refusée.");
    }
}
