<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mon espace client
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Carte de bienvenue --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700">
                    Bonjour, {{ $user->name }} 👋
                </h3>
                <p class="text-gray-500 mt-1">Voici un résumé de votre activité.</p>
            </div>

            {{-- Mes ventes --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h4 class="font-semibold text-gray-700 mb-4">Mes ventes</h4>

                @forelse ($user->ventes as $vente)
                    <div class="border-b py-2 flex justify-between">
                        <span>{{ $vente->libelle ?? 'Vente #'.$vente->id }}</span>
                        <span class="text-green-600 font-medium">{{ number_format($vente->montant, 2) }} Ar</span>
                    </div>
                @empty
                    <p class="text-gray-400">Aucune vente pour le moment.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
