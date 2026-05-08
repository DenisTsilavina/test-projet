@extends('layouts.client')
@section('title', 'Mes commandes')

@section('content')
    <div class="max-w-5xl mx-auto mt-12 mb-12 px-4">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Mes commandes</h2>
            <a href="{{ route('client.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                + Nouvelle commande
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @forelse($commandes as $commande)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden">

                {{-- Header carte --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                    <div>
                        <p class="font-mono font-semibold text-gray-700">{{ $commande->numero_commande }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $commande->date_commande->format('d/m/Y') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- Badge statut commande --}}
                        @php
                            $statusBadge = match($commande->status) {
                                'pending'   => ['bg-yellow-100 text-yellow-700', 'En attente'],
                                'approved'  => ['bg-green-100 text-green-700',   'Approuvée'],
                                'cancelled' => ['bg-red-100 text-red-600',       'Annulée'],
                                default     => ['bg-gray-100 text-gray-500',     $commande->status],
                            };
                        @endphp
                        <span class="text-xs px-3 py-1 rounded-full font-semibold {{ $statusBadge[0] }}">
                    {{ $statusBadge[1] }}
                </span>

                        {{-- Badge statut paiement --}}
                        @php
                            $payBadge = match($commande->payment_status) {
                                'payée'   => ['bg-green-100 text-green-700',   'Payée'],
                                'avance'  => ['bg-orange-100 text-orange-600', 'Avance'],
                                'nonpayé' => ['bg-red-100 text-red-600',       'Non payé'],
                                default   => ['bg-gray-100 text-gray-500',     $commande->payment_status],
                            };
                        @endphp
                        <span class="text-xs px-3 py-1 rounded-full font-semibold {{ $payBadge[0] }}">
                    {{ $payBadge[1] }}
                </span>
                    </div>
                </div>

                {{-- Corps carte --}}
                <div class="px-6 py-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400 text-xs mb-1">Produit</p>
                        <p class="font-medium text-gray-800">{{ $commande->nom_produit }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-1">Total</p>
                        <p class="font-semibold text-gray-800">
                            {{ number_format($commande->total_payements, 2) }} Ar
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-1">Montant payé</p>
                        <p class="font-semibold text-green-600">
                            {{ number_format($commande->montant_paye, 2) }} Ar
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-1">Reste à payer</p>
                        @if($commande->reste_a_payer > 0)
                            <p class="font-semibold text-red-500">
                                {{ number_format($commande->reste_a_payer, 2) }} Ar
                            </p>
                        @else
                            <p class="font-semibold text-green-600">Soldée</p>
                        @endif
                    </div>
                </div>

                {{-- Footer carte --}}
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center gap-4 text-xs text-gray-400">
            <span>
                Mode :
                {{ match($commande->payment_method) {
                    'cash'         => 'Espèces',
                    'mobile_money' => 'Mobile Money',
                    'virement'     => 'Virement',
                    'carte'        => 'Carte',
                    default        => $commande->payment_method
                } }}
            </span>
                    <span>•</span>
                    <span>Livraison : {{ $commande->address_livraison }}</span>
                </div>

            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center text-gray-400">
                <p class="text-lg font-medium mb-2">Aucune commande</p>
                <p class="text-sm mb-6">Vous n'avez pas encore passé de commande.</p>
                <a href="{{ route('client.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-3 rounded-xl transition">
                    Lancer ma première commande
                </a>
            </div>
        @endforelse

    </div>
@endsection
