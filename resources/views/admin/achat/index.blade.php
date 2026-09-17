@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Achats</h1>
            <a href="{{ route('achat.create') }}" class="btn btn-primary btn-sm">+ Nouvel achat</a>
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
                        <th>Fournisseur</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($achats as $achat)
                        <tr>
                            <td>{{ $achat->id }}</td>
                            <td>{{ $achat->fournisseur->nom ?? '—' }}</td>
                            <td>{{ $achat->date_achat?->format('d/m/Y H:i') }}</td>
                            <td>{{ number_format($achat->total, 0, ',', ' ') }} Ar</td>
                            <td>
                                @php
                                    $badgeClass = match($achat->statut) {
                                        'en_attente' => 'bg-warning text-dark',
                                        'valide'     => 'bg-success',
                                        default      => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', $achat->statut) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('achat.show', $achat) }}" class="btn btn-sm btn-outline-primary">
                                    Voir
                                </a>
                                @if ($achat->statut === 'en_attente')
                                    <form action="{{ route('achat.valider', $achat) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success"
                                                onclick="return confirm('Valider la réception de cet achat ? Le stock sera mis à jour.');">
                                            Valider
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucun achat enregistré.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
