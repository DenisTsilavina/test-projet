<template>
    <div class="min-h-screen bg-gray-50 font-sans">
        <!-- BARRE DE NAVIGATION -->
        <nav class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center space-x-3">
                        <span class="text-xl font-bold text-indigo-600">Espace Client</span>
                    </div>

                    <div class="flex items-center space-x-4" v-if="user">
                        <span class="text-sm font-semibold text-gray-700">{{ user.name }}</span>
                        <button
                            @click="handleLogout"
                            :disabled="loggingOut"
                            class="px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-md transition"
                        >
                            {{ loggingOut ? 'Déconnexion...' : 'Déconnexion' }}
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- CONTENU PRINCIPAL -->
        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

            <!-- Signalement de Chargement -->
            <div v-if="loading" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-10 w-10 border-4 border-indigo-600 border-t-transparent"></div>
            </div>

            <!-- Erreur éventuelle -->
            <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md text-sm">
                {{ error }}
            </div>

            <!-- Dashboard -->
            <div v-else class="space-y-8">
                <!-- Message de bienvenue -->
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Bienvenue, {{ user.name }} 👋</h1>
                    <p class="text-sm text-gray-500 mt-1">Consultez l'état de vos commandes et vos dépenses récentes.</p>
                </div>

                <!-- CARTES DE STATISTIQUES -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                        <dt class="text-xs font-semibold text-gray-500 uppercase">Commandes Totales</dt>
                        <dd class="mt-2 text-3xl font-extrabold text-gray-900">{{ stats.total_commandes }}</dd>
                    </div>

                    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                        <dt class="text-xs font-semibold text-amber-600 uppercase">En Cours</dt>
                        <dd class="mt-2 text-3xl font-extrabold text-amber-600">{{ stats.commandes_en_cours }}</dd>
                    </div>

                    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                        <dt class="text-xs font-semibold text-emerald-600 uppercase">Livrées</dt>
                        <dd class="mt-2 text-3xl font-extrabold text-emerald-600">{{ stats.commandes_livrees }}</dd>
                    </div>

                    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                        <dt class="text-xs font-semibold text-indigo-600 uppercase">Total Dépensé</dt>
                        <dd class="mt-2 text-3xl font-extrabold text-indigo-600">{{ formatCurrency(stats.total_depense) }}</dd>
                    </div>
                </div>

                <!-- TABLEAU DES COMMANDES / ACHATS -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">Mes Dernières Commandes</h2>
                        <span class="text-xs font-medium text-gray-500">{{ commandes.length }} affichées</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paiement</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="cmd in commandes" :key="cmd.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                                    {{ cmd.reference }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ cmd.created_at }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ cmd.mode_paiement }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    {{ formatCurrency(cmd.montant_total) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                    <span
                        class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full"
                        :class="getStatusBadgeClass(cmd.status)"
                    >
                      {{ getStatusLabel(cmd.status) }}
                    </span>
                                </td>
                            </tr>

                            <tr v-if="commandes.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                    Aucune commande trouvée.
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const user = ref(null)
const stats = ref({})
const commandes = ref([])
const loading = ref(true)
const loggingOut = ref(false)
const error = ref(null)

// Appel API pour récupérer les informations
const fetchDashboard = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/client/dashboard')

        user.value = response.data.user
        stats.value = response.data.stats
        commandes.value = response.data.commandes
    } catch (err) {
        error.value = "Erreur lors du chargement des données."
        console.error(err)
    } finally {
        loading.value = false
    }
}

// Formatage de la monnaie (ex: Ariary)
const formatCurrency = (amount) => {
    if (amount === null || amount === undefined) return '0,00 Ar'
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount) + ' Ar'
}

// Style des badges selon le statut
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

// Libellé lisible du statut
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

// Action de déconnexion
const handleLogout = async () => {
    try {
        loggingOut.value = true
        await axios.post('/api/client/logout')
        window.location.href = '/login'
    } catch (err) {
        console.error("Erreur déconnexion", err)
    } finally {
        loggingOut.value = false
    }
}

onMounted(() => {
    fetchDashboard()
})
</script>
