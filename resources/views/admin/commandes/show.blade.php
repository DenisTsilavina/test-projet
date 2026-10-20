@extends('layouts.admin')

@section('title', 'Détails de la commande')
@section('page-title', 'Commande #' . $commande->id)

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Fil d'ariane & Retour -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.commandes.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3.5 py-2 rounded-xl shadow-sm transition-colors">
                <i class="ti ti-arrow-left"></i> Retour à la liste
            </a>
            <span class="text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider
            {{ $commande->statut === 'en_cours' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
            {{ str_replace('_', ' ', $commande->statut) }}
        </span>
        </div>

        <!-- Messages Flash -->
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Carte Info Client & Commande -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Informations Client</h3>
                <p class="font-bold text-slate-900 text-base">{{ $commande->client->name ?? '—' }}</p>
                <p class="text-sm text-slate-500">{{ $commande->client->email ?? '—' }}</p>
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Détails temporels</h3>
                <p class="text-sm text-slate-700"><strong class="text-slate-900">Date commande :</strong> {{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y H:i') }}</p>
                <p class="text-sm text-slate-700"><strong class="text-slate-900">Date besoin :</strong> {{ $commande->date_besoin ? \Carbon\Carbon::parse($commande->date_besoin)->format('d/m/Y') : 'Non spécifiée' }}</p>
            </div>
            @if($commande->remarque)
                <div class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <strong class="text-xs text-slate-500 uppercase tracking-wider block mb-1">Remarque du client :</strong>
                    <p class="text-sm text-slate-700 italic">"{{ $commande->remarque }}"</p>
                </div>
            @endif
        </div>

        <!-- Tableau des lignes de commande -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 font-bold text-slate-900">
                Produits commandés
            </div>
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                <tr class="text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                    <th class="px-6 py-3">Produit</th>
                    <th class="px-6 py-3 text-center">Quantité</th>
                    <th class="px-6 py-3 text-right">Prix Unitaire</th>
                    <th class="px-6 py-3 text-right">Total Ligne</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @foreach ($commande->lignes as $ligne)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $ligne->produit->nom ?? 'Produit #' . $ligne->produit_id }}</td>
                        <td class="px-6 py-4 text-center font-semibold text-slate-700">{{ $ligne->quantite }}</td>
                        <td class="px-6 py-4 text-right text-slate-600">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} Ar</td>
                        <td class="px-6 py-4 text-right font-bold text-blue-600">{{ number_format($ligne->total_ligne, 0, ',', ' ') }} Ar</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr class="bg-slate-50 border-t border-slate-200">
                    <td colspan="3" class="px-6 py-4 font-bold text-right text-slate-700">Total Général :</td>
                    <td class="px-6 py-4 text-right font-extrabold text-lg text-emerald-700">{{ number_format($commande->total, 0, ',', ' ') }} Ar</td>
                </tr>
                </tfoot>
            </table>
        </div>

        <!-- Actions d'administration (Approuver / Demander info / Refuser) -->
        @if (in_array($commande->statut, ['en_attente', 'infos_demandees']))
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <h3 class="font-bold text-slate-900 text-sm">Actions de traitement :</h3>

                <div class="flex items-center gap-3 flex-wrap">
                    <!-- Bouton Confirmer -->
                    <form action="{{ route('admin.commandes.confirmer', $commande->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow transition-colors">
                            Approuver & déduire le stock
                        </button>
                    </form>

                    <!-- Bouton Refuser -->
                    <form action="{{ route('admin.commandes.refuser', $commande->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment refuser cette commande ?');">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-sm rounded-xl shadow transition-colors">
                            Refuser
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>
@endsection
