{{-- resources/views/stock/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Stocks')
@section('page-title', 'Stocks')

@section('content')
    <div class="space-y-6">

        {{-- ===== FLASH MESSAGES ===== --}}
        @if (session('success'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-sm">
                <i class="ti ti-circle-check text-emerald-500 text-lg shrink-0"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                <i class="ti ti-alert-circle text-rose-500 text-lg shrink-0"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- ===== EN-TÊTE ===== --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Stocks disponibles</h2>
                <p class="text-sm text-slate-400 mt-0.5">Vue complète de tous les stocks avec descriptions et sous-catégories.</p>
            </div>
            <a href="{{ route('stock.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
                <i class="ti ti-plus"></i> Nouveau stock
            </a>
        </div>

        {{-- ===== ÉTAT VIDE ===== --}}
        @if ($stocks->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center bg-white border border-slate-200 rounded-2xl shadow-sm">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                    <i class="ti ti-box-off text-3xl text-slate-400"></i>
                </div>
                <h3 class="text-base font-bold text-slate-700 mb-1">Aucun stock enregistré</h3>
                <p class="text-sm text-slate-400 mb-5">Commencez par créer votre premier stock.</p>
                <a href="{{ route('stock.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
                    <i class="ti ti-plus"></i> Créer un stock
                </a>
            </div>

        @else

            @foreach ($stocks as $stock)
                @php $descriptions = $stock->descriptions; @endphp

                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                    {{-- ===== EN-TÊTE STOCK ===== --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4 bg-slate-50 border-b border-slate-200">

                        {{-- Infos stock --}}
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                                <i class="ti ti-building-warehouse text-indigo-600 text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 truncate">{{ $stock->name_stock }}</h3>
                                <p class="text-xs text-slate-400 flex items-center gap-1 flex-wrap">
                                    <i class="ti ti-user-shield"></i> {{ $stock->persn_stock }}
                                    &bull;
                                    <i class="ti ti-calendar"></i>
                                    {{ \Carbon\Carbon::parse($stock->date_stock)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Badges unités --}}
                        <div class="flex flex-wrap gap-1.5">
                            @forelse ($stock->unites as $unite)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold
                                     {{ $unite->pivot->quantite > 0
                                            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                            : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                            {{ $unite->symbole }}
                            <span class="font-bold">{{ number_format($unite->pivot->quantite, 2, ',', ' ') }}</span>
                        </span>
                            @empty
                                <span class="text-xs text-slate-400 italic">Aucune unité</span>
                            @endforelse
                        </div>

                        {{-- Actions stock --}}
                        <div class="flex items-center gap-1 shrink-0">
                            {{-- Ajouter description (ouvre le modal) --}}
                            <button type="button"
                                    onclick="openModal('modal-desc-{{ $stock->id }}')"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                                <i class="ti ti-plus"></i> Description
                            </button>
                            <a href="{{ route('stock.edit', $stock->id) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors">
                                <i class="ti ti-pencil"></i>
                            </a>
                            <form action="{{ route('stock.destroy', $stock->id) }}" method="POST"
                                  onsubmit="return confirm('Supprimer ce stock et toutes ses données ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- ===== DESCRIPTIONS ===== --}}
                    @if ($descriptions->isEmpty())
                        <div class="flex items-center gap-3 px-5 py-5 text-sm text-slate-400 italic">
                            <i class="ti ti-info-circle text-slate-300 text-base"></i>
                            Aucune description enregistrée pour ce stock.
                            <button type="button"
                                    onclick="openModal('modal-desc-{{ $stock->id }}')"
                                    class="ml-auto inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors not-italic">
                                <i class="ti ti-plus"></i> Ajouter
                            </button>
                        </div>

                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead>
                                <tr class="text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                                    <th class="px-5 py-3">Description</th>
                                    <th class="px-5 py-3">Sous-catégorie</th>
                                    <th class="px-5 py-3 text-center">Origine</th>
                                    <th class="px-5 py-3 text-right">Prix achat</th>
                                    <th class="px-5 py-3 text-right">Prix vente</th>
                                    <th class="px-5 py-3 text-right">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">

                                @foreach ($descriptions as $desc)
                                    @php $subCats = $desc->sousCategories; @endphp

                                    @if ($subCats->isEmpty())
                                        {{-- Description sans sous-catégorie --}}
                                        <tr class="hover:bg-slate-50/60 transition-colors">
                                            <td class="px-5 py-3.5 font-medium text-slate-800">{{ $desc->description }}</td>
                                            <td class="px-5 py-3.5">
                                                <a href="{{ route('souscategorie.create', $desc->id) }}"
                                                   class="inline-flex items-center gap-1 text-xs text-indigo-500 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 px-2 py-0.5 rounded-md transition-colors">
                                                    <i class="ti ti-plus"></i> Ajouter sous-cat.
                                                </a>
                                            </td>
                                            <td class="px-5 py-3.5 text-center font-semibold text-slate-700">{{ $desc->effectif }}</td>
                                            <td class="px-5 py-3.5 text-right text-slate-300 text-xs">—</td>
                                            <td class="px-5 py-3.5 text-right text-slate-300 text-xs">—</td>
                                            <td class="px-5 py-3.5 text-right">
                                                <div class="flex items-center justify-end gap-1">
                                                    <a href="{{ route('description.edit', $desc->id) }}"
                                                       class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors">
                                                        <i class="ti ti-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('description.destroy', $desc->id) }}" method="POST"
                                                          onsubmit="return confirm('Supprimer cette description ?')">
                                                        @csrf @method('DELETE')
                                                        <button class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                    @else
                                        {{-- Description avec sous-catégories --}}
                                        @foreach ($subCats as $i => $subCat)
                                            <tr class="hover:bg-slate-50/60 transition-colors">
                                                <td class="px-5 py-3.5 font-medium text-slate-800">
                                                    @if ($i === 0)
                                                        {{ $desc->description }}
                                                        @if ($subCats->count() > 1)
                                                            <span class="ml-1 text-xs text-slate-400 font-normal">({{ $subCats->count() }})</span>
                                                        @endif
                                                    @else
                                                        <span class="text-slate-300 pl-3 border-l-2 border-slate-100 text-xs italic">↳</span>
                                                    @endif
                                                </td>
                                                <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-semibold rounded-md">
                                            <i class="ti ti-tag text-indigo-400 text-xs"></i>
                                            {{ $subCat->stock_categorie }}
                                        </span>
                                                </td>
                                                <td class="px-5 py-3.5 text-center font-semibold text-slate-700">{{ $desc->region }}</td>
                                                <td class="px-5 py-3.5 text-right text-slate-500 tabular-nums text-xs">
                                                    {{ $subCat->prix_achat ? number_format($subCat->prix_achat, 0, ',', ' ') . ' Ar' : '—' }}
                                                </td>
                                                <td class="px-5 py-3.5 text-right font-semibold text-emerald-700 tabular-nums">
                                                    {{ number_format($subCat->prix_vente, 0, ',', ' ') }}
                                                    <span class="text-xs font-normal text-slate-400">Ar</span>
                                                </td>
                                                <td class="px-5 py-3.5 text-right">
                                                    <div class="flex items-center justify-end gap-1">
                                                        @if ($i === 0)
                                                            <a href="{{ route('souscategorie.create', $desc->id) }}"
                                                               class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                                                                <i class="ti ti-plus"></i>
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('souscategorie.edit', $subCat->id) }}"
                                                           class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('souscategorie.destroy', $subCat->id) }}" method="POST"
                                                              onsubmit="return confirm('Supprimer cette sous-catégorie ?')">
                                                            @csrf @method('DELETE')
                                                            <button class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach

                                </tbody>
                            </table>
                        </div>

                        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50">
                            <button type="button"
                                    onclick="openModal('modal-desc-{{ $stock->id }}')"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                <i class="ti ti-plus"></i> Ajouter une description
                            </button>
                        </div>
                    @endif

                </div>

                {{-- ===== MODAL DESCRIPTION ===== --}}
                <div id="modal-desc-{{ $stock->id }}"
                     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
                     role="dialog" aria-modal="true">

                    {{-- Overlay --}}
                    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
                         onclick="closeModal('modal-desc-{{ $stock->id }}')"></div>

                    {{-- Boîte --}}
                    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">

                        {{-- Header modal --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <i class="ti ti-file-description text-indigo-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Nouvelle description</h3>
                                    <p class="text-xs text-slate-400">{{ $stock->name_stock }}</p>
                                </div>
                            </div>
                            <button type="button" onclick="closeModal('modal-desc-{{ $stock->id }}')"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-200 text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="ti ti-x text-sm"></i>
                            </button>
                        </div>

                        {{-- Formulaire --}}
                        <form action="{{ route('description.store') }}" method="POST" class="px-6 py-5 space-y-4">
                            @csrf
                            <input type="hidden" name="stock_id" value="{{ $stock->id }}">

                            {{-- Description --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Description <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="description"
                                       value="{{ old('description') }}"
                                       placeholder="Ex: Riz blanc 50kg"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900"
                                       required>
                                @error('description')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- region d'origine --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    region d'origine <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="region"
                                       value="{{ old('region') }}"
                                       placeholder="region d'origine, ex:vakinakaratra"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900"
                                       required>
                                @error('region')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Boutons --}}
                            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
                                    <i class="ti ti-device-floppy"></i> Enregistrer
                                </button>
                                <button type="button"
                                        onclick="closeModal('modal-desc-{{ $stock->id }}')"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-sm rounded-xl shadow-sm transition-colors">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            @endforeach
        @endif

    </div>

    {{-- ===== SCRIPT MODALS ===== --}}
    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        // Fermer avec Echap
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id^="modal-"]').forEach(m => {
                    m.classList.add('hidden');
                    m.classList.remove('flex');
                });
                document.body.classList.remove('overflow-hidden');
            }
        });
    </script>
@endsection
