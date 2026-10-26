@extends('layouts.admin')

@section('title', 'Produits')

@section('content')
    @php
        $hasFilters = request()->filled('q') || request()->filled('type') || request()->filled('statut');
        $isPaginated = method_exists($produits, 'hasPages');
        $total = $isPaginated ? $produits->total() : $produits->count();
    @endphp

    <div class="space-y-6">

        {{-- En-tête --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Produits</h1>
                <p class="text-sm text-slate-500">
                    {{ number_format($total, 0, ',', ' ') }} produit{{ $total > 1 ? 's' : '' }}
                    @if ($hasFilters) correspondant à votre recherche @endif
                </p>
            </div>
            <a href="{{ route('produit.create') }}"
               class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                <i class="ti ti-plus text-base" aria-hidden="true"></i>
                Nouveau produit
            </a>
        </div>

        {{-- Filtres --}}
        <form method="GET" action="{{ route('produit.index') }}" role="search"
              class="grid grid-cols-1 gap-3 p-4 bg-white border shadow-sm rounded-2xl border-slate-200 sm:grid-cols-2 lg:grid-cols-[1fr_200px_160px_auto]">
            <div class="relative">
                <label for="filter-q" class="sr-only">Rechercher un produit</label>
                <i class="absolute -translate-y-1/2 ti ti-search left-3 top-1/2 text-slate-400" aria-hidden="true"></i>
                <input type="search" id="filter-q" name="q" value="{{ request('q') }}" placeholder="Rechercher un produit..."
                       class="w-full py-2 pl-9 pr-3 text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="filter-type" class="sr-only">Type</label>
                <select id="filter-type" name="type" class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tous les types</option>
                    <option value="stock" @selected(request('type') === 'stock')>Stock direct</option>
                    <option value="fini" @selected(request('type') === 'fini')>Produit fini</option>
                </select>
            </div>

            <div>
                <label for="filter-statut" class="sr-only">Statut</label>
                <select id="filter-statut" name="statut" class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tous les statuts</option>
                    <option value="actif" @selected(request('statut') === 'actif')>Actif</option>
                    <option value="inactif" @selected(request('statut') === 'inactif')>Inactif</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white rounded-lg bg-slate-900 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 transition">
                    Filtrer
                </button>
                @if ($hasFilters)
                    <a href="{{ route('produit.index') }}"
                       class="px-4 py-2 text-sm font-semibold rounded-lg text-slate-600 hover:bg-slate-100 transition">
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>

        {{-- Tableau --}}
        <div class="overflow-hidden bg-white border shadow-sm rounded-2xl border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-slate-200">
                    <caption class="sr-only">Liste des produits</caption>
                    <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold tracking-wider text-left uppercase text-slate-500">
                        <th scope="col" class="px-5 py-3">Nom</th>
                        <th scope="col" class="px-5 py-3">Type</th>
                        <th scope="col" class="px-5 py-3">Détail</th>
                        <th scope="col" class="px-5 py-3 text-right">Prix de vente</th>
                        <th scope="col" class="px-5 py-3">Statut</th>
                        <th scope="col" class="px-5 py-3 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse ($produits as $produit)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                {{ $produit->nom }}
                            </td>

                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if ($produit->type === 'stock')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-600/20">
                                        <i class="ti ti-package" aria-hidden="true"></i> Stock direct
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-600/20">
                                        <i class="ti ti-chef-hat" aria-hidden="true"></i> Produit fini
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-3.5 text-slate-500">
                                @if ($produit->type === 'stock')
                                    @php
                                        $quantite = $produit->produitStock?->quantite_disponible ?? 0;
                                    @endphp
                                    <span class="text-slate-700">{{ $produit->produitStock?->stock?->name_stock ?? '—' }}</span>
                                    <span class="text-xs">· {{ rtrim(rtrim(number_format($quantite, 2, ',', ' '), '0'), ',') }} dispo</span>
                                @else
                                    @php
                                        $nbIngredients = $produit->produitFini?->compositions?->count() ?? 0;
                                    @endphp
                                    {{ $nbIngredients }} ingrédient{{ $nbIngredients > 1 ? 's' : '' }}
                                @endif
                            </td>

                            <td class="px-5 py-3.5 text-right font-medium tabular-nums text-slate-900 whitespace-nowrap">
                                {{ number_format($produit->prix_vente, 0, ',', ' ') }} Ar
                            </td>

                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if ($produit->statut === 'actif')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span> Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400" aria-hidden="true"></span> Inactif
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('produit.show', $produit) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-indigo-600 rounded-lg hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                                       aria-label="Voir le produit {{ $produit->nom }}">
                                        <i class="ti ti-eye" aria-hidden="true"></i> Voir
                                    </a>
                                    <a href="{{ route('produit.edit', $produit) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-400 transition"
                                       aria-label="Modifier le produit {{ $produit->nom }}">
                                        <i class="ti ti-pencil" aria-hidden="true"></i> Modifier
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center">
                                <i class="text-4xl ti ti-tag-off text-slate-300" aria-hidden="true"></i>
                                <p class="mt-2 text-sm font-semibold text-slate-700">
                                    {{ $hasFilters ? 'Aucun produit ne correspond à votre recherche.' : 'Aucun produit pour le moment.' }}
                                </p>
                                @unless ($hasFilters)
                                    <a href="{{ route('produit.create') }}"
                                       class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-500 transition">
                                        <i class="ti ti-plus" aria-hidden="true"></i> Créer le premier produit
                                    </a>
                                @endunless
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if ($isPaginated && $produits->hasPages())
                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50">
                    {{ $produits->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
