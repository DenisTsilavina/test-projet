@extends('layouts.admin')

@section('title', 'Gestion des Commandes')
@section('page-title', 'Liste des Commandes')

@section('content')
    <div class="space-y-6">

        <!-- Messages Flash -->
        @if (session('success'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-sm">
                <i class="ti ti-circle-check text-emerald-500 text-lg shrink-0"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                <i class="ti ti-alert-circle text-rose-500 text-lg shrink-0"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Suivi des commandes clients</h2>
                <p class="text-sm text-slate-400 mt-0.5">Gérez, approuvez ou refusez les commandes passées depuis l'espace client.</p>
            </div>
        </div>

        <!-- Tableau -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            @if ($commandes->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                        <i class="ti ti-receipt-off text-3xl text-slate-400"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700 mb-1">Aucune commande pour le moment</h3>
                    <p class="text-sm text-slate-400">Les commandes des clients apparaîtront ici.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                        <tr class="text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="px-5 py-3.5"># Réf</th>
                            <th class="px-5 py-3.5">Client</th>
                            <th class="px-5 py-3.5">Date commande</th>
                            <th class="px-5 py-3.5">Date besoin</th>
                            <th class="px-5 py-3.5 text-right">Total</th>
                            <th class="px-5 py-3.5 text-center">Statut</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        @foreach ($commandes as $commande)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-4 font-bold text-slate-900">#{{ $commande->id }}</td>
                                <td class="px-5 py-4 text-slate-700 font-medium">{{ $commande->client->name ?? 'Inconnu' }}</td>
                                <td class="px-5 py-4 text-slate-500">{{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-4 text-slate-500">{{ $commande->date_besoin ? \Carbon\Carbon::parse($commande->date_besoin)->format('d/m/Y') : '—' }}</td>
                                <td class="px-5 py-4 text-right font-extrabold text-blue-600">{{ number_format($commande->total, 0, ',', ' ') }} Ar</td>
                                <td class="px-5 py-4 text-center">
                                    @php
                                        $badges = [
                                            'en_attente' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'infos_demandees' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            'en_cours' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'annule' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badges[$commande->statut] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                        {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.commandes.show', $commande->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                        <i class="ti ti-eye"></i> Détails
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
@endsection
