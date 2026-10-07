<template>
    <div class="client-dashboard p-6">
        <header class="mb-6">
            <h1 class="text-2xl font-bold">Bienvenue, {{ user.name }}</h1>
        </header>

        <div v-if="loading" class="text-center py-8 text-gray-500">
            Chargement des produits...
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div
                v-for="produit in produits"
                :key="produit.id"
                class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col justify-between"
            >
                <div>
                    <!-- Affichage de l'image -->
                    <div class="w-full h-48 bg-gray-100 overflow-hidden relative">
                        <img
                            :src="produit.image_url"
                            :alt="produit.nom"
                            class="w-full h-full object-cover"
                            @error="handleImageError"
                        />
                        <!-- Badge de stock sur l'image -->
                        <span
                            class="absolute top-2 right-2 px-2.5 py-1 text-xs font-semibold rounded-full shadow"
                            :class="getStockBadgeClass(produit.stock)"
                        >
              {{ getStockText(produit.stock) }}
            </span>
                    </div>

                    <!-- Contenu -->
                    <div class="p-4">
                        <h3 class="font-bold text-lg text-gray-900 mb-1">{{ produit.nom }}</h3>
                        <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                            {{ produit.description }}
                        </p>
                    </div>
                </div>

                <!-- Pied de carte -->
                <div class="p-4 pt-0 flex items-center justify-between mt-auto">
                    <div>
                        <span class="text-xs text-gray-500 block">Prix</span>
                        <span class="text-lg font-extrabold text-blue-600">
              {{ formatPrix(produit.prix_vente) }} Ar
            </span>
                    </div>

                    <button
                        :disabled="produit.stock <= 0"
                        class="px-3 py-1.5 text-sm font-medium rounded-lg text-white transition-colors"
                        :class="produit.stock > 0 ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-300 cursor-not-allowed'"
                    >
                        {{ produit.stock > 0 ? 'Commander' : 'Rupture' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const user = ref({})
const produits = ref([])
const loading = ref(true)

const loadDashboardData = async () => {
    try {
        const response = await axios.get('/client/data')
        user.value = response.data.user
        produits.value = response.data.produits
    } catch (error) {
        console.error('Erreur lors du chargement :', error)
    } finally {
        loading.value = false
    }
}

// Remplacement d'image si le fichier est cassé/introuvable
const handleImageError = (e) => {
    e.target.src = '/images/default-product.png'
}

const formatPrix = (valeur) => {
    return new Intl.NumberFormat('fr-FR').format(valeur)
}

const getStockText = (stock) => {
    if (stock <= 0) return 'Rupture'
    return `Stock : ${stock}`
}

const getStockBadgeClass = (stock) => {
    if (stock <= 0) return 'bg-red-100 text-red-800'
    if (stock <= 5) return 'bg-amber-100 text-amber-800'
    return 'bg-emerald-100 text-emerald-800'
}

onMounted(() => {
    loadDashboardData()
})
</script>
