@extends('layouts.app')
@section('title', 'Gestion des commandes')

@section('content')
    <div class="max-w-7xl mx-auto mt-10 px-4 pb-12">

        <h2 class="text-2xl font-bold text-gray-800 mb-6">Gestion des commandes</h2>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats commandes --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
                <p class="text-sm text-gray-400">Total</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-yellow-50 rounded-xl border border-yellow-100 p-4 text-center">
                <p class="text-sm text-yellow-600">En attente</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-green-50 rounded-xl border border-green-100 p-4 text-center">
                <p class="text-sm text-green-600">Approuvées</p>
                <p class="text-2xl font-bold text-green-700">{{ $stats['approved'] }}</p>
            </div>
            <div class="bg-red-50 rounded-xl border border-red-100 p-4 text-center">
                <p class="text-sm text-red-400">Annulées</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] }}</p>
            </div>
        </div>

        {{-- Stats paiements --}}
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-green-50 rounded-xl border border-green-100 p-4 text-center">
                <p class="text-sm text-green-600">Payées</p>
                <p class="text-2xl font-bold text-green-700">{{ $stats['paid'] }}</p>
            </div>
            <div class="bg-orange-50 rounded-xl border border-orange-100 p-4 text-center">
                <p class="text-sm text-orange-500">En avance</p>
                <p class="text-2xl font-bold text-orange-600">{{ $stats['advance'] }}</p>
            </div>
            <div class="bg-red-50 rounded-xl border border-red-100 p-4 text-center">
                <p class="text-sm text-red-400">Non payées</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['unpaid'] }}</p>
            </div>
        </div>

        {{-- Table commandes --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-4 text-left font-semibold text-gray-500">Référence</th>
                    <th class="px-5 py-4 text-left font-semibold text-gray-500">Client</th>
                    <th class="px-5 py-4 text-left font-semibold text-gray-500">Produit</th>
                    <th class="px-5 py-4 text-left font-semibold text-gray-500">Paiement</th>
                    <th class="px-5 py-4 text-left font-semibold text-gray-500">Statut</th>
                    <th class="px-5 py-4 text-left font-semibold text-gray-500">Action</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @forelse($commandes as $commande)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- Référence --}}
                        <td class="px-5 py-4">
                            <p class="font-mono font-medium text-gray-700">
                                {{ $commande->numero_commande }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $commande->date_commande->format('d/m/Y') }}
                            </p>
                        </td>

                        {{-- Client --}}
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-800">
                                {{ $commande->client->user->name ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $commande->client->user->email ?? '' }}
                            </p>
                        </td>

                        {{-- Produit --}}
                        <td class="px-5 py-4 text-gray-700">
                            {{ $commande->nom_produit }}
                        </td>

                        {{-- Paiement --}}
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-800">
                                {{ number_format($commande->total_payements, 2) }} Ar
                            </p>
                            <p class="text-xs text-green-600 mt-0.5">
                                Payé : {{ number_format($commande->montant_paye, 2) }} Ar
                            </p>
                            @if($commande->reste_a_payer > 0)
                                <p class="text-xs text-red-500">
                                    Reste : {{ number_format($commande->reste_a_payer, 2) }} Ar
                                </p>
                            @else
                                <p class="text-xs font-semibold text-green-600">Soldée</p>
                            @endif

                            {{-- Mode paiement --}}
                            <span class="text-xs px-2 py-0.5 rounded-md bg-gray-100 text-gray-500 mt-1 inline-block">
                            {{ match($commande->payment_method) {
                                'cash'         => 'Cash',
                                'mobile_money' => 'Mobile Money',
                                'virement'     => 'Virement',
                                'carte'        => 'Carte',
                                default        => $commande->payment_method
                            } }}
                        </span>

                            {{-- Statut paiement --}}
                            @php
                                $payBadge = match($commande->payment_status) {
                                    'payée'   => ['bg-green-100 text-green-700',   'Payée'],
                                    'avance'  => ['bg-orange-100 text-orange-600', 'Avance'],
                                    'nonpayé' => ['bg-red-100 text-red-600',       'Non payé'],
                                    default   => ['bg-gray-100 text-gray-500',     $commande->payment_status],
                                };
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-md font-semibold mt-1 inline-block {{ $payBadge[0] }}">
                            {{ $payBadge[1] }}
                        </span>
                        </td>

                        {{-- Statut commande --}}
                        <td class="px-5 py-4">
                            @php
                                $statusBadge = match($commande->status) {
                                    'pending'   => ['bg-yellow-100 text-yellow-700', 'En attente'],
                                    'approved'  => ['bg-green-100 text-green-700',   'Approuvé'],
                                    'cancelled' => ['bg-red-100 text-red-600',       'Annulé'],
                                    default     => ['bg-gray-100 text-gray-600',     $commande->status],
                                };
                            @endphp
                            <span class="text-xs px-3 py-1 rounded-full font-semibold {{ $statusBadge[0] }}">
                            {{ $statusBadge[1] }}
                        </span>
                        </td>

                        {{-- Action --}}
                        <td class="px-5 py-4">
                            <form action="{{ route('admin.commandes.updateStatus', $commande) }}"
                                  method="POST"
                                  class="flex flex-col gap-2">
                                @csrf
                                @method('PATCH')

                                {{-- Changer statut commande --}}
                                <select name="status"
                                        class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 bg-white w-full">
                                    <option value="pending"
                                        {{ $commande->status == 'pending'   ? 'selected' : '' }}>
                                        En attente
                                    </option>
                                    <option value="approved"
                                        {{ $commande->status == 'approved'  ? 'selected' : '' }}>
                                        Approuvé
                                    </option>
                                    <option value="cancelled"
                                        {{ $commande->status == 'cancelled' ? 'selected' : '' }}>
                                        Annulé
                                    </option>
                                </select>

                                {{-- Ajouter montant payé --}}
                                <div class="relative">
                                    <input type="number"
                                           name="montant_paye"
                                           step="0.01"
                                           min="0"
                                           value="{{ $commande->montant_paye }}"
                                           placeholder="Montant payé"
                                           class="text-sm border border-gray-200 rounded-lg pl-2 pr-8 py-1.5 w-full" />
                                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-400">
                                    Ar
                                </span>
                                </div>

                                <button type="submit"
                                        class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition font-medium">
                                    Mettre à jour
                                </button>
                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            Aucune commande trouvée.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $commandes->links() }}
            </div>
        </div>
    </div>
@endsection
