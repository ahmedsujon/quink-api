// server.js

const express = require('express');
const http = require('http');
const socketIo = require('socket.io');

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    }
});

// Middleware to parse JSON requests
app.use(express.json());

let users = {};

io.on('connection', (socket) => {
    console.log('New client connected');

    socket.on('join', (username) => {
        users[socket.id] = username; // Store username with socket ID
        console.log(`${username} joined the chat`);
    });

    socket.on('disconnect', () => {
        console.log('Client disconnected');
        delete users[socket.id]; // Clean up when user disconnects
    });
});

// API endpoint to send a message to a specific user
app.post('/send_message', (req, res) => {
    const { message, recipientId } = req.body; // Expect recipient ID from the request
    const recipientSocketId = Object.keys(users).find(key => users[key] === recipientId);

    if (recipientSocketId) {
        io.to(recipientSocketId).emit('receive_message', {
            user: users[recipientSocketId],
            message: message
        });
        res.status(200).json({ status: 'Message sent' });
    } else {
        res.status(404).json({ status: 'User not found' });
    }
});

server.listen(3000, () => {
    console.log('Socket.io server running on port 3000');
});
