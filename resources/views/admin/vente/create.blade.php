@php
    $isClient    = Auth::guard('client')->check();
    $actionRoute = $isClient
        ? route('client.vente.store')
        : route('admin.vente.store');
@endphp

@extends($isClient ? 'layouts.client' : 'layouts.admin.admin-layout')
@section('title', 'Vente rapide')

@section('content')
    <style>
        .product-card { transition: all 0.2s ease; border: 1px solid #edf2f7 !important; border-radius: 12px; }
        .product-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; border-color: #3182ce !important; }
        .bg-soft-primary { background-color: #ebf8ff; color: #3182ce; }
        .scroll-container { max-height: 450px; overflow-y: auto; padding: 10px; border: 1px solid #eee; border-radius: 8px; background: #fdfdfd; }
    </style>

    <div class="container py-4">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ✅ Action dynamique selon le guard --}}
        <form method="POST" action="{{ $actionRoute }}" id="vente-form">
            @csrf

            {{-- ══ GRILLE PRODUITS ══ --}}
            <h4 class="mb-3">🛒 Produits disponibles</h4>
            <div class="row scroll-container mb-4">
                @foreach($stocks as $stock)
                    @foreach($stock->descriptions as $description)
                        @foreach($description->sousCategories as $sous)
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <div class="card h-100 product-card shadow-sm border-0"
                                     style="cursor:pointer"
                                     onclick="document.getElementById('btn-add-{{ $sous->id }}').click()">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-soft-primary small">{{ $stock->name_stock }}</span>
                                            <span class="badge {{ $description->effectif > 5 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $description->effectif }} dispos
                                        </span>
                                        </div>
                                        <h6 class="fw-bold mb-1">{{ $description->description }}</h6>
                                        <p class="text-muted small mb-2">{{ $sous->stock_categorie }}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                        <span class="fw-bold text-dark">
                                            {{ number_format($sous->prix_vente, 0, ',', ' ') }} Ar
                                        </span>
                                            <button type="button"
                                                    id="btn-add-{{ $sous->id }}"
                                                    class="btn btn-primary btn-sm rounded-circle add-to-sale"
                                                    data-stock="{{ $stock->id }}"
                                                    data-description="{{ $description->id }}"
                                                    data-categorie="{{ $sous->id }}"
                                                    data-name="{{ $description->description }}"
                                                    data-sous-nom="{{ $sous->stock_categorie }}"
                                                    data-prix="{{ (float) $sous->prix_vente }}"
                                                    data-stock-dispo="{{ (int) $description->effectif }}"
                                                    data-stock-name="{{ $stock->name_stock }}"
                                                    data-unite-type="{{ $description->unite->type }}"
                                                    data-unite-factor="{{ $description->unite->factor }}"
                                                    data-unite-id="{{ $description->unite->id }}"
                                                    data-unite-symbol="{{ $description->unite->symbol }}">
                                                +
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @endforeach
            </div>

            {{-- ══ PANIER ══ --}}
            <h4 class="mb-3">🧾 Panier</h4>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Produit</th>
                        <th>Sous-catégorie</th>
                        <th>Stock</th>
                        <th>Quantité</th>
                        <th>Unité</th>
                        <th>Prix unitaire</th>
                        <th>Total ligne</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="sale-list">
                    <tr id="empty-row">
                        <td colspan="8" class="text-center text-muted py-4">
                            Aucun produit sélectionné — cliquez sur un produit pour l'ajouter.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end align-items-center gap-3 mt-2 mb-4">
                <span class="fw-bold fs-5">Total général :</span>
                <span class="fw-bold fs-5 text-success" id="grand-total">0 Ar</span>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" id="submit-btn"
                        class="btn btn-success px-4" disabled
                        data-bs-toggle="modal"
                        data-bs-target="#modalClient">
                    ✅ Valider la vente
                </button>
            </div>
        </form>

        {{-- ══ MODAL CLIENT ══ --}}
        <div class="modal fade" id="modalClient" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-person-fill me-2"></i>Informations du client
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                                data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        @if($isClient)
                            {{-- ✅ CLIENT : champs pré-remplis depuis la BDD --}}
                            @php $clientAuth = Auth::guard('client')->user(); @endphp

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nom complet</label>
                                <input class="form-control bg-light"
                                       value="{{ $clientAuth->prenom }} {{ $clientAuth->nom }}"
                                       disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Téléphone</label>
                                <input type="tel" id="client_telephone"
                                       class="form-control bg-light"
                                       value="{{ $clientAuth->phone }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Adresse</label>
                                <input type="text" id="client_adresse"
                                       class="form-control bg-light"
                                       value="{{ $clientAuth->adresse }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Ville</label>
                                <input type="text" id="client_ville"
                                       class="form-control bg-light"
                                       value="{{ $clientAuth->ville }}" readonly>
                            </div>

                        @else
                            {{-- ✅ ADMIN / USER : saisie manuelle --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Vendeur
                                </label>
                                <input class="form-control bg-light"
                                       value="{{ Auth::user()->name }}" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Téléphone client <span class="text-danger">*</span>
                                </label>
                                <input type="tel" id="client_telephone"
                                       class="form-control"
                                       placeholder="Ex : 034 00 000 00">
                                <div class="invalid-feedback">Le téléphone est obligatoire.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Adresse</label>
                                <input type="text" id="client_adresse"
                                       class="form-control"
                                       placeholder="Ex : Lot II A Antananarivo">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Ville <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="client_ville"
                                       class="form-control"
                                       placeholder="Ex : Antananarivo">
                                <div class="invalid-feedback">La ville est obligatoire.</div>
                            </div>
                        @endif

                        {{-- Mode de paiement — commun aux deux --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Mode de paiement <span class="text-danger">*</span>
                            </label>
                            <select id="mode_paiement" class="form-select">
                                <option value="">-- Choisir --</option>
                                <option value="espece">Espèces</option>
                                <option value="mvola">MVola</option>
                                <option value="airtel_money">Airtel Money</option>
                                <option value="virement">Virement bancaire</option>
                            </select>
                            <div class="invalid-feedback">Veuillez choisir un mode de paiement.</div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-success fw-bold"
                                id="btn-confirm-vente">
                            <i class="bi bi-check-circle me-1"></i> Confirmer la vente
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- ══ FIN MODAL ══ --}}

    </div>
@endsection

@push('scripts')
    <script>
        const UNITES    = {!! json_encode($unites ?? []) !!};
        const IS_CLIENT = {{ $isClient ? 'true' : 'false' }};

        let rowIndex = 0;

        // ════════════════════════════════════════════
        // TOTAL GÉNÉRAL
        // ════════════════════════════════════════════
        function updateGrandTotal() {
            let total = 0;
            document.querySelectorAll('#sale-list tr[data-row]').forEach(tr => {
                total += parseFloat(tr.dataset.total) || 0;
            });
            document.getElementById('grand-total').textContent =
                total.toLocaleString('fr-MG', { minimumFractionDigits: 0 }) + ' Ar';
            document.getElementById('submit-btn').disabled =
                document.querySelectorAll('#sale-list tr[data-row]').length === 0;
        }

        // ════════════════════════════════════════════
        // CALCUL D'UNE LIGNE
        // ════════════════════════════════════════════
        function calculerLigne(tr) {
            const type        = tr.dataset.uniteType;
            const effectif    = parseFloat(tr.dataset.effectif);
            const factorStock = parseFloat(tr.dataset.factorStock);
            const prixStock   = parseFloat(tr.dataset.prixStock);

            const qtyInput   = tr.querySelector('.qty-input');
            const quantite   = parseFloat(qtyInput.value) || 0;
            const uniteSelect = tr.querySelector('.unite-select');

            const hiddenQty   = tr.querySelector('.hidden-quantite');
            const hiddenUnite = tr.querySelector('.hidden-unite');
            const prixDisplay  = tr.querySelector('.prix-display');
            const totalDisplay = tr.querySelector('.total-display');

            if (hiddenQty) hiddenQty.value = quantite;

            // ── Reset si vide ──
            if (quantite <= 0) {
                prixDisplay.textContent  = '—';
                totalDisplay.textContent = '0 Ar';
                totalDisplay.classList.remove('text-danger');
                tr.dataset.total = 0;
                updateGrandTotal();
                return;
            }

            let prixUnitaire = 0;
            let total        = 0;
            let insuffisant  = false;

            // ── CAS 1 : UNIT (pcs) ──
            if (type === 'unit') {
                insuffisant  = quantite > effectif;
                prixUnitaire = prixStock;
                total        = quantite * prixUnitaire;

                // ── CAS 2 : MASSE / VOLUME ──
            } else {
                const factorAchat = uniteSelect
                    ? parseFloat(uniteSelect.selectedOptions[0]?.dataset.factor || factorStock)
                    : factorStock;

                const stockBase = effectif * factorStock;
                const achatBase = quantite * factorAchat;

                insuffisant  = achatBase > stockBase;
                prixUnitaire = prixStock / stockBase;
                total        = achatBase * prixUnitaire;
            }

            // ── Stock insuffisant ──
            if (insuffisant) {
                prixDisplay.textContent  = '—';
                totalDisplay.textContent = '⚠ Stock insuffisant';
                totalDisplay.classList.add('text-danger');
                tr.dataset.total = 0;
                updateGrandTotal();
                return;
            }

            // ── Affichage final ──
            const uniteSymbol = uniteSelect
                ? uniteSelect.selectedOptions[0]?.dataset.symbol || ''
                : tr.dataset.uniteSymbol;

            totalDisplay.classList.remove('text-danger');
            prixDisplay.textContent  = prixUnitaire.toLocaleString('fr-MG', { minimumFractionDigits: 2 }) + ' Ar / ' + uniteSymbol;
            totalDisplay.textContent = total.toLocaleString('fr-MG', { minimumFractionDigits: 0 }) + ' Ar';
            tr.dataset.total         = total;

            if (uniteSelect && hiddenUnite) hiddenUnite.value = uniteSelect.value;

            updateGrandTotal();
        }

        // ════════════════════════════════════════════
        // AJOUT D'UN PRODUIT AU PANIER
        // ════════════════════════════════════════════
        document.querySelectorAll('.add-to-sale').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();

                const d    = this.dataset;
                const type = d.uniteType;

                const unitesCompatibles = UNITES.filter(u => u.type === type);
                const optionsHtml = unitesCompatibles
                    .map(u => `<option value="${u.id}" data-factor="${u.factor}" data-symbol="${u.symbol}">${u.symbol}</option>`)
                    .join('');

                document.getElementById('empty-row').style.display = 'none';

                const tr = document.createElement('tr');
                tr.dataset.row        = rowIndex;
                tr.dataset.uniteType  = type;
                tr.dataset.effectif   = d.stockDispo;
                tr.dataset.factorStock = d.uniteFactor;
                tr.dataset.prixStock  = d.prix;
                tr.dataset.total      = 0;
                tr.dataset.uniteId    = d.uniteId;
                tr.dataset.uniteSymbol = d.uniteSymbol;

                const uniteCol = type === 'unit'
                    ? `<span class="text-muted small">${d.uniteSymbol}</span>
                   <input type="hidden" name="ventes[${rowIndex}][unite_id]" class="hidden-unite" value="${d.uniteId}">`
                    : `<select class="form-select form-select-sm unite-select">${optionsHtml}</select>
                   <input type="hidden" name="ventes[${rowIndex}][unite_id]" class="hidden-unite" value="${d.uniteId}">`;

                tr.innerHTML = `
                <td><small class="fw-bold">${d.name}</small></td>
                <td><small>${d.sousNom}</small></td>
                <td>
                    <span class="badge bg-light text-dark">${d.stockName}</span><br>
                    <small class="text-muted">Dispo : ${d.stockDispo} ${d.uniteSymbol}</small>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm qty-input"
                           min="0.01" step="any" placeholder="Qté">
                    <input type="hidden" name="ventes[${rowIndex}][quantite]"       class="hidden-quantite" value="">
                    <input type="hidden" name="ventes[${rowIndex}][stock_id]"       value="${d.stock}">
                    <input type="hidden" name="ventes[${rowIndex}][description_id]" value="${d.description}">
                    <input type="hidden" name="ventes[${rowIndex}][categorie_id]"   value="${d.categorie}">
                </td>
                <td>${uniteCol}</td>
                <td class="prix-display text-muted small">—</td>
                <td class="total-display fw-bold">0 Ar</td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove">×</button>
                </td>
            `;

                document.getElementById('sale-list').appendChild(tr);

                tr.querySelector('.hidden-unite').value = d.uniteId;
                tr.querySelector('.qty-input').addEventListener('input', () => calculerLigne(tr));

                const sel = tr.querySelector('.unite-select');
                if (sel) {
                    sel.addEventListener('change', () => {
                        tr.querySelector('.hidden-unite').value = sel.value;
                        calculerLigne(tr);
                    });
                }

                tr.querySelector('.btn-remove').addEventListener('click', () => {
                    tr.remove();
                    if (!document.querySelectorAll('#sale-list tr[data-row]').length) {
                        document.getElementById('empty-row').style.display = '';
                    }
                    updateGrandTotal();
                });

                rowIndex++;
                updateGrandTotal();
            });
        });

        // ════════════════════════════════════════════
        // MODAL — reset classes invalides à l'ouverture
        // ════════════════════════════════════════════
        document.getElementById('modalClient').addEventListener('show.bs.modal', function () {
            ['client_telephone', 'client_ville', 'mode_paiement'].forEach(id => {
                document.getElementById(id)?.classList.remove('is-invalid');
            });
        });

        // ════════════════════════════════════════════
        // CONFIRMER LA VENTE
        // ════════════════════════════════════════════
        document.getElementById('btn-confirm-vente').addEventListener('click', function () {
            const telephone = document.getElementById('client_telephone');
            const adresse   = document.getElementById('client_adresse');
            const ville     = document.getElementById('client_ville');
            const paiement  = document.getElementById('mode_paiement');

            let valid = true;

            // Validation téléphone + ville uniquement pour admin/user
            if (!IS_CLIENT) {
                if (!telephone.value.trim()) {
                    telephone.classList.add('is-invalid'); valid = false;
                } else {
                    telephone.classList.remove('is-invalid');
                }
                if (!ville.value.trim()) {
                    ville.classList.add('is-invalid'); valid = false;
                } else {
                    ville.classList.remove('is-invalid');
                }
            }

            // Mode de paiement — obligatoire pour tous
            if (!paiement.value) {
                paiement.classList.add('is-invalid'); valid = false;
            } else {
                paiement.classList.remove('is-invalid');
            }

            if (!valid) return;

            const form = document.getElementById('vente-form');
            form.querySelectorAll('.injected-client').forEach(el => el.remove());

            const addHidden = (name, value) => {
                const inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = name;
                inp.value = value;
                inp.classList.add('injected-client');
                form.appendChild(inp);
            };

            addHidden('phone',         telephone.value.trim());
            addHidden('address',       adresse.value.trim());
            addHidden('ville',         ville.value.trim());
            addHidden('mode_paiement', paiement.value);

            form.submit();
        });
    </script>
@endpush
