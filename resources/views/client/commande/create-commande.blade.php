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
                <p class="mt-1 text-sm text-gray-500">Remplissez le formulaire ci-dessous pour enregistrer votre commande.</p>
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

            <form action="{{ route('commande.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Type de commande --}}
                <div>
                    <label for="type_commande" class="block text-sm font-semibold text-gray-700 mb-2">Que voulez-vous commander ?</label>
                    <select id="type_commande" name="type_commande" class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                        <option value="" disabled {{ old('type_commande') ? '' : 'selected' }}>-- Sélectionner le type --</option>
                        <option value="article" {{ old('type_commande') == 'article' ? 'selected' : '' }}>Un Article du catalogue</option>
                        <option value="stock" {{ old('type_commande') == 'stock' ? 'selected' : '' }}>Un élément du Stock</option>
                        <option value="autre" {{ old('type_commande') == 'autre' ? 'selected' : '' }}>Autre chose (Hors catalogue / Sur-mesure)</option>
                    </select>
                </div>

                {{-- Bloc Article --}}
                <div id="bloc_article" class="hidden">
                    <label for="article_id" class="block text-sm font-semibold text-gray-700 mb-2">Sélectionner l'article</label>
                    <select id="article_id" name="article_id" class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Choisir un article --</option>
                       {{-- @foreach($articles as $article)
                            <option value="{{ $article->id }}" {{ old('article_id') == $article->id ? 'selected' : '' }}>
                                {{ $article->nom }}
                            </option>
                        @endforeach-}}
                    </select>
                </div>

                {{-- Bloc Stock --}}
                <div id="bloc_stock" class="hidden">
                    <label for="stock_id" class="block text-sm font-semibold text-gray-700 mb-2">Sélectionner dans le stock</label>
                    <select id="stock_id" name="stock_id" class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">-- Choisir un élément du stock --</option>
                        @foreach($stocks as $stock)
                            <option value="{{ $stock->id }}" {{ old('stock_id') == $stock->id ? 'selected' : '' }}>
                                {{ $stock->designation }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Bloc Autre --}}
                <div id="bloc_autre" class="hidden">
                    <label for="nom_commande" class="block text-sm font-semibold text-gray-700 mb-2">Nom / Désignation de la commande</label>
                    <input type="text" id="nom_commande" name="nom_commande" value="{{ old('nom_commande') }}" placeholder="Ex: Prestation informatique, gâteau personnalisé..." class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                {{-- Quantité --}}
                <div>
                    <label for="effectif" class="block text-sm font-semibold text-gray-700 mb-2">Effectif / Quantité requise</label>
                    <input type="number" id="effectif" name="effectif" min="1" value="{{ old('effectif', 1) }}" class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                </div>

                {{-- Date --}}
                <div>
                    <label for="date_besoin" class="block text-sm font-semibold text-gray-700 mb-2">Date de besoin</label>
                    <input type="date" id="date_besoin" name="date_besoin" value="{{ old('date_besoin') }}" class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                </div>

                {{-- Commentaires --}}
                <div>
                    <label for="commentaires" class="block text-sm font-semibold text-gray-700 mb-2">Commentaires ou détails additionnels (Optionnel)</label>
                    <textarea id="commentaires" name="commentaires" rows="3" placeholder="Ajoutez des précisions (ex: détails livraison mini-hôtel)..." class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">{{ old('commentaires') }}</textarea>
                </div>

                {{-- Boutons d'action --}}
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                   {{--}} <a href="{{ route('commandes.index') }}" class="px-5 py-3 rounded-lg text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                        Annuler
                    </a>--}}
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-3 rounded-lg text-sm font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition shadow">
                        Confirmer et lancer la commande
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script natif réinjecté pour s'exécuter dans le DOM du layout --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type_commande');

            const blocs = {
                article: document.getElementById('bloc_article'),
                stock: document.getElementById('bloc_stock'),
                autre: document.getElementById('bloc_autre')
            };

            const inputs = {
                article: document.getElementById('article_id'),
                stock: document.getElementById('stock_id'),
                autre: document.getElementById('nom_commande')
            };

            function toggleFields(selectedType) {
                Object.keys(blocs).forEach(key => {
                    if (blocs[key]) blocs[key].classList.add('hidden');
                    if (inputs[key]) inputs[key].required = false;
                });

                if (selectedType && blocs[selectedType]) {
                    blocs[selectedType].classList.remove('hidden');
                    inputs[selectedType].required = true;
                }
            }

            typeSelect.addEventListener('change', function() {
                toggleFields(this.value);
            });

            if(typeSelect.value) {
                toggleFields(typeSelect.value);
            }
        });
    </script>
@endsection
