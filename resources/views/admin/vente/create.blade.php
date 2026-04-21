@extends('layouts.app')
@section('title', 'Vente rapide')

@section('content')
    <style>
        .product-card { transition: all 0.2s ease; border: 1px solid #edf2f7 !important; border-radius: 12px; }
        .product-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; border-color: #3182ce !important; }
        .bg-soft-primary { background-color: #ebf8ff; color: #3182ce; }
        .scroll-container { max-height: 450px; overflow-y: auto; padding: 10px; border: 1px solid #eee; border-radius: 8px; background: #fdfdfd; }
    </style>

    <div class="container py-4">

        {{-- Erreurs globales --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.vente.store') }}" id="vente-form">
            @csrf

            {{-- GRILLE DES PRODUITS --}}
            <h4 class="mb-3">🛒 Produits disponibles</h4>
            <div class="row scroll-container mb-4">
                @foreach($stocks as $stock)
                    @foreach($stock->descriptions as $description)
                        @foreach($description->sousCategories as $sous)
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <div class="card h-100 product-card shadow-sm border-0"
                                     style="cursor: pointer;"
                                     onclick="document.getElementById('btn-add-{{ $sous->id }}').click()">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-soft-primary text-primary small">{{ $stock->name_stock }}</span>
                                            <span class="badge {{ $description->effectif > 5 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $description->effectif }} dispos
                                            </span>
                                        </div>
                                        <h6 class="fw-bold mb-1">{{ $description->description }}</h6>
                                        <p class="text-muted small mb-2">{{ $sous->stock_categorie }}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                            <span class="fw-bold text-dark">{{ number_format($sous->prix_vente, 0, ',', ' ') }} Ar</span>
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
                                                    data-stock-name="{{ $stock->name_stock }}">
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

            {{-- PANIER DE VENTE --}}
            <h4 class="mb-3">📄 Panier de vente</h4>
            <div class="table-responsive shadow-sm mb-3">
                <table class="table table-bordered align-middle bg-white" id="sale-list">
                    <thead class="table-dark">
                    <tr>
                        <th>Article</th>
                        <th>Catégorie</th>
                        <th>Stock</th>
                        <th style="width:120px;">Qté</th>
                        <th>Prix U</th>
                        <th>Total</th>
                        <th style="width:50px;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr id="empty-row">
                        <td colspan="7" class="text-center py-4 text-muted">
                            Sélectionnez un produit ci-dessus
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr class="table-light">
                        <th colspan="5" class="text-end text-uppercase">Total à payer</th>
                        <th id="grand-total" class="text-primary fw-bold" style="font-size: 1.3rem;">0 Ar</th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Bouton ouvre le modal --}}
            <button type="button"
                    class="btn btn-success w-100 py-3 fw-bold shadow"
                    id="submit-btn"
                    disabled
                    data-bs-toggle="modal"
                    data-bs-target="#modalClient">
                ✅ VALIDER ET ENREGISTRER LA VENTE
            </button>

        </form>

        {{-- ======= MODAL CLIENT (EN DEHORS DU FORM) ======= --}}
        <div class="modal fade" id="modalClient" tabindex="-1" aria-labelledby="modalClientLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="modalClientLabel">
                            <i class="bi bi-person-fill me-2"></i>Informations du client
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        {{-- Vendeur connecté (non modifiable) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Vendeur</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="{{ auth()->user()->name }}"
                                   disabled>
                        </div>

                        {{-- Téléphone --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Téléphone <span class="text-danger">*</span>
                            </label>
                            <input type="tel" id="client_telephone"
                                   class="form-control"
                                   placeholder="Ex : 034 00 000 00">
                            <div class="invalid-feedback">Le téléphone est obligatoire.</div>
                        </div>

                        {{-- Adresse --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Adresse</label>
                            <input type="text" id="client_adresse"
                                   class="form-control"
                                   placeholder="Ex : Lot II A Antananarivo">
                        </div>

                        {{-- Ville --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Ville <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="client_ville"
                                   class="form-control"
                                   placeholder="Ex : Antananarivo">
                            <div class="invalid-feedback">La ville est obligatoire.</div>
                        </div>

                        {{-- Mode de paiement --}}
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Annuler
                        </button>
                        <button type="button" class="btn btn-success fw-bold" id="btn-confirm-vente">
                            <i class="bi bi-check-circle me-1"></i> Confirmer la vente
                        </button>
                    </div>

                </div>
            </div>
        </div>
        {{-- ======= FIN MODAL ======= --}}

    </div>
@endsection

@push('scripts')
    <script>
        let index = 0;

        // ── Calcul du total général ──
        function updateTotal() {
            let total = 0;
            const rows = document.querySelectorAll('#sale-list tbody tr:not(#empty-row)');

            rows.forEach(tr => {
                const lineTotal = parseFloat(tr.querySelector('.line-total').dataset.total) || 0;
                total += lineTotal;
            });

            document.getElementById('grand-total').textContent = total.toLocaleString('fr-MG') + ' Ar';

            // Bouton actif uniquement si panier non vide
            document.getElementById('submit-btn').disabled = rows.length === 0;
        }

        // ── Ajout d'un produit au panier ──
        document.querySelectorAll('.add-to-sale').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();

                const emptyRow = document.getElementById('empty-row');
                if (emptyRow) emptyRow.style.display = 'none';

                const d    = this.dataset;
                const prix = parseFloat(d.prix) || 0;
                const tbody = document.querySelector('#sale-list tbody');
                const tr    = document.createElement('tr');

                tr.innerHTML = `
                <td><small class="fw-bold">${d.name}</small></td>
                <td><small>${d.sousNom}</small></td>
                <td><span class="badge bg-light text-dark">${d.stockName}</span></td>
                <td>
                    <input type="number"
                           name="ventes[${index}][effectif]"
                           value="1" min="1" max="${d.stockDispo}"
                           class="form-control form-control-sm qty">
                    <small class="text-muted">Dispo : ${d.stockDispo}</small>
                </td>
                <td>
                    ${prix.toLocaleString('fr-MG')} Ar
                    <input type="hidden" name="ventes[${index}][prix]" value="${prix}">
                </td>
                <td class="line-total fw-bold" data-total="${prix}">
                    ${prix.toLocaleString('fr-MG')} Ar
                </td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm remove">×</button>
                </td>
                <input type="hidden" name="ventes[${index}][stock_id]"       value="${d.stock}">
                <input type="hidden" name="ventes[${index}][description_id]" value="${d.description}">
                <input type="hidden" name="ventes[${index}][categorie_id]"   value="${d.categorie}">
            `;

                tbody.appendChild(tr);

                // Changement de quantité
                tr.querySelector('.qty').addEventListener('input', function () {
                    const q     = parseInt(this.value) || 0;
                    const total = q * prix;
                    const cell  = tr.querySelector('.line-total');
                    cell.dataset.total = total;
                    cell.textContent   = total.toLocaleString('fr-MG') + ' Ar';
                    updateTotal();
                });

                // Suppression de la ligne
                tr.querySelector('.remove').addEventListener('click', function () {
                    tr.remove();
                    if (document.querySelectorAll('#sale-list tbody tr:not(#empty-row)').length === 0) {
                        document.getElementById('empty-row').style.display = '';
                    }
                    updateTotal();
                });

                index++;
                updateTotal();
            });
        });

        // ── Réinitialiser les erreurs à l'ouverture du modal ──
        document.getElementById('modalClient').addEventListener('show.bs.modal', function () {
            ['client_telephone', 'client_ville', 'mode_paiement'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.remove('is-invalid');
            });
        });

        // ── Validation et soumission depuis le modal ──
        document.getElementById('btn-confirm-vente').addEventListener('click', function () {
            const telephone = document.getElementById('client_telephone');
            const adresse   = document.getElementById('client_adresse');
            const ville     = document.getElementById('client_ville');
            const paiement  = document.getElementById('mode_paiement');

            let valid = true;

            // Reset erreurs
            [telephone, ville, paiement].forEach(el => el.classList.remove('is-invalid'));

            // Validation des champs obligatoires
            if (!telephone.value.trim()) { telephone.classList.add('is-invalid'); valid = false; }
            if (!ville.value.trim())     { ville.classList.add('is-invalid');     valid = false; }
            if (!paiement.value)         { paiement.classList.add('is-invalid');  valid = false; }

            if (!valid) return;

            const form = document.getElementById('vente-form');

            // Supprimer les anciens champs injectés (évite les doublons si on clique plusieurs fois)
            form.querySelectorAll('.injected-client').forEach(el => el.remove());

            // Injecter les champs client dans le formulaire
            const appendHidden = (name, value) => {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = name;
                input.value = value;
                input.classList.add('injected-client');
                form.appendChild(input);
            };

            // Noms correspondant exactement au modèle Client et au contrôleur
            appendHidden('phone',         telephone.value.trim());
            appendHidden('address',       adresse.value.trim());
            appendHidden('ville',         ville.value.trim());
            appendHidden('mode_paiement', paiement.value);

            // Soumettre le formulaire
            form.submit();
        });
    </script>
@endpush
