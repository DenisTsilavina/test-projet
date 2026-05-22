@extends('layouts.admin.admin-layout')
@section('title', 'Historique des ventes')

@push('styles')
    <style>
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .page-header h2 { font-size: 20px; font-weight: 600; color: var(--text); }
        .page-header p  { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

        .card {
            background: var(--surface); border: 0.5px solid var(--border);
            border-radius: var(--radius-lg); padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        /* ── Boutons ── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 8px 16px; border-radius: var(--radius-md);
            font-size: 13px; font-weight: 500; text-decoration: none;
            border: 0.5px solid var(--border); cursor: pointer;
            background: var(--surface); color: var(--text);
            transition: all 0.2s ease;
        }
        .btn:hover { background: var(--bg); }
        .btn-primary { background: var(--accent); color: #fff; border-color: var(--accent); }
        .btn-primary:hover { background: var(--accent-dark); }
        .btn-sm { padding: 5px 10px; font-size: 12px; border-radius: var(--radius-sm); }
        .btn-danger { background: #FFF5F5; color: #E53E3E; border-color: #FED7D7; }
        .btn-danger:hover { background: #E53E3E; color: #fff; border-color: #E53E3E; }
        .btn-outline {
            background: transparent; border: 0.5px solid var(--border);
            color: var(--text); font-size: 12px; padding: 4px 10px;
            border-radius: var(--radius-sm); text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-outline:hover { background: var(--bg); border-color: var(--text-muted); }

        /* ── Barre de résumé ── */
        .summary-bar {
            display: flex; gap: 1.5rem; flex-wrap: wrap;
            margin-bottom: 1.5rem;
            background: var(--accent-light); border-radius: var(--radius-md);
            padding: 1rem 1.25rem; font-size: 13px;
            border-left: 4px solid var(--accent);
        }
        .summary-item { display: flex; flex-direction: column; gap: 2px; }
        .summary-label { color: var(--text-muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; }
        .summary-value { font-size: 15px; font-weight: 600; color: var(--text); }
        .summary-value strong { color: var(--accent-dark); }

        /* ── Tableau principal ── */
        .table-responsive { width: 100%; overflow-x: auto; }
        .ventes-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
        .ventes-table thead th {
            font-size: 11px; font-weight: 600;
            color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em;
            padding: 12px 10px; border-bottom: 1px solid var(--border);
            background: rgba(0,0,0,0.01);
        }
        .ventes-table tbody td { padding: 12px 10px; vertical-align: top; }
        .ventes-table .vente-row { border-bottom: 0.5px solid var(--border); }
        .ventes-table .vente-row:last-of-type { border-bottom: none; }
        .ventes-table .vente-row:hover td { background: var(--bg); }

        /* ── Listes Produits & Quantités alignées ── */
        .produit-list, .quantite-list { display: flex; flex-direction: column; gap: 6px; }
        .produit-item { font-weight: 500; color: var(--text); height: 20px; display: flex; align-items: center; }

        .quantite-badge {
            font-size: 11px; color: var(--text-muted); background: var(--bg);
            padding: 2px 6px; border-radius: 4px; border: 0.5px solid var(--border);
            width: fit-content; height: 20px; display: inline-flex; align-items: center;
        }

        /* ── Badges Mode de Paiement ── */
        .badge {
            display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 99px;
            font-size: 11px; font-weight: 600; letter-spacing: 0.02em;
        }
        .badge-espece      { background: #E8F5E9; color: #1B5E20; }
        .badge-mvola       { background: #FFF3E0; color: #E65100; }
        .badge-airtel      { background: #FCE4EC; color: #880E4F; }
        .badge-virement    { background: #E3F2FD; color: #0D47A1; }

        .revenue-positive { color: var(--accent-dark); font-weight: 600; font-size: 14px; }
        .muted { color: var(--text-muted); font-size: 12px; }
        .user-title { font-weight: 500; color: var(--text); }

        .empty-state {
            text-align: center; padding: 4rem 1rem;
            color: var(--text-muted); font-size: 14px;
        }
        .actions-cell { display: flex; flex-direction: column; gap: 4px; }
    </style>
@endpush

@section('content')

    {{-- ══ En-tête ══ --}}
    <div class="page-header">
        <div>
            <h2>Historique des ventes</h2>
            <p>{{ $varotra->count() }} vente(s) enregistrée(s)</p>
        </div>
        <a href="{{ route('admin.vente.create') }}" class="btn btn-primary">
            <svg style="margin-right: 6px;" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvelle vente
        </a>
    </div>

    {{-- ══ Barre de résumé ══ --}}
    @if($varotra->count() > 0)
        <div class="summary-bar">
            <div class="summary-item">
                <span class="summary-label">Chiffre d'affaires</span>
                <span class="summary-value"><strong>{{ number_format($varotra->sum('total_general'), 0, ',', ' ') }} Ar</strong></span>
            </div>
            <div class="summary-item" style="border-left: 1px solid var(--border); padding-left: 1.5rem;">
                <span class="summary-label">Volume Transactions</span>
                <span class="summary-value">{{ $varotra->count() }} ventes</span>
            </div>
            <div class="summary-item" style="border-left: 1px solid var(--border); padding-left: 1.5rem;">
                <span class="summary-label">Articles vendus</span>
                <span class="summary-value">{{ $varotra->flatMap->lignes->sum('quantite') }} unités</span>
            </div>
        </div>
    @endif

    {{-- ══ Tableau principal ══ --}}
    <div class="card">
        <div class="table-responsive">
            <table class="ventes-table">
                <thead>
                <tr>
                    <th style="width:85px;"># Réf</th>
                    <th>Auteur / Client</th>
                    <th>Produits</th>
                    <th style="width:110px;">Quantité</th>
                    <th style="width:120px;">Paiement</th>
                    <th style="width:130px;">Total général</th>
                    <th style="width:130px;">Date</th>
                    <th style="width:90px; text-align: right;">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($varotra as $vente)
                    <tr class="vente-row">

                        {{-- ID --}}
                        <td class="muted" style="font-family: monospace; font-size: 12px;">
                            #V-{{ str_pad($vente->id, 4, '0', STR_PAD_LEFT) }}
                        </td>

                        {{-- Client / Vendeur --}}
                        <td>
                            @if($vente->vendeur)
                                <div class="user-title">{{ $vente->vendeur->name }}</div>
                                <div class="muted">Vendeur</div>
                            @elseif($vente->clientAnon)
                                <div class="user-title">
                                    {{ $vente->clientAnon->name ?? $vente->clientAnon->telephone ?? '—' }}
                                </div>
                                <div class="muted">Client</div>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>

                        {{-- Colonne Produits --}}
                        <td>
                            @if($vente->lignes->count())
                                <div class="produit-list">
                                    @foreach($vente->lignes as $ligne)
                                        <div class="produit-item">{{ $ligne->produit_nom }}</div>
                                    @endforeach
                                </div>
                            @else
                                <span class="muted">Aucun produit</span>
                            @endif
                        </td>

                        {{-- Colonne Quantités séparée (Corrigée de l'erreur $) --}}
                        <td>
                            @if($vente->lignes->count())
                                <div class="quantite-list">
                                    @foreach($vente->lignes as $ligne)
                                        <div>
                                            <span class="quantite-badge">
                                                {{ $ligne->quantite }} {{ $ligne->unite_symbol }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>

                        {{-- Mode de paiement --}}
                        <td>
                            @php
                                $badgeClass = match($vente->mode_paiement) {
                                    'espece' => 'badge-espece',
                                    'mvola' => 'badge-mvola',
                                    'airtel_money' => 'badge-airtel',
                                    'virement' => 'badge-virement',
                                    default => '',
                                };
                                $labels = [
                                    'espece' => 'Espèces',
                                    'mvola' => 'MVola',
                                    'airtel_money' => 'Airtel Money',
                                    'virement' => 'Virement Bancaire',
                                ];
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ $labels[$vente->mode_paiement] ?? ucfirst($vente->mode_paiement) }}
                            </span>
                        </td>

                        {{-- Total général --}}
                        <td class="revenue-positive">
                            {{ number_format($vente->total_general, 0, ',', ' ') }} Ar
                        </td>

                        {{-- Date --}}
                        <td class="muted">
                            {{ $vente->created_at->format('d/m/Y') }}
                            <div style="font-size: 11px; opacity: 0.6; margin-top: 1px;">{{ $vente->created_at->format('H:i') }}</div>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="actions-cell" style="align-items: flex-end;">
                                <a href="{{ route('admin.vente.recu', $vente->id) }}" class="btn-outline" style="text-align:center; width: 100%;">Reçu</a>

                                <form method="POST"
                                      action="{{ route('admin.vente.destroy', $vente->id) }}"
                                      onsubmit="return confirm('Supprimer cette vente et restaurer le stock ?')"
                                      style="margin: 0; width: 100%;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" style="width: 100%; padding: 4px;">Supprimer</button>
                                </form>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8"> {{-- Mis à jour à 8 colonnes --}}
                            <div class="empty-state">
                                <p>Aucune vente enregistrée pour le moment.</p>
                                <a href="{{ route('admin.vente.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                                    Enregistrer la première vente
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
