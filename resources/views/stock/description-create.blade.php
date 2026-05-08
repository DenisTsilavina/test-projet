@extends('layouts.admin.admin-layout')

@section('content')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h4 class="mb-0 text-dark">Nouvelle Description et Catégorie</h4>
                <a href="{{ route('stock.index') }}" class="btn btn-outline-warning">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card-body">
                {{-- Affichage des erreurs de validation --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('description.store', $stock->id) }}" method="POST">
                    @csrf

                    {{-- Gestion du Stock ID --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Stock concerné</label>
                        <input type="text" class="form-control bg-light" value="{{ $stock->name_stock }}" readonly>
                        <input type="hidden" name="stock_id" value="{{ $stock->id }}">
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" name="description" id="description"
                                   class="form-control" placeholder="Ex: Couleur, Taille, Modèle..." required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="effectif" class="form-label">Effectif (Quantité)</label>
                            <input type="number" name="effectif" id="effectif" class="form-control" required>
                        </div>
                    </div>

                    {{-- ✅ SECTION UNITÉS EN CHECKBOX --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Choisir l'unité de mesure</label>
                        <div class="border p-3 rounded bg-light">
                            @forelse ($unites as $unite)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="unite_id"
                                           id="unite_{{ $unite->id }}" value="{{ $unite->id }}">
                                    <label class="form-check-label" for="unite_{{ $unite->id }}">
                                        {{ $unite->name }} <span class="text-muted">({{ $unite->symbol }})</span>
                                    </label>
                                </div>
                            @empty
                                <span class="text-danger small">Aucune unité disponible. Veuillez en créer dans les paramètres.</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="stock_categorie" class="form-label">Sous catégorie</label>
                        <input type="text" name="stock_categorie" id="stock_categorie" class="form-control" placeholder="Ex: Électronique, Alimentaire...">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prix_achat" class="form-label">Prix achat</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="prix_achat" id="prix_achat" class="form-control">
                                <span class="input-group-text">Ariary</span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="prix_vente" class="form-label">Prix vente</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="prix_vente" id="prix_vente" class="form-control">
                                <span class="input-group-text">Ariary</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 border-top pt-3">
                        <button type="submit" class="btn btn-success px-5 shadow-sm">
                            <i class="fas fa-check-circle"></i> Enregistrer la nouvelle description
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
