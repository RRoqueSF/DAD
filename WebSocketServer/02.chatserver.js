const httpServer = require('http').createServer();
const io = require("socket.io")(httpServer, {
  cors: {
    origin: [
      'http://localhost:5174',  // Add the correct frontend URL here
      'http://127.0.0.1:5500',  // If you have other frontend origins, add them here
      'http://yourdomain.com',   // Example: other allowed domains
    ],
    methods: ["GET", "POST"],
    allowedHeaders: ["Content-Type"],
    credentials: true // Optional, depending on whether you need cookies/auth headers
  }
});

httpServer.listen(8086, () => {
  console.log('WebSocket server running on http://localhost:8086');
});

io.on('connection', (socket) => {
  console.log('A client has connected');
  socket.on('message', (msg) => {
    io.sockets.emit('message', msg);
  });
});
