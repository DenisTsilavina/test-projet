{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
    <div class="space-y-6">

        {{-- En-tête --}}
        <div class="pb-4 border-b border-slate-100">
            <h2 class="text-xl font-bold text-slate-900">
                Bonjour, {{ auth()->user()->name }} 👋
            </h2>
            <p class="text-sm text-slate-400 mt-0.5">
                Voici un aperçu de l'activité de {{ config('app.name', 'l\'application') }}.
            </p>
        </div>

        {{-- ===== CARTES STATISTIQUES ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- Utilisateurs --}}
            <div class="flex items-center gap-4 p-5 bg-white border border-slate-200 rounded-2xl shadow-sm">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-50 shrink-0">
                    <i class="text-2xl ti ti-users text-indigo-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold tracking-wider uppercase text-slate-400">Utilisateurs</p>
                    <p class="text-2xl font-bold text-slate-900 tabular-nums">{{ number_format($totalUsers, 0, ',', ' ') }}</p>
                </div>
            </div>

            {{-- Ventes --}}
            <div class="flex items-center gap-4 p-5 bg-white border border-slate-200 rounded-2xl shadow-sm">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-50 shrink-0">
                    <i class="text-2xl ti ti-receipt text-emerald-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold tracking-wider uppercase text-slate-400">Ventes enregistrées</p>
                    <p class="text-2xl font-bold text-slate-900 tabular-nums">{{ number_format($totalVentes, 0, ',', ' ') }}</p>
                </div>
            </div>

            {{-- Rôle connecté --}}
            <div class="flex items-center gap-4 p-5 bg-white border border-slate-200 rounded-2xl shadow-sm">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-amber-50 shrink-0">
                    <i class="text-2xl ti ti-shield-check text-amber-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold tracking-wider uppercase text-slate-400">Connecté en tant que</p>
                    <p class="text-lg font-bold text-slate-900 truncate">{{ auth()->user()->roleService()->role()->label() }}</p>
                </div>
            </div>
        </div>

        {{-- ===== ACTIONS RAPIDES ===== --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                <i class="text-indigo-500 ti ti-bolt"></i> Actions rapides
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                <a href="{{ route('admin.vente.create') }}"
                   class="flex items-center gap-3 p-4 border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50/40 transition-colors group">
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 group-hover:bg-indigo-100 shrink-0 transition-colors">
                        <i class="text-lg ti ti-plus text-indigo-600"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800">Nouvelle vente</p>
                        <p class="text-xs text-slate-400">Enregistrer une transaction</p>
                    </div>
                </a>

                <a href="{{ route('stock.index') }}"
                   class="flex items-center gap-3 p-4 border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50/40 transition-colors group">
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 group-hover:bg-emerald-100 shrink-0 transition-colors">
                        <i class="text-lg ti ti-package text-emerald-600"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800">Gérer les stocks</p>
                        <p class="text-xs text-slate-400">Voir descriptions & prix</p>
                    </div>
                </a>

                @if(auth()->user()->roleService()->role() === \App\Enums\UserRole::SUPER_ADMIN)
                    <a href="{{ route('admin.users.list') }}"
                       class="flex items-center gap-3 p-4 border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50/40 transition-colors group">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 group-hover:bg-amber-100 shrink-0 transition-colors">
                            <i class="text-lg ti ti-user-cog text-amber-600"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800">Gérer les utilisateurs</p>
                            <p class="text-xs text-slate-400">Rôles & accès</p>
                        </div>
                    </a>
                @endif

            </div>
        </div>

    </div>
@endsection
