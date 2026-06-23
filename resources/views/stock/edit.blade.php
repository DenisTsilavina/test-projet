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

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description_stock" class="form-control @error('description_stock') is-invalid @enderror"
                          rows="3">{{ old('description_stock', $stock->description_stock) }}</textarea>
                @error('description_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                {{-- On formate pour l'input date (Y-m-d) même si le cast affiche d/m/Y --}}
                <input type="date" name="date_stock" class="form-control @error('date_stock') is-invalid @enderror"
                       value="{{ old('date_stock', $stock->getRawOriginal('date_stock') ? \Carbon\Carbon::parse($stock->getRawOriginal('date_stock'))->format('Y-m-d') : '') }}" required>
                @error('date_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <h5 class="mt-4">Unités &amp; Quantités</h5>
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                <tr>
                    <th>Unité</th>
                    <th>Symbole</th>
                    <th style="width:180px">Quantité</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($unites as $unite)
                    @php
                        // Récupérer la quantité actuelle pour cette unité (si elle existe)
                        $pivot = $stock->unites->firstWhere('id', $unite->id);
                        $currentQty = $pivot ? $pivot->pivot->quantite : 0;
                    @endphp
                    <tr>
                        <td>{{ $unite->nom }}</td>
                        <td><span class="badge bg-secondary">{{ $unite->symbole }}</span></td>
                        <td>
                            <input type="number" step="0.01" min="0"
                                   name="unites[{{ $unite->id }}]"
                                   class="form-control form-control-sm"
                                   value="{{ old('unites.' . $unite->id, $currentQty) }}">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="{{ route('stock.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
@endsection
