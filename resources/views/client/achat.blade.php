@extends('layouts.client')
@section('title', 'Achat rapide')
@section('content')
    <div class="container py-4">

      {{--  <form method="POST" action="{{ route('admin.vente.store') }}">
            @csrf

            // Erreurs globales
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
            @endif

            <h4 class="mb-3">Stocks disponibles</h4>

            <div class="row">
                @foreach($stocks as $stock)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white fw-semibold">
                                {{ $stock->name_stock }}
                            </div>
                            <div class="card-body">

                                @forelse($stock->descriptions as $description)
                                    @foreach($description->sousCategories as $sous)

                                        <button type="button"
                                                class="btn btn-outline-secondary w-100 mb-2 add-to-sale"
                                                data-stock="{{ $stock->id }}"
                                                data-description="{{ $description->id }}"
                                                data-categorie="{{ $sous->id }}"
                                                data-name="{{ $description->description }}"
                                                data-sous-nom="{{ $sous->stock_categorie }}"
                                                data-prix="{{ (float) $sous->prix_vente }}"
                                                data-stock-dispo="{{ (int) $description->effectif }}"
                                                data-stock-name="{{ $stock->name_stock }}">

                                            <span class="fw-semibold">{{ $description->description }}</span>
                                            — {{ $sous->stock_categorie }}
                                            <span class="badge bg-success ms-1">
                                                {{ number_format($sous->prix_vente, 0, ',', ' ') }} Ar
                                            </span>
                                        </button>

                                    @endforeach
                                @empty
                                    <p class="text-muted small">Aucun article disponible.</p>
                                @endforelse

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <h4 class="mt-4 mb-3">Liste des ventes</h4>

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="sale-list">
                    <thead class="table-light">
                    <tr>
                        <th>Article</th>
                        <th>Sous-catégorie</th>
                        <th>Stock</th>
                        <th style="width:120px;">Quantité</th>
                        <th>Prix unitaire</th>
                        <th style="width:140px;">Remise (%)</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr id="empty-row">
                        <td colspan="8" class="text-center text-muted py-3">
                            Cliquez sur un article pour l'ajouter.
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr class="table-light">
                        <th colspan="6" class="text-end">Total général</th>
                        <th id="grand-total" class="text-black">0 Ar</th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-2" id="submit-btn" disabled>
                <i class="bi bi-check-circle me-1"></i> Enregistrer la vente
            </button>

        </form>--}}
        @include('client.achat')
    </div>

@endsection

@push('scripts')
    <script>
        let index = 0;

        document.querySelectorAll('.add-to-sale').forEach(btn => {
            btn.addEventListener('click', function () {

                const stock_id       = this.dataset.stock;
                const description_id = this.dataset.description;
                const categorie_id   = this.dataset.categorie;
                const name           = this.dataset.name;
                const sous_nom       = this.dataset.sousNom;
                const prix           = parseFloat(this.dataset.prix) || 0;
                const stock_dispo    = parseInt(this.dataset.stockDispo) || 0;
                const stock_name     = this.dataset.stockName;

                const emptyRow = document.getElementById('empty-row');
                if (emptyRow) emptyRow.style.display = 'none';

                const tbody = document.querySelector('#sale-list tbody');
                const tr    = document.createElement('tr');
                const i     = index; // capture pour les closures

                tr.innerHTML = `
                <td>${name}</td>
                <td>${sous_nom}</td>
                <td>${stock_name}</td>

                <td>
                    <input type="number"
                           name="ventes[${i}][effectif]"
                           value="1" min="1" max="${stock_dispo}"
                           class="form-control form-control-sm qty">
                    <small class="text-muted">Dispo : ${stock_dispo}</small>
                </td>

                <td>
                    <span class="prix-base">${prix.toLocaleString('fr-MG')} Ar</span>
                    <input type="hidden" name="ventes[${i}][prix]" value="${prix}" class="hidden-prix">
                </td>

                <td>
                    <div class="input-group input-group-sm">
                        <input type="number"
                               name="ventes[${i}][remise]"
                               value="0" min="0" max="100"
                               class="form-control form-control-sm remise"
                               placeholder="0">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-success remise-montant"></small>
                </td>

                <td class="line-total fw-semibold" data-prix="${prix}" data-total="${prix}">
                    ${prix.toLocaleString('fr-MG')} Ar
                </td>

                <td>
                    <button type="button" class="btn btn-danger btn-sm remove">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>

                <input type="hidden" name="ventes[${i}][stock_id]"       value="${stock_id}">
                <input type="hidden" name="ventes[${i}][description_id]" value="${description_id}">
                <input type="hidden" name="ventes[${i}][categorie_id]"   value="${categorie_id}">
            `;

                tbody.appendChild(tr);

                // ── Recalcul commun ──
                function recalcLine() {
                    const qty     = parseInt(tr.querySelector('.qty').value)    || 1;
                    const remise  = parseFloat(tr.querySelector('.remise').value) || 0;
                    const cell    = tr.querySelector('.line-total');
                    const montantRemise = prix * (remise / 100);
                    const prixNet       = prix - montantRemise;
                    const total         = prixNet * qty;

                    // Afficher le montant économisé sur la remise
                    const remiseMontantEl = tr.querySelector('.remise-montant');
                    if (remise > 0) {
                        remiseMontantEl.textContent = `- ${montantRemise.toLocaleString('fr-MG')} Ar`;
                    } else {
                        remiseMontantEl.textContent = '';
                    }

                    // Mettre à jour le prix net dans le champ caché (pour le backend)
                    tr.querySelector('.hidden-prix').value = prixNet.toFixed(2);

                    cell.dataset.total = total;
                    cell.textContent   = total.toLocaleString('fr-MG') + ' Ar';

                    // Colorer la ligne si remise active
                    cell.classList.toggle('text-success', remise > 0);

                    updateTotal();
                }

                tr.querySelector('.qty').addEventListener('input', recalcLine);
                tr.querySelector('.remise').addEventListener('input', recalcLine);

                tr.querySelector('.remove').addEventListener('click', function () {
                    tr.remove();
                    updateTotal();
                    if (document.querySelectorAll('#sale-list tbody tr:not(#empty-row)').length === 0) {
                        document.getElementById('empty-row').style.display = '';
                    }
                });

                index++;
                updateTotal();
            });
        });

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.line-total').forEach(td => {
                total += parseFloat(td.dataset.total) || 0;
            });
            document.getElementById('grand-total').textContent =
                total.toLocaleString('fr-MG') + ' Ar';

            const hasRows = document.querySelectorAll('#sale-list tbody tr:not(#empty-row)').length > 0;
            document.getElementById('submit-btn').disabled = !hasRows;
        }
    </script>
@endpush
