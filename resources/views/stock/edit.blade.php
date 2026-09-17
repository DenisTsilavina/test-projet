{{-- resources/views/stock/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Modifier le Stock : {{ $stock->name_stock }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('stock.update', $stock) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nom du stock <span class="text-danger">*</span></label>
                <input type="text" name="name_stock" class="form-control @error('name_stock') is-invalid @enderror"
                       value="{{ old('name_stock', $stock->name_stock) }}" required>
                @error('name_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- CHANGEMENT : description_stock supprimé, cette colonne n'existe
                 plus sur `stocks`. Remplacé par les vrais champs du schéma
                 actuel : unite_id, quantite, prix_achat_moyen. --}}

            <div class="mb-3">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" name="date_stock" class="form-control @error('date_stock') is-invalid @enderror"
                       value="{{ old('date_stock', $stock->getRawOriginal('date_stock') ? \Carbon\Carbon::parse($stock->getRawOriginal('date_stock'))->format('Y-m-d') : '') }}" required>
                @error('date_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Unité <span class="text-danger">*</span></label>
                    <select name="unite_id" class="form-select @error('unite_id') is-invalid @enderror" required>
                        <option value="">-- Choisir --</option>
                        @foreach ($unites as $unite)
                            <option value="{{ $unite->id }}" {{ old('unite_id', $stock->unite_id) == $unite->id ? 'selected' : '' }}>
                                {{ $unite->nom }} ({{ $unite->symbole }})
                            </option>
                        @endforeach
                    </select>
                    @error('unite_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Quantité <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="quantite"
                           class="form-control @error('quantite') is-invalid @enderror"
                           value="{{ old('quantite', $stock->quantite) }}" required>
                    @error('quantite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Prix d'achat moyen</label>
                    <input type="number" min="0" name="prix_achat_moyen"
                           class="form-control @error('prix_achat_moyen') is-invalid @enderror"
                           value="{{ old('prix_achat_moyen', $stock->prix_achat_moyen) }}">
                    @error('prix_achat_moyen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="{{ route('stock.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
