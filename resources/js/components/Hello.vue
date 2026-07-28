<template>
    <div class="container mx-auto px-4 py-8">

        <!-- En-tête de la page -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 border-b border-gray-200 pb-5">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Tableau de Bord du Magasin</h1>
                <p class="text-sm text-gray-500 mt-1">Gérez vos produits en stock, vos articles finis et suivez l'activité en temps réel.</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                    Exporter le rapport
                </button>
                <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                    Nouveau mouvement
                </button>
            </div>
        </div>

        <!-- Grille Principale -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- COLONNE GAUCHE & CENTRE : STOCKS ET PRODUITS FINIS -->
            <div class="lg:col-span-2 space-y-8">

                <!-- SECTION 1 : PRODUITS RETIRÉS DU STOCK -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
              <span class="p-1.5 bg-amber-100 text-amber-800 rounded-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </span>
                            <h2 class="text-lg font-semibold text-gray-800">Produits retirés du Stock général (Ingrédients / Matières)</h2>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800">
              {{ produitsRetires.length }} en attente
            </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence / Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité Retirée</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Retrait</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <template v-if="produitsRetires.length > 0">
                                <tr v-for="produit in produitsRetires" :key="produit.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ produit.nom }}</div>
                                        <div class="text-xs text-gray-500">Ref: {{ produit.code }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">{{ produit.quantite_retiree }}</span>
                                        <span class="text-xs text-gray-500"> {{ produit.unite }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ produit.updated_at }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        Transféré au magasin
                      </span>
                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Aucun retrait de stock récent à afficher.
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- SECTION 2 : ARTICLES FINIS DU MAGASIN -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
              <span class="p-1.5 bg-emerald-100 text-emerald-800 rounded-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </span>
                            <h2 class="text-lg font-semibold text-gray-800">Articles Finis (Prêts à la vente / utilisation)</h2>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
              {{ articlesFinis.length }} articles
            </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">En Stock Magasin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">État Alerte</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <template v-if="articlesFinis.length > 0">
                                <tr v-for="article in articlesFinis" :key="article.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ article.nom }}</div>
                                        <div class="text-xs text-gray-500">Catégorie: {{ article.categorie }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="['text-sm font-bold', article.stock_actuel <= article.stock_alerte ? 'text-red-600' : 'text-gray-900']">
                        {{ article.stock_actuel }}
                      </span>
                                        <span class="text-xs text-gray-500"> / {{ article.unite || 'pcs' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        {{ formatPrix(article.prix) }} Ar
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                          v-if="article.stock_actuel <= article.stock_alerte"
                          class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 animate-pulse"
                      >
                        Stock Critique
                      </span>
                                        <span
                                            v-else
                                            class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                        >
                        Ok
                      </span>
                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Aucun article fini enregistré dans ce magasin.
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- COLONNE DROITE : ACTIVITÉ DU MAGASIN (TIMELINE) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                    <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-3">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center space-x-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Activités Récentes</span>
                        </h2>
                        <span class="text-xs text-gray-400">Temps réel</span>
                    </div>

                    <div class="flow-root">
                        <ul v-if="activites.length > 0" role="list" class="-mb-8">
                            <li v-for="(activite, index) in activites" :key="activite.id">
                                <div class="relative pb-8">
                                    <!-- Ligne verticale de la timeline -->
                                    <span
                                        v-if="index < activites.length - 1"
                                        class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                        aria-hidden="true"
                                    ></span>

                                    <div class="relative flex space-x-3">
                                        <div>
                      <span :class="['h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white text-white text-xs font-bold', getBgClass(activite)]">
                        {{ (activite.type || 'ACT').substring(0, 1).toUpperCase() }}
                      </span>
                                        </div>
                                        <div class="flex-1 min-w-0 pt-1.5">
                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ activite.description }}
                                            </p>
                                            <div class="text-xs text-gray-500 mt-0.5 flex justify-between">
                                                <span>Par : {{ activite.user?.name || 'Système' }}</span>
                                                <span class="font-medium text-indigo-600">{{ activite.created_at_human }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <p v-else class="text-sm text-gray-500 text-center py-4">Aucune activité enregistrée aujourd'hui.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

// Données réactives (remplacez ces tableaux par vos données provenant de props ou d'une API)
const produitsRetires = ref([
    { id: 1, nom: 'Farine T55', code: 'MAT-001', quantite_retiree: 50, unite: 'kg', updated_at: '28/07/2026 10:30' },
    { id: 2, nom: 'Sucre Blanc', code: 'MAT-004', quantite_retiree: 20, unite: 'kg', updated_at: '28/07/2026 11:15' }
])

const articlesFinis = ref([
    { id: 1, nom: 'Pain de Mie 500g', categorie: 'Boulangerie', stock_actuel: 12, stock_alerte: 15, prix: 2500, unite: 'pcs' },
    { id: 2, nom: 'Brioche Tressée', categorie: 'Viennoiserie', stock_actuel: 45, stock_alerte: 10, prix: 4000, unite: 'pcs' }
])

const activites = ref([
    { id: 1, type: 'Retrait', description: 'Retrait de 50kg Farine T55', user: { name: 'Jean' }, created_at_human: 'Il y a 10 min' },
    { id: 2, type: 'Ajout', description: 'Ajout de 30 pcs Brioche Tressée', user: { name: 'Marie' }, created_at_human: 'Il y a 1 heure' }
])

// Helper pour le formatage du prix (équivalent de number_format)
const formatPrix = (prix) => {
    return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(prix)
}

// Helper pour déterminer la couleur du badge dans la timeline
const getBgClass = (activite) => {
    const type = (activite.type || '').toLowerCase()
    const desc = (activite.description || '').toLowerCase()

    if (type.includes('retrait') || desc.includes('retir')) {
        return 'bg-amber-500'
    } else if (type.includes('vente') || type.includes('ajout')) {
        return 'bg-emerald-500'
    }
    return 'bg-gray-400'
}
</script>
