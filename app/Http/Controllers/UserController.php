<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Vente;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Redirection principale selon le rôle
     */
    public function index()
    {
        $user = Auth::user();

        return $user->role === UserRole::CLIENT
            ? redirect()->route('client.dashboard')
            : redirect()->route('admin.dashboard');
    }

    /**
     * Vue hôte Blade pour charger l'application SPA Vue.js client
     */
    public function clientDashboard()
    {
        if (Auth::user()?->role !== UserRole::CLIENT) {
            return redirect()->route('admin.dashboard');
        }

        return view('layouts.client');
    }

    /**
     * Endpoint API (session web) appelé par Axios dans Vue.js
     */
    public function clientData(Request $request): JsonResponse
    {
        $user = $request->user();

        $produits = Produit::where('statut', 'actif')
            ->with('description') // charger la relation description si nécessaire
            ->latest()
            ->get()
            ->map(function ($produit) {
                return [
                    'id' => $produit->id,
                    'nom' => $produit->nom,
                    'prix_vente'  => $produit->prix_vente,
                    'stock' => $produit->stock,
                    'description' => $produit->description?->description ?? 'Aucune description',
                    // Utilise l'image de la description ou du produit
                    'image_url' => $produit->description?->image_url ?? asset('images/default-product.png'),
                ];
            });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'label' => $user->role->label(),
            ],
            'produits' => $produits,
        ]);
    }

    public function homeData(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
            'produits' => Produit::where('statut', 'actif')->latest()->take(8)->get(),
        ]);
    }

    public function list()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.list', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|integer|in:' . UserRole::SUPER_ADMIN->value . ',' . UserRole::ADMINS->value,
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
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
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|integer',
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

    public function adminDashboard()
    {
        $totalUsers  = User::count();
        $totalVentes = Vente::count();

        return view('admin.dashboard', compact('totalUsers', 'totalVentes'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
