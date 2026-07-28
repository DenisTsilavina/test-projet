<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tableau de bord client : stats + activité récente.
     * Alimente les variables attendues par client/dashboard.blade.php.
     */
    public function dashboard()
    {
        $commandes = Auth::user()->commandes();

        $totalCommandes = (clone $commandes)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $enCours = (clone $commandes)->where('statut', 'en_cours')->count();
        $livrees = (clone $commandes)
            ->where('statut', 'livre')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $montantTotal = (clone $commandes)->sum('montant');

        $recentCommandes = (clone $commandes)->latest()->take(5)->get();

        return view('client.dashboard', compact(
            'totalCommandes',
            'enCours',
            'livrees',
            'montantTotal',
            'recentCommandes'
        ));
    }

    /**
     * Liste des commandes de l'utilisateur connecté.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->commandes()->latest();

        // Filtre par texte
        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('reference',   'like', "%{$q}%")
                    ->orWhere('designation', 'like', "%{$q}%");
            });
        }

        // Filtre par statut
        if ($statut = $request->input('statut')) {
            $query->where('statut', $statut);
        }

        $commandes = $query->paginate(10);

        return view('client.commande.index', compact('commandes'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        $stocks = Stock::all();

        // Bug corrigé : $stocks doit être transmis à la vue.
        return view('client.commande.create-commande', compact('stocks'));
    }

    /**
     * Enregistrement d'une nouvelle commande.
     *
     * NB : 'statut' et 'montant' ne sont plus saisis par le client.
     * - 'statut' est fixé à 'en_attente' automatiquement (voir Commande::booted()).
     * - 'montant' sera calculé plus tard via recalculerMontant()
     *   une fois les lignes (ingrédients / main d'œuvre) ajoutées.
     */
    public function store(Request $request)
    {
        // 'reference' n'est plus saisie par le client : elle est générée
        // automatiquement par le modèle (Commande::genererReference()).
        $data = $request->validate([
            'designation' => 'required|string|max:255',
            'quantite'    => 'required|integer|min:1',
            'note'        => 'nullable|string|max:500',
        ], [
            'designation.required' => 'La désignation est obligatoire.',
            'quantite.min'         => 'La quantité doit être au moins 1.',
        ]);

        $data['user_id'] = Auth::id();

        $commande = Commande::create($data);

        return redirect()->back()
            ->with('success', 'Commande ' . $commande->reference . ' créée avec succès ! Vous pouvez maintenant y ajouter les ingrédients et la main d\'œuvre.');
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Commande $commande)
    {
        // Vérifier que la commande appartient à l'utilisateur connecté
        abort_if($commande->user_id !== Auth::id(), 403);

        return view('client.commande.edit-commande', compact('commande'));
    }

    public function show(Commande $commande)
    {
        abort_if($commande->user_id !== Auth::id(), 403);

        $stocks = Stock::all(); // Pour le select des ingrédients

        return view('client.commande.show-commande', compact('commande', 'stocks'));
    }

    /**
     * Mise à jour d'une commande.
     *
     * Le client ne peut modifier ni le statut ni le montant ici.
     */
    public function update(Request $request, Commande $commande)
    {
        abort_if($commande->user_id !== Auth::id(), 403);

        // 'reference' n'est jamais modifiable après création.
        $data = $request->validate([
            'designation' => 'required|string|max:255',
            'quantite'    => 'required|integer|min:1',
            'note'        => 'nullable|string|max:500',
        ], [
            'designation.required' => 'La désignation est obligatoire.',
            'quantite.min'         => 'La quantité doit être au moins 1.',
        ]);

        $commande->update($data);

        return redirect()
            ->route('commandes.index')
            ->with('success', 'Commande ' . $commande->reference . ' modifiée avec succès !');
    }

    /**
     * Suppression d'une commande.
     */
    public function destroy(Commande $commande)
    {
        abort_if($commande->user_id !== Auth::id(), 403);

        $ref = $commande->reference;
        $commande->delete();

        return redirect()
            ->back()
            ->with('success', 'Commande ' . $ref . ' supprimée.');
    }
}
