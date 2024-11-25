import './assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import axios from 'axios'
import { io } from "socket.io-client";


import App from './App.vue'
import router from './router'
import ErrorMessage from './components/common/ErrorMessage.vue'

// Create Vue app instance
const app = createApp(App)

// Set up global state management
app.use(createPinia())
app.use(router)

// Set up socket.io connection
const socket = io("http://localhost:8086");
app.provide('socket', socket);

// Default Axios configuration
axios.defaults.baseURL = 'http://localhost/api'

// Register global components (e.g., error messages)
app.component('ErrorMessage', ErrorMessage)

// Mount the app
app.mount('#app')
