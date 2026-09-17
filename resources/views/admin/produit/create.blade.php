@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="h4 mb-3">Nouveau produit</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('produit.store') }}" method="POST" id="produit-form">
            @csrf

            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nom du produit</label>
                        <input type="text" name="nom" class="form-control" value="{{ old('nom') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prix de vente</label>
                            <input type="number" min="0" name="prix_vente" class="form-control" value="{{ old('prix_vente') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label d-block">Type de produit</label>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="type" id="type-stock" value="stock" checked onchange="basculerType()">
                            <label class="btn btn-outline-primary" for="type-stock">Produit de stock (vendu tel quel)</label>

                            <input type="radio" class="btn-check" name="type" id="type-fini" value="fini" onchange="basculerType()">
                            <label class="btn btn-outline-primary" for="type-fini">Produit fini (fabriqué)</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section affichée si type = stock --}}
            <div class="card mb-3" id="section-stock">
                <div class="card-header">Lien vers le stock</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Stock source</label>
                        <select name="stock_id" class="form-select">
                            <option value="">-- Choisir --</option>
                            @foreach ($stocks as $stock)
                                <option value="{{ $stock->id }}">{{ $stock->name_stock }} (dispo: {{ $stock->quantite }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Seuil d'alerte</label>
                        <input type="number" min="0" name="seuil_alerte" class="form-control" value="{{ old('seuil_alerte', 0) }}">
                    </div>
                </div>
            </div>

            {{-- Section affichée si type = fini --}}
            <div class="card mb-3" id="section-fini" style="display:none">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recette (composition)</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="ajouterComposition()">+ Ajouter un ingrédient</button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Temps de préparation (minutes)</label>
                        <input type="number" min="0" name="temps_preparation" class="form-control" value="{{ old('temps_preparation') }}">
                    </div>

                    <table class="table">
                        <thead>
                        <tr>
                            <th>Ingrédient (stock)</th>
                            <th style="width:150px">Quantité nécessaire</th>
                            <th style="width:120px">Unité</th>
                            <th style="width:50px"></th>
                        </tr>
                        </thead>
                        <tbody id="compositions-body"></tbody>
                    </table>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer le produit</button>
            <a href="{{ route('produit.index') }}" class="btn btn-outline-secondary">Annuler</a>
        </form>
    </div>

    <template id="composition-template">
        <tr>
            <td>
                <select name="compositions[__INDEX__][stock_id]" class="form-select">
                    <option value="">-- Choisir --</option>
                    @foreach ($stocks as $stock)
                        <option value="{{ $stock->id }}">{{ $stock->name_stock }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" name="compositions[__INDEX__][quantite_necessaire]" class="form-control">
            </td>
            <td>
                <input type="text" name="compositions[__INDEX__][unite]" class="form-control" placeholder="kg, L...">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">✕</button>
            </td>
        </tr>
    </template>

    <script>
        let compositionIndex = 0;

        function ajouterComposition() {
            const template = document.getElementById('composition-template').innerHTML;
            const html = template.replaceAll('__INDEX__', compositionIndex);
            document.getElementById('compositions-body').insertAdjacentHTML('beforeend', html);
            compositionIndex++;
        }

        function basculerType() {
            const estStock = document.getElementById('type-stock').checked;
            document.getElementById('section-stock').style.display = estStock ? '' : 'none';
            document.getElementById('section-fini').style.display = estStock ? 'none' : '';

            // Rend les champs requis seulement pour la section active
            document.querySelector('select[name="stock_id"]').required = estStock;

            if (!estStock && document.getElementById('compositions-body').children.length === 0) {
                ajouterComposition();
            }
        }
    </script>
@endsection
