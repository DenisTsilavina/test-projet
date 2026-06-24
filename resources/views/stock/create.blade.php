{{-- resources/views/stock/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Nouveau Stock')
@section('page-title', 'Création d\'un nouveau stock')

@section('content')
    <div class="max-w-3xl mx-auto">

        {{-- En-tête --}}
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <h2 class="text-xl font-bold text-slate-900">Ajouter un nouveau stock</h2>
        </div>

        {{-- Erreurs de validation --}}
        @if ($errors->any())
            <div class="flex items-start gap-3 p-4 mb-6 border rounded-xl bg-rose-50 border-rose-200 text-rose-800 shadow-sm">
                <i class="text-lg ti ti-alert-circle text-rose-600 mt-0.5"></i>
                <div>
                    <p class="text-sm font-semibold mb-1">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="list-disc list-inside text-xs space-y-1 text-rose-700">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('stock.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Nom du Stock --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Nom du stock <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name_stock"
                       class="w-full px-3 py-2 border rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                              @error('name_stock') border-rose-500 bg-rose-50/30 text-rose-900 @else border-slate-200 text-slate-900 @enderror"
                       value="{{ old('name_stock') }}" required placeholder="Ex: Stock Central A">
                @error('name_stock')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Responsable --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Responsable du stock</label>
                <div class="relative rounded-lg shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="text-base ti ti-user-shield"></i>
                    </div>
                    <input type="text"
                           class="w-full pl-9 pr-3 py-2 border border-slate-200 bg-slate-50 text-slate-500 rounded-lg text-sm cursor-not-allowed"
                           value="{{ auth()->user()->name }} ({{ auth()->user()->roleService()->role()->label() }})"
                           readonly>
                    <input type="hidden" name="persn_stock" value="{{ auth()->user()->name }}">
                </div>
                <p class="mt-1 text-xs text-slate-400">Pré-rempli avec votre identifiant d'administrateur.</p>
            </div>

            {{-- Date du Stock --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Date de création <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="date_stock"
                       class="w-full px-3 py-2 border rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                              @error('date_stock') border-rose-500 bg-rose-50/30 text-rose-900 @else border-slate-200 text-slate-900 @enderror"
                       value="{{ old('date_stock', now()->format('Y-m-d')) }}" required>
                @error('date_stock')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- ===================== UNITÉS EN CARDS ===================== --}}
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-md font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <i class="text-indigo-500 ti ti-list-details"></i> Unités &amp; Quantités initiales
                </h3>
                <p class="text-xs text-slate-400 mb-4">Cochez les unités concernées et renseignez la quantité de départ.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($unites as $unite)
                        @php
                            $is_checked = old('unites_checked.' . $unite->id) ? true : false;
                        @endphp

                        {{-- Card Unité --}}
                        <label class="unit-card relative flex flex-col gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-150 select-none
                                      {{ $is_checked
                                            ? 'border-indigo-500 bg-indigo-50/60 shadow-sm'
                                            : 'border-slate-200 bg-white hover:border-indigo-300 hover:bg-slate-50/50' }}"
                               data-id="{{ $unite->id }}">

                            {{-- Checkbox cachée --}}
                            <input type="checkbox"
                                   name="unites_checked[{{ $unite->id }}]"
                                   value="1"
                                   class="unit-checkbox sr-only"
                                {{ $is_checked ? 'checked' : '' }}>

                            {{-- Ligne haute : nom + badge symbole + indicateur --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    {{-- Indicateur visuel coché / non coché --}}
                                    <span class="unit-indicator flex items-center justify-center w-5 h-5 rounded-full border-2 transition-all duration-150
                                                 {{ $is_checked
                                                        ? 'border-indigo-500 bg-indigo-500'
                                                        : 'border-slate-300 bg-white' }}">
                                        <i class="ti ti-check text-white text-xs {{ $is_checked ? '' : 'opacity-0' }}"></i>
                                    </span>
                                    <span class="font-semibold text-sm text-slate-800">{{ $unite->nom }}</span>
                                </div>
                                <span class="px-2 py-0.5 text-xs font-bold tracking-widest bg-slate-100 border border-slate-200 text-slate-500 rounded-md">
                                    {{ $unite->symbole }}
                                </span>
                            </div>

                            {{-- Champ quantité --}}
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-medium text-slate-500 whitespace-nowrap">Qté initiale</span>
                                <input type="number" step="0.01" min="0"
                                       name="unites[{{ $unite->id }}]"
                                       class="unit-quantity flex-1 px-3 py-1.5 text-right border rounded-lg text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all
                                              {{ $is_checked
                                                    ? 'border-slate-300 text-slate-900 bg-white'
                                                    : 'border-slate-100 bg-slate-50 text-slate-300 cursor-not-allowed' }}"
                                       value="{{ old('unites.' . $unite->id, 0) }}"
                                       {{ $is_checked ? '' : 'disabled' }}
                                       onclick="event.preventDefault()">
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            {{-- ==================== FIN UNITÉS ========================= --}}

            {{-- Boutons d'actions --}}
            <div class="flex items-center gap-3 pt-6 border-t border-slate-100">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors duration-150">
                    <i class="ti ti-device-floppy"></i> Enregistrer le stock
                </button>
                <a href="{{ route('stock.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-sm rounded-xl shadow-sm transition-colors duration-150">
                    Annuler
                </a>
            </div>
        </form>
    </div>

    {{-- Script : toggle card --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.unit-card').forEach(card => {
                card.addEventListener('click', function (e) {
                    // Éviter les doubles déclenchements depuis l'input number
                    if (e.target.tagName === 'INPUT' && e.target.type === 'number') return;

                    const checkbox  = card.querySelector('.unit-checkbox');
                    const qtyInput  = card.querySelector('.unit-quantity');
                    const indicator = card.querySelector('.unit-indicator');
                    const icon      = indicator.querySelector('i');

                    checkbox.checked = !checkbox.checked;
                    const checked = checkbox.checked;

                    // Card border & bg
                    card.classList.toggle('border-indigo-500', checked);
                    card.classList.toggle('bg-indigo-50/60',   checked);
                    card.classList.toggle('shadow-sm',         checked);
                    card.classList.toggle('border-slate-200',  !checked);
                    card.classList.toggle('bg-white',          !checked);

                    // Indicator
                    indicator.classList.toggle('border-indigo-500', checked);
                    indicator.classList.toggle('bg-indigo-500',     checked);
                    indicator.classList.toggle('border-slate-300',  !checked);
                    indicator.classList.toggle('bg-white',          !checked);
                    icon.classList.toggle('opacity-0', !checked);

                    // Champ quantité
                    qtyInput.disabled = !checked;
                    qtyInput.classList.toggle('border-slate-300', checked);
                    qtyInput.classList.toggle('text-slate-900',   checked);
                    qtyInput.classList.toggle('bg-white',         checked);
                    qtyInput.classList.toggle('border-slate-100', !checked);
                    qtyInput.classList.toggle('text-slate-300',   !checked);
                    qtyInput.classList.toggle('bg-slate-50',      !checked);
                    qtyInput.classList.toggle('cursor-not-allowed', !checked);

                    if (checked) {
                        qtyInput.focus();
                        qtyInput.select();
                    } else {
                        qtyInput.value = 0;
                    }
                });

                // Permettre la saisie dans le champ sans refermer la card
                card.querySelector('.unit-quantity').addEventListener('click', e => e.stopPropagation());
            });
        });
    </script>
@endsection
