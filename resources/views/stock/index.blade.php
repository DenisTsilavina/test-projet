@extends('layouts.admin.admin-layout')

@section('title', 'Gestion des Stocks')

@section('content')
    <div class="container-fluid py-4">

        {{-- SECTION 1 : FORMULAIRE DE CRÉATION --}}
        <div class="row justify-content-center mb-5">
            <div class="col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0"><i class="bi bi-plus-circle-fill text-primary me-2"></i>Nouveau Mouvement de Stock</h4>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreerUnite">
                        <i class="bi bi-rulers me-2"></i>Nouvelle Unité
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm"
                            data-bs-toggle="modal" data-bs-target="#modalConversion">
                        <i class="bi bi-arrow-left-right me-2"></i>Conversion
                    </button>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('stock.store') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">Désignation du stock</label>
                                    <input type="text" class="form-control @error('name_stock') is-invalid @enderror"
                                           name="name_stock" placeholder="ex: Arrivage composants" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Responsable</label>
                                    <input type="text" class="form-control bg-light" value="{{ auth()->user()->name }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Date</label>
                                    <input type="date" class="form-control" name="date_stock" value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100" title="Enregistrer">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2 : TABLEAU RÉCAPITULATIF --}}
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-table me-2 text-secondary"></i>Stocks Disponibles</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Stock (ID)</th>
                                    <th>Responsable</th>
                                    <th>Description</th>
                                    <th>Sous-Catégorie</th>
                                    <th class="text-center">Effectif</th>
                                    <th class="text-end">Prix Vente</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($stocks as $stock)
                                    @php
                                        $descriptions = $stock->descriptions;
                                        $totalRows = 0;
                                        if($descriptions->isEmpty()) {
                                            $totalRows = 1;
                                        } else {
                                            foreach($descriptions as $d) {
                                                $totalRows += $d->sousCategories->isEmpty() ? 1 : $d->sousCategories->count();
                                            }
                                        }
                                        $firstStockRow = true;
                                    @endphp

                                    @forelse($descriptions as $desc)
                                        @php
                                            $subCats = $desc->sousCategories;
                                            $firstDescRow = true;
                                        @endphp

                                        @forelse($subCats as $subCat)
                                            <tr>
                                                @if($firstStockRow)
                                                    <td rowspan="{{ $totalRows }}" class="ps-4 border-end fw-bold text-primary">
                                                        {{ $stock->name_stock }} <br>
                                                        <small class="text-muted fw-normal">#{{ $stock->id }}</small>
                                                    </td>
                                                    <td rowspan="{{ $totalRows }}" class="border-end text-muted">{{ $stock->persn_stock }}</td>
                                                    @php $firstStockRow = false; @endphp
                                                @endif

                                                @if($firstDescRow)
                                                    <td rowspan="{{ $subCats->count() }}" class="bg-light-subtle">{{ $desc->description }}</td>
                                                    @php $firstDescRow = false; @endphp
                                                @endif

                                                <td><span class="badge bg-info-subtle text-info">{{ $subCat->stock_categorie }}</span></td>

                                                {{-- ✅ Effectif + unité --}}
                                                <td class="text-center fw-bold">
                                                    {{ $desc->effectif }}
                                                    <span class="badge bg-secondary ms-1">{{ $desc->unite->symbol ?? '—' }}</span>
                                                </td>

                                                <td class="text-end fw-bold text-success">{{ number_format($subCat->prix_vente, 0, ',', ' ') }} Ar</td>
                                                <td class="text-center">
                                                    <div class="btn-group shadow-sm">
                                                        <a href="{{ route('description.create', $stock->id) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-plus-lg"></i></a>
                                                        <a href="{{ route('description.edit', $desc->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            {{-- Cas Description sans sous-catégorie --}}
                                            <tr>
                                                @if($firstStockRow)
                                                    <td rowspan="{{ $totalRows }}" class="ps-4 border-end fw-bold text-primary">{{ $stock->name_stock }}</td>
                                                    <td rowspan="{{ $totalRows }}" class="border-end">{{ $stock->persn_stock }}</td>
                                                    @php $firstStockRow = false; @endphp
                                                @endif
                                                <td class="bg-light-subtle">{{ $desc->description }}</td>
                                                <td colspan="2" class="text-warning small italic">Aucune catégorie</td>

                                                {{-- ✅ Effectif + unité --}}
                                                <td class="text-center fw-bold">
                                                    {{ $desc->effectif }}
                                                    <span class="badge bg-secondary ms-1">{{ $desc->unite->symbol ?? '—' }}</span>
                                                </td>

                                                <td class="text-center">
                                                    <a href="{{ route('description.descriptionUpdate', $desc->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    @empty
                                        {{-- Stock sans description --}}
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary">{{ $stock->name_stock }}</td>
                                            <td>{{ $stock->persn_stock }}</td>
                                            <td colspan="4" class="text-center text-muted small italic py-3">Aucune donnée liée</td>
                                            <td class="text-center">
                                                <a href="{{ route('description.create', $stock->id) }}" class="btn btn-sm btn-success px-3"><i class="bi bi-plus"></i></a>
                                            </td>
                                        </tr>
                                    @endforelse
                                @empty
                                    <tr><td colspan="7" class="text-center py-5 text-muted">La base de données est vide.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL UNITÉ --}}
    <div class="modal fade" id="modalCreerUnite" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-rulers me-2"></i>Nouvelle Unité</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('stock.createUnite') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-8">
                                <label class="form-label fw-semibold">Nom</label>
                                <input type="text" name="name" class="form-control" placeholder="Kilogramme" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-semibold">Symbole</label>
                                <input type="text" name="symbol" class="form-control" placeholder="kg" required>
                            </div>
                            <div class="col-8">
                                <label class="form-label fw-semibold">Type</label>
                                <select class="form-select" name="type" required>
                                    <option value="mass">Masse</option>
                                    <option value="volume">Volume</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-semibold">Facteur</label>
                                <input type="number" name="factor" class="form-control"
                                       placeholder="1" step="0.001" min="0" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="is_base" value="1"
                                           class="form-check-input" id="isBase">
                                    <label class="form-check-label" for="isBase">
                                        Unité de base
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        {{-- MODAL CONVERSION --}}
    <div class="modal fade" id="modalConversion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-arrow-left-right me-2"></i>Convertir une quantité</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">

                        {{-- Choisir la description --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <select class="form-select" id="conv_description_id" onchange="loadDescription()">
                                <option value="" disabled selected>-- Choisir --</option>
                                @foreach($descriptions as $desc)
                                    <option value="{{ $desc->id }}"
                                            data-effectif="{{ $desc->effectif }}"
                                            data-unite="{{ $desc->unite->symbol ?? '—' }}">
                                        {{ $desc->description }}
                                        {{ $desc->effectif }} {{ $desc->unite->symbol ?? '—' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Effectif (chargé automatiquement) --}}
                        <div class="col-6">
                            <label class="form-label fw-semibold">Effectif</label>
                            <div class="input-group">
                                <input type="number" id="conv_effectif" class="form-control bg-light" readonly>
                                <span class="input-group-text" id="conv_unite_base">—</span>
                            </div>
                            <small class="text-muted">Chargé depuis la description</small>
                        </div>

                        {{-- Contenance par unité --}}
                        <div class="col-6">
                            <label class="form-label fw-semibold">Contenance par unité</label>
                            <div class="input-group">
                                <input type="number" id="conv_contenance" class="form-control"
                                       placeholder="ex: 50" min="0" step="0.01" oninput="calculerConversion()">
                                <span class="input-group-text" id="conv_unite_cible_label">—</span>
                            </div>
                            <small class="text-muted">ex: 1 sac contient <strong>50 kg</strong></small>
                        </div>

                        {{-- Unité cible --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Unité de conversion</label>
                            <select class="form-select" id="conv_unite_cible" onchange="updateLabel(); calculerConversion()">
                                <option value="" disabled selected>-- Choisir une unité --</option>
                                @foreach($unites as $unite)
                                    <option value="{{ $unite->id }}"
                                            data-symbol="{{ $unite->symbol }}">
                                        {{ $unite->name }} ({{ $unite->symbol }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                            {{-- Formule affichée --}}
                            <div class="col-12" id="conv_formule_box" style="display:none">
                                <div class="bg-light rounded p-3 text-center border">
                                    <small class="text-muted d-block mb-1">Calcul</small>
                                    <span id="conv_formule" class="fw-semibold text-dark fs-6"></span>
                                </div>
                            </div>

                            {{-- Résultat --}}
                        <div class="col-12">
                            <div class="alert alert-success d-none" id="conv_resultat">
                                <i class="bi bi-check-circle me-2"></i>
                                <span id="conv_resultat_text" class="fs-6"></span>
                            </div>
                            <div class="alert alert-danger d-none" id="conv_erreur">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <span id="conv_erreur_text"></span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-success" onclick="calculerConversion()">
                        <i class="bi bi-arrow-left-right me-1"></i> Convertir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-light-subtle { background-color: #fbfbfb; }
        .table th { font-size: 0.75rem; text-transform: uppercase; color: #6c757d; }
        .border-end { border-right: 1px solid #f0f0f0 !important; }
        .btn-group .btn { border-radius: 4px; margin: 0 1px; }
    </style>
    @push('scripts')
        <script>
            function loadDescription() {
                const select  = document.getElementById('conv_description_id');
                const option  = select.options[select.selectedIndex];

                document.getElementById('conv_effectif').value = option.dataset.effectif;
                document.getElementById('conv_unite_base').innerText = option.dataset.unite;

                // Reset
                document.getElementById('conv_resultat').classList.add('d-none');
                document.getElementById('conv_erreur').classList.add('d-none');
                document.getElementById('conv_formule_box').style.display = 'none';
                document.getElementById('conv_contenance').value = '';
            }

            function updateLabel() {
                const cibleSelect = document.getElementById('conv_unite_cible');
                const symbol = cibleSelect.options[cibleSelect.selectedIndex]?.dataset.symbol ?? '—';
                document.getElementById('conv_unite_cible_label').innerText = symbol;
            }

            function calculerConversion() {
                const descSelect = document.getElementById('conv_description_id');
                const cibleSelect = document.getElementById('conv_unite_cible');
                const effectif = parseFloat(document.getElementById('conv_effectif').value);
                const contenance = parseFloat(document.getElementById('conv_contenance').value);

                const resultatBox = document.getElementById('conv_resultat');
                const erreurBox = document.getElementById('conv_erreur');
                const formuleBox = document.getElementById('conv_formule_box');

                // Reset
                resultatBox.classList.add('d-none');
                erreurBox.classList.add('d-none');
                formuleBox.style.display = 'none';

                // Vérifications
                if (!descSelect.value) { afficherErreur('Choisir une description.'); return; }
                if (!cibleSelect.value) { afficherErreur('Choisir une unité cible.'); return; }
                if (isNaN(contenance) || contenance <= 0) { afficherErreur('Entrer la contenance par unité.'); return; }

                const uniteSource = document.getElementById('conv_unite_base').innerText;
                const uniteCible  = cibleSelect.options[cibleSelect.selectedIndex].dataset.symbol;

                // Règle de trois : effectif × contenance = résultat
                const resultat = effectif * contenance;

                // Afficher la formule
                document.getElementById('conv_formule').innerHTML =
                    `${effectif} ${uniteSource} × ${contenance} ${uniteCible}/${uniteSource} = <strong>${resultat.toLocaleString()} ${uniteCible}</strong>`;
                formuleBox.style.display = 'block';

                // Afficher le résultat
                document.getElementById('conv_resultat_text').innerHTML =
                    `<strong>${effectif} ${uniteSource}</strong> =
             <strong class="text-success fs-5">${resultat.toLocaleString()} ${uniteCible}</strong>`;
                resultatBox.classList.remove('d-none');
            }

            function afficherErreur(msg) {
                document.getElementById('conv_erreur_text').innerText = msg;
                document.getElementById('conv_erreur').classList.remove('d-none');
            }
        </script>
    @endpush
@endsection
