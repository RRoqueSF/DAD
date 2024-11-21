import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import axios from 'axios'

import App from './App.vue'
import router from './router'

import ErrorMessage from './components/common/ErrorMessage.vue'

const app = createApp(App)

app.use(createPinia())
app.use(router)

// Default Axios configuration
axios.defaults.baseURL = 'http://localhost/api'


app.component('ErrorMessage', ErrorMessage)

app.mount('#app')
