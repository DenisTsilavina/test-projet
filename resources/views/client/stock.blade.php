@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                📦 État des Stocks Disponibles
            </h1>
            <a href="{{ route('admin.vente.index') }}" class="btn btn-outline-dark">Retour au Dashboard</a>
        </div>

        {{-- Grille de cartes --}}
        <div class="stock-grid">
            @forelse($stocks as $stock)
                @foreach($stock->descriptions as $desc)
                    @php $subCats = $desc->sousCategories; @endphp

                    {{-- Une div (carte) par combinaison Stock + Description --}}
                    <div class="stock-card shadow-sm">
                        <div class="card-badge">{{ $stock->name_stock }}</div>

                        <div class="card-content">
                            <div class="main-info">
                                <h5 class="desc-text">{{ $desc->description }}</h5>
                                <p class="responsible-text">👤 Resp: {{ $stock->persn_stock }}</p>
                            </div>

                            <div class="sub-info">
                                <div class="info-box">
                                    <span class="label">Sous-Catégorie</span>
                                    <span class="value text-primary">
                                    {{ $subCats->first()->stock_categorie ?? 'N/A' }}
                                </span>
                                </div>
                                <div class="info-box">
                                    <span class="label">Effectif</span>
                                    <span class="value {{ $desc->effectif > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $desc->effectif }} unités
                                </span>
                                </div>
                                <div class="info-box">
                                    <span class="label">Prix Vente</span>
                                    <span class="value fw-bold">
                                    {{ number_format($subCats->first()->prix_vente ?? 0, 0, ',', ' ') }} Ar
                                </span>
                                </div>
                            </div>
                        </div>

                        <div class="card-actions">
                            <a href="{{ route('description.create', $stock->id) }}" class="btn-action green" title="Ajouter">➕</a>
                            <a href="{{ route('description.edit', $desc->id) }}" class="btn-action orange" title="Modifier">✏</a>
                            <form action="{{ route('description.destroy', $desc->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn-action red" onclick="return confirm('Supprimer ?')">🗑</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="alert alert-info w-100">Aucun stock disponible actuellement.</div>
            @endforelse
        </div>
    </div>

    <style>
        /* Configuration de la grille pour occuper tout l'espace */
        .stock-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            width: 100%;
        }

        .stock-card {
            background: #fff;
            border-radius: 15px;
            border: 1px solid #eef2f7;
            position: relative;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .stock-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }

        /* Badge pour le nom du stock */
        .card-badge {
            position: absolute;
            top: -10px;
            left: 20px;
            background: #1a202c;
            color: white;
            padding: 4px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .desc-text {
            font-weight: 700;
            color: #2d3748;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .responsible-text {
            font-size: 13px;
            color: #718096;
            margin-bottom: 15px;
        }

        /* Section Infos (Sous-cat, Effectif, Prix) */
        .sub-info {
            display: flex;
            justify-content: space-between;
            background: #f7fafc;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .info-box {
            display: flex;
            flex-direction: column;
            text-align: center;
        }

        .info-box .label {
            font-size: 10px;
            color: #a0aec0;
            text-transform: uppercase;
        }

        .info-box .value {
            font-size: 13px;
            font-weight: 600;
        }

        /* Actions */
        .card-actions {
            display: flex;
            gap: 8px;
            margin-top: auto;
            border-top: 1px solid #f1f5f9;
            padding-top: 12px;
        }

        .btn-action {
            flex: 1;
            border: none;
            border-radius: 8px;
            padding: 6px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            transition: opacity 0.2s;
        }

        .btn-action:hover { opacity: 0.8; color: white; }
        .green { background: #c6f6d5; color: #22543d; }
        .orange { background: #feebc8; color: #744210; }
        .red { background: #fed7d7; color: #822727; }
    </style>
@endsection
