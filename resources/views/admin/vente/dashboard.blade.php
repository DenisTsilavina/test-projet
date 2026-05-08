@extends('layouts.admin.admin-layout')
@section('title', 'Tableau de bord')

@push('styles')
    <style>
        /* Grille des métriques : 4 colonnes égales */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: #fff;
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .metric-card .mc-label {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .metric-card .mc-value {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }

        .metric-card .mc-sub {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 6px;
        }

        /* Couleur spécifique pour le revenu */
        .card-revenue .mc-value { color: #10b981; }

        /* Organisation du contenu : Tableau en large, Accès rapide en dessous ou à côté */
        .dashboard-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .card {
            background: #fff;
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Tableau plein écran */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #f8fafc;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            padding: 12px;
            border-bottom: 2px solid #f1f5f9;
        }
        tbody td { padding: 14px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

        .revenue-positive { color: #10b981; font-weight: 600; }

        @media (max-width: 1024px) {
            .metrics-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .metrics-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')

    <div class="dashboard-container">

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="mc-label">Total ventes</div>
                <div class="mc-value">{{ $totalVente }}</div>
                <div class="mc-sub">Nombre total de transactions</div>
            </div>

            <div class="metric-card card-revenue">
                <div class="mc-label">Revenus nets</div>
                <div class="mc-value">{{ number_format($totalRevenue, 0, ',', ' ') }} Ar</div>
                <div class="mc-sub">Bénéfice après achat</div>
            </div>

            <div class="metric-card">
                <div class="mc-label">Chiffre d'affaires</div>
                <div class="mc-value">{{ number_format($venteRecentes->sum('prix_total'), 0, ',', ' ') }} Ar</div>
                <div class="mc-sub">Volume de vente récent</div>
            </div>

            <div class="metric-card">
                <div class="mc-label">Articles vendus</div>
                <div class="mc-value">{{ $venteRecentes->sum('effectif') }}</div>
                <div class="mc-sub">Quantité totale écoulée</div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">
                <span><i class="bi bi-clock-history me-2"></i>Ventes récentes</span>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.vente.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Nouvelle vente
                    </a>
                    <a href="{{ route('admin.vente.index') }}" class="btn btn-outline-secondary btn-sm">Voir tout</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Description</th>
                        <th>Catégorie</th>
                        <th>Qté</th>
                        <th>Prix Unit.</th>
                        <th>Total</th>
                        <th>Revenu</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    @forelse($venteRecentes as $vente)
                        @php
                            $prixAchat = (int) ($vente->categorie->prix_achat ?? 0);
                            $revenu = ((int)$vente->prix - $prixAchat) * (int)$vente->effectif;
                        @endphp
                        <tr>
                            <td class="text-muted small">#V-{{ str_pad($vente->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="text-start fw-medium">{{ $vente->description->description ?? '—' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $vente->categorie->stock_categorie ?? '—' }}</span></td>
                            <td>{{ $vente->effectif }}</td>
                            <td>{{ number_format($vente->prix, 0, ',', ' ') }} Ar</td>
                            <td class="fw-bold">{{ number_format($vente->prix_total, 0, ',', ' ') }} Ar</td>
                            <td class="revenue-positive">+ {{ number_format($revenu, 0, ',', ' ') }} Ar</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Aucune vente enregistrée.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── 3. Accès Rapide (Optionnel en bas) ── --}}
        <div class="row">
            <div class="col-md-4">
                <div class="card border-0 bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title text-white">Action rapide</h5>
                        <p class="small">Besoin d'ajouter un nouveau produit en stock ?</p>
                        <a href="{{ route('stock.create') }}" class="btn btn-light btn-sm w-100">Aller au Stock</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
