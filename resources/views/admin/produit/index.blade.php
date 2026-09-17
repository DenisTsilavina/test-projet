@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Produits</h1>
            <a href="{{ route('produit.create') }}" class="btn btn-primary btn-sm">+ Nouveau produit</a>
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
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Détail</th>
                        <th class="text-end">Prix de vente</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($produits as $produit)
                        <tr>
                            <td>{{ $produit->nom }}</td>
                            <td>
                                <span class="badge {{ $produit->type === 'stock' ? 'bg-info text-dark' : 'bg-primary' }}">
                                    {{ $produit->type }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                @if ($produit->type === 'stock')
                                    Stock lié : {{ $produit->produitStock->stock->name_stock ?? '—' }}
                                    ({{ $produit->produitStock->quantite_disponible ?? 0 }} dispo)
                                @else
                                    {{ $produit->produitFini->compositions->count() ?? 0 }} ingrédient(s)
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($produit->prix_vente, 0, ',', ' ') }} Ar</td>
                            <td>
                                <span class="badge {{ $produit->statut === 'actif' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $produit->statut }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('produit.show', $produit) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                                <a href="{{ route('produit.edit', $produit) }}" class="btn btn-sm btn-outline-secondary">Modifier</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucun produit pour le moment.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
