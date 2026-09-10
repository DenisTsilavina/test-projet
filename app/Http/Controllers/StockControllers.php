<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Unite;
use App\Enums\UserRole;
use Illuminate\Http\Request;

class StockControllers extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $userRole = auth()->user()->roleService()->role();

            if ($userRole !== UserRole::ADMINS && $userRole !== UserRole::SUPER_ADMIN) {
                abort(403, "Action non autorisée. Vous devez être administrateur.");
            }

            return $next($request);
        })->except(['index', 'show', 'inventaire']);
    }

    public function index()
    {
        // CHANGEMENT : 'unites' (many-to-many) -> 'unite' (belongsTo, une seule unité par stock)
        $stocks = Stock::with(['unite', 'descriptions'])->get();

        return view('stock.index', compact('stocks'));
    }

    public function create()
    {
        $unites = Unite::all();

        return view('stock.create', compact('unites'));
    }

    public function store(Request $request)
    {
        // CHANGEMENT : 'unites' (tableau id => quantite) -> 'unite_id' + 'quantite' simples,
        // puisqu'un stock n'appartient plus qu'à une seule unité.
        $validated = $request->validate([
            'name_stock' => 'required|string|max:255|unique:stocks,name_stock',
            'date_stock' => 'nullable|date',
            'unite_id'   => 'required|exists:unites,id',
            'quantite'   => 'nullable|numeric|min:0',
        ]);

        $stock = Stock::create([
            'name_stock'     => $validated['name_stock'],
            'date_stock'     => $validated['date_stock'] ?? now(),
            'responsable_id' => auth()->id(),
            'unite_id'       => $validated['unite_id'],
            'quantite'       => $validated['quantite'] ?? 0,
        ]);

        return redirect()->route('stock.index')
            ->with('success', 'Stock créé avec succès.');
    }

    public function show(Stock $stock)
    {
        $stock->load(['unite', 'descriptions']);

        return view('stock.show', compact('stock'));
    }

    public function edit(Stock $stock)
    {
        $unites = Unite::all();
        $stock->load('unite');

        return view('stock.edit', compact('stock', 'unites'));
    }

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'name_stock' => 'required|string|max:255|unique:stocks,name_stock,' . $stock->id,
            'date_stock' => 'nullable|date',
            'unite_id'   => 'required|exists:unites,id',
            'quantite'   => 'nullable|numeric|min:0',
        ]);

        $stock->update([
            'name_stock' => $validated['name_stock'],
            'date_stock' => $validated['date_stock'] ?? $stock->date_stock,
            'unite_id'   => $validated['unite_id'],
            'quantite'   => $validated['quantite'] ?? $stock->quantite,
            // Décommenter pour permettre le changement de responsable :
            // 'responsable_id' => $request->input('responsable_id', $stock->responsable_id),
        ]);

        return redirect()->route('stock.index')
            ->with('success', 'Stock mis à jour avec succès.');
    }

    public function inventaire()
    {
        $stocks = Stock::with('unite')->get();
        return view('stock.inventaire', compact('stocks'));
    }

    public function destroy(Stock $stock)
    {
        // CHANGEMENT : plus besoin de detach() (pas de pivot), la FK unite_id
        // est simplement supprimée avec le stock.
        $stock->descriptions()->delete();
        $stock->delete();

        return redirect()->route('stock.index')
            ->with('success', 'Stock supprimé avec succès.');
    }
}
