<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application</title>
    <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
</head>
<body>
    <div id="chat">
        <form id="message-form">
            <input id="message-input" autocomplete="off" placeholder="Type your message here..." />
            <input id="recipient-id" placeholder="Recipient username..." />
            <button id="message-send">Send</button>
        </form>
        <ul id="messages"></ul>
    </div>
    <script>
        const socket = io('http://localhost:3000'); // Update with your Socket.io server URL
        const username = 'User' + Date.now(); // Change as needed

        // Join the chat
        socket.emit('join', username);

        // Listen for incoming messages
        socket.on('receive_message', function(data) {
            const messages = document.getElementById('messages');
            const newMessage = document.createElement('li');
            newMessage.textContent = `${data.user}: ${data.message}`;
            messages.appendChild(newMessage); // Append new message to the list
        });

        // Send message on form submission
        document.getElementById('message-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const message = document.getElementById('message-input').value;
            const recipientId = document.getElementById('recipient-id').value;

            if (message && recipientId) {
                // Display the sent message locally
                const messages = document.getElementById('messages');
                const sentMessage = document.createElement('li');
                sentMessage.textContent = `You: ${message}`; // Show sent message
                messages.appendChild(sentMessage); // Append sent message to the list

                // Send message to the server
                fetch('/api/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: message, recipientId: recipientId })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Message sent:', data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });

                // Clear input fields
                document.getElementById('message-input').value = '';
                // document.getElementById('recipient-id').value = '';
            }
        });
    </script>
</body>
</html>
