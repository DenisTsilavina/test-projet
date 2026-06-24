<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Tsena Vohitsoa ') }} - Espace Client</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">

<div class="min-h-screen">
    {{-- Barre de Navigation Globale --}}
    <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                {{-- Partie Gauche : Logo & Liens --}}
                <div class="flex space-x-8">
                    <div class="shrink-0 flex items-center font-bold text-xl text-indigo-600 tracking-wider">
                        <i class="fa-solid fa-cubes-stacked mr-2"></i>VOHITSOA TSENA
                    </div>
                </div>

                {{-- Partie Droite : Panier & Menu "Autre" --}}
                <div class="flex items-center space-x-6">
                    <div class="hidden sm:-my-px sm:flex sm:space-x-6">
                        <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-indigo-500 text-sm font-medium leading-5 text-gray-900 focus:outline-none transition">
                            <i class="fa-solid fa-house mr-2 text-indigo-500"></i> Accueil
                        </a>
                        <a href="{{ route('commande.create') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none transition">
                            <i class="fa-solid fa-box mr-2 text-gray-400"></i> Commandes
                        </a>
                    </div>

                    <a href="#" class="relative p-2 text-gray-400 hover:text-gray-500 transition">
                        <i class="fa-solid fa-cart-shopping text-xl"></i>
                        <span class="absolute top-1 right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                                3
                            </span>
                    </a>

                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                            <i class="fa-solid fa-circle-user text-xl mr-2 text-gray-400"></i>
                            <span>Autre</span>
                            <i class="fa-solid fa-chevron-down ml-2 text-xs transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                             style="display: none;">

                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                <i class="fa-solid fa-user mr-2 text-gray-400 w-4"></i> Mon Profil
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                <i class="fa-solid fa-gear mr-2 text-gray-400 w-4"></i> Paramètres
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                <i class="fa-solid fa-question-circle mr-2 text-gray-400 w-4"></i> Aide
                            </a>

                            <div class="border-t border-gray-100 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium transition">
                                    <i class="fa-solid fa-right-from-bracket mr-2 text-red-500 w-4"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </nav>

    {{-- En-tête de la Page (Dynamique) --}}
    @if (isset($header))
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    {{-- Contenu de la Page (Dynamique) --}}
    <main>
        @yield('content')
    </main>
</div>

</body>
</html>
