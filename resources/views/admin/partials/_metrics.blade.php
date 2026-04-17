<style>
    .metrics-grid {
        display: grid;
        /* Utilisation de 1fr pour forcer l'occupation de TOUT l'espace disponible */
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2.5rem;
        width: 100%; /* Occupe 100% du parent */
    }

    .metric-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px; /* Arrondi plus prononcé pour le look moderne */
        padding: 2rem 1.5rem; /* Padding augmenté pour "agrandir" visuellement */
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease-in-out;
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .metric-card .mc-label {
        font-size: 13px; /* Légèrement plus grand */
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 12px;
    }

    .metric-card .mc-value {
        font-size: 32px; /* Taille de police augmentée (22px -> 32px) */
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .metric-card .mc-sub {
        font-size: 13px;
        color: #94a3b8;
        margin-top: 8px;
    }

    .card-revenue .mc-value { color: #10b981; }

    /* Responsive : on passe à 2 colonnes sur tablette et 1 sur mobile */
    @media (max-width: 1200px) {
        .metrics-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .metrics-grid { grid-template-columns: 1fr; }
    }
</style>

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
