<template>
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-10">

            <!-- En-tête + recherche -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Bonjour{{ user?.name ? ', ' + user.name : '' }} 👋
                </h1>
                <p class="text-sm text-gray-500 mt-1">Parcourez le stock disponible et lancez votre prochaine commande.</p>

                <div class="mt-5 relative max-w-md">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Rechercher un article..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>

            <!-- Bandeau "Recommander" (basé sur l'historique du client) -->
            <div v-if="!loadingCommandes && recommandations.length">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold text-gray-800">Vous avez déjà commandé</h2>
                    <router-link :to="{ name: 'commande.index' }" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                        Voir tout l'historique →
                    </router-link>
                </div>
                <div class="flex gap-4 overflow-x-auto pb-2 -mx-1 px-1">
                    <div
                        v-for="item in recommandations"
                        :key="item.designation"
                        class="shrink-0 w-56 bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center mb-3">
                            <i class="fa-solid fa-rotate-right text-indigo-500"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ item.designation }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Commandé {{ item.count }} fois</p>
                        <button
                            @click="reorder(item.designation)"
                            class="mt-3 w-full text-xs font-semibold text-indigo-600 border border-indigo-200 rounded-md py-1.5 hover:bg-indigo-50 transition">
                            Recommander
                        </button>
                    </div>
                </div>
            </div>

            <!-- Catalogue -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Produits disponibles</h2>
                    <span class="text-xs text-gray-500">{{ filteredStocks.length }} article(s)</span>
                </div>

                <!-- Chargement -->
                <div v-if="loadingStocks" class="flex justify-center py-12">
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-indigo-600 border-t-transparent"></div>
                </div>

                <!-- Erreur -->
                <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md text-sm">
                    {{ error }}
                </div>

                <!-- Aucun résultat -->
                <div v-else-if="filteredStocks.length === 0" class="bg-white border border-gray-200 rounded-xl p-10 text-center">
                    <i class="fa-solid fa-box-open text-3xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-500">Aucun article ne correspond à votre recherche.</p>
                </div>

                <!-- Grille produits -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    <div
                        v-for="stock in filteredStocks"
                        :key="stock.id"
                        class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col">

                        <div class="h-32 bg-gray-50 flex items-center justify-center border-b border-gray-100">
                            <i class="fa-solid fa-cube text-4xl text-gray-300"></i>
                        </div>

                        <div class="p-4 flex flex-col flex-1">
                            <h3 class="text-sm font-semibold text-gray-900 truncate">
                                {{ stock.designation || stock.name_stock }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2 flex-1">
                                {{ stock.description_stock || 'Aucune description disponible.' }}
                            </p>

                            <div class="flex items-center justify-between mt-3">
                                <span
                                    class="text-xs font-medium px-2 py-0.5 rounded-full"
                                    :class="totalQuantite(stock) > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'">
                                    {{ totalQuantite(stock) > 0 ? totalQuantite(stock) + ' en stock' : 'Rupture' }}
                                </span>
                            </div>

                            <button
                                @click="reorder(stock.designation || stock.name_stock)"
                                :disabled="totalQuantite(stock) === 0"
                                class="mt-3 w-full text-sm font-semibold text-white bg-indigo-600 rounded-lg py-2 hover:bg-indigo-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                Commander
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const user = ref(null)
const stocks = ref([])
const commandes = ref([])
const search = ref('')
const loadingStocks = ref(true)
const loadingCommandes = ref(true)
const error = ref(null)

// Produits filtrés selon la recherche
const filteredStocks = computed(() => {
    if (!search.value.trim()) return stocks.value
    const q = search.value.toLowerCase()
    return stocks.value.filter(s =>
        (s.designation || s.name_stock || '').toLowerCase().includes(q)
    )
})

// Quantité totale d'un article (somme des unités liées)
const totalQuantite = (stock) => {
    if (!stock.unites || stock.unites.length === 0) return stock.quantite ?? 0
    return stock.unites.reduce((sum, unite) => sum + (Number(unite.pivot?.quantite) || 0), 0)
}

// Articles déjà commandés par le client, groupés et comptés
const recommandations = computed(() => {
    const counts = {}
    for (const cmd of commandes.value) {
        if (!cmd.designation) continue
        counts[cmd.designation] = (counts[cmd.designation] || 0) + 1
    }
    return Object.entries(counts)
        .map(([designation, count]) => ({ designation, count }))
        .sort((a, b) => b.count - a.count)
        .slice(0, 8)
})

const fetchUser = async () => {
    try {
        const response = await axios.get('/api/user')
        user.value = response.data.user ?? response.data
    } catch (err) {
        console.error("Erreur lors du chargement de l'utilisateur", err)
    }
}

const fetchStocks = async () => {
    try {
        loadingStocks.value = true
        const response = await axios.get('/api/stocks')
        stocks.value = response.data.stocks ?? response.data
    } catch (err) {
        error.value = "Erreur lors du chargement du catalogue."
        console.error(err)
    } finally {
        loadingStocks.value = false
    }
}

const fetchCommandes = async () => {
    try {
        loadingCommandes.value = true
        const response = await axios.get('/api/commandes')
        commandes.value = response.data.data ?? response.data
    } catch (err) {
        console.error("Erreur lors du chargement des commandes", err)
    } finally {
        loadingCommandes.value = false
    }
}

// Envoie vers le formulaire de commande avec la désignation pré-remplie
const reorder = (designation) => {
    router.push({ name: 'commande.create', query: { designation } })
}

onMounted(() => {
    fetchUser()
    fetchStocks()
    fetchCommandes()
})
</script>
