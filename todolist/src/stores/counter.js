import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useCounterStore = defineStore('counter', () => {
  const socket = ref(null)  // Initialize socket as a reactive reference
  
  const count = ref(0)

  // Function to send a message from Pinia store
  function sendSocketMessageFromPinia(msg) {
    if (socket.value) {
      console.log('Send message from Pinia - Socket ID = ' + socket.value.id + ' - Message = ' + msg)
      socket.value.emit('message', msg + ' (from Pinia)')
    } else {
      console.warn('Socket is not initialized yet!')
    }
  }

  // Listen for socket events
  const setSocket = (socketInstance) => {
    socket.value = socketInstance
    socket.value.on("connect", () => {
      console.log('Pinia Store - Connected - Socket ID = ' + socket.value.id)
    })

    socket.value.on("disconnect", () => {
      console.log('Pinia Store - Disconnected - Socket ID = ' + socket.value.id)
    })

    socket.value.on("message", (data) => {
      console.log('Pinia Store - Received "' + data + '" from WebSocket server')
      count.value++
    })
  }

  return { count, sendSocketMessageFromPinia, setSocket }
})
