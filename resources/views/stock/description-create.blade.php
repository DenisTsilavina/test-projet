{{-- resources/views/stock/description-create.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center bg-light text-black">
                <h4 class="mb-0 flex-grow-1">Nouvelle description — {{ $stock->name_stock }}</h4>
                <a href="{{ route('stock.index') }}" class="btn btn-outline-warning">
                    Retour
                </a>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- CHANGEMENT : ce formulaire ne crée QUE la description.
                     Les champs stock_categorie / prix_achat / prix_vente ont été
                     retirés : ils n'existent plus (stock_categorie a disparu du
                     schéma) ou appartiennent à `sous_categories`, qui se crée
                     séparément une fois la description existante (voir
                     souscategorie-create.blade.php, disponible depuis la liste
                     des stocks après enregistrement). --}}
                <form action="{{ route('description.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="stock_id" value="{{ $stock->id }}">

                    <div class="mb-3">
                        <label for="description">Description</label>
                        <input type="text" name="description" id="description"
                               class="form-control @error('description') is-invalid @enderror"
                               value="{{ old('description') }}" required>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="region">Région d'origine</label>
                        <input type="text" name="region" id="region"
                               class="form-control @error('region') is-invalid @enderror"
                               value="{{ old('region') }}" required>
                        @error('region')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
@endsection
