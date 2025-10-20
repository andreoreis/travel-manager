import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'

const app = createApp(App)


app.use(createPinia())
app.use(router)

app.mount('#app')

// inicializa o token
const authStore = useAuthStore()
authStore.initializeToken()
