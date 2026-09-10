import { createApp } from 'vue'
import axios from 'axios'
import router from './router'
import App from './App.vue'

// Configuration axios (CSRF Laravel via balise meta déjà présente dans client.blade.php)
axios.defaults.headers.common['X-CSRF-TOKEN'] = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content')

// App.vue contient désormais la NavBar + le <router-view> pour les pages
const app = createApp(App)

app.use(router)
app.mount('#app')
