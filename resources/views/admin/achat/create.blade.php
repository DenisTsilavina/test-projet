@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="h4 mb-3">Nouvel achat</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('achat.store') }}" method="POST" id="achat-form">
            @csrf

            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Fournisseur</label>
                        <select name="fournisseur_id" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            @foreach ($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                    {{ $fournisseur->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Remarque</label>
                        <textarea name="remarque" class="form-control" rows="2">{{ old('remarque') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Articles achetés</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="ajouterLigne()">+ Ajouter une ligne</button>
                </div>
                <div class="card-body">
                    <table class="table" id="lignes-table">
                        <thead>
                        <tr>
                            <th>Stock</th>
                            <th style="width:120px">Quantité</th>
                            <th style="width:150px">Prix unitaire</th>
                            <th style="width:50px"></th>
                        </tr>
                        </thead>
                        <tbody id="lignes-body">
                        {{-- Les lignes sont ajoutées ici en JS --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer l'achat</button>
            <a href="{{ route('achat.index') }}" class="btn btn-outline-secondary">Annuler</a>
        </form>
    </div>

    <template id="ligne-template">
        <tr>
            <td>
                <select name="lignes[__INDEX__][stock_id]" class="form-select" required>
                    <option value="">-- Choisir --</option>
                    @foreach ($stocks as $stock)
                        <option value="{{ $stock->id }}">{{ $stock->name_stock }} (dispo: {{ $stock->quantite }})</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" name="lignes[__INDEX__][quantite]" class="form-control" required>
            </td>
            <td>
                <input type="number" min="0" name="lignes[__INDEX__][prix_unitaire]" class="form-control" required>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">✕</button>
            </td>
        </tr>
    </template>

    <script>
        let ligneIndex = 0;

        function ajouterLigne() {
            const template = document.getElementById('ligne-template').innerHTML;
            const html = template.replaceAll('__INDEX__', ligneIndex);
            document.getElementById('lignes-body').insertAdjacentHTML('beforeend', html);
            ligneIndex++;
        }

        // Au moins une ligne au chargement
        document.addEventListener('DOMContentLoaded', () => ajouterLigne());
    </script>
@endsection
