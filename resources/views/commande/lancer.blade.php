@extends('layouts.client')
@section('title', 'Lancer une commande')

@section('content')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Sora', sans-serif; background: #f0f4ff; }

        .page-wrapper { max-width: 860px; margin: 0 auto; padding: 2rem 1rem 4rem; }

        .card-main {
            border: none;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 12px 48px rgba(59,130,246,.12);
        }

        .card-header-custom {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            padding: 2.2rem 2.5rem;
            color: #fff;
        }
        .card-header-custom h2 { font-weight: 800; font-size: 1.75rem; margin: 0; }
        .card-header-custom p  { color: rgba(255,255,255,.75); margin: .35rem 0 0; font-size: .93rem; }

        .form-body { padding: 2rem 2.5rem; background: #fff; }

        .section-block {
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.75rem;
        }
        .section-block.light  { background: #f8faff; border: 1px solid #e8eeff; }
        .section-block.muted  { background: #f9fafb; border: 1px solid #e5e7eb; }
        .section-title { font-weight: 700; font-size: 1rem; color: #1e293b; margin-bottom: 1.1rem; }

        .form-control, .form-select {
            border-radius: .85rem;
            border: 1.5px solid #dde3f0;
            padding: .72rem 1rem;
            font-size: .93rem;
            transition: border-color .2s, box-shadow .2s;
            font-family: 'Sora', sans-serif;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.12);
            outline: none;
        }
        .form-label { font-size: .82rem; font-weight: 600; color: #475569; margin-bottom: .35rem; }

        .input-amount { font-size: 1.2rem; font-weight: 700; padding: .85rem 1rem; }
        .input-total  { color: #2563eb; }
        .input-paid   { color: #16a34a; }

        .reste-card {
            background: linear-gradient(135deg, #f0f4ff, #fff);
            border: 1.5px solid #e0e7ff;
            border-radius: 1.2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.8rem;
            gap: .6rem;
            min-height: 140px;
        }
        .reste-label  { font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; color: #94a3b8; }
        .reste-amount { font-size: 2.2rem; font-weight: 800; color: #1e293b; line-height: 1; }

        .badge-status {
            padding: .4rem 1.1rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .04em;
            opacity: 0;
            transition: opacity .25s;
        }
        .badge-status.show    { opacity: 1; }
        .status-unpaid        { background: #fee2e2; color: #dc2626; }
        .status-partial       { background: #fef3c7; color: #d97706; }
        .status-paid          { background: #dcfce7; color: #16a34a; }
        .status-overpaid      { background: #ede9fe; color: #7c3aed; }

        .btn-submit {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            border: none;
            border-radius: 1rem;
            padding: .95rem;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            width: 100%;
            letter-spacing: .02em;
            transition: opacity .2s, transform .15s, box-shadow .2s;
            box-shadow: 0 6px 24px rgba(79,70,229,.3);
        }
        .btn-submit:hover {
            opacity: .93;
            transform: translateY(-1px);
            box-shadow: 0 10px 30px rgba(79,70,229,.35);
            color: #fff;
        }
        .btn-submit:active { transform: translateY(0); }

        .breadcrumb-item a { color: #64748b; text-decoration: none; font-size: .85rem; }
        .breadcrumb-item a:hover { color: #2563eb; }
        .breadcrumb-item.active { font-size: .85rem; font-weight: 600; color: #1e293b; }
        .breadcrumb-item + .breadcrumb-item::before { color: #94a3b8; }
    </style>

    <div class="page-wrapper">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item active">Nouvelle commande</li>
            </ol>
        </nav>

        {{-- Erreurs de validation --}}
        @if ($errors->any())
            <div class="alert alert-danger rounded-3 mb-4">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Veuillez corriger les erreurs suivantes :</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Message succès --}}
        @if (session('success'))
            <div class="alert alert-success rounded-3 mb-4">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="card card-main">

            {{-- Header --}}
            <div class="card-header-custom">
                <h2><i class="bi bi-bag-plus me-2"></i>Lancer une commande</h2>
                <p>Complétez les informations ci-dessous pour créer une nouvelle commande</p>
            </div>

            {{-- Form --}}
            <div class="form-body">
                <form action="{{ route('client.lanceCommande') }}" method="POST">
                    @csrf

                    {{-- PRODUIT --}}
                    <div class="section-block light">
                        <p class="section-title"><i class="bi bi-box-seam me-2 text-primary"></i>Informations produit</p>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Produit</label>
                                <input type="text" name="nom_produit" class="form-control"
                                       placeholder="Ex : MacBook Pro" value="{{ old('nom_produit') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Numéro de commande</label>
                                <input type="text" name="numero_commande" class="form-control"
                                       placeholder="CMD-001" value="{{ old('numero_commande') }}">
                            </div>
                        </div>
                    </div>

                    {{-- PAIEMENT --}}
                    <div class="section-block muted">
                        <p class="section-title"><i class="bi bi-cash-coin me-2 text-success"></i>Paiement</p>

                        <div class="row g-3 mb-3">
                            {{-- Inputs montants --}}
                            <div class="col-md-6">
                                <div class="d-flex flex-column gap-3">
                                    <div>
                                        <label class="form-label">Montant total (Ar)</label>
                                        <input type="number" id="total_payements" name="total_payements"
                                               class="form-control input-amount input-total"
                                               placeholder="0.00" oninput="calculerReste()"
                                               value="{{ old('total_payements') }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Montant payé (Ar)</label>
                                        <input type="number" id="montant_paye" name="montant_paye"
                                               class="form-control input-amount input-paid"
                                               placeholder="0.00" oninput="calculerReste()"
                                               value="{{ old('montant_paye') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Reste à payer --}}
                            <div class="col-md-6 d-flex align-items-stretch">
                                <div class="reste-card w-100">
                                    <span class="reste-label">Reste à payer</span>
                                    <span class="reste-amount" id="reste_affiche">0.00 Ar</span>
                                    <span class="badge-status" id="badge_paiement">—</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Moyen de paiement</label>
                                <select name="payment_method" class="form-select">
                                    <option value="cash">💵 Cash</option>
                                    <option value="mobile_money">📱 Mobile Money</option>
                                    <option value="virement">🏦 Virement</option>
                                    <option value="carte">💳 Carte bancaire</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de commande</label>
                                <input type="date" name="date_commande" class="form-control"
                                       value="{{ old('date_commande') }}">
                            </div>
                        </div>
                    </div>

                    {{-- LIVRAISON --}}
                    <div class="section-block light">
                        <p class="section-title"><i class="bi bi-truck me-2 text-warning"></i>Livraison</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Adresse de livraison</label>
                                <textarea name="address_livraison" class="form-control" rows="3"
                                          placeholder="Entrez l'adresse complète..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Notes complémentaires</label>
                                <textarea name="notes" class="form-control" rows="3"
                                          placeholder="Instructions spéciales, commentaires..."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- SUBMIT --}}
                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-check-circle me-2"></i>Confirmer la commande
                    </button>

                </form>
            </div>
        </div>

    </div>

    <script>
        function calculerReste() {
            const total = parseFloat(document.getElementById('total_payements').value) || 0;
            const paye  = parseFloat(document.getElementById('montant_paye').value)    || 0;
            const reste = Math.max(0, total - paye);

            const display = document.getElementById('reste_affiche');
            const badge   = document.getElementById('badge_paiement');

            display.textContent = reste.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' Ar';

            badge.className = 'badge-status show';

            if (total <= 0) {
                badge.className = 'badge-status';
                return;
            }

            if (paye <= 0) {
                badge.textContent = 'Non payée';
                badge.classList.add('status-unpaid');
            } else if (paye > total) {
                badge.textContent = 'Surpayé';
                badge.classList.add('status-overpaid');
            } else if (paye === total) {
                badge.textContent = '✓ Payée';
                badge.classList.add('status-paid');
            } else {
                badge.textContent = 'Partiel';
                badge.classList.add('status-partial');
            }
        }
    </script>

@endsection
