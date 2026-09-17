@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="h4 mb-3">Modifier « {{ $produit->nom }} »</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('produit.update', $produit) }}" method="POST" id="produit-form">
            @csrf
            @method('PUT')

            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nom du produit</label>
                        <input type="text" name="nom" class="form-control" value="{{ old('nom', $produit->nom) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $produit->description) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prix de vente</label>
                            <input type="number" min="0" name="prix_vente" class="form-control" value="{{ old('prix_vente', $produit->prix_vente) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="actif" {{ $produit->statut === 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="inactif" {{ $produit->statut === 'inactif' ? 'selected' : '' }}>Inactif</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-secondary mb-0">
                        Type : <strong>{{ $produit->type }}</strong>
                        <span class="text-muted">(non modifiable — supprimez et recréez le produit pour changer de type)</span>
                    </div>
                </div>
            </div>

            @if ($produit->type === 'stock')
                <div class="card mb-3">
                    <div class="card-header">Stock lié</div>
                    <div class="card-body">
                        <p class="text-muted">
                            Stock actuel : <strong>{{ $produit->produitStock->stock->name_stock ?? '—' }}</strong>
                            (non modifiable ici)
                        </p>
                        <div class="mb-0">
                            <label class="form-label">Seuil d'alerte</label>
                            <input type="number" min="0" name="seuil_alerte" class="form-control"
                                   value="{{ old('seuil_alerte', $produit->produitStock->seuil_alerte ?? 0) }}">
                        </div>
                    </div>
                </div>
            @else
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Recette (composition)</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="ajouterComposition()">+ Ajouter un ingrédient</button>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info small">
                            Modifier la liste ci-dessous <strong>remplace entièrement</strong> l'ancienne recette.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Temps de préparation (minutes)</label>
                            <input type="number" min="0" name="temps_preparation" class="form-control"
                                   value="{{ old('temps_preparation', $produit->produitFini->temps_preparation ?? '') }}">
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
                            <tbody id="compositions-body">
                            @foreach ($produit->produitFini->compositions ?? [] as $i => $composition)
                                <tr>
                                    <td>
                                        <select name="compositions[{{ $i }}][stock_id]" class="form-select">
                                            @foreach ($stocks as $stock)
                                                <option value="{{ $stock->id }}" {{ $composition->stock_id == $stock->id ? 'selected' : '' }}>
                                                    {{ $stock->name_stock }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0.01" name="compositions[{{ $i }}][quantite_necessaire]"
                                               class="form-control" value="{{ $composition->quantite_necessaire }}">
                                    </td>
                                    <td>
                                        <input type="text" name="compositions[{{ $i }}][unite]" class="form-control" value="{{ $composition->unite }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">✕</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="{{ route('produit.show', $produit) }}" class="btn btn-outline-secondary">Annuler</a>
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
        let compositionIndex = {{ $produit->produitFini->compositions->count() ?? 0 }};

        function ajouterComposition() {
            const template = document.getElementById('composition-template').innerHTML;
            const html = template.replaceAll('__INDEX__', compositionIndex);
            document.getElementById('compositions-body').insertAdjacentHTML('beforeend', html);
            compositionIndex++;
        }
    </script>
@endsection
