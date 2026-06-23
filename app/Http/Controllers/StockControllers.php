<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Unite;
use App\Enums\UserRole; // Importation de l'Enum
use Illuminate\Http\Request;

class StockControllers extends Controller
{
    // ─── Middleware : create/store/edit/update/destroy → admin & super_admin ──

    public function __construct()
    {
        $this->middleware('auth');

        // Sécurisation stricte au niveau du contrôleur
        $this->middleware(function ($request, $next) {
            $userRole = auth()->user()->roleService()->role(); // Retourne l'instance UserRole

            // On vérifie si le rôle correspond à ADMIN ou SUPER_ADMIN de l'Enum
            if ($userRole !== UserRole::ADMINS && $userRole !== UserRole::SUPER_ADMIN) {
                abort(403, "Action non autorisée. Vous devez être administrateur.");
            }

            return $next($request);
        })->except(['index', 'show', 'inventaire']);
        // L'index, le show et l'inventaire restent accessibles aux autres (ex: Vendeur)
    }

    // ─── Liste (tous les rôles connectés) ────────────────────────────────────

    public function index()
    {
        $stocks = Stock::with(['unites', 'descriptions'])->get();

        return view('stock.index', compact('stocks'));
    }

    // ─── Création ────────────────────────────────────────────────────────────

    public function create()
    {
        $unites = Unite::all();

        return view('stock.create', compact('unites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_stock'        => 'required|string|max:255|unique:stocks,name_stock',
            'description_stock' => 'nullable|string',
            'date_stock'        => 'nullable|date',
            'unites'            => 'nullable|array',
            'unites.*'          => 'numeric|min:0',
        ]);

        $stock = Stock::create([
            'name_stock'        => $validated['name_stock'],
            'description_stock' => $validated['description_stock'] ?? null,
            'date_stock'        => $validated['date_stock'] ?? now(),
            'persn_stock'       => auth()->user()->name,
        ]);

        $this->syncUnites($stock, $request->input('unites', []));

        return redirect()->route('stock.index')
            ->with('success', 'Stock créé avec succès.');
    }

    // ─── Affichage détail ────────────────────────────────────────────────────

    public function show(Stock $stock)
    {
        $stock->load(['unites', 'descriptions']);

        return view('stock.show', compact('stock'));
    }

    // ─── Modification ────────────────────────────────────────────────────────

    public function edit(Stock $stock)
    {
        $unites = Unite::all();
        $stock->load('unites');

        return view('stock.edit', compact('stock', 'unites'));
    }

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'name_stock'        => 'required|string|max:255|unique:stocks,name_stock,' . $stock->id,
            'description_stock' => 'nullable|string',
            'date_stock'        => 'nullable|date',
            'unites'            => 'nullable|array',
            'unites.*'          => 'numeric|min:0',
        ]);

        $stock->update([
            'name_stock'        => $validated['name_stock'],
            'description_stock' => $validated['description_stock'] ?? null,
            'date_stock'        => $validated['date_stock'] ?? $stock->date_stock,
        ]);

        $this->syncUnites($stock, $request->input('unites', []));

        return redirect()->route('stock.index')
            ->with('success', 'Stock mis à jour avec succès.');
    }

    // ─── Inventaire ──────────────────────────────────────────────────────────

    public function inventaire()
    {
        $stocks = Stock::with('unites')->get();
        return view('stock.inventaire', compact('stocks'));
    }

    // ─── Suppression ─────────────────────────────────────────────────────────

    public function destroy(Stock $stock)
    {
        $stock->unites()->detach();
        $stock->descriptions()->delete();
        $stock->delete();

        return redirect()->route('stock.index')
            ->with('success', 'Stock supprimé avec succès.');
    }

    // ─── Helpers privés ──────────────────────────────────────────────────────

    private function syncUnites(Stock $stock, array $unites): void
    {
        $syncData = [];

        foreach ($unites as $uniteId => $quantite) {
            if ($quantite > 0) {
                $syncData[(int) $uniteId] = ['quantite' => $quantite];
            }
        }

        $stock->unites()->sync($syncData);
    }
}
