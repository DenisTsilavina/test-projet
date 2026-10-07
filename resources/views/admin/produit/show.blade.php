@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">{{ $produit->nom }}</h1>
            <div>
                <a href="{{ route('produit.edit', $produit) }}" class="btn btn-sm btn-outline-secondary">Modifier</a>
                <a href="{{ route('produit.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Retour</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                @if ($produit->type === 'stock')
                    <div class="card">
                        <div class="card-header">Stock lié</div>
                        <div class="card-body">
                            @if ($produit->produitStock)
                                @php $stock = $produit->produitStock->stock; @endphp
                                <p class="mb-3"><strong>{{ $stock->name_stock ?? '—' }}</strong></p>

                                {{-- AJOUT : détail magasin / réserve --}}
                                <div class="row text-center mb-3">
                                    <div class="col-4">
                                        <div class="p-2 border rounded bg-light">
                                            <div class="text-muted small">🏪 Magasin</div>
                                            <div class="fw-bold">{{ number_format($stock->quantite_magasin, 2, ',', ' ') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 border rounded bg-light">
                                            <div class="text-muted small">📦 Réserve</div>
                                            <div class="fw-bold">{{ number_format($stock->quantite, 2, ',', ' ') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 border rounded bg-success bg-opacity-10">
                                            <div class="text-muted small">Total</div>
                                            <div class="fw-bold">{{ number_format($stock->quantite_totale, 2, ',', ' ') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <p class="mb-0 text-muted">Seuil d'alerte : {{ $produit->produitStock->seuil_alerte }}</p>

                                @if ($produit->produitStock->enAlerte())
                                    <div class="alert alert-warning mt-3 mb-0">⚠️ Stock sous le seuil d'alerte.</div>
                                @endif
                            @else
                                <p class="text-muted mb-0">Aucun stock lié.</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-header">Recette (composition)</div>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Ingrédient</th>
                                    <th class="text-end">Quantité nécessaire</th>
                                    <th>Unité</th>
                                    <th class="text-end">🏪 Magasin</th>
                                    <th class="text-end">📦 Réserve</th>
                                    <th class="text-end">Total dispo</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($produit->produitFini->compositions ?? [] as $composition)
                                    <tr>
                                        <td>{{ $composition->stock->name_stock ?? '—' }}</td>
                                        <td class="text-end">{{ $composition->quantite_necessaire }}</td>
                                        <td>{{ $composition->unite }}</td>
                                        <td class="text-end">{{ number_format($composition->stock->quantite_magasin ?? 0, 2, ',', ' ') }}</td>
                                        <td class="text-end">{{ number_format($composition->stock->quantite ?? 0, 2, ',', ' ') }}</td>
                                        <td class="text-end fw-bold">{{ number_format($composition->stock->quantite_totale ?? 0, 2, ',', ' ') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">Aucun ingrédient défini.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($produit->produitFini?->temps_preparation)
                            <div class="card-body border-top">
                                <span class="text-muted">Temps de préparation : {{ $produit->produitFini->temps_preparation }} min</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Informations</div>
                    <div class="card-body">
                        <p class="mb-1">Type :
                            <span class="badge {{ $produit->type === 'stock' ? 'bg-info text-dark' : 'bg-primary' }}">
                            {{ $produit->type }}
                        </span>
                        </p>
                        <p class="mb-1">Prix de vente : <strong>{{ number_format($produit->prix_vente, 0, ',', ' ') }} Ar</strong></p>
                        <p class="mb-1">Statut :
                            <span class="badge {{ $produit->statut === 'actif' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $produit->statut }}
                        </span>
                        </p>
                        @if ($produit->description)
                            <p class="mb-0 text-muted small mt-2">{{ $produit->description }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
