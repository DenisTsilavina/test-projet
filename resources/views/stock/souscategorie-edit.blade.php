{{-- resources/views/stock/souscategorie-edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Modifier la sous-catégorie')
@section('page-title', 'Modifier la sous-catégorie')

@section('content')
    <div class="max-w-xl mx-auto">

        {{-- Fil d'ariane --}}
        <nav class="flex items-center gap-2 text-xs text-slate-400 mb-6">
            <a href="{{ route('stock.index') }}" class="hover:text-indigo-600 transition-colors">Stocks</a>
            <i class="ti ti-chevron-right text-slate-300"></i>
            <span class="text-slate-600 font-medium">{{ $sousCategory->description->stock->name_stock }}</span>
            <i class="ti ti-chevron-right text-slate-300"></i>
            <span class="text-slate-600 font-medium">{{ $sousCategory->description->description }}</span>
            <i class="ti ti-chevron-right text-slate-300"></i>
            <span class="text-slate-900 font-semibold">Modifier</span>
        </nav>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center gap-3 px-6 py-4 bg-slate-50 border-b border-slate-200">
                <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center">
                    <i class="ti ti-tag text-amber-600 text-lg"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Modifier la sous-catégorie</h2>
                    <p class="text-xs text-slate-400">{{ $sousCategory->stock_categorie }}</p>
                </div>
            </div>

            {{-- Erreurs --}}
            @if ($errors->any())
                <div class="flex items-start gap-3 mx-6 mt-5 p-4 border rounded-xl bg-rose-50 border-rose-200 text-rose-800">
                    <i class="ti ti-alert-circle text-rose-600 mt-0.5 shrink-0"></i>
                    <ul class="text-xs space-y-1 text-rose-700 list-disc list-inside">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulaire --}}
            <form action="{{ route('souscategorie.update', $sousCategory->id) }}" method="POST" class="px-6 py-6 space-y-5">
                @csrf
                @method('PUT')

                {{-- Nom catégorie --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Nom de la sous-catégorie <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="stock_categorie"
                           value="{{ old('stock_categorie', $sousCategory->stock_categorie) }}"
                           class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                              @error('stock_categorie') border-rose-500 bg-rose-50/30 @else border-slate-200 @enderror text-slate-900"
                           required>
                    @error('stock_categorie')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Prix côte à côte --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Prix d'achat</label>
                        <div class="relative">
                            <input type="number" name="prix_achat" min="0"
                                   value="{{ old('prix_achat', $sousCategory->prix_achat) }}"
                                   class="w-full pr-10 pl-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                                      @error('prix_achat') border-rose-500 bg-rose-50/30 @else border-slate-200 @enderror text-slate-900">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-slate-400 font-medium">Ar</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Prix de vente</label>
                        <div class="relative">
                            <input type="number" name="prix_vente" min="0"
                                   value="{{ old('prix_vente', $sousCategory->prix_vente) }}"
                                   class="w-full pr-10 pl-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                                      @error('prix_vente') border-rose-500 bg-rose-50/30 @else border-slate-200 @enderror text-slate-900">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-slate-400 font-medium">Ar</span>
                        </div>
                    </div>
                </div>

                {{-- Marge --}}
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 flex items-center gap-2">
                    <i class="ti ti-calculator text-indigo-400"></i>
                    Marge estimée :
                    <span id="marge-display" class="font-bold text-slate-700">—</span>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
                        <i class="ti ti-device-floppy"></i> Mettre à jour
                    </button>
                    <a href="{{ route('stock.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-sm rounded-xl shadow-sm transition-colors">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const achat  = document.querySelector('[name="prix_achat"]');
        const vente  = document.querySelector('[name="prix_vente"]');
        const marge  = document.getElementById('marge-display');

        function updateMarge() {
            const a = parseFloat(achat.value) || 0;
            const v = parseFloat(vente.value) || 0;
            if (a > 0 && v > 0) {
                const diff = v - a;
                const pct  = ((diff / a) * 100).toFixed(1);
                marge.textContent = diff.toLocaleString('fr-FR') + ' Ar (' + pct + ' %)';
                marge.className = diff >= 0 ? 'font-bold text-emerald-600' : 'font-bold text-rose-600';
            } else {
                marge.textContent = '—';
                marge.className = 'font-bold text-slate-700';
            }
        }

        // Initialiser avec les valeurs existantes
        updateMarge();
        achat?.addEventListener('input', updateMarge);
        vente?.addEventListener('input', updateMarge);
    </script>
@endsection
