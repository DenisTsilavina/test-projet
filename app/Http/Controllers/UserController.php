<?php

namespace App\Http\Controllers;

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
        $user = Auth::user();

        return $user->isAdmin()
            ? redirect()->route('admin.vente.dashboard')
            : redirect()->route('client.dashboard');
    }

    /**
     * Liste de tous les users (Super Admin)
     */
    public function list()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
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
     * Dashboard client
     */
    public function clientDashboard()
    {
        $user = Auth::user();
        return view('client.dashboard', compact('user'));
    }

    /**
     * Dashboard admin
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
            'produits' => \App\Models\Stock::latest()->take(8)->get(), // Récupère les 8 derniers stocks/produits
        ]);
    }
}
