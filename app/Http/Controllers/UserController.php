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
        $users = User::latest()->paginate(10);
        return view('admin.users.list', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Force systématiquement le rôle CLIENT (0)
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => UserRole::CLIENT,
        ]);

        return redirect()->route('admin.users.login')
            ->with('status', 'Compte créé avec succès. Vous pouvez maintenant vous connecter.');
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

    /**
     * Dashboard Client
     * Si l'utilisateur n'est pas CLIENT (ex: Super Admin), on le redirige vers l'admin dashboard
     */

    public function clientDashboard()
    {
        if (Auth::user()?->role !== UserRole::CLIENT) {
            return redirect()->route('admin.dashboard');
        }
        return view('layouts.client');
    }

    /**
     * Dashboard admin
     */
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

        return redirect('/login');
    }

    public function homeData(Request $request)
    {
        return response()->json([
            'user'     => $request->user(),
            'produits' => \App\Models\Stock::latest()->take(8)->get(),
        ]);
    }
}
