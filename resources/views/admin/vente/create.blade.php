@extends('layouts.admin')
@section('title', 'Vente rapide')

@section('content')
    <div class="container py-4">

        <form method="POST" action="{{ route('admin.vente.store') }}">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h4 class="mb-3">Produits disponibles</h4>

            <div class="row">
                @forelse ($produits as $produit)
                    @php
                        $dispo = $produit->type === 'stock'
                            ? ($produit->produitStock->quantite_disponible ?? 0)
                            : null; // pour 'fini', la dispo dépend de la recette entière, pas d'un seul nombre
                    @endphp

                    <div class="col-md-4 mb-3">
                        <button type="button"
                                class="btn btn-outline-gray w-100 h-100 text-start add-to-sale"
                                data-produit="{{ $produit->id }}"
                                data-name="{{ $produit->nom }}"
                                data-type="{{ $produit->type }}"
                                data-prix="{{ (float) $produit->prix_vente }}"
                                data-stock-dispo="{{ $dispo ?? 9999 }}">
                            <div class="fw-semibold">{{ $produit->nom }}</div>
                            <span class="badge {{ $produit->type === 'stock' ? 'bg-info text-dark' : 'bg-primary' }}">
                                {{ $produit->type }}
                            </span>
                            <span class="badge bg-secondary">
                                {{ number_format($produit->prix_vente, 0, ',', ' ') }} Ar
                            </span>
                            @if ($produit->type === 'stock')
                                <div class="text-muted small mt-1">Dispo : {{ $dispo }}</div>
                            @endif
                        </button>
                    </div>
                @empty
                    <p class="text-muted">Aucun produit actif.</p>
                @endforelse
            </div>

            <h4 class="mt-4 mb-3">Liste des ventes</h4>

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="sale-list">
                    <thead class="table-light">
                    <tr>
                        <th>Produit</th>
                        <th style="width:130px;">Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr id="empty-row">
                        <td colspan="5" class="text-center text-muted py-3">
                            Cliquez sur un produit pour l'ajouter.
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr class="table-light">
                        <th colspan="3" class="text-end">Total général</th>
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

    <script>
        let index = 0;

        document.querySelectorAll('.add-to-sale').forEach(btn => {
            btn.addEventListener('click', function () {

                const produit_id  = this.dataset.produit;
                const name        = this.dataset.name;
                const prix        = parseFloat(this.dataset.prix) || 0;
                const stock_dispo = parseInt(this.dataset.stockDispo) || 9999;

                const emptyRow = document.getElementById('empty-row');
                if (emptyRow) emptyRow.style.display = 'none';

                const tbody = document.querySelector('#sale-list tbody');
                const tr    = document.createElement('tr');

                tr.innerHTML = `
                <td>${name}</td>

                <td>
                    <input type="number"
                           name="ventes[${index}][effectif]"
                           value="1" min="1" max="${stock_dispo}"
                           class="form-control form-control-sm qty">
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

                <input type="hidden" name="ventes[${index}][produit_id]" value="${produit_id}">
            `;

                tbody.appendChild(tr);

                tr.querySelector('.qty').addEventListener('input', function () {
                    const q     = parseInt(this.value) || 1;
                    const total = prix * q;
                    const cell  = tr.querySelector('.line-total');

                    cell.dataset.total  = total;
                    cell.textContent    = total.toLocaleString('fr-MG') + ' Ar';
                    updateTotal();
                });

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
@endsection
