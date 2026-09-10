<template>
    <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                <!-- Partie Gauche : Logo -->
                <div class="flex space-x-8">
                    <div class="shrink-0 flex items-center font-bold text-xl text-indigo-600 tracking-wider">
                        <i class="fa-solid fa-cubes-stacked mr-2"></i>VOHITSOA TSENA
                    </div>
                </div>

                <!-- Partie Droite -->
                <div class="flex items-center space-x-6">
                    <div class="hidden sm:-my-px sm:flex sm:items-center sm:space-x-6">

                        <!-- LIEN ACCUEIL -->
                        <router-link
                            :to="{ name: 'client.dashboard' }"
                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition focus:outline-none"
                            :class="route.name === 'client.dashboard'
                                ? 'border-indigo-500 text-gray-900'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                            <i class="fa-solid fa-house mr-2" :class="route.name === 'client.dashboard' ? 'text-indigo-500' : 'text-gray-400'"></i>
                            Accueil
                        </router-link>

                        <!-- MENU "COMMANDES" avec sous-menu -->
                        <div class="relative" ref="commandesRef">
                            <button
                                @click="openCommandes = !openCommandes"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition focus:outline-none"
                                :class="isCommandeRoute
                                    ? 'border-indigo-500 text-gray-900'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                                <i class="fa-solid fa-box mr-2" :class="isCommandeRoute ? 'text-indigo-500' : 'text-gray-400'"></i>
                                Commandes
                                <i class="fa-solid fa-chevron-down ml-2 text-xs transition-transform duration-200"
                                   :class="{ 'rotate-180': openCommandes }"></i>
                            </button>

                            <transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95">
                                <div
                                    v-if="openCommandes"
                                    class="absolute left-0 mt-2 w-56 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">

                                    <router-link
                                        :to="{ name: 'commande.index' }"
                                        @click="openCommandes = false"
                                        class="flex items-center px-4 py-2 text-sm transition"
                                        :class="route.name === 'commande.index'
                                            ? 'text-indigo-600 bg-indigo-50 font-medium'
                                            : 'text-gray-700 hover:bg-gray-100'">
                                        <i class="fa-solid fa-list mr-2 text-gray-400 w-4"></i> Mes Commandes
                                    </router-link>
                                    <router-link
                                        :to="{ name: 'commande.create' }"
                                        @click="openCommandes = false"
                                        class="flex items-center px-4 py-2 text-sm transition"
                                        :class="route.name === 'commande.create'
                                            ? 'text-indigo-600 bg-indigo-50 font-medium'
                                            : 'text-gray-700 hover:bg-gray-100'">
                                        <i class="fa-solid fa-plus mr-2 text-gray-400 w-4"></i> Nouvelle Commande
                                    </router-link>
                                </div>
                            </transition>
                        </div>

                        <!-- PANIER -->
                        <a href="#" class="relative p-2 text-gray-400 hover:text-gray-500 transition">
                            <i class="fa-solid fa-cart-shopping text-xl"></i>
                            <span
                                v-if="cartCount > 0"
                                class="absolute top-1 right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                                {{ cartCount }}
                            </span>
                        </a>

                        <!-- BLOC REGROUPÉ : menu "Autre" + déconnexion -->
                        <div class="flex items-center space-x-3">

                            <!-- MENU "AUTRE" : infos utilisateur -->
                            <div class="relative" ref="autreRef">
                                <button
                                    @click="openAutre = !openAutre"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                    <i class="fa-solid fa-circle-user text-xl mr-2 text-gray-400"></i>
                                    <span>{{ user?.name || 'Autre' }}</span>
                                    <i class="fa-solid fa-chevron-down ml-2 text-xs transition-transform duration-200"
                                       :class="{ 'rotate-180': openAutre }"></i>
                                </button>

                                <transition
                                    enter-active-class="transition ease-out duration-200"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95">
                                    <div
                                        v-if="openAutre"
                                        class="absolute right-0 mt-2 w-64 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">

                                        <div class="px-4 py-3 border-b border-gray-100">
                                            <p class="text-sm font-semibold text-gray-900 truncate">
                                                {{ user?.name || 'Utilisateur' }}
                                            </p>
                                            <p class="text-xs text-gray-500 truncate">
                                                {{ user?.email || '' }}
                                            </p>
                                            <span
                                                v-if="user?.role_label"
                                                class="inline-block mt-1 px-2 py-0.5 text-xs font-medium bg-indigo-50 text-indigo-600 rounded-full">
                                                {{ user.role_label }}
                                            </span>
                                        </div>

                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                            <i class="fa-solid fa-user mr-2 text-gray-400 w-4"></i> Mon Profil
                                        </a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                            <i class="fa-solid fa-gear mr-2 text-gray-400 w-4"></i> Paramètres
                                        </a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                            <i class="fa-solid fa-question-circle mr-2 text-gray-400 w-4"></i> Aide
                                        </a>

                                        <div class="border-t border-gray-100 my-1"></div>

                                        <button
                                            @click="logout"
                                            :disabled="loggingOut"
                                            class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium transition">
                                            <i class="fa-solid fa-right-from-bracket mr-2 text-red-500 w-4"></i>
                                            {{ loggingOut ? 'Déconnexion...' : 'Déconnexion' }}
                                        </button>
                                    </div>
                                </transition>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const user = ref(null)
const cartCount = ref(0)
const loggingOut = ref(false)

const openCommandes = ref(false)
const openAutre = ref(false)
const commandesRef = ref(null)
const autreRef = ref(null)

// Le bouton "Commandes" reste actif pour toutes les routes commande.*
const isCommandeRoute = computed(() => route.name?.toString().startsWith('commande.'))

// Ferme les menus si on clique en dehors (équivalent de @click.away d'Alpine)
const handleClickOutside = (event) => {
    if (commandesRef.value && !commandesRef.value.contains(event.target)) {
        openCommandes.value = false
    }
    if (autreRef.value && !autreRef.value.contains(event.target)) {
        openAutre.value = false
    }
}

// Récupère l'utilisateur connecté (nom, email, rôle)
const fetchUser = async () => {
    try {
        const response = await axios.get('/api/user')
        user.value = response.data.user ?? response.data
    } catch (err) {
        console.error("Erreur lors du chargement de l'utilisateur", err)
    }
}

// Déconnexion
const logout = async () => {
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
    fetchUser()
    document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>
