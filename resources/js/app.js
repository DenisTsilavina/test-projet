import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { createApp } from 'vue';
import Hello from './components/Hello.vue';
import ClientDashboard from './components/ClientDashboard.vue';
import router from './router';

const app = createApp({});

// Enregistrement des composants Vue
app.component('hello-component', Hello);
app.component('client-dashboard', ClientDashboard);

// Utilisation du Router Vue
app.use(router);

// Montage de l'application Vue
app.mount('#app');
