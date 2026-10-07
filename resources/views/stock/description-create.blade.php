@extends('layouts.admin')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md mt-6">
        <h2 class="text-xl font-bold mb-4">Ajouter une Description - {{ $stock->nom ?? $stock->name_stock }}</h2>

        <form action="{{ route('description.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="stock_id" value="{{ $stock->id }}">

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Description / Libellé</label>
                <input type="text" name="description" value="{{ old('description') }}" class="w-full border rounded p-2 @error('description') border-red-500 @enderror" required>
                @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Effectif</label>
                <input type="number" name="effectif" value="{{ old('effectif', 0) }}" class="w-full border rounded p-2 @error('effectif') border-red-500 @enderror" min="0" required>
                @error('effectif')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Région</label>
                <input type="text" name="region" value="{{ old('region') }}" class="w-full border rounded p-2 @error('region') border-red-500 @enderror" required>
                @error('region')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Image du produit</label>
                <input type="file" name="image" accept="image/*" class="w-full border rounded p-2 @error('image') border-red-500 @enderror">
                @error('image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('stock.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
@endsection
