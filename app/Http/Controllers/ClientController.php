<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Seuls les admins (ADMINS / SUPER_ADMIN) gèrent les comptes clients.
        $this->middleware(function ($request, $next) {
            $userRole = auth()->user()->roleService()->role();

            if ($userRole !== UserRole::ADMINS && $userRole !== UserRole::SUPER_ADMIN) {
                abort(403, "Action non autorisée. Vous devez être administrateur.");
            }

            return $next($request);
        })->except(['index', 'show']);
    }

    public function index()
    {
        // On ne remonte que les users avec role = CLIENT.
        // CHANGEMENT : 'achats' (client_id) et non 'ventes' (user_id) :
        // ventes() = ventes faites par un vendeur, achats() = ventes reçues par un client.
        $clients = User::where('role', UserRole::CLIENT)
            ->withCount('achats')
            ->get();

        return view('client.index', compact('clients'));
    }

    public function create()
    {
        return view('client.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'telephone' => 'nullable|string|max:30',
            'adresse'   => 'nullable|string',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email'  => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'adresse'   => $validated['adresse'] ?? null,
            'password'  => $validated['password'], // cast 'hashed' sur le modèle, pas besoin de Hash::make
            'role' => UserRole::CLIENT,
        ]);

        return redirect()->route('client.index')
            ->with('success', 'Client créé avec succès.');
    }

    public function show(User $client)
    {
        abort_unless($client->role === UserRole::CLIENT, 404);

        $client->load('achats');

        return view('client.show', compact('client'));
    }

    public function edit(User $client)
    {
        abort_unless($client->role === UserRole::CLIENT, 404);

        return view('client.edit', compact('client'));
    }

    public function update(Request $request, User $client)
    {
        abort_unless($client->role === UserRole::CLIENT, 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $client->id,
            'telephone' => 'nullable|string|max:30',
            'adresse' => 'nullable|string',
        ]);

        $client->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'adresse' => $validated['adresse'] ?? null,
        ]);

        return redirect()->route('client.index')
            ->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(User $client)
    {
        abort_unless($client->role === UserRole::CLIENT, 404);

        if ($client->achats()->exists()) {
            return redirect()->route('client.index')
                ->with('error', 'Impossible de supprimer ce client : des achats lui sont associés.');
        }

        $client->delete();

        return redirect()->route('client.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}
