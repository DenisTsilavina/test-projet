@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Achat #{{ $achat->id }}</h1>
            <a href="{{ route('achat.index') }}" class="btn btn-sm btn-outline-secondary">&larr; Retour</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-3">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Articles</div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Stock</th>
                                <th class="text-end">Quantité</th>
                                <th class="text-end">Prix unitaire</th>
                                <th class="text-end">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($achat->lignes as $ligne)
                                <tr>
                                    <td>{{ $ligne->stock->name_stock ?? '—' }}</td>
                                    <td class="text-end">{{ $ligne->quantite }}</td>
                                    <td class="text-end">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} Ar</td>
                                    <td class="text-end">{{ number_format($ligne->total_ligne, 0, ',', ' ') }} Ar</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Total</td>
                                <td class="text-end">{{ number_format($achat->total, 0, ',', ' ') }} Ar</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Fournisseur</div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ $achat->fournisseur->nom ?? '—' }}</strong></p>
                        <p class="mb-0 text-muted">{{ $achat->fournisseur->telephone ?? '' }}</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Statut</div>
                    <div class="card-body">
                        @php
                            $badgeClass = $achat->statut === 'valide' ? 'bg-success' : 'bg-warning text-dark';
                        @endphp
                        <span class="badge {{ $badgeClass }} mb-3">{{ str_replace('_', ' ', $achat->statut) }}</span>

                        @if ($achat->remarque)
                            <p class="text-muted small">{{ $achat->remarque }}</p>
                        @endif

                        @if ($achat->statut === 'en_attente')
                            <form action="{{ route('achat.valider', $achat) }}" method="POST"
                                  onsubmit="return confirm('Valider la réception ? Le stock sera mis à jour.');">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">Valider la réception</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
