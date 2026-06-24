{{-- resources/views/stock/description-edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Modifier la description')
@section('page-title', 'Modifier la description')

@section('content')
<div class="max-w-xl mx-auto">

    {{-- Fil d'ariane --}}
    <nav class="flex items-center gap-2 text-xs text-slate-400 mb-6">
        <a href="{{ route('stock.index') }}" class="hover:text-indigo-600 transition-colors">Stocks</a>
        <i class="ti ti-chevron-right text-slate-300"></i>
        <span class="text-slate-600 font-medium">{{ $description->stock->name_stock }}</span>
        <i class="ti ti-chevron-right text-slate-300"></i>
        <span class="text-slate-900 font-semibold">Modifier la description</span>
    </nav>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-6 py-4 bg-slate-50 border-b border-slate-200">
            <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center">
                <i class="ti ti-pencil text-amber-600 text-lg"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900">Modifier la description</h2>
                <p class="text-xs text-slate-400">Stock : {{ $description->stock->name_stock }}</p>
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
        <form action="{{ route('description.update', $description->id) }}" method="POST" class="px-6 py-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Description --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Description <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="description"
                       value="{{ old('description', $description->description) }}"
                       class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                              @error('description') border-rose-500 bg-rose-50/30 @else border-slate-200 @enderror text-slate-900"
                       required>
                @error('description')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Effectif --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Effectif <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="effectif"
                       value="{{ old('effectif', $description->effectif) }}"
                       class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                              @error('effectif') border-rose-500 bg-rose-50/30 @else border-slate-200 @enderror text-slate-900"
                       required>
                @error('effectif')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
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
@endsection
