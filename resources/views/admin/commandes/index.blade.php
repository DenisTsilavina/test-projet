@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Commandes</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($commandes as $commande)
                        <tr>
                            <td>{{ $commande->id }}</td>
                            <td>{{ $commande->client->name ?? '—' }}</td>
                            <td>{{ $commande->date_commande?->format('d/m/Y H:i') }}</td>
                            <td>{{ number_format($commande->total, 0, ',', ' ') }} Ar</td>
                            <td>
                                @php
                                    $badgeClass = match($commande->statut) {
                                        'en_attente'      => 'bg-warning text-dark',
                                        'infos_demandees' => 'bg-info text-dark',
                                        'en_cours'        => 'bg-success',
                                        'livre'           => 'bg-primary',
                                        'annule'          => 'bg-secondary',
                                        default           => 'bg-light text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', $commande->statut) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.commandes.show', $commande) }}" class="btn btn-sm btn-outline-primary">
                                    Voir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucune commande pour le moment.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
