<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900">

<div class="flex min-h-screen">

    <aside class="fixed inset-y-0 left-0 z-20 flex flex-col justify-between w-64 border-r bg-slate-900 border-slate-800 text-slate-300 md:sticky md:top-0 h-screen">
        <div>
            <div class="flex items-center justify-between h-16 px-6 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="font-bold tracking-wide text-white text-md">{{ config('app.name', 'Laravel') }}</span>
                    <span class="px-2 py-0.5 text-xs font-semibold tracking-wider text-amber-400 uppercase rounded bg-amber-400/10 border border-amber-400/20">Admin</span>
                </div>
            </div>

            <nav class="px-4 py-6 space-y-7">
                <div class="space-y-1">
                    <a href="{{ route('admin.vente.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.vente.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="text-lg ti ti-layout-dashboard {{ request()->routeIs('admin.vente.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        Tableau de bord
                    </a>
                </div>

                <div class="space-y-1">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider uppercase text-slate-500">Ventes & Stocks</p>

                    <a href="{{ route('admin.vente.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.vente.index') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i class="text-lg ti ti-receipt {{ request()->routeIs('admin.vente.index') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        Ventes
                    </a>

                    <div class="relative">
                        <button type="button" id="stock-menu-btn"
                                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('stock.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
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

                            {{-- CORRECTION : Seul un administrateur (ADMINS) ou un super administrateur (SUPER_ADMIN) peut voir le lien de création --}}
                            @if(auth()->check() && (auth()->user()->roleService()->role() === \App\Enums\UserRole::ADMINS || auth()->user()->roleService()->role() === \App\Enums\UserRole::SUPER_ADMIN))
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

                {{-- Sécurité : Seul le rôle SUPER_ADMIN a accès au menu d'administration globale --}}
                @if(auth()->check() && auth()->user()->roleService()->role() === \App\Enums\UserRole::SUPER_ADMIN)
                    <div class="space-y-1">
                        <p class="px-3 mb-2 text-xs font-semibold tracking-wider uppercase text-slate-500">Super Admin</p>
                        <a href="{{ route('admin.super.dashboard') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.super.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i class="text-lg ti ti-shield {{ request()->routeIs('admin.super.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                            Gestion admins
                        </a>
                    </div>
                @endif
            </nav>
        </div>

        @if(auth()->check())
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center font-bold text-white uppercase rounded-full w-9 h-9 bg-gradient-to-tr from-indigo-500 to-purple-500 shrink-0">
                        {{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1 truncate">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->roleService()->role()->label() }}</p>
                    </div>

                    {{-- Bouton déconnexion --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                title="Se déconnecter"
                                class="flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-colors duration-200">
                            <i class="text-base ti ti-logout"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </aside>

    <div class="flex flex-col flex-1 min-w-0">



        <main class="flex-1 p-6 md:p-8 max-w-[1600px] w-full mx-auto">

            @if(session('success'))
                <div class="flex items-center gap-3 p-4 mb-6 border rounded-xl bg-emerald-50 border-emerald-200 text-emerald-800 shadow-sm animate-fade-in">
                    <i class="text-lg ti ti-check-circle text-emerald-600"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center gap-3 p-4 mb-6 border rounded-xl bg-rose-50 border-rose-200 text-rose-800 shadow-sm animate-fade-in">
                    <i class="text-lg ti ti-alert-circle text-rose-600"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white border rounded-2xl border-slate-200 shadow-sm p-6 min-h-[calc(100vh-12rem)]">
                @yield('content')
            </div>
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('stock-menu-btn');
        const items = document.getElementById('stock-menu-items');
        const arrow = document.getElementById('stock-menu-arrow');

        if(btn && items && arrow) {
            btn.addEventListener('click', function () {
                items.classList.toggle('hidden');
                arrow.classList.toggle('rotate-180');
            });
        }
    });
</script>
</body>
</html>
