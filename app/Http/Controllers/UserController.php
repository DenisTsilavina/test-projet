<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return $user->role === UserRole::CLIENT
            ? redirect()->route('client.dashboard')
            : redirect()->route('admin.dashboard');
    }

    /**
     * Liste de tous les users (Super Admin)
     */
    public function list()
    {
        // CHANGEMENT : renvoyait 'admin.vente.dashboard' par erreur (copier-coller),
        // qui attend totalRevenue/totalVente/venteRecentes — aucun n'était fourni.
        // Corrigé vers une vraie vue de liste d'utilisateurs.
        $users = User::latest()->paginate(10);
        return view('admin.users.list', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Formulaire de création d'un membre du personnel (admin/vendeur).
     * Séparé de create() : ici le rôle n'est PAS figé, mais limité à
     * SUPER_ADMIN/ADMINS — jamais CLIENT.
     */
    public function createStaff()
    {
        $rolesStaff = array_filter(
            UserRole::cases(),
            fn ($role) => $role !== UserRole::CLIENT
        );

        return view('admin.users.create-staff', compact('rolesStaff'));
    }

    public function storeStaff(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|integer|in:' . UserRole::SUPER_ADMIN->value . ',' . UserRole::ADMINS->value,
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        return redirect()->route('admin.users.list')
            ->with('success', 'Membre du personnel créé avec succès.');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|integer',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users.list')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|integer',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
            ...($request->filled('password')
                ? ['password' => Hash::make($request->password)]
                : []),
        ]);

        return redirect()->route('admin.users.list')
            ->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.users.list')
            ->with('success', 'Utilisateur supprimé.');
    }

    public function clientDashboard()
    {
        // CHANGEMENT : redirigeait vers 'client.dashboard' (lui-même !),
        // ce qui provoquait une boucle / un contenu incohérent avec l'URL
        // pour tout utilisateur non-CLIENT (comme un Super Admin) qui
        // visite cette URL. Corrigé vers 'admin.dashboard'.
        if (Auth::user()?->role !== UserRole::CLIENT) {
            return redirect()->route('admin.dashboard');
        }

        return view('layouts.client');
    }

    /**
     * Dashboard admin (Blade)
     */
    public function adminDashboard()
    {
        // CHANGEMENT : totalVentes décommenté et réellement calculé,
        // et ajouté au compact() — la vue en a besoin depuis la correction
        // du commentaire Blade cassé qui le masquait.
        $totalUsers  = User::count();
        $totalVentes = Vente::count();

        return view('admin.dashboard', compact('totalUsers', 'totalVentes'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // CHANGEMENT : redirigeait vers /login. Maintenant vers la page
        // d'accueil publique (vue 'welcome', route '/').
        return redirect('/');
    }

    public function homeData(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'produits' => \App\Models\Stock::latest()->take(8)->get(),
        ]);
    }
}
