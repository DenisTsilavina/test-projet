<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Redirection post-login selon rôle
     */
    public function index()
    {
        // CHANGEMENT : isAdmin() ne couvrait que 2 des 4 rôles (ADMINS /
        // SUPER_ADMIN). Un VENDEUR tombait dans la branche "else" et était
        // envoyé vers client.dashboard — route protégée par role:0 (CLIENT
        // uniquement) — d'où le 403 "Accès refusé" pour un Vendeur qui vient
        // de se connecter. Seul CLIENT va côté client ; tous les autres
        // rôles (VENDEUR, ADMINS, SUPER_ADMIN) vont côté admin.
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
        $users = User::latest()->paginate(10);
        return view('admin.vente.dashboard', compact('users'));
    }

    /**
     * Formulaire création user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Enregistrement d'un nouveau user
     */
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

    /**
     * Afficher un user
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Formulaire modification user
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Mise à jour d'un user
     */
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

    /**
     * Suppression d'un user
     */
    public function destroy(User $user): RedirectResponse
    {
        // Empêcher de se supprimer soi-même
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.users.list')
            ->with('success', 'Utilisateur supprimé.');
    }

    /**
     * Dashboard client (SPA Vue)
     */
    public function clientDashboard()
    {
        // CHANGEMENT : même logique que index() — on compare le rôle exact
        // (CLIENT ou non) plutôt que isAdmin(), pour que ce garde-fou
        // bloque aussi un VENDEUR qui arriverait ici, pas seulement un
        // admin. Et 'admin.vente.dashboard' -> 'admin.dashboard' (ancien
        // nom de route, supprimé lors du renommage).
        if (Auth::user()?->role !== UserRole::CLIENT) {
            return redirect()->route('client.dashboard');
        }

        return view('layouts.client');
    }

    /**
     * Dashboard admin (Blade)
     */
    public function adminDashboard()
    {
        $totalUsers  = User::count();
        $totalVentes = \App\Models\Vente::count();
        return view('admin.dashboard', compact('totalUsers', 'totalVentes'));
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function homeData(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'produits' => \App\Models\Stock::latest()->take(8)->get(),
        ]);
    }
}
