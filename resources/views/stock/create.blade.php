{{-- resources/views/stock/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Nouveau Stock')
@section('page-title', 'Création d\'un nouveau stock')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <h2 class="text-xl font-bold text-slate-900">Ajouter un nouveau stock</h2>
        </div>

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
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nom du stock <span class="text-rose-500">*</span></label>
                <input type="text" name="name_stock"
                       class="w-full px-3 py-2 border rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 @error('name_stock') border-rose-500 bg-rose-50/30 text-rose-900 @else border-slate-200 text-slate-900 @enderror"
                       value="{{ old('name_stock') }}" required placeholder="Ex: Stock Central A">
                @error('name_stock') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Responsable (Affiche l'admin connecté automatiquement) --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Responsable du stock</label>
                <div class="relative rounded-lg shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="text-base ti ti-user-shield"></i>
                    </div>
                    <input type="text"
                           class="w-full pl-9 pr-3 py-2 border border-slate-200 bg-slate-50 text-slate-500 rounded-lg text-sm select-none cursor-not-allowed"
                           value="{{ auth()->user()->name }} ({{ auth()->user()->roleService()->role()->label() }})"
                           readonly>
                    {{-- Champ masqué pour envoyer la donnée brute 'persn_stock' au contrôleur lors de la validation --}}
                    <input type="hidden" name="persn_stock" value="{{ auth()->user()->name }}">
                </div>
                <p class="mt-1 text-xs text-slate-400">Ce champ est pré-rempli avec votre identifiant d'administrateur.</p>
            </div>

            {{-- Date du Stock --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Date de création <span class="text-rose-500">*</span></label>
                <input type="date" name="date_stock"
                       class="w-full px-3 py-2 border rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 @error('date_stock') border-rose-500 bg-rose-50/30 text-rose-900 @else border-slate-200 text-slate-900 @enderror"
                       value="{{ old('date_stock', now()->format('Y-m-d')) }}" required>
                @error('date_stock') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
            </div>

            {{-- Unités & Quantités --}}
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-md font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <i class="text-indigo-500 ti ti-list-details"></i> Unités &amp; Quantités initiales
                </h3>

                <div class="overflow-x-auto border border-slate-200 rounded-xl shadow-sm bg-white">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="px-4 py-3">Unité</th>
                            <th class="px-4 py-3 text-center">Symbole</th>
                            <th class="px-4 py-3 text-right" style="width:200px">Quantité</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @foreach ($unites as $unite)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-3.5 font-medium text-slate-900">{{ $unite->nom }}</td>
                                <td class="px-4 py-3.5 text-center">
                                        <span class="px-2 py-0.5 text-xs font-medium tracking-wide bg-slate-100 border border-slate-200 text-slate-600 rounded">
                                            {{ $unite->symbole }}
                                        </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <input type="number" step="0.01" min="0"
                                           name="unites[{{ $unite->id }}]"
                                           class="w-full px-3 py-1.5 text-right border border-slate-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900"
                                           value="{{ old('unites.' . $unite->id, 0) }}">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Boutons d'actions --}}
            <div class="flex items-center gap-3 pt-6 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl shadow-sm transition-colors duration-150">
                    <i class="ti ti-device-floppy"></i> Enregistrer le stock
                </button>
                <a href="{{ route('stock.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-sm rounded-xl shadow-sm transition-colors duration-150">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection
