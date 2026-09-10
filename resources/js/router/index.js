import { createRouter, createWebHistory } from 'vue-router'

// Import des composants de page
const ClientDashboard = () => import('../components/ClientDashboard.vue')
const CommandeCreate  = () => import('../components/CommandeCreate.vue')
const CommandeIndex   = () => import('../components/CommandeIndex.vue') // liste des commandes (à créer si besoin)
const StockIndex      = () => import('../components/StockIndex.vue')

const routes = [
    {
        path: '/client/dashboard',
        name: 'client.dashboard',
        component: ClientDashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/commandes',
        name: 'commande.index',
        component: CommandeIndex,
        meta: { requiresAuth: true }
    },
    {
        path: '/commandes/create',
        name: 'commande.create',
        component: CommandeCreate,
        meta: { requiresAuth: true }
    },
    {
        path: '/stocks',
        name: 'stock.index',
        component: StockIndex,
        meta: { requiresAuth: true }
    },
    {
        // Redirection par défaut vers le dashboard client
        path: '/',
        redirect: { name: 'client.dashboard' }
    },
    {
        // Route 404 -> retour au dashboard (adaptez si vous avez une page dédiée)
        path: '/:pathMatch(.*)*',
        redirect: { name: 'client.dashboard' }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Garde de navigation simple (optionnel, adaptez selon votre gestion d'auth côté front)
router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth) {
        // Exemple : vérifier un cookie de session Laravel / Sanctum déjà présent
        // Si votre auth est gérée côté serveur (session Blade), cette garde
        // peut simplement laisser passer, car Laravel redirige déjà les
        // requêtes non authentifiées vers /login au niveau des routes web.
        next()
    } else {
        next()
    }
})

export default router
