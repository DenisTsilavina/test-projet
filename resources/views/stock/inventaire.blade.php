{{-- resources/views/stock/inventaire.blade.php --}}
@extends('layouts.admin')

@section('title', 'Inventaire')
@section('page-title', 'Inventaire')

@section('content')
    <div class="space-y-6">

        {{-- ===== EN-TÊTE ===== --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Inventaire général</h2>
                <p class="text-sm text-slate-400 mt-0.5">Récapitulatif consolidé de tous les stocks, descriptions et quantités.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('stock.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
                    <i class="ti ti-plus"></i> Nouveau stock
                </a>
                <a href="#"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-sm rounded-xl shadow-sm transition-colors">
                    <i class="ti ti-file-spreadsheet text-emerald-600"></i> Exporter
                </a>
            </div>
        </div>

        {{-- ===== CARTES STATISTIQUES ===== --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                    <i class="ti ti-building-warehouse text-indigo-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium">Stocks</p>
                    <p class="text-xl font-bold text-slate-900">{{ $stocks->count() }}</p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                    <i class="ti ti-file-description text-emerald-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium">Descriptions</p>
                    <p class="text-xl font-bold text-slate-900">{{ $stocks->sum(fn($s) => $s->descriptions->count()) }}</p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                    <i class="ti ti-tag text-amber-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium">Sous-catégories</p>
                    <p class="text-xl font-bold text-slate-900">
                        {{ $stocks->sum(fn($s) => $s->descriptions->sum(fn($d) => $d->sousCategories->count())) }}
                    </p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-violet-50 flex items-center justify-center shrink-0">
                    <i class="ti ti-users text-violet-500 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-medium">Responsables</p>
                    <p class="text-xl font-bold text-slate-900">{{ $stocks->pluck('persn_stock')->unique()->count() }}</p>
                </div>
            </div>
        </div>

        {{-- ===== RECHERCHE ===== --}}
        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
            <div class="relative flex-1 max-w-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="ti ti-search text-sm"></i>
                </div>
                <input id="search-input" type="text" placeholder="Rechercher un stock, description..."
                       class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white text-slate-900 shadow-sm">
            </div>
            <p class="text-xs text-slate-400" id="result-count">{{ $stocks->count() }} stock(s)</p>
        </div>

        {{-- ===== ÉTAT VIDE ===== --}}
        @if ($stocks->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center bg-white border border-slate-200 rounded-2xl shadow-sm">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                    <i class="ti ti-box-off text-3xl text-slate-400"></i>
                </div>
                <h3 class="text-base font-bold text-slate-700 mb-1">Inventaire vide</h3>
                <p class="text-sm text-slate-400 mb-5">Aucun stock enregistré pour le moment.</p>
                <a href="{{ route('stock.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
                    <i class="ti ti-plus"></i> Créer un stock
                </a>
            </div>

        @else

            {{-- ===== TABLEAU INVENTAIRE COMPLET ===== --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" id="inventaire-table">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="px-5 py-3.5">Stock</th>
                            <th class="px-5 py-3.5">Responsable</th>
                            <th class="px-5 py-3.5">Date</th>
                            <th class="px-5 py-3.5">Description</th>
                            <th class="px-5 py-3.5">Sous-catégorie</th>
                            <th class="px-5 py-3.5 text-center">Effectif</th>
                            <th class="px-5 py-3.5 text-right">Prix vente</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">

                        @forelse ($stocks as $stock)
                            @php $descriptions = $stock->descriptions; @endphp

                            @if ($descriptions->isEmpty())
                                {{-- Stock sans description --}}
                                <tr class="hover:bg-slate-50/60 transition-colors inv-row"
                                    data-search="{{ strtolower($stock->name_stock . ' ' . $stock->persn_stock) }}">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                                                <i class="ti ti-building-warehouse text-indigo-500 text-sm"></i>
                                            </div>
                                            <span class="font-semibold text-slate-900">{{ $stock->name_stock }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $stock->persn_stock }}</td>
                                    <td class="px-5 py-4 text-slate-400 tabular-nums text-xs">
                                        {{ \Carbon\Carbon::parse($stock->date_stock)->format('d/m/Y') }}
                                    </td>
                                    <td colspan="3" class="px-5 py-4">
                                <span class="text-xs text-rose-500 italic bg-rose-50 border border-rose-100 px-2 py-0.5 rounded-md">
                                    Aucune description
                                </span>
                                    </td>
                                    <td class="px-5 py-4 text-right text-slate-300">—</td>
                                    <td class="px-5 py-4 text-right">
                                        {{--<a href="{{ route('description.create', $stock->id) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                                            <i class="ti ti-plus"></i> Description
                                        </a>--}}
                                    </td>
                                </tr>

                            @else
                                @foreach ($descriptions as $desc)
                                    @php $subCats = $desc->sousCategories; @endphp

                                    @if ($subCats->isEmpty())
                                        <tr class="hover:bg-slate-50/60 transition-colors inv-row"
                                            data-search="{{ strtolower($stock->name_stock . ' ' . $stock->persn_stock . ' ' . $desc->description) }}">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                                                        <i class="ti ti-building-warehouse text-indigo-500 text-sm"></i>
                                                    </div>
                                                    <span class="font-semibold text-slate-900">{{ $stock->name_stock }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 text-slate-600">{{ $stock->persn_stock }}</td>
                                            <td class="px-5 py-4 text-slate-400 tabular-nums text-xs">
                                                {{ \Carbon\Carbon::parse($stock->date_stock)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-5 py-4 font-medium text-slate-800">{{ $desc->description }}</td>
                                            <td class="px-5 py-4">
                                        <span class="text-xs text-amber-500 italic bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-md">
                                            Aucune sous-catégorie
                                        </span>
                                            </td>
                                            <td class="px-5 py-4 text-center font-semibold text-slate-700">{{ $desc->effectif }}</td>
                                            <td class="px-5 py-4 text-right text-slate-300 text-xs">—</td>
                                            <td class="px-5 py-4 text-right">
                                                <div class="flex items-center justify-end gap-1">
                                                    <a href="{{ route('description.create', $stock->id) }}"
                                                       class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                                                        <i class="ti ti-plus"></i>
                                                    </a>
                                                    <a href="{{ route('description.edit', $desc->id) }}"
                                                       class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors">
                                                        <i class="ti ti-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('description.destroy', $desc->id) }}" method="POST"
                                                          onsubmit="return confirm('Supprimer cette description ?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                                class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                    @else
                                        @foreach ($subCats as $subCat)
                                            <tr class="hover:bg-slate-50/60 transition-colors inv-row"
                                                data-search="{{ strtolower($stock->name_stock . ' ' . $stock->persn_stock . ' ' . $desc->description . ' ' . $subCat->stock_categorie) }}">
                                                <td class="px-5 py-4">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                                                            <i class="ti ti-building-warehouse text-indigo-500 text-sm"></i>
                                                        </div>
                                                        <span class="font-semibold text-slate-900">{{ $stock->name_stock }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-5 py-4 text-slate-600">{{ $stock->persn_stock }}</td>
                                                <td class="px-5 py-4 text-slate-400 tabular-nums text-xs">
                                                    {{ \Carbon\Carbon::parse($stock->date_stock)->format('d/m/Y') }}
                                                </td>
                                                <td class="px-5 py-4 font-medium text-slate-800">{{ $desc->description }}</td>
                                                <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-semibold rounded-md">
                                            <i class="ti ti-tag text-indigo-400 text-xs"></i>
                                            {{ $subCat->stock_categorie }}
                                        </span>
                                                </td>
                                                <td class="px-5 py-4 text-center font-semibold text-slate-700">{{ $desc->effectif }}</td>
                                                <td class="px-5 py-4 text-right font-semibold text-emerald-700 tabular-nums">
                                                    {{ number_format($subCat->prix_vente, 0, ',', ' ') }}
                                                    <span class="text-xs font-normal text-slate-400">Ar</span>
                                                </td>
                                                <td class="px-5 py-4 text-right">
                                                    <div class="flex items-center justify-end gap-1">
                                                        <a href="{{ route('description.create', $stock->id) }}"
                                                           class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                                                            <i class="ti ti-plus"></i>
                                                        </a>
                                                        <a href="{{ route('description.edit', $desc->id) }}"
                                                           class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('description.destroy', $desc->id) }}" method="POST"
                                                              onsubmit="return confirm('Supprimer cette description ?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                    class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                @endforeach
                            @endif

                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center text-sm text-slate-400 italic">
                                    Aucune donnée disponible.
                                </td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table>
                </div>

                {{-- Pied de tableau --}}
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                    <p class="text-xs text-slate-400" id="result-count-table">
                        {{ $stocks->sum(fn($s) => max($s->descriptions->count(), 1)) }} ligne(s) affichée(s)
                    </p>
                    @if (method_exists($stocks, 'links'))
                        {{ $stocks->links() }}
                    @endif
                </div>
            </div>

        @endif

    </div>

    {{-- Script recherche --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('search-input');
            const rows  = document.querySelectorAll('.inv-row');
            const count = document.getElementById('result-count');
            const countTable = document.getElementById('result-count-table');

            input?.addEventListener('input', function () {
                const q = this.value.toLowerCase().trim();
                let visible = 0;
                rows.forEach(row => {
                    const match = row.dataset.search?.includes(q) ?? true;
                    row.style.display = match ? '' : 'none';
                    if (match) visible++;
                });
                if (count) count.textContent = visible + ' stock(s)';
                if (countTable) countTable.textContent = visible + ' ligne(s) affichée(s)';
            });
        });
    </script>
@endsection
