@extends('layouts.client') {{-- Remplace par ton layout principal --}}

@section('content')
    <div class="container py-4">

        {{-- Fil d'Ariane / Retour --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            {{-- route('commande.index') --}}
            <a href="" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour à mes commandes
            </a>
            <span class="badge bg-dark fs-6">Référence : {{ $commande->reference }}</span>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- 1. TRACKER DE STATUT DU CLIENT --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">Statut de votre commande</h5>

                <div class="row text-center position-relative">
                    @php
                        $statutsOrdered = ['en_attente', 'en_cours', 'livre'];
                        $currentIndex = array_search($commande->statut, $statutsOrdered);
                        $isAnnule = $commande->statut === 'annule';
                    @@endphp

                    @if($isAnnule)
                        <div class="col-12 text-center py-3">
                            <span class="badge bg-danger fs-5 py-2 px-4">Commande Annulée</span>
                            <p class="text-muted mt-2">Cette commande a été annulée. Contactez le support pour plus d'informations.</p>
                        </div>
                    @else
                        {{-- Barre de progression d'arrière plan --}}
                        <div class="position-absolute top-50 start-0 translate-y-middle w-100 px-5 d-none d-md-block" style="z-index: 1; height: 4px; background-color: #e9ecef;">
                            <div class="bg-primary h-100" style="width: {{ $currentIndex * 50 }}%; transition: width 0.5s;"></div>
                        </div>

                        {{-- Étape 1 : En attente --}}
                        <div class="col-md-4 position-relative mb-3 mb-md-0" style="z-index: 2;">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center {{ $currentIndex >= 0 ? 'bg-primary text-white' : 'bg-light text-muted' }}" style="width: 40px; height: 40px;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h6 class="mt-2 {{ $currentIndex >= 0 ? 'text-primary fw-bold' : 'text-muted' }}">1. En attente</h6>
                            <small class="text-muted">Prise en compte de votre demande</small>
                        </div>

                        {{-- Étape 2 : En cours --}}
                        <div class="col-md-4 position-relative mb-3 mb-md-0" style="z-index: 2;">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center {{ $currentIndex >= 1 ? 'bg-primary text-white' : 'bg-light text-muted' }}" style="width: 40px; height: 40px;">
                                <i class="fas fa-cog fa-spin"></i>
                            </div>
                            <h6 class="mt-2 {{ $currentIndex >= 1 ? 'text-primary fw-bold' : 'text-muted' }}">2. En cours</h6>
                            <small class="text-muted">Préparation et main d'œuvre</small>
                        </div>

                        {{-- Étape 3 : Livré --}}
                        <div class="col-md-4 position-relative" style="z-index: 2;">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center {{ $currentIndex >= 2 ? 'bg-success text-white' : 'bg-light text-muted' }}" style="width: 40px; height: 40px;">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <h6 class="mt-2 {{ $currentIndex >= 2 ? 'text-success fw-bold' : 'text-muted' }}">3. Livré</h6>
                            <small class="text-muted">Prêt / Reçu</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            {{-- COLONNE GAUCHE : Informations de base & Formulaire d'Ajout --}}
            <div class="col-lg-5 mb-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary border-bottom pb-2 mb-3">Détails généraux</h5>
                        <p><strong>Désignation :</strong> {{ $commande->designation }}</p>
                        <p><strong>Quantité globale demandée :</strong> {{ $commande->quantite }}</p>
                        <p><strong>Notes / Consignes :</strong></p>
                        <div class="bg-light p-3 rounded text-muted italic">
                            {{ $commande->note ?? 'Aucune note complémentaire laissée.' }}
                        </div>
                    </div>
                </div>

                {{-- FORMULAIRE D'AJOUT DES LIGNES (Visible uniquement si "En attente") --}}
                @if($commande->statut === 'en_attente')
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">Ajouter des compléments</h5>

                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pills-ingredient-tab" data-bs-toggle="pill" data-bs-target="#pills-ingredient" type="button" role="tab">
                                        <i class="fas fa-apple-alt me-1"></i> Ingrédient
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-oeuvre-tab" data-bs-toggle="pill" data-bs-target="#pills-oeuvre" type="button" role="tab">
                                        <i class="fas fa-tools me-1"></i> Main d'œuvre
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="pills-tabContent">
                                {{-- Onglet Ingrédient --}}
                                <div class="tab-pane fade show active" id="pills-ingredient" role="tabpanel">
                                    <form action="{{ route('commandes.lignes.store', $commande) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="ingredient">

                                        <div class="mb-3">
                                            <label class="form-label">Sélectionner un ingrédient du stock</label>
                                            <select name="stock_id" class="form-select" required>
                                                <option value="">-- Choisir --</option>
                                                @foreach($stocks as $stock)
                                                    <option value="{{ $stock->id }}">{{ $stock->nom }} (Prix: {{ number_format($stock->prix, 2) }} €)</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Quantité nécessaire</label>
                                            <input type="number" name="quantite" class="form-control" min="1" value="1" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Ajouter l'ingrédient</button>
                                    </form>
                                </div>

                                {{-- Onglet Main d'œuvre --}}
                                <div class="tab-pane fade" id="pills-oeuvre" role="tabpanel">
                                    <form action="{{ route('commandes.lignes.store', $commande) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="main_oeuvre">

                                        <div class="mb-3">
                                            <label class="form-label">Libellé de la prestation</label>
                                            <input type="text" name="libelle" class="form-control" placeholder="Ex: Montage, Peinture..." required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Quantité / Heures</label>
                                                <input type="number" name="quantite" class="form-control" min="1" value="1" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Prix unitaire estimé (€)</label>
                                                <input type="number" step="0.01" name="prix_unitaire" class="form-control" min="0" required>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-secondary w-100">Ajouter la main d'œuvre</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- COLONNE DROITE : Tableau récapitulatif des lignes de frais --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-primary border-bottom pb-2 mb-3">Composition de la commande</h5>

                        @if($commande->lignes->isEmpty())
                            <div class="text-center py-5 my-auto">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucune ligne n'a encore été ajoutée à cette commande.</p>
                            </div>
                        @else
                            <div class="table-responsive flex-grow-1">
                                <table class="table align-middle">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Libellé</th>
                                        <th class="text-center">Qté</th>
                                        <th class="text-end">P.U (€)</th>
                                        <th class="text-end">Total (€)</th>
                                        @if($commande->statut === 'en_attente') <th></th> @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($commande->lignes as $ligne)
                                        <tr>
                                            <td>
                                                <span class="badge {{ $ligne->type === 'ingredient' ? 'bg-info text-dark' : 'bg-warning text-dark' }}">
                                                    {{ $ligne->type === 'ingredient' ? 'Ingrédient' : 'Main d\'œuvre' }}
                                                </span>
                                            </td>
                                            <td>{{ $ligne->libelle }}</td>
                                            <td class="text-center">{{ $ligne->quantite }}</td>
                                            <td class="text-end">{{ number_format($ligne->prix_unitaire, 2) }}</td>
                                            <td class="text-end fw-bold">{{ number_format($ligne->quantite * $ligne->prix_unitaire, 2) }}</td>
                                            @if($commande->statut === 'en_attente')
                                                <td class="text-end">
                                                    <form action="{{ route('commandes.lignes.destroy', [$commande, $ligne]) }}" method="POST" onsubmit="return confirm('Supprimer cet élément ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm text-danger p-0 border-0">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Section Totaux --}}
                            <div class="border-top pt-3 mt-auto">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Sous-total Ingrédients :</span>
                                    <span>{{ number_format($commande->montant_ingredients, 2) }} €</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Sous-total Main d'œuvre :</span>
                                    <span>{{ number_format($commande->montant_main_oeuvre, 2) }} €</span>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-2 fw-bold fs-5">
                                    <span>Montant Global Estimé :</span>
                                    <span class="text-success">{{ number_format($commande->montant, 2) }} €</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
