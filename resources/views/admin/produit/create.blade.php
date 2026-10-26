@extends('layouts.admin')

@section('title', 'Créer un produit')

@section('content')
    @php
        // Point unique de correspondance : si la colonne du nom du stock change, modifier uniquement ici.
        $stocksJs = $stocks->map(fn ($s) => ['id' => $s->id, 'nom' => $s->name_stock])->values();
    @endphp

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Créer un nouveau produit</h1>
                <p class="text-sm text-slate-500">Ajoutez un article directement relié au stock ou un produit fini composé de plusieurs ingrédients.</p>
            </div>
            <a href="{{ route('produit.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-900">
                <i class="ti ti-arrow-left text-base"></i> Retour
            </a>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <p class="font-semibold mb-1">Veuillez corriger les erreurs suivantes :</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('produit.store') }}" method="POST" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom -->
                <div class="space-y-1.5">
                    <label for="nom" class="block text-sm font-semibold text-slate-700">Nom du produit <span class="text-rose-500">*</span></label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required maxlength="255" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Prix de vente -->
                <div class="space-y-1.5">
                    <label for="prix_vente" class="block text-sm font-semibold text-slate-700">Prix de vente (Ar) <span class="text-rose-500">*</span></label>
                    <input type="number" id="prix_vente" step="0.01" min="0" name="prix_vente" value="{{ old('prix_vente') }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Type de produit -->
                <div class="space-y-1.5">
                    <label for="type-select" class="block text-sm font-semibold text-slate-700">Type de produit <span class="text-rose-500">*</span></label>
                    <select name="type" id="type-select" required class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="stock" {{ old('type', 'stock') == 'stock' ? 'selected' : '' }}>Stock direct (Article simple)</option>
                        <option value="fini" {{ old('type') == 'fini' ? 'selected' : '' }}>Produit fini (Recette / Composition)</option>
                    </select>
                </div>

                <!-- Statut -->
                <div class="space-y-1.5">
                    <label for="statut" class="block text-sm font-semibold text-slate-700">Statut</label>
                    <select id="statut" name="statut" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="actif" {{ old('statut', 'actif') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ old('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-1.5">
                <label for="description" class="block text-sm font-semibold text-slate-700">Description</label>
                <textarea id="description" name="description" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>

            <hr class="border-slate-200">

            <!-- SECTION: TYPE = STOCK -->
            <div id="section-stock" class="space-y-4">
                <h3 class="text-base font-bold text-slate-900">Paramètres du stock direct</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label for="stock_id" class="block text-sm font-semibold text-slate-700">Article de stock lié <span class="text-rose-500">*</span></label>
                        <select id="stock_id" name="stock_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sélectionner un stock...</option>
                            @foreach ($stocks as $stock)
                                <option value="{{ $stock->id }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}>{{ $stock->name_stock }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label for="seuil_alerte" class="block text-sm font-semibold text-slate-700">Seuil d'alerte</label>
                        <input type="number" id="seuil_alerte" min="0" step="1" name="seuil_alerte" value="{{ old('seuil_alerte', 0) }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- SECTION: TYPE = FINI -->
            <div id="section-fini" class="space-y-4 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Composition du produit fini</h3>
                        <p class="text-xs text-slate-500">Indiquez les ingrédients et quantités nécessaires prélevés du stock.</p>
                    </div>
                    <button type="button" id="add-composition" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                        <i class="ti ti-plus"></i> Ajouter un ingrédient
                    </button>
                </div>

                <div class="space-y-3" id="compositions-container">
                    <!-- Lignes injectées dynamiquement -->
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <div class="space-y-1.5">
                        <label for="temps_preparation" class="block text-sm font-semibold text-slate-700">Temps de préparation (en minutes)</label>
                        <input type="number" id="temps_preparation" min="0" step="1" name="temps_preparation" value="{{ old('temps_preparation') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('produit.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition">Annuler</a>
                <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg shadow-sm transition">Enregistrer le produit</button>
            </div>
        </form>
    </div>

    <!-- Script de bascule dynamique Stock / Produit Fini -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stocksData = @json($stocksJs);
            const oldCompositions = @json(old('compositions', []));

            const typeSelect = document.getElementById('type-select');
            const sectionStock = document.getElementById('section-stock');
            const sectionFini = document.getElementById('section-fini');
            const addCompositionBtn = document.getElementById('add-composition');
            const container = document.getElementById('compositions-container');
            let index = 0;

            // Échappe les caractères HTML pour éviter toute injection via un nom de stock
            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, function (c) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
                });
            }

            function stockOptions(selectedId) {
                let html = '<option value="">Sélectionner l\'ingrédient...</option>';
                stocksData.forEach(function (stock) {
                    const selected = String(stock.id) === String(selectedId) ? ' selected' : '';
                    html += '<option value="' + escapeHtml(stock.id) + '"' + selected + '>' + escapeHtml(stock.nom) + '</option>';
                });
                return html;
            }

            function toggleSections() {
                const isStock = typeSelect.value === 'stock';

                sectionStock.classList.toggle('hidden', !isStock);
                sectionFini.classList.toggle('hidden', isStock);

                // Désactive les champs de la section masquée : pas de blocage "required", pas de données inutiles envoyées
                sectionStock.querySelectorAll('input, select').forEach(function (el) { el.disabled = !isStock; });
                sectionFini.querySelectorAll('input, select').forEach(function (el) { el.disabled = isStock; });

                if (!isStock && container.children.length === 0) {
                    addRow();
                }
            }

            function addRow(data) {
                data = data || {};
                const html = `
                <div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl composition-row">
                    <div class="flex-1">
                        <select name="compositions[${index}][stock_id]" required aria-label="Ingrédient" class="w-full rounded-lg border-slate-300 text-xs focus:ring-indigo-500">
                            ${stockOptions(data.stock_id)}
                        </select>
                    </div>
                    <div class="w-32">
                        <input type="number" step="0.01" min="0.01" name="compositions[${index}][quantite_necessaire]" value="${escapeHtml(data.quantite_necessaire)}" placeholder="Quantité" required aria-label="Quantité nécessaire" class="w-full rounded-lg border-slate-300 text-xs focus:ring-indigo-500">
                    </div>
                    <div class="w-28">
                        <input type="text" name="compositions[${index}][unite]" value="${escapeHtml(data.unite)}" placeholder="Unité (g, ml...)" aria-label="Unité" class="w-full rounded-lg border-slate-300 text-xs focus:ring-indigo-500">
                    </div>
                    <button type="button" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg remove-row transition" aria-label="Supprimer l'ingrédient">
                        <i class="ti ti-trash text-base"></i>
                    </button>
                </div>`;
                container.insertAdjacentHTML('beforeend', html);
                index++;
            }

            typeSelect.addEventListener('change', toggleSections);
            addCompositionBtn.addEventListener('click', function () { addRow(); });

            container.addEventListener('click', function (e) {
                if (e.target.closest('.remove-row')) {
                    e.target.closest('.composition-row').remove();
                }
            });

            // Restaure les ingrédients saisis si la validation serveur a échoué
            Object.values(oldCompositions || {}).forEach(function (composition) {
                addRow(composition);
            });

            toggleSections();
        });
    </script>
@endsection
