@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Commande #{{ $commande->id }}</h1>
            <a href="{{ route('admin.commandes.index') }}" class="btn btn-sm btn-outline-secondary">
                &larr; Retour à la liste
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">Articles commandés</div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Produit</th>
                                <th>Type</th>
                                <th class="text-end">Quantité</th>
                                <th class="text-end">Prix unitaire</th>
                                <th class="text-end">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($commande->lignes as $ligne)
                                <tr>
                                    <td>{{ $ligne->produit->nom ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $ligne->type_produit }}</span>
                                    </td>
                                    <td class="text-end">{{ $ligne->quantite }}</td>
                                    <td class="text-end">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} Ar</td>
                                    <td class="text-end">{{ number_format($ligne->total_ligne, 0, ',', ' ') }} Ar</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Total commande</td>
                                <td class="text-end">{{ number_format($commande->total, 0, ',', ' ') }} Ar</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                @if ($commande->paiements->isNotEmpty())
                    <div class="card mb-3">
                        <div class="card-header">Paiements</div>
                        <ul class="list-group list-group-flush">
                            @foreach ($commande->paiements as $paiement)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ $paiement->date_paiement?->format('d/m/Y') }} — {{ $paiement->mode_paiement ?? 'N/A' }}</span>
                                    <span>{{ number_format($paiement->montant, 0, ',', ' ') }} Ar</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($commande->transports->isNotEmpty())
                    <div class="card mb-3">
                        <div class="card-header">Transport</div>
                        <ul class="list-group list-group-flush">
                            @foreach ($commande->transports as $transport)
                                <li class="list-group-item">
                                    {{ $transport->depart ?? '—' }} &rarr; {{ $transport->arrivee ?? '—' }}
                                    <span class="text-muted">({{ $transport->transporteur ?? 'N/A' }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Client</div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ $commande->client->name ?? '—' }}</strong></p>
                        <p class="mb-1 text-muted">{{ $commande->client->email ?? '' }}</p>
                        <p class="mb-0 text-muted">{{ $commande->client->telephone ?? '' }}</p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Statut</div>
                    <div class="card-body">
                        <p class="mb-2">
                            <span class="badge bg-secondary">{{ str_replace('_', ' ', $commande->statut) }}</span>
                        </p>

                        @if ($commande->remarque)
                            <p class="text-muted small mb-3">{{ $commande->remarque }}</p>
                        @endif

                        @if (in_array($commande->statut, ['en_attente', 'infos_demandees']))
                            <form action="{{ route('admin.commandes.confirmer', $commande) }}" method="POST" class="mb-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-100">
                                    Confirmer (déduit le stock)
                                </button>
                            </form>

                            <form action="{{ route('admin.commandes.demander-infos', $commande) }}" method="POST" class="mb-2">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="remarque" class="form-control form-control-sm mb-2" placeholder="Précisez ce qu'il manque...">
                                <button type="submit" class="btn btn-outline-secondary w-100">
                                    Demander des précisions
                                </button>
                            </form>

                            <form action="{{ route('admin.commandes.refuser', $commande) }}" method="POST"
                                  onsubmit="return confirm('Refuser cette commande ?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    Refuser
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
