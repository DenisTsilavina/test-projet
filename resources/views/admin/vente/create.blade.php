@extends('layouts.admin')
@section('title', 'Vente rapide')

@section('content')
    <div class="container py-4">

        <form method="POST" action="{{ route('admin.vente.store') }}">
            @csrf

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
                                                class="btn btn-outline-gray w-100 mb-2 add-to-sale"
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
                                            <span class="badge bg-secondary ms-1">{{ number_format($sous->prix_achat, 0, ',', ' ') }} Ar</span>
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
                        <th style="width:130px;">Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr id="empty-row">
                        <td colspan="7" class="text-center text-muted py-3">
                            Cliquez sur un article pour l'ajouter.
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr class="table-light">
                        <th colspan="5" class="text-end">Total général</th>
                        <th id="grand-total" class="text-black">0 Ar</th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-2" id="submit-btn" disabled>
                Enregistrer toutes les ventes
            </button>

        </form>

    </div>
@endsection

@push('scripts')
    <script>
        let index = 0;

        document.querySelectorAll('.add-to-sale').forEach(btn => {
            btn.addEventListener('click', function () {

                // ── Récupérer les data-* ──
                const stock_id      = this.dataset.stock;
                const description_id= this.dataset.description;
                const categorie_id  = this.dataset.categorie;       // ← categorie_id
                const name          = this.dataset.name;
                const sous_nom      = this.dataset.sousNom;
                const prix          = parseFloat(this.dataset.prix) || 0;
                const stock_dispo   = parseInt(this.dataset.stockDispo) || 0;
                const stock_name    = this.dataset.stockName;

                // ── Masquer la ligne vide ──
                const emptyRow = document.getElementById('empty-row');
                if (emptyRow) emptyRow.style.display = 'none';

                const tbody = document.querySelector('#sale-list tbody');
                const tr    = document.createElement('tr');

                tr.innerHTML = `
                <td>${name}</td>
                <td>${sous_nom}</td>
                <td>${stock_name}</td>

                <td>
                    <input type="number"
                           name="ventes[${index}][effectif]"
                           value="1" min="1" max="${stock_dispo}"
                           class="form-control form-control-sm qty">
                    <small class="text-muted">Dispo : ${stock_dispo}</small>
                </td>

                <td>
                    ${prix.toLocaleString('fr-MG')} Ar
                    <input type="hidden" name="ventes[${index}][prix]" value="${prix}">
                </td>

                <td class="line-total fw-semibold" data-total="${prix}">
                    ${prix.toLocaleString('fr-MG')} Ar
                </td>

                <td>
                    <button type="button" class="btn btn-danger btn-sm remove">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>

                <input type="hidden" name="ventes[${index}][stock_id]"       value="${stock_id}">
                <input type="hidden" name="ventes[${index}][description_id]" value="${description_id}">
                <input type="hidden" name="ventes[${index}][categorie_id]"   value="${categorie_id}">
            `;

                tbody.appendChild(tr);

                // ── Quantité change → recalculer ──
                tr.querySelector('.qty').addEventListener('input', function () {
                    const q     = parseInt(this.value) || 1;
                    const total = prix * q;
                    const cell  = tr.querySelector('.line-total');

                    cell.dataset.total  = total;
                    cell.textContent    = total.toLocaleString('fr-MG') + ' Ar';
                    updateTotal();
                });

                // ── Supprimer la ligne ──
                tr.querySelector('.remove').addEventListener('click', function () {
                    tr.remove();
                    updateTotal();

                    // Réafficher la ligne vide si plus rien
                    if (document.querySelectorAll('#sale-list tbody tr:not(#empty-row)').length === 0) {
                        document.getElementById('empty-row').style.display = '';
                    }
                });

                index++;
                updateTotal();
            });
        });

        // ── Calcul du total général ──
        function updateTotal() {
            let total = 0;

            document.querySelectorAll('.line-total').forEach(td => {
                total += parseFloat(td.dataset.total) || 0;   // ← data-total (pas textContent)
            });

            document.getElementById('grand-total').textContent =
                total.toLocaleString('fr-MG') + ' Ar';

            // Activer/désactiver le bouton submit
            const hasRows = document.querySelectorAll('#sale-list tbody tr:not(#empty-row)').length > 0;
            document.getElementById('submit-btn').disabled = !hasRows;
        }
    </script>
@endpush
