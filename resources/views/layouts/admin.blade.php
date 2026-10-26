<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3/dist/tabler-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-4px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in .25s ease-out; }
    </style>

    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-slate-900">

@php
    $authUser     = auth()->user();
    $currentRole  = $authUser?->roleService()->role();
    $isSuperAdmin = $currentRole === \App\Enums\UserRole::SUPER_ADMIN;
    $isAdmin      = $isSuperAdmin || $currentRole === \App\Enums\UserRole::ADMINS;
@endphp

<div class="flex min-h-screen">

    {{-- Overlay mobile --}}
    <div id="sidebar-overlay" class="fixed inset-0 z-20 hidden bg-slate-900/50 md:hidden"></div>

    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-30 flex flex-col justify-between w-64 h-screen overflow-y-auto border-r bg-slate-900 border-slate-800 text-slate-300 transition-transform duration-200 -translate-x-full md:translate-x-0 md:sticky md:top-0 md:shrink-0">
        <div>
            <div class="flex items-center justify-between h-16 px-6 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="font-bold tracking-wide text-white text-md">{{ config('app.name', 'Laravel') }}</span>
                    <span class="px-2 py-0.5 text-xs font-semibold tracking-wider text-amber-400 uppercase rounded bg-amber-400/10 border border-amber-400/20">Admin</span>
                </div>
                <button type="button" id="sidebar-close" class="md:hidden text-slate-400 hover:text-white" aria-label="Fermer le menu">
                    <i class="text-xl ti ti-x"></i>
                </button>
            </div>

            <nav class="px-4 py-6 space-y-7">
                <div class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="text-lg ti ti-layout-dashboard {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        Tableau de bord
                    </a>
                </div>

                <div class="space-y-1">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider uppercase text-slate-500">Ventes &amp; Stocks</p>

                    <a href="{{ route('admin.vente.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.vente.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="text-lg ti ti-receipt {{ request()->routeIs('admin.vente.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        Ventes
                    </a>

                    <div class="relative">
                        <button type="button" id="stock-menu-btn"
                                aria-expanded="{{ request()->routeIs('stock.*') ? 'true' : 'false' }}"
                                class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('stock.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <span class="flex items-center gap-3">
                                <i class="text-lg ti ti-package {{ request()->routeIs('stock.*') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span>Stocks</span>
                            </span>
                            <i id="stock-menu-arrow" class="transition-transform duration-200 ti ti-chevron-down {{ request()->routeIs('stock.*') ? 'rotate-180' : '' }}"></i>
                        </button>

                        <div id="stock-menu-items" class="mt-1 space-y-1 pl-9 {{ request()->routeIs('stock.*') ? '' : 'hidden' }}">
                            <a href="{{ route('stock.index') }}"
                               class="block px-3 py-2 text-xs font-medium rounded-md transition-colors {{ request()->routeIs('stock.index') ? 'text-white bg-indigo-600/50' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                                Liste des stocks
                            </a>

                            {{-- Seuls ADMINS et SUPER_ADMIN peuvent créer un stock --}}
                            @if($isAdmin)
                                <a href="{{ route('stock.create') }}"
                                   class="block px-3 py-2 text-xs font-medium rounded-md transition-colors {{ request()->routeIs('stock.create') ? 'text-white bg-indigo-600/50' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                                    Créer un stock
                                </a>
                            @endif

                            <a href="{{ route('stock.inventaire') }}"
                               class="block px-3 py-2 text-xs font-medium rounded-md transition-colors {{ request()->routeIs('stock.inventaire') ? 'text-white bg-indigo-600/50' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                                Inventaire
                            </a>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider uppercase text-slate-500">Catalogue</p>

                    <div class="relative">
                        <button type="button" id="produit-menu-btn"
                                aria-expanded="{{ request()->routeIs('produit.*') ? 'true' : 'false' }}"
                                class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('produit.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <span class="flex items-center gap-3">
                                <i class="text-lg ti ti-tag {{ request()->routeIs('produit.*') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                                <span>Produits</span>
                            </span>
                            <i id="produit-menu-arrow" class="transition-transform duration-200 ti ti-chevron-down {{ request()->routeIs('produit.*') ? 'rotate-180' : '' }}"></i>
                        </button>

                        <div id="produit-menu-items" class="mt-1 space-y-1 pl-9 {{ request()->routeIs('produit.*') ? '' : 'hidden' }}">
                            <a href="{{ route('produit.index') }}"
                               class="block px-3 py-2 text-xs font-medium rounded-md transition-colors {{ request()->routeIs('produit.index') ? 'text-white bg-indigo-600/50' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                                Liste des produits
                            </a>

                            <a href="{{ route('produit.create') }}"
                               class="block px-3 py-2 text-xs font-medium rounded-md transition-colors {{ request()->routeIs('produit.create') ? 'text-white bg-indigo-600/50' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                                Ajouter un produit
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Seul le SUPER_ADMIN accède à l'administration globale --}}
                @if($isSuperAdmin)
                    <div class="space-y-1">
                        <p class="px-3 mb-2 text-xs font-semibold tracking-wider uppercase text-slate-500">Super Admin</p>
                        <a href="{{ route('admin.users.list') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="text-lg ti ti-shield {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                            Gestion admins
                        </a>
                    </div>
                @endif
            </nav>
        </div>

        @if($authUser)
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center font-bold text-white uppercase rounded-full w-9 h-9 bg-gradient-to-tr from-indigo-500 to-purple-500 shrink-0">
                        {{ Str::upper(Str::substr($authUser->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ $authUser->name }}</p>
                        <p class="text-xs truncate text-slate-400">{{ $currentRole?->label() }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                title="Se déconnecter"
                                class="flex items-center justify-center w-8 h-8 transition-colors duration-200 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800">
                            <i class="text-base ti ti-logout"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </aside>

    <div class="flex flex-col flex-1 min-w-0">

        {{-- Barre supérieure mobile --}}
        <header class="sticky top-0 z-10 flex items-center h-14 gap-3 px-4 border-b md:hidden bg-white border-slate-200">
            <button type="button" id="sidebar-open" class="text-slate-600 hover:text-slate-900" aria-label="Ouvrir le menu">
                <i class="text-2xl ti ti-menu-2"></i>
            </button>
            <span class="text-sm font-bold text-slate-900">{{ config('app.name', 'Laravel') }}</span>
        </header>

        <main class="flex-1 w-full p-4 mx-auto md:p-8 max-w-[1600px]">

            @if(session('success'))
                <div class="flex items-center gap-3 p-4 mb-6 border shadow-sm rounded-xl bg-emerald-50 border-emerald-200 text-emerald-800 animate-fade-in">
                    <i class="text-lg ti ti-circle-check text-emerald-600"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center gap-3 p-4 mb-6 border shadow-sm rounded-xl bg-rose-50 border-rose-200 text-rose-800 animate-fade-in">
                    <i class="text-lg ti ti-alert-circle text-rose-600"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Pas de carte englobante : chaque vue gère son propre conteneur --}}
            @yield('content')
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sous-menus déroulants (Stocks, Produits)
        ['stock', 'produit'].forEach(function (name) {
            const btn = document.getElementById(name + '-menu-btn');
            const items = document.getElementById(name + '-menu-items');
            const arrow = document.getElementById(name + '-menu-arrow');

            if (btn && items && arrow) {
                btn.addEventListener('click', function () {
                    const isHidden = items.classList.toggle('hidden');
                    arrow.classList.toggle('rotate-180', !isHidden);
                    btn.setAttribute('aria-expanded', String(!isHidden));
                });
            }
        });

        // Sidebar mobile
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }

        document.getElementById('sidebar-open')?.addEventListener('click', openSidebar);
        document.getElementById('sidebar-close')?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);
    });
</script>

@stack('scripts')
</body>
</html>
