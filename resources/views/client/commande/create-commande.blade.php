@extends('layouts.client')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Lancer une nouvelle commande
    </h2>
@endsection

@section('content')
    <div class="py-12 px-4 sm:px-6 lg:px-8 text-gray-700">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-md border border-gray-100">

            <div class="mb-8 border-b border-gray-200 pb-4">
                <h3 class="text-xl font-bold text-gray-900">Nouvelle demande</h3>
                <p class="mt-1 text-sm text-gray-500">Remplissez le formulaire ci-dessous pour enregistrer votre commande. Le montant sera calculé automatiquement une fois les ingrédients et la main d'œuvre ajoutés.</p>
            </div>

            {{-- Messages Flash de succès --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Erreurs de validation --}}
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg text-sm">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>Veuillez corriger les erreurs suivantes :</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Route corrigée : 'commandes.store' (cohérent avec 'commandes.index' utilisé ailleurs) --}}
            <form action="{{ route('commande.store') }}" method="POST" class="space-y-6">
                @csrf

                {{--
                    La référence n'est plus saisie ici : elle est générée
                    automatiquement à l'enregistrement (ex: CMD-2026-0001).
                --}}

                {{-- Type de sélection (Pour dynamique / UX) --}}
                <div>
                    <label for="type_selection" class="block text-sm font-semibold text-gray-700 mb-2">Source de la commande</label>
                    <select id="type_selection" class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="autre" selected>Saisir une désignation personnalisée</option>
                        <option value="stock">Choisir depuis le Stock disponible</option>
                    </select>
                </div>

                {{-- Bloc Stock (Masqué par défaut, géré par JS) --}}
                <div id="bloc_stock" class="hidden">
                    <label for="stock_select" class="block text-sm font-semibold text-gray-700 mb-2">Sélectionner dans le stock</label>
                    <select id="stock_select" class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Choisir un élément du stock --</option>
                        @foreach($stocks as $stock)
                            <option value="{{ $stock->designation }}">{{ $stock->designation }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Désignation (Le champ final soumis au contrôleur) --}}
                <div>
                    <label for="designation" class="block text-sm font-semibold text-gray-700 mb-2">Désignation de la commande</label>
                    <input type="text" id="designation" name="designation" value="{{ old('designation') }}" placeholder="Ex: Prestation informatique, Matériel..." class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                </div>

                {{-- Quantité --}}
                <div>
                    <label for="quantite" class="block text-sm font-semibold text-gray-700 mb-2">Quantité</label>
                    <input type="number" id="quantite" name="quantite" min="1" value="{{ old('quantite', 1) }}" class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                </div>

                {{--
                    Champs "Montant" et "Statut" retirés du formulaire client :
                    - Le montant est calculé automatiquement à partir des ingrédients
                      et de la main d'œuvre (voir Commande::recalculerMontant()).
                    - Le statut est fixé à "en_attente" à la création et n'est
                      modifiable que par un administrateur.
                --}}

                {{-- Note / Commentaires --}}
                <div>
                    <label for="note" class="block text-sm font-semibold text-gray-700 mb-2">Note ou détails additionnels (Optionnel)</label>
                    <textarea id="note" name="note" rows="3" placeholder="Ajoutez des précisions ici..." class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">{{ old('note') }}</textarea>
                </div>

                {{-- Boutons d'action --}}
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    {{-- route('commandes.index') --}}
                    <a href="" class="px-5 py-3 rounded-lg text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                        Annuler
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-3 rounded-lg text-sm font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition shadow">
                        Confirmer et lancer la commande
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script pour lier le choix du stock au champ désignation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelection = document.getElementById('type_selection');
            const blocStock = document.getElementById('bloc_stock');
            const stockSelect = document.getElementById('stock_select');
            const designationInput = document.getElementById('designation');

            typeSelection.addEventListener('change', function() {
                if (this.value === 'stock') {
                    blocStock.classList.remove('hidden');
                } else {
                    blocStock.classList.add('hidden');
                    stockSelect.value = "";
                }
            });

            // Quand on choisit un élément du stock, cela remplit automatiquement la désignation
            stockSelect.addEventListener('change', function() {
                if (this.value) {
                    designationInput.value = this.value;
                }
            });
        });
    </script>
@endsection
