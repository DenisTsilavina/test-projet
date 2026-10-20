<template>
    <div class="client-dashboard p-6 max-w-7xl mx-auto">
        <!-- En-tête -->
        <header class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bienvenue, {{ user.name }}</h1>
                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2.5 py-0.5 rounded-full font-medium mt-1">
          {{ user.label }}
        </span>
            </div>

            <!-- Bouton Panier -->
            <button
                @click="showCartModal = true"
                class="relative inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Panier
                <span v-if="cartCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-6 h-6 rounded-full flex items-center justify-center font-bold">
          {{ cartCount }}
        </span>
            </button>
        </header>

        <!-- Messages d'alerte -->
        <div v-if="successMessage" class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm">
            {{ successMessage }}
        </div>

        <!-- Chargement -->
        <div v-if="loading" class="text-center py-12 text-gray-500">
            Chargement du catalogue...
        </div>

        <!-- Catalogue des produits -->
        <div v-else>
            <h2 class="text-xl font-semibold mb-6 text-gray-800">Catalogue des produits disponibles</h2>

            <div v-if="produits.length === 0" class="bg-gray-50 p-8 rounded-2xl text-center text-gray-500">
                Aucun produit disponible pour le moment.
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <div
                    v-for="produit in produits"
                    :key="produit.id"
                    class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col justify-between"
                >
                    <div>
                        <!-- Image du produit -->
                        <div class="w-full h-48 bg-gray-100 overflow-hidden relative">
                            <img
                                :src="produit.image_url"
                                :alt="produit.nom"
                                class="w-full h-full object-cover"
                                @error="handleImageError"
                            />
                            <span
                                class="absolute top-2 right-2 px-2.5 py-1 text-xs font-semibold rounded-full shadow"
                                :class="getStockBadgeClass(produit.stock)"
                            >
                {{ getStockText(produit.stock) }}
              </span>
                        </div>

                        <!-- Détails -->
                        <div class="p-4">
                            <h3 class="font-bold text-lg text-gray-900 mb-1">{{ produit.nom }}</h3>
                            <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                                {{ produit.description }}
                            </p>
                        </div>
                    </div>

                    <!-- Prix & Action Panier -->
                    <div class="p-4 pt-0 flex items-center justify-between mt-auto">
                        <div>
                            <span class="text-xs text-gray-500 block">Prix unitaire</span>
                            <span class="text-lg font-extrabold text-blue-600">
                {{ formatPrix(produit.prix_vente) }} Ar
              </span>
                        </div>

                        <button
                            @click="addToCart(produit)"
                            :disabled="produit.stock <= 0"
                            class="px-3.5 py-2 text-sm font-medium rounded-xl text-white transition-colors"
                            :class="produit.stock > 0 ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-300 cursor-not-allowed'"
                        >
                            {{ produit.stock > 0 ? 'Ajouter' : 'Rupture' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal du Panier / Commande -->
        <div v-if="showCartModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full overflow-hidden flex flex-col max-h-[90vh]">

                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-bold text-lg text-gray-900">Mon Panier</h3>
                    <button @click="showCartModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
                </div>

                <div class="p-6 overflow-y-auto flex-1">
                    <div v-if="cart.length === 0" class="text-center py-8 text-gray-400">
                        Votre panier est vide.
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="(item, index) in cart" :key="index" class="flex items-center justify-between border-b pb-3">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ item.nom }}</h4>
                                <p class="text-xs text-gray-500">{{ formatPrix(item.prix_vente) }} Ar x {{ item.quantite }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-blue-600">{{ formatPrix(item.prix_vente * item.quantite) }} Ar</span>
                                <button @click="removeFromCart(index)" class="text-red-500 hover:text-red-700 text-sm font-bold">Retirer</button>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="pt-4 flex justify-between items-center font-bold text-lg border-t">
                            <span>Total général :</span>
                            <span class="text-blue-600">{{ formatPrix(cartTotal) }} Ar</span>
                        </div>

                        <!-- Formulaire Informations complémentaires -->
                        <div class="mt-4 space-y-3 pt-4 border-t">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Date de besoin souhaitée</label>
                                <input type="date" v-model="form.date_besoin" class="w-full border rounded-lg p-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Remarque éventuelle</label>
                                <textarea v-model="form.remarque" rows="2" class="w-full border rounded-lg p-2 text-sm" placeholder="Instructions particulières..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-2">
                    <button @click="showCartModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-xl">
                        Fermer
                    </button>
                    <button
                        @click="submitOrder"
                        :disabled="cart.length === 0 || submittable"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl disabled:opacity-50"
                    >
                        Valider la commande
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const user = ref({})
const produits = ref([])
const cart = ref([])
const loading = ref(true)
const showCartModal = ref(false)
const successMessage = ref('')
const submittable = ref(false)

const form = ref({
    date_besoin: '',
    remarque: ''
})

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

const addToCart = (produit) => {
    const existing = cart.value.find(item => item.id === produit.id)
    if (existing) {
        if (existing.quantite < produit.stock) {
            existing.quantite++
        } else {
            alert('Stock maximal atteint pour ce produit.')
        }
    } else {
        cart.value.push({
            id: produit.id,
            nom: produit.nom,
            prix_vente: produit.prix_vente,
            quantite: 1,
            stock: produit.stock
        })
    }
}

const removeFromCart = (index) => {
    cart.value.splice(index, 1)
}

const cartCount = computed(() => cart.value.reduce((sum, item) => sum + item.quantite, 0))
const cartTotal = computed(() => cart.value.reduce((sum, item) => sum + (item.prix_vente * item.quantite), 0))

const submitOrder = async () => {
    submittable.value = true
    try {
        const payload = {
            date_besoin: form.value.date_besoin || null,
            remarque: form.value.remarque || null,
            lignes: cart.value.map(item => ({
                produit_id: item.id,
                quantite: item.quantite,
                prix_unitaire: item.prix_vente
            }))
        }

        await axios.post('/commandes', payload)

        successMessage.value = 'Votre commande a été enregistrée avec succès ! Elle est en attente de validation.'
        cart.value = []
        showCartModal.value = false
        form.value.date_besoin = ''
        form.value.remarque = ''

        loadDashboardData() // Recharger pour mettre à jour les stocks
    } catch (error) {
        alert(error.response?.data?.message || 'Erreur lors de la validation de la commande.')
    } finally {
        submittable.value = false
    }
}

const formatPrix = (val) => new Intl.NumberFormat('fr-FR').format(val)
const getStockText = (stock) => stock <= 0 ? 'Rupture' : `Stock : ${stock}`
const getStockBadgeClass = (stock) => stock <= 0 ? 'bg-red-100 text-red-800' : (stock <= 5 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800')
const handleImageError = (e) => { e.target.src = '/images/default-product.png' }

onMounted(() => { loadDashboardData() })
</script>
