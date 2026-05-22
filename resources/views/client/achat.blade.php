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

        {{-- ══ MODAL CHOIX UNITÉ ══ --}}
        {{-- ══ MODAL CHOIX UNITÉ ══ --}}
        <div class="modal fade" id="modalUnite" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-rulers me-2"></i>Choisir l'unité
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                                data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-1">Produit :</p>
                        <h6 class="fw-bold mb-3" id="modal-unite-produit-name"></h6>

                        <label class="form-label fw-semibold">
                            Unité de mesure <span class="text-danger">*</span>
                        </label>
                        <div id="modal-unite-options" class="d-flex flex-wrap gap-2 mt-2"></div>
                        <div id="modal-unite-error" class="text-danger small mt-2" style="display:none">
                            Veuillez choisir une unité.
                        </div>

                        {{-- ✅ NOUVEAU : affichage prix --}}
                        <div id="modal-prix-box" class="mt-4 p-3 rounded-3 border bg-light" style="display:none">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Prix unitaire</span>
                                <span class="fw-bold fs-5 text-primary" id="modal-prix-unitaire">—</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="text-muted small">Stock disponible</span>
                                <span class="fw-bold" id="modal-stock-dispo">—</span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary fw-bold"
                                id="btn-confirm-unite">
                            <i class="bi bi-check-circle me-1"></i> Confirmer
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- ══ FIN MODAL UNITÉ ══ --}}

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
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Vendeur</label>
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
        {{-- ══ FIN MODAL CLIENT ══ --}}

    </div>
@endsection

@push('scripts')
    <script>


        const UNITES = {!! json_encode($unites ?? []) !!};
        const IS_CLIENT = {{ $isClient ? 'true' : 'false' }};

        let rowIndex = 0;
        let pendingProduct = null;
        let selectedUnite  = null;

        const modalUnite = new bootstrap.Modal(document.getElementById('modalUnite'));

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
        // AFFICHER PRIX DANS LE MODAL
        // ════════════════════════════════════════════
        function afficherPrixModal(unite) {
            const d = pendingProduct;
            const factorStock = parseFloat(d.uniteFactor);
            const prixStock = parseFloat(d.prix);
            const effectif = parseFloat(d.stockDispo);
            const factorAchat = parseFloat(unite.factor);

            const prixParBase = prixStock / factorStock;
            const prixUnitaire = prixParBase * factorAchat;
            const stockBase = effectif * factorStock;
            const stockEnUniteChoisie = stockBase / factorAchat;

            const box = document.getElementById('modal-prix-box');
            box.style.display = 'block';

            document.getElementById('modal-prix-unitaire').textContent =
                prixUnitaire.toLocaleString('fr-MG', { minimumFractionDigits: 0 }) + ' Ar / ' + unite.symbol;

            document.getElementById('modal-stock-dispo').textContent =
                stockEnUniteChoisie.toLocaleString('fr-MG', { minimumFractionDigits: 2 }) + ' ' + unite.symbol;
        }

        // ════════════════════════════════════════════
        // CALCUL D'UNE LIGNE
        // ════════════════════════════════════════════
        function calculerLigne(tr) {
            const effectif = parseFloat(tr.dataset.effectif);
            const factorStock = parseFloat(tr.dataset.factorStock);
            const prixStock = parseFloat(tr.dataset.prixStock);
            const factorAchat = parseFloat(tr.dataset.selectedFactor || factorStock);
            const uniteSymbol = tr.dataset.uniteSymbol;

            const qtyInput = tr.querySelector('.qty-input');
            const quantite = parseFloat(qtyInput.value) || 0;

            const hiddenQty = tr.querySelector('.hidden-quantite');
            const prixDisplay  = tr.querySelector('.prix-display');
            const totalDisplay = tr.querySelector('.total-display');

            if (hiddenQty) hiddenQty.value = quantite;

            if (quantite <= 0) {
                prixDisplay.textContent  = '—';
                totalDisplay.textContent = '0 Ar';
                totalDisplay.classList.remove('text-danger');
                tr.dataset.total = 0;
                updateGrandTotal();
                return;
            }

            // Unité de base : stock total et quantité achetée
            const stockBase = effectif * factorStock;
            const achatBase = quantite * factorAchat;

            if (achatBase > stockBase) {
                prixDisplay.textContent  = '—';
                totalDisplay.textContent = '⚠ Stock insuffisant';
                totalDisplay.classList.add('text-danger');
                tr.dataset.total = 0;
                updateGrandTotal();
                return;
            }

            const prixParBase  = prixStock / factorStock;
            const prixUnitaire = prixParBase * factorAchat;
            const total = quantite * prixUnitaire;

            totalDisplay.classList.remove('text-danger');
            prixDisplay.textContent  = prixUnitaire.toLocaleString('fr-MG', { minimumFractionDigits: 2 }) + ' Ar / ' + uniteSymbol;
            totalDisplay.textContent = total.toLocaleString('fr-MG', { minimumFractionDigits: 0 }) + ' Ar';
            tr.dataset.total = total;

            updateGrandTotal();
        }

        // ════════════════════════════════════════════
        // CLIC SUR + → OUVRIR MODAL UNITÉ
        // ════════════════════════════════════════════
        document.querySelectorAll('.add-to-sale').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();

                const d = this.dataset;
                const type = d.uniteType;

                const unitesCompatibles = UNITES.filter(u => u.type === type);

                pendingProduct = d;
                selectedUnite = null;

                document.getElementById('modal-unite-produit-name').textContent =
                    d.name + ' — ' + d.sousNom;

                const container = document.getElementById('modal-unite-options');
                container.innerHTML = '';
                document.getElementById('modal-unite-error').style.display  = 'none';
                document.getElementById('modal-prix-box').style.display = 'none';

                unitesCompatibles.forEach((u, i) => {
                    const optBtn = document.createElement('button');
                    optBtn.type = 'button';
                    optBtn.className = 'btn btn-outline-primary px-4 py-2 unite-option-btn';
                    optBtn.dataset.id = u.id;
                    optBtn.dataset.factor = u.factor;
                    optBtn.dataset.symbol = u.symbol;
                    optBtn.textContent = u.symbol;

                    // Présélectionner le premier
                    if (i === 0) {
                        optBtn.classList.add('active', 'btn-primary');
                        optBtn.classList.remove('btn-outline-primary');
                        selectedUnite = { id: u.id, factor: u.factor, symbol: u.symbol };
                        afficherPrixModal(selectedUnite);
                    }

                    optBtn.addEventListener('click', function () {
                        container.querySelectorAll('.unite-option-btn').forEach(b => {
                            b.classList.remove('active', 'btn-primary');
                            b.classList.add('btn-outline-primary');
                        });
                        this.classList.add('active', 'btn-primary');
                        this.classList.remove('btn-outline-primary');
                        selectedUnite = {
                            id: this.dataset.id,
                            factor: this.dataset.factor,
                            symbol: this.dataset.symbol,
                        };
                        document.getElementById('modal-unite-error').style.display = 'none';
                        afficherPrixModal(selectedUnite);
                    });

                    container.appendChild(optBtn);
                });

                modalUnite.show();
            });
        });

        // ════════════════════════════════════════════
        // CONFIRMER UNITÉ → AJOUTER AU PANIER
        // ════════════════════════════════════════════
        document.getElementById('btn-confirm-unite').addEventListener('click', function () {
            if (!selectedUnite) {
                document.getElementById('modal-unite-error').style.display = 'block';
                return;
            }

            modalUnite.hide();

            const d = pendingProduct;

            document.getElementById('empty-row').style.display = 'none';

            const tr = document.createElement('tr');
            tr.dataset.row = rowIndex;
            tr.dataset.uniteType = d.uniteType;
            tr.dataset.effectif = d.stockDispo;
            tr.dataset.factorStock = d.uniteFactor;
            tr.dataset.selectedFactor = selectedUnite.factor;
            tr.dataset.prixStock = d.prix;
            tr.dataset.total = 0;
            tr.dataset.uniteId = selectedUnite.id;
            tr.dataset.uniteSymbol = selectedUnite.symbol;

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
            <input type="hidden" name="ventes[${rowIndex}][unite_id]"       class="hidden-unite"    value="${selectedUnite.id}">
            <input type="hidden" name="ventes[${rowIndex}][stock_id]"       value="${d.stock}">
            <input type="hidden" name="ventes[${rowIndex}][description_id]" value="${d.description}">
            <input type="hidden" name="ventes[${rowIndex}][categorie_id]"   value="${d.categorie}">
        </td>
        <td>
            <span class="badge bg-soft-primary px-3 py-2 fs-6">${selectedUnite.symbol}</span>
        </td>
        <td class="prix-display text-muted small">—</td>
        <td class="total-display fw-bold">0 Ar</td>
        <td>
            <button type="button" class="btn btn-outline-danger btn-sm btn-remove">×</button>
        </td>
        `;

            document.getElementById('sale-list').appendChild(tr);

            tr.querySelector('.qty-input').addEventListener('input', () => calculerLigne(tr));

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

        // ════════════════════════════════════════════
        // MODAL CLIENT — reset classes invalides
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
            const adresse = document.getElementById('client_adresse');
            const ville = document.getElementById('client_ville');
            const paiement = document.getElementById('mode_paiement');

            let valid = true;

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
                inp.type = 'hidden';
                inp.name = name;
                inp.value = value;
                inp.classList.add('injected-client');
                form.appendChild(inp);
            };

            addHidden('phone', telephone.value.trim());
            addHidden('address', adresse.value.trim());
            addHidden('ville', ville.value.trim());
            addHidden('mode_paiement', paiement.value);

            form.submit();
        });
    </script>
@endpush
