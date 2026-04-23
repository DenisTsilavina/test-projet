
<style>
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
        cursor: pointer;  {{-- ← cliquable pour ajouter au panier --}}
    }

    .stock-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }

    .card-badge {
        position: absolute;
        top: -10px;
        left: 20px;
        background: #4a5568;
        color: white;
        padding: 4px 15px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .desc-text {
        font-weight: 700;
        color: #2d3748;
        margin-top: 10px;
        margin-bottom: 5px;
    }

    .sub-info {
        display: flex;
        justify-content: space-between;
        background: #f8fafc;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 5px;
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
        margin-bottom: 2px;
    }

    .info-box .value {
        font-size: 13px;
        font-weight: 600;
    }
</style>

<div class="container-fluid px-4 mt-4">
    <div class="d-flex flex-column align-items-center mb-4 w-100">
        <h1 style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"
            class="text-center">
            Les produits au magasin
        </h1>
    </div>

    <div class="stock-grid">
        @forelse($stocks as $stock)
            @foreach($stock->descriptions as $desc)
                @php $subCats = $desc->sousCategories; @endphp

                <div class="stock-card shadow-sm"
                     @guest('client')
                         data-bs-toggle="modal"
                     data-bs-target="#authModal"
                    @endguest
                @auth('client')
                    {{-- ici plus tard : ajouter au panier --}}
                    @endauth
                >
                    <div class="card-badge">
                        {{ $stock->name_stock }}
                    </div>

                    <div class="card-content">
                        <h5 class="desc-text">{{ $desc->description }}</h5>

                        <div class="sub-info">
                            <div class="info-box">
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
                                <span class="label">Prix</span>
                                <span class="value fw-bold">
                                    {{ number_format($subCats->first()->prix_vente ?? 0, 0, ',', ' ') }} Ar
                                </span>
                            </div>
                        </div>



                    </div>
                </div>
            @endforeach
        @empty
            <div class="alert alert-info w-100">Aucun stock disponible actuellement.</div>
        @endforelse
    </div>
</div>
