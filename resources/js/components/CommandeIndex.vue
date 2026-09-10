<template>
    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">

            <!-- En-tête -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Mes Commandes</h1>
                    <p class="text-sm text-gray-500 mt-1">Retrouvez ici l'historique et le statut de vos commandes.</p>
                </div>
                <router-link
                    :to="{ name: 'commande.create' }"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition shadow">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Nouvelle commande
                </router-link>
            </div>

            <!-- Chargement -->
            <div v-if="loading" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-10 w-10 border-4 border-indigo-600 border-t-transparent"></div>
            </div>

            <!-- Erreur -->
            <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md text-sm">
                {{ error }}
            </div>

            <!-- Aucune commande -->
            <div v-else-if="commandes.length === 0" class="bg-white border border-gray-200 rounded-xl p-10 text-center">
                <i class="fa-solid fa-box-open text-3xl text-gray-300 mb-3"></i>
                <p class="text-sm text-gray-500">Vous n'avez encore passé aucune commande.</p>
                <router-link
                    :to="{ name: 'commande.create' }"
                    class="inline-block mt-4 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                    Lancer votre première commande →
                </router-link>
            </div>

            <!-- Tableau des commandes -->
            <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="cmd in commandes" :key="cmd.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                                {{ cmd.reference }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                                {{ cmd.designation }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ cmd.quantite }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ formatCurrency(cmd.montant_total) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(cmd.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full"
                                        :class="getStatusBadgeClass(cmd.status)">
                                        {{ getStatusLabel(cmd.status) }}
                                    </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination simple -->
                <div v-if="pagination.last_page > 1" class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <button
                        @click="changePage(pagination.current_page - 1)"
                        :disabled="pagination.current_page <= 1"
                        class="text-sm font-medium text-gray-600 hover:text-indigo-600 disabled:opacity-40 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-chevron-left mr-1"></i> Précédent
                    </button>
                    <span class="text-xs text-gray-500">
                        Page {{ pagination.current_page }} sur {{ pagination.last_page }}
                    </span>
                    <button
                        @click="changePage(pagination.current_page + 1)"
                        :disabled="pagination.current_page >= pagination.last_page"
                        class="text-sm font-medium text-gray-600 hover:text-indigo-600 disabled:opacity-40 disabled:cursor-not-allowed">
                        Suivant <i class="fa-solid fa-chevron-right ml-1"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const commandes = ref([])
const loading = ref(true)
const error = ref(null)

const pagination = reactive({
    current_page: 1,
    last_page: 1
})

// Récupère les commandes du client connecté (paginées)
const fetchCommandes = async (page = 1) => {
    try {
        loading.value = true
        error.value = null
        const response = await axios.get('/api/commandes', { params: { page } })

        // Compatible avec une réponse paginée Laravel classique
        const data = response.data.data ?? response.data
        commandes.value = data

        if (response.data.current_page) {
            pagination.current_page = response.data.current_page
            pagination.last_page = response.data.last_page
        }
    } catch (err) {
        error.value = "Erreur lors du chargement de vos commandes."
        console.error(err)
    } finally {
        loading.value = false
    }
}

const changePage = (page) => {
    if (page < 1 || page > pagination.last_page) return
    fetchCommandes(page)
}

const formatCurrency = (amount) => {
    if (amount === null || amount === undefined) return '0,00 Ar'
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount) + ' Ar'
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
}

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'livre':
        case 'termine':
            return 'bg-emerald-100 text-emerald-800'
        case 'en_cours':
        case 'expedie':
            return 'bg-blue-100 text-blue-800'
        case 'en_attente':
            return 'bg-amber-100 text-amber-800'
        case 'annule':
            return 'bg-red-100 text-red-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const getStatusLabel = (status) => {
    const labels = {
        'en_attente': 'En attente',
        'en_cours': 'En cours',
        'expedie': 'Expédié',
        'livre': 'Livré',
        'annule': 'Annulé'
    }
    return labels[status] || status
}

onMounted(() => {
    fetchCommandes()
})
</script>
