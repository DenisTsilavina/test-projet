@extends('layouts.admin.admin-layout')

@section('title', 'Création article')

@section('content')

    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4>Créer un article</h4>
            </div>

            <div class="card-body">

                {{-- Message succès --}}
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Affichage erreurs --}}
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.article.store') }}" method="POST">
                    @csrf

                    {{-- Nom article --}}
                    <div class="mb-3">
                        <label class="form-label">Nom article</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name') }}"
                               required>
                    </div>

                    {{-- Total produit fini --}}
                    <div class="mb-3">
                        <label class="form-label">Total produit fini</label>
                        <input type="number"
                               name="total_prd_finit"
                               class="form-control"
                               min="1"
                               value="{{ old('total_prd_finit') }}"
                               required>
                    </div>

                    {{-- Note --}}
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note"
                                  class="form-control"
                                  rows="3">{{ old('note') }}</textarea>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Ingrédients</h5>

                        <button type="button"
                                class="btn btn-primary"
                                id="addIngredient">
                            + Ajouter
                        </button>
                    </div>

                    <div id="ingredients-container">

                        <div class="ingredient-item border rounded p-3 mb-3">

                            {{-- Produit --}}
                            <div class="mb-3">
                                <label class="form-label">Produit</label>

                                <select name="ingredients[0][description_id]"
                                        class="form-select"
                                        required>

                                    <option value="">-- Choisir --</option>

                                    @foreach($descriptions as $description)
                                        <option value="{{ $description->id }}">
                                            {{ $description->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- Unite --}}
                            <div class="mb-3">
                                <label class="form-label">Unité</label>

                                <select name="ingredients[0][unite_id]"
                                        class="form-select"
                                        required>

                                    <option value="">-- Choisir --</option>

                                    @foreach($unites as $unite)
                                        <option value="{{ $unite->id }}">
                                            {{ $unite->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- Effectif --}}
                            <div class="mb-3">
                                <label class="form-label">Effectif</label>

                                <input type="number"
                                       name="ingredients[0][effectif]"
                                       class="form-control"
                                       min="1"
                                       required>
                            </div>

                            <button type="button"
                                    class="btn btn-danger removeIngredient">
                                Supprimer
                            </button>

                        </div>

                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            Enregistrer
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@endsection

@push('scripts')

    <script>

        let ingredientIndex = 1;

        document.getElementById('addIngredient').addEventListener('click', function () {

            let container = document.getElementById('ingredients-container');

            let html = `
            <div class="ingredient-item border rounded p-3 mb-3">

                <div class="mb-3">
                    <label class="form-label">Produit</label>

                    <select name="ingredients[${ingredientIndex}][description_id]"
                            class="form-select"
                            required>

                        <option value="">-- Choisir --</option>

                        @foreach($descriptions as $description)
            <option value="{{ $description->id }}">
                                {{ $description->name }}
            </option>
@endforeach

            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Unité</label>

            <select name="ingredients[${ingredientIndex}][unite_id]"
                            class="form-select"
                            required>

                        <option value="">-- Choisir --</option>

                        @foreach($unites as $unite)
            <option value="{{ $unite->id }}">
                                {{ $unite->name }}
            </option>
@endforeach

            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Effectif</label>

            <input type="number"
                   name="ingredients[${ingredientIndex}][effectif]"
                           class="form-control"
                           min="1"
                           required>
                </div>

                <button type="button"
                        class="btn btn-danger removeIngredient">
                    Supprimer
                </button>

            </div>
        `;

            container.insertAdjacentHTML('beforeend', html);

            ingredientIndex++;
        });

        document.addEventListener('click', function (e) {

            if (e.target.classList.contains('removeIngredient')) {
                e.target.closest('.ingredient-item').remove();
            }

        });

    </script>

@endpush
