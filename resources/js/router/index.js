import { createRouter, createWebHistory } from 'vue-router';
import ClientHome from '../components/ClientHome.vue';
import ClientDashboard from '../components/ClientDashboard.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: ClientHome
    },
    {
        path: '/client/dashboard',
        name: 'client.dashboard',
        component: ClientDashboard
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
