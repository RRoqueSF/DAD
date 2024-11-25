const httpServer = require('http').createServer()
const io = require("socket.io")(httpServer, {
  cors: {
        origin: [
            'http://localhost:5173',
            'http://127.0.0.1:5500',
            'http://other.domain'
        ],
    methods: ["GET", "POST"]
  }
})
httpServer.listen(8086, () => {
  console.log('listening on *:8086')
})
io.on('connection', (socket) => {
  console.log('client has connected')
  socket.emit('server_to_client', {msg: 'Hello world'})
  socket.on('client_to_server', (data) => {
    console.log('Received ' + data.msg)
    socket.emit('server_to_client', 
             {msg: 'Resending ' + data.msg })
  })
})

