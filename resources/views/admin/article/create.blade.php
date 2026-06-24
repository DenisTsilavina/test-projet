@extends('layouts.client')

@section('content')
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Créer un nouvel Article (Recette)</h3>
            <a href="{{ route('articles.index') }}" class="btn btn-light btn-sm">Retour</a>
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

            <form action="{{ route('articles.store') }}" method="POST">
                @csrf

                <input type="hidden" name="user_id" value="{{ $userId ?? 1 }}">

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold">Nom de la recette</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Ex: Gâteau au Chocolat" required>
                    </div>
                    <div class="col-md-6">
                        <label for="total_prd_finit" class="form-label fw-bold">Quantité produite (Total produit fini)</label>
                        <input type="number" class="form-control" id="total_prd_finit" name="total_prd_finit" value="{{ old('total_prd_finit', 1) }}" min="1" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="note" class="form-label fw-bold">Note / Description</label>
                    <textarea class="form-control" id="note" name="note" rows="2" placeholder="Ex: Pour 8 parts, cuire à 180°C...">{{ old('note') }}</textarea>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0 text-secondary">Ingrédients</h4>
                    <button type="button" class="btn btn-success btn-sm" id="add-ingredient">+ Ajouter un ingrédient</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="ingredients-table">
                        <thead class="table-dark">
                        <tr>
                            <th>Matière Première</th>
                            <th>Unité</th>
                            <th>Quantité (Effectif)</th>
                            <th>Prix (€)</th>
                            <th width="100 text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-5">Enregistrer la recette</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Listes récupérées depuis le contrôleur pour alimenter les selects
        const descriptions = @json($descriptions);
        const unites = @json($unites);
        let ingredientIndex = 0;

        function addRow() {
            const tbody = document.querySelector('#ingredients-table tbody');

            let descOptions = '<option value="">-- Choisir --</option>';
            descriptions.forEach(d => descOptions += `<option value="${d.id}">${d.libelle}</option>`);

            let uniteOptions = '<option value="">-- Choisir --</option>';
            unites.forEach(u => uniteOptions += `<option value="${u.id}">${u.nom} (${u.unite})</option>`);

            const row = document.createElement('tr');
            row.innerHTML = `
            <td><select name="ingredients[${ingredientIndex}][description_id]" class="form-select" required>${descOptions}</select></td>
            <td><select name="ingredients[${ingredientIndex}][unite_id]" class="form-select" required>${uniteOptions}</select></td>
            <td><input type="number" step="0.01" name="ingredients[${ingredientIndex}][effectif]" class="form-control" placeholder="0.00" required></td>
            <td><input type="number" step="0.01" name="ingredients[${ingredientIndex}][prix]" class="form-control" placeholder="0.00" required></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">Supprimer</button></td>
        `;

            tbody.appendChild(row);
            ingredientIndex++;
        }

        // Ajouter la première ligne automatiquement au chargement
        document.addEventListener('DOMContentLoaded', () => {
            addRow();

            document.getElementById('add-ingredient').addEventListener('click', addRow);

            document.querySelector('#ingredients-table').addEventListener('click', (e) => {
                if(e.target.classList.contains('remove-row')) {
                    e.target.closest('tr').remove();
                }
            });
        });
    </script>
@endsection
