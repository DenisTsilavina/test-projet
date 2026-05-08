@extends('layouts.admin.admin-layout')
@section('title', 'Historique des ventes')

@push('styles')
    <style>
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
        .page-header h2 { font-size: 18px; font-weight: 600; }
        .page-header p  { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

        .card { background: var(--surface); border: 0.5px solid var(--border); border-radius: var(--radius-lg); padding: 1.25rem; }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th { text-align: left; font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; padding: 0 10px 10px; border-bottom: 0.5px solid var(--border); }
        tbody td { padding: 12px 10px; border-bottom: 0.5px solid var(--border); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: var(--bg); }

        .btn { display: inline-block; padding: 8px 16px; border-radius: var(--radius-md); font-size: 13px; font-weight: 500; text-decoration: none; border: 0.5px solid var(--border); cursor: pointer; background: var(--surface); color: var(--text); }
        .btn:hover { background: var(--bg); }
        .btn-primary { background: var(--accent); color: #fff; border-color: var(--accent); }
        .btn-primary:hover { background: var(--accent-dark); }
        .btn-sm { padding: 4px 10px; font-size: 12px; }
        .btn-danger { background: #FCEBEB; color: #501313; border-color: #F09595; }
        .btn-danger:hover { background: #F7C1C1; }

        .revenue-positive { color: var(--accent-dark); font-weight: 600; }

        .summary-bar {
            display: flex; gap: 1rem; margin-bottom: 1.25rem;
            background: var(--accent-light); border-radius: var(--radius-md);
            padding: 0.75rem 1rem; font-size: 13px;
        }
        .summary-bar strong { color: var(--accent-dark); }

        .empty-state { text-align: center; padding: 3rem 1rem; color: var(--text-muted); font-size: 14px; }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <div>
            <h2>Historique des ventes</h2>
            <p>{{ $varotra->count() }} vente(s) enregistrée(s)</p>
        </div>
        <a href="{{ route('admin.vente.create') }}" class="btn btn-primary">+ Nouvelle vente</a>
    </div>

    {{-- Barre de résumé --}}
    @if($varotra->count() > 0)
        <div class="summary-bar">
            <span>Chiffre d'affaires total :
                <strong>{{ number_format($varotra->sum('prix_total'), 0, ',', ' ') }} Ar</strong>
            </span>
            <span style="color:var(--border);">|</span>
            <span>Revenu net :
                <strong>
                    {{-- prix_achat vient de la relation categorie --}}
                    {{ number_format(
                        $varotra->sum(fn($v) =>
                            ((int)$v->prix - (int)($v->categorie->prix_achat ?? 0)) * (int)$v->effectif
                        ), 0, ',', ' ')
                    }} Ar
                </strong>
            </span>
            <span style="color:var(--border);">|</span>
            <span>Articles vendus :
                <strong>{{ $varotra->sum('effectif') }}</strong>
            </span>
        </div>
    @endif

    <div class="card">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-dark text-center">
                <tr>
                    <th>#</th>
                    <th>Catégorie</th>
                    <th>Description</th>
                    <th>Effectif</th>
                    <th>Prix unit.</th>
                    <th>Prix achat</th>
                    <th>Total</th>
                    <th>Revenu net</th>
                    <th>Vendeur</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($varotra as $vente)
                @php
                    $prixAchat  = (int) ($vente->categorie->prix_achat ?? 0);
                    $revenuNet  = ((int)$vente->prix - $prixAchat) * (int)$vente->effectif;
                @endphp
                <tr>
                    {{-- ID --}}
                    <td style="color:var(--text-muted);font-size:12px;">
                        #V-{{ str_pad($vente->id, 4, '0', STR_PAD_LEFT) }}
                    </td>

                    {{-- Catégorie ← relation categorie --}}
                    <td>{{ $vente->categorie->stock_categorie ?? '—' }}</td>

                    {{-- Description ← relation description --}}
                    <td>{{ $vente->description->description ?? '—' }}</td>

                    {{-- Effectif --}}
                    <td>{{ $vente->effectif }}</td>

                    {{-- Prix unitaire --}}
                    <td>{{ number_format($vente->prix, 0, ',', ' ') }} Ar</td>

                    {{-- Prix achat ← depuis categorie --}}
                    <td style="color:var(--text-muted);">
                        {{ number_format($prixAchat, 0, ',', ' ') }} Ar
                    </td>

                    {{-- Total --}}
                    <td style="font-weight:600;">
                        {{ number_format($vente->prix_total, 0, ',', ' ') }} Ar
                    </td>

                    {{-- Revenu net --}}
                    <td class="revenue-positive">
                        +{{ number_format($revenuNet, 0, ',', ' ') }} Ar
                    </td>

                    {{-- Vendeur ← relation user --}}
                    <td style="font-size:12px;">
                        {{ $vente->user->name ?? '—' }}
                    </td>

                    {{-- Date --}}
                    <td style="color:var(--text-muted);font-size:12px;">
                        {{ $vente->created_at->format('d/m/Y H:i') }}
                    </td>

                    {{-- Supprimer --}}
                    <td>
                        <form method="POST"
                              action="{{ route('admin.vente.destroy', $vente->id) }}"
                              onsubmit="return confirm('Supprimer cette vente ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">
                        <div class="empty-state">
                            Aucune vente pour le moment.<br>
                            <a href="{{ route('admin.vente.create') }}"
                               class="btn btn-primary"
                               style="margin-top:1rem;display:inline-block;">
                                Enregistrer la première vente
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
