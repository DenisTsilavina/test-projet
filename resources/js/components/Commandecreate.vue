<template>
    <div class="py-12 px-4 sm:px-6 lg:px-8 text-gray-700">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-md border border-gray-100">

            <div class="mb-8 border-b border-gray-200 pb-4">
                <h3 class="text-xl font-bold text-gray-900">Nouvelle demande</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Remplissez le formulaire ci-dessous pour enregistrer votre commande. Le montant sera calculé
                    automatiquement une fois les ingrédients et la main d'œuvre ajoutés.
                </p>
            </div>

            <!-- Message de succès -->
            <div v-if="successMessage" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ successMessage }}</span>
            </div>

            <!-- Erreurs de validation -->
            <div v-if="Object.keys(errors).length" class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg text-sm">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>Veuillez corriger les erreurs suivantes :</span>
                </div>
                <ul class="list-disc pl-5 space-y-1 text-xs">
                    <li v-for="(messages, field) in errors" :key="field">
                        {{ messages[0] }}
                    </li>
                </ul>
            </div>

            <form @submit.prevent="submitCommande" class="space-y-6">

                <!-- La référence n'est pas saisie ici : générée automatiquement à l'enregistrement (ex: CMD-2026-0001) -->

                <!-- Source de la commande -->
                <div>
                    <label for="type_selection" class="block text-sm font-semibold text-gray-700 mb-2">Source de la commande</label>
                    <select
                        id="type_selection"
                        v-model="form.typeSelection"
                        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="autre">Saisir une désignation personnalisée</option>
                        <option value="stock">Choisir depuis le Stock disponible</option>
                    </select>
                </div>

                <!-- Bloc Stock (affiché si "stock" sélectionné) -->
                <div v-if="form.typeSelection === 'stock'">
                    <label for="stock_select" class="block text-sm font-semibold text-gray-700 mb-2">Sélectionner dans le stock</label>
                    <select
                        id="stock_select"
                        v-model="selectedStock"
                        @change="onStockSelected"
                        :disabled="loadingStocks"
                        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">
                            {{ loadingStocks ? 'Chargement du stock...' : '-- Choisir un élément du stock --' }}
                        </option>
                        <option v-for="stock in stocks" :key="stock.id" :value="stock.designation">
                            {{ stock.designation }}
                        </option>
                    </select>
                </div>

                <!-- Désignation -->
                <div>
                    <label for="designation" class="block text-sm font-semibold text-gray-700 mb-2">Désignation de la commande</label>
                    <input
                        type="text"
                        id="designation"
                        v-model="form.designation"
                        placeholder="Ex: Prestation informatique, Matériel..."
                        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        required>
                </div>

                <!-- Quantité -->
                <div>
                    <label for="quantite" class="block text-sm font-semibold text-gray-700 mb-2">Quantité</label>
                    <input
                        type="number"
                        id="quantite"
                        v-model.number="form.quantite"
                        min="1"
                        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        required>
                </div>

                <!--
                    Champs "Montant" et "Statut" retirés du formulaire client :
                    - Le montant est calculé automatiquement à partir des ingrédients et de la main d'œuvre.
                    - Le statut est fixé à "en_attente" à la création et n'est modifiable que par un administrateur.
                -->

                <!-- Note -->
                <div>
                    <label for="note" class="block text-sm font-semibold text-gray-700 mb-2">Note ou détails additionnels (Optionnel)</label>
                    <textarea
                        id="note"
                        v-model="form.note"
                        rows="3"
                        placeholder="Ajoutez des précisions ici..."
                        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </textarea>
                </div>

                <!-- Boutons d'action -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <router-link
                        :to="{ name: 'commande.index' }"
                        class="px-5 py-3 rounded-lg text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                        Annuler
                    </router-link>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="bg-indigo-600 text-white px-5 py-3 rounded-lg text-sm font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition shadow disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ submitting ? 'Envoi en cours...' : 'Confirmer et lancer la commande' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const stocks = ref([])
const loadingStocks = ref(false)
const selectedStock = ref('')
const submitting = ref(false)
const successMessage = ref('')
const errors = ref({})

const form = reactive({
    typeSelection: 'autre',
    designation: '',
    quantite: 1,
    note: ''
})

// Récupère la liste du stock disponible (pour le select "stock")
const fetchStocks = async () => {
    try {
        loadingStocks.value = true
        const response = await axios.get('/api/stocks')
        stocks.value = response.data.stocks ?? response.data
    } catch (err) {
        console.error("Erreur lors du chargement du stock", err)
    } finally {
        loadingStocks.value = false
    }
}

// Remplit automatiquement la désignation quand un élément du stock est choisi
const onStockSelected = () => {
    if (selectedStock.value) {
        form.designation = selectedStock.value
    }
}

// Soumission du formulaire
const submitCommande = async () => {
    submitting.value = true
    successMessage.value = ''
    errors.value = {}

    try {
        const response = await axios.post('/api/commandes', {
            designation: form.designation,
            quantite: form.quantite,
            note: form.note
        })

        successMessage.value = response.data.message || 'Commande enregistrée avec succès.'

        // Réinitialisation du formulaire
        form.typeSelection = 'autre'
        form.designation = ''
        form.quantite = 1
        form.note = ''
        selectedStock.value = ''
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {}
        } else {
            errors.value = { general: ['Une erreur est survenue lors de l\'enregistrement.'] }
        }
        console.error(err)
    } finally {
        submitting.value = false
    }
}

onMounted(() => {
    fetchStocks()
})
</script>
