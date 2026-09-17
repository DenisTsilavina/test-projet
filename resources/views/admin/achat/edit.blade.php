@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="h4 mb-3">Modifier l'achat #{{ $achat->id }}</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('achat.update', $achat) }}" method="POST" id="achat-form">
            @csrf
            @method('PUT')

            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Fournisseur</label>
                        <select name="fournisseur_id" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            @foreach ($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}" {{ $achat->fournisseur_id == $fournisseur->id ? 'selected' : '' }}>
                                    {{ $fournisseur->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Remarque</label>
                        <textarea name="remarque" class="form-control" rows="2">{{ old('remarque', $achat->remarque) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="alert alert-secondary">
                Les lignes d'articles ne se modifient pas ici. Pour changer les articles ou quantités,
                supprimez cet achat (tant qu'il est en attente) et recréez-en un.
            </div>

            <div class="card mb-3">
                <div class="card-header">Articles (lecture seule)</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Stock</th>
                            <th class="text-end">Quantité</th>
                            <th class="text-end">Prix unitaire</th>
                            <th class="text-end">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($achat->lignes as $ligne)
                            <tr>
                                <td>{{ $ligne->stock->name_stock ?? '—' }}</td>
                                <td class="text-end">{{ $ligne->quantite }}</td>
                                <td class="text-end">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} Ar</td>
                                <td class="text-end">{{ number_format($ligne->total_ligne, 0, ',', ' ') }} Ar</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="{{ route('achat.show', $achat) }}" class="btn btn-outline-secondary">Annuler</a>
        </form>
    </div>
@endsection
