{{-- resources/views/stock/souscategorie-create.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center bg-light text-black">
                <h4 class="mb-0 flex-grow-1">Nouvelle sous-catégorie — {{ $description->description }}</h4>
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

                <p class="text-muted small">
                    Stock : <strong>{{ $description->stock->name_stock }}</strong>
                    &bull; Région : <strong>{{ $description->region }}</strong>
                </p>

                {{-- CHANGEMENT : le champ stock_categorie a été retiré, cette
                     colonne n'existe plus dans `sous_categories`. Il ne reste
                     que prix_achat / prix_vente, rattachés à la description
                     via description_id. --}}
                <form action="{{ route('souscategorie.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="description_id" value="{{ $description->id }}">

                    <div class="mb-3">
                        <label for="prix_achat">Prix achat</label>
                        <input type="number" step="0.01" min="0" name="prix_achat" id="prix_achat"
                               class="form-control @error('prix_achat') is-invalid @enderror"
                               value="{{ old('prix_achat') }}">
                        @error('prix_achat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="prix_vente">Prix vente</label>
                        <input type="number" step="0.01" min="0" name="prix_vente" id="prix_vente"
                               class="form-control @error('prix_vente') is-invalid @enderror"
                               value="{{ old('prix_vente') }}">
                        @error('prix_vente')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
@endsection
