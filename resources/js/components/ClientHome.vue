<template>
    <div class="min-h-screen bg-gray-50 font-sans">
        <!-- BARRE DE NAVIGATION -->
        <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Logo / App Name -->
                    <div class="flex items-center space-x-3">
                        <span class="text-xl font-bold text-indigo-600">Tsena Vohitsoa</span>
                    </div>

                    <!-- Liens de navigation -->
                    <div class="hidden md:flex space-x-8">
                        <a href="#produits" class="text-gray-600 hover:text-indigo-600 font-medium transition">Produits</a>
                        <a href="#services" class="text-gray-600 hover:text-indigo-600 font-medium transition">Services</a>
                        <a href="#about" class="text-gray-600 hover:text-indigo-600 font-medium transition">À propos</a>
                    </div>

                    <!-- Espace Utilisateur / Connexion -->
                    <div class="flex items-center space-x-4">
                        <template v-if="user">
                            <span class="text-sm font-semibold text-gray-700 hidden sm:inline">{{ user.name }}</span>
                            <router-link
                                to="/client/dashboard"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition"
                            >
                                Mon Espace
                            </router-link>
                            <button
                                @click="handleLogout"
                                :disabled="loggingOut"
                                class="px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition"
                            >
                                {{ loggingOut ? '...' : 'Déconnexion' }}
                            </button>
                        </template>
                        <template v-else>
                            <a
                                href="/login"
                                class="px-4 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                            >
                                Connexion
                            </a>
                            <a
                                href="/register"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition shadow-sm"
                            >
                                S'inscrire
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- SECTION HERO (En-tête de bienvenue) -->
        <section class="bg-gradient-to-r from-indigo-700 to-indigo-500 text-white py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto text-center">
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight">
                    Bienvenue sur Tsena Vohitsoa
                </h1>
                <p class="mt-4 text-lg sm:text-xl text-indigo-100 max-w-2xl mx-auto">
                    Découvrez nos meilleurs produits et gérez vos commandes en toute simplicité.
                </p>
                <div class="mt-8 flex justify-center space-x-4">
                    <a
                        href="#produits"
                        class="px-6 py-3 text-indigo-700 bg-white hover:bg-gray-100 font-bold rounded-lg shadow transition"
                    >
                        Explorer la boutique
                    </a>
                </div>
            </div>
        </section>

        <!-- CONTENU PRINCIPAL -->
        <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 space-y-12">

            <!-- Chargement -->
            <div v-if="loading" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-10 w-10 border-4 border-indigo-600 border-t-transparent"></div>
            </div>

            <!-- Erreur éventuelle -->
            <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md text-sm">
                {{ error }}
            </div>

            <template v-else>
                <!-- SECTION CATALOGUE DE PRODUITS -->
                <section id="produits">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Nos Produits en Vedette</h2>
                            <p class="text-sm text-gray-500">Sélectionnés spécialement pour vous</p>
                        </div>
                    </div>

                    <div v-if="produits.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div
                            v-for="produit in produits"
                            :key="produit.id"
                            class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition group"
                        >
                            <div class="h-48 bg-gray-100 relative overflow-hidden">
                                <img
                                    :src="produit.image_url || '/images/placeholder.png'"
                                    :alt="produit.nom"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                />
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ produit.nom }}</h3>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ produit.description }}</p>
                                <div class="mt-4 flex justify-between items-center">
                                    <span class="text-lg font-extrabold text-indigo-600">
                                        {{ formatCurrency(produit.prix) }}
                                    </span>
                                    <button
                                        @click="commander(produit)"
                                        class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition"
                                    >
                                        Commander
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="bg-white p-8 text-center rounded-xl border border-gray-200 text-gray-500">
                        Aucun produit disponible pour le moment.
                    </div>
                </section>

                <!-- SECTION RAPPEL MON ESPACE (Si connecté) -->
                <section v-if="user" class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-indigo-900">Consulter vos commandes en cours</h3>
                            <p class="text-sm text-indigo-700 mt-1">Vous avez des commandes en cours de livraison.</p>
                        </div>
                        <router-link
                            to="/client/dashboard"
                            class="px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-lg shadow hover:bg-indigo-700 transition whitespace-nowrap"
                        >
                            Accéder au Dashboard
                        </router-link>
                    </div>
                </section>
            </template>
        </main>

        <!-- PIED DE PAGE -->
        <footer class="bg-white border-t border-gray-200 mt-16 py-8">
            <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-500">
                &copy; {{ new Date().getFullYear() }} Tsena Vohitsoa. Tous droits réservés.
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const user = ref(null)
const produits = ref([])
const loading = ref(true)
const loggingOut = ref(false)
const error = ref(null)

const fetchHomeData = async () => {
    try {
        loading.value = true
        // Appel pour récupérer les données de la page d'accueil
        const response = await axios.get('/api/client/home')

        user.value = response.data.user || null
        produits.value = response.data.produits || []
    } catch (err) {
        // Fallback si l'utilisateur n'est pas connecté
        if (err.response && err.response.status === 401) {
            user.value = null
        } else {
            error.value = "Erreur lors du chargement des données."
        }
        console.error(err)
    } finally {
        loading.value = false
    }
}

const formatCurrency = (amount) => {
    if (amount === null || amount === undefined) return '0,00 Ar'
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount) + ' Ar'
}

const commander = (produit) => {
    // Redirection vers le formulaire de commande
    window.location.href = `/client/commande/create?stock_id=${produit.id}`
}

const handleLogout = async () => {
    try {
        loggingOut.value = true
        await axios.post('/logout')
        window.location.href = '/login'
    } catch (err) {
        console.error("Erreur déconnexion", err)
    } finally {
        loggingOut.value = false
    }
}

onMounted(() => {
    fetchHomeData()
})
</script>
