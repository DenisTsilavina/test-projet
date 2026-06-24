@extends('layouts.client')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mon Espace Client & Tableau de Bord
        </h2>
        <span class="text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-medium self-start md:self-auto">
            Mode : Mini-Hôtel & Logistique
        </span>
    </div>
@endsection

@section('content')
    <div class="py-12 text-gray-700">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. CARTE DE BIENVENUE --}}
            <div class="bg-white shadow rounded-xl p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">
                            Bonjour, {{ $user->name }} 👋
                        </h3>
                        <p class="text-gray-500 mt-1">Ravi de vous revoir. Voici l'état actuel de vos stocks, de l'hôtel et des transports.</p>
                    </div>
                    <div class="hidden sm:block text-right">
                        <p class="text-sm text-gray-400">Date du jour</p>
                        <p class="font-medium text-gray-600">{{ now()->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- 2. SECTION STOCKS & ARTICLES (Par Sous-Catégories) --}}
            <div class="bg-white shadow rounded-xl p-6">
                <div class="border-b pb-4 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        📦 État des Stocks & Articles
                    </h3>
                    <p class="text-sm text-gray-500">Vue d'ensemble de vos produits disponibles par sous-catégorie.</p>
                </div>

                {{-- Boucle sur les Stocks / Catégories --}}
                @forelse ($stocks ?? [] as $stock) {{-- À adapter selon votre variable --}}
                <div class="mb-8 last:mb-0 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div class="mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider bg-gray-200 text-gray-700 px-2.5 py-1 rounded">
                                {{ $stock->sous_categorie ?? 'Sous-catégorie principale' }}
                            </span>
                        <p class="text-xs text-gray-400 mt-1">{{ $stock->description ?? 'Aucune description fournie pour cette catégorie.' }}</p>
                    </div>

                    {{-- Grille des Articles --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse ($stock->articles ?? [] as $article)
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h5 class="font-semibold text-gray-800">{{ $article->nom }}</h5>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($article->description, 60) }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $article->quantite > 5 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            Qté: {{ $article->quantite }}
                                        </span>
                                </div>
                                <div class="mt-4 pt-3 border-t border-gray-50 flex justify-between items-center text-sm">
                                    <span class="text-gray-400">Prix unitaire :</span>
                                    <span class="font-bold text-gray-900">{{ number_format($article->prix, 2, ',', ' ') }} Ar</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 italic col-span-full">Aucun article enregistré dans cette sous-catégorie.</p>
                        @endforelse
                    </div>
                </div>
                @empty
                    {{-- Exemple statique de secours pour le visuel --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Exemple Sous-catégorie 1 -->
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide">🍞 Boulangerie / Gâteaux</h4>
                            <p class="text-xs text-gray-500 mb-3">Produits frais et ingrédients pour la pâtisserie de l'hôtel.</p>
                            <div class="space-y-2">
                                <div class="bg-white p-3 rounded shadow-sm flex justify-between items-center">
                                    <span class="text-sm font-medium">Croissants nature</span>
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">15 dispo</span>
                                </div>
                                <div class="bg-white p-3 rounded shadow-sm flex justify-between items-center">
                                    <span class="text-sm font-medium">Gâteaux au chocolat (parts)</span>
                                    <span class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded">4 restantes</span>
                                </div>
                            </div>
                        </div>
                        <!-- Exemple Sous-catégorie 2 -->
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide">🥤 Boissons & Cafétéria</h4>
                            <p class="text-xs text-gray-500 mb-3">Boissons stockées pour le comptoir et le room-service.</p>
                            <div class="space-y-2">
                                <div class="bg-white p-3 rounded shadow-sm flex justify-between items-center">
                                    <span class="text-sm font-medium">Jus naturel de Madagascar (1L)</span>
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">24 dispo</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- 3. GRILLE DEUX COLONNES : MINI-HÔTEL & VLOG TRANSPORT --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- MODULE : MINI-HÔTEL --}}
                <div class="bg-white shadow rounded-xl p-6 flex flex-col justify-between">
                    <div>
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                🏨 Gestion Mini-Hôtel
                            </h3>
                            <p class="text-sm text-gray-500">Statut des chambres et services associés.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="p-4 bg-blue-50 rounded-lg text-center">
                                <span class="block text-2xl font-bold text-blue-600">3 / 5</span>
                                <span class="text-xs text-gray-500 font-medium uppercase">Chambres Occupées</span>
                            </div>
                            <div class="p-4 bg-green-50 rounded-lg text-center">
                                <span class="block text-2xl font-bold text-green-600">2</span>
                                <span class="text-xs text-gray-500 font-medium uppercase">Chambres Libres</span>
                            </div>
                        </div>

                        <ul class="divide-y divide-gray-100 text-sm">
                            <li class="py-2.5 flex justify-between items-center">
                                <span class="font-medium">Chambre 101 (Double)</span>
                                <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded">Occupée</span>
                            </li>
                            <li class="py-2.5 flex justify-between items-center">
                                <span class="font-medium">Chambre 102 (Suicte)</span>
                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded">Disponible</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 pt-4 border-t">
                        <a href="#" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 gap-1">
                            Gérer les réservations &rarr;
                        </a>
                    </div>
                </div>

                {{-- MODULE : VLOG DE TRANSPORT & EXCURSIONS --}}
                <div class="bg-white shadow rounded-xl p-6 flex flex-col justify-between">
                    <div>
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                🚐 Vlog de Transport & Navettes
                            </h3>
                            <p class="text-sm text-gray-500">Suivi des trajets clients, transferts aéroport et excursions.</p>
                        </div>

                        <div class="space-y-3">
                            <div class="p-3 bg-amber-50 rounded-lg border border-amber-100 flex gap-3">
                                <div class="text-2xl">📍</div>
                                <div class="text-sm">
                                    <p class="font-semibold text-gray-800">Transfert Aéroport en cours</p>
                                    <p class="text-xs text-gray-500">Véhicule : Navette Interne Van #1</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-2xl font-bold text-amber-800 bg-amber-100 rounded text-[10px] uppercase">En route</span>
                                </div>
                            </div>

                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100 flex gap-3">
                                <div class="text-2xl">🗺️</div>
                                <div class="text-sm">
                                    <p class="font-semibold text-gray-700">Circuit Touristique de demain</p>
                                    <p class="text-xs text-gray-500">Départ prévu à 08:00 — 4 Personnes inscrites.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t">
                        <a href="#" class="inline-flex items-center text-sm font-semibold text-amber-600 hover:text-amber-800 gap-1">
                            Voir le journal de bord des chauffeurs &rarr;
                        </a>
                    </div>
                </div>

            </div>


        </div>
    </div>
@endsection
