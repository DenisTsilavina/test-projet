@extends('layouts.app')
@section('title', 'Tableau de bord')

@push('styles')
    <style>
        .metrics { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 1.5rem; }
        .metric-card {
            background: var(--surface); border: 0.5px solid var(--border);
            border-radius: var(--radius-lg); padding: 1rem 1.25rem;
        }
        .metric-card .mc-label { font-size: 12px; color: var(--text-muted); margin-bottom: 6px; }
        .metric-card .mc-value { font-size: 22px; font-weight: 600; color: var(--text); }
        .metric-card .mc-sub   { font-size: 11px; color: var(--text-muted); margin-top: 4px; }
        .metric-card.green .mc-value { color: var(--accent-dark); }

        .grid-2 { display: grid; grid-template-columns: 1fr 360px; gap: 1rem; }
        @media (max-width: 900px) { .grid-2 { grid-template-columns: 1fr; } }

        .card { background: var(--surface); border: 0.5px solid var(--border); border-radius: var(--radius-lg); padding: 1.25rem; }
        .card-title { font-size: 14px; font-weight: 600; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 0.5px solid var(--border); }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th { text-align: left; font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; padding: 0 8px 8px; border-bottom: 0.5px solid var(--border); }
        tbody td { padding: 10px 8px; border-bottom: 0.5px solid var(--border); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: var(--bg); }

        .btn { display: inline-block; padding: 8px 16px; border-radius: var(--radius-md); font-size: 13px; font-weight: 500; text-decoration: none; border: 0.5px solid var(--border); cursor: pointer; background: var(--surface); color: var(--text); transition: background .15s; }
        .btn:hover { background: var(--bg); }
        .btn-primary { background: var(--accent); color: #fff; border-color: var(--accent); }
        .btn-primary:hover { background: var(--accent-dark); }

        .revenue-positive { color: var(--accent-dark); font-weight: 600; }
    </style>
@endpush

@section('content')

    {{-- ── Métriques ── --}}
    <div class="metrics bg-dark-subtle">
        <div class="metric-card border-b-2">
            <div class="mc-label border-s-black">Total ventes</div>
            <div class="mc-value">{{ $totalVente }}</div>
            <div class="mc-sub">Toutes périodes</div>
        </div>

        <div class="metric-card b">
            <div class="mc-label">Revenus nets</div>
            <div class="mc-value">{{ number_format($totalRevenue, 0, ',', ' ') }} Ar</div>
            <div class="mc-sub">Bénéfice cumulé</div>
        </div>

        <div class="metric-card">
            <div class="mc-label">Chiffre d'affaires</div>
            <div class="mc-value">{{ number_format($venteRecentes->sum('prix_total'), 0, ',', ' ') }} Ar</div>
            <div class="mc-sub">Prix de vente total</div>
        </div>

        <div class="metric-card">
            <div class="mc-label">Articles vendus</div>
            <div class="mc-value">{{ $venteRecentes->sum('effectif') }}</div>
            <div class="mc-sub">Unités écoulées</div>
        </div>
    </div>

    {{-- ── Contenu principal ── --}}
    <div class="grid-2">

        {{-- Tableau des ventes récentes --}}
        <div class="card lg:px-8">
            <div class="card-title" style="display:flex;justify-content:space-between;align-items:center;">
                Ventes récentes
                <a href="{{ route('admin.vente.index') }}" class="btn btn-dark">Voir tout</a>
            </div>

            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th>Catégorie</th>
                        <th>Qté</th>
                        <th>Prix unit.</th>
                        <th>Total</th>
                        <th>Revenu</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @forelse($venteRecentes as $vente)
                        @php
                            $prixAchat = (int) ($vente->categorie->prix_achat ?? 0);
                            $revenu    = ((int)$vente->prix - $prixAchat) * (int)$vente->effectif;
                        @endphp
                        <tr>
                            <td style="color:var(--text-muted);">
                                #V-{{ str_pad($vente->id, 4, '0', STR_PAD_LEFT) }}
                            </td>

                            {{-- ← relation description --}}
                            <td>{{ $vente->description->description ?? '—' }}</td>

                            {{-- ← relation categorie (remplace sousCategory) --}}
                            <td>{{ $vente->categorie->stock_categorie ?? '—' }}</td>

                            <td>{{ $vente->effectif }}</td>

                            <td>{{ number_format($vente->prix, 0, ',', ' ') }} Ar</td>

                            <td style="font-weight:600;">
                                {{ number_format($vente->prix_total, 0, ',', ' ') }} Ar
                            </td>

                            {{-- ← prix_achat depuis categorie, cast int --}}
                            <td class="revenue-positive">
                                +{{ number_format($revenu, 0, ',', ' ') }} Ar
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--text-muted);padding:2rem;">
                                Aucune vente enregistrée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Accès rapide --}}
        <div class="card gray">
            <div class="card-title ">Accès rapide</div>
            <p style="font-size:13px;margin-bottom:1rem;">
                Enregistrez une vente directement depuis cette page.
            </p>
            <a href="{{ route('admin.vente.create') }}"
               class="btn btn-primary"
               style="display:block;text-align:center;margin-bottom:10px;">
                + Nouvelle vente
            </a>
            <a class="btn btn-danger" href="{{ route('admin.vente.index') }}"
               style="display:block;text-align:center;">
                Voir l'historique
            </a>
        </div>

    </div>

@endsection
