<script setup>
import { ref, inject, onMounted } from 'vue'
import { useCounterStore } from "@/stores/counter.js"

// Inject socket instance
const socket = inject('socket')

// Initialize Pinia store
const counterStore = useCounterStore()

const msgToSend = ref('')
const messages = ref([])

// Ensure socket is passed to Pinia store once it's available
onMounted(() => {
  if (socket) {
    counterStore.setSocket(socket)  // Set the socket instance in the store
  }
})

// Send message from component (via socket directly)
const sendMsg = () => {
  if (msgToSend.value.trim() !== '') {
    console.log('Send message from Chat Component - Socket ID = ' + socket.id + ' - Message = ' + msgToSend.value) 
    socket.emit('message', msgToSend.value)
    msgToSend.value = ""
  }
}

// Send message via Pinia store
const sendMsgFromPinia = () => {
  if (msgToSend.value.trim() !== '') {
    console.log('Sending message from Pinia...')
    counterStore.sendSocketMessageFromPinia(msgToSend.value)
    msgToSend.value = ""
  }
}

// Listen to socket events
socket.on("connect", () => {
  console.log('Chat Component - Connected - Socket ID = ' + socket.id)
})

socket.on("disconnect", () => {
  console.log('Chat Component - Disconnected - Socket ID = ' + socket.id)
})

socket.on("message", (data) => {
  messages.value.unshift(data)
  console.log('Chat Component - Received "' + data + '" from WebSocket server')
})
</script>

<template>
  <div style="max-width: 640px;">
    <h1>Chat</h1>
    <input type="text" v-model="msgToSend" @keyup.enter="sendMsg" placeholder="Type a message" style="width: 100%; padding: 10px;">
    <hr>
    <p>Total Messages Received From The Server (check Pinia Store code) = {{ counterStore.count }}</p>
    <hr>
    <div>
      <button @click="sendMsg">Send Message to Server</button>
      <button @click="sendMsgFromPinia">Send Message From Pinia</button>
    </div>
    <hr>
    <ul>
      <li v-for="(msg, index) in messages" :key="index">
        {{ msg }}
      </li>
    </ul>
  </div>
</template>

<style scoped>
button {
  padding: 10px;
  margin-right: 10px;
  cursor: pointer;
}
button:hover {
  background-color: #ddd;
}
</style>
