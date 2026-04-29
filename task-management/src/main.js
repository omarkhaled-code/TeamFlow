import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

// Language File
import i18n from './locales'

// For Channels and listening
import './echo'


const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(i18n) // #add i18n
app.mount('#app')
