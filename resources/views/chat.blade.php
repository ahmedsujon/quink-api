<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application</title>
    <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
    <style>
        .message {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 5px;
        }
        .message small {
            display: block;
            color: #888;
        }

        ul li {
            list-style: none !important;
        }
    </style>
</head>
<body>
    <div id="chat">
        <ul id="messages" style="padding-left: 0px !important;"></ul>
    </div>
    <script>
        const socket = io('http://localhost:3000'); // Change to your server URL
        const username = 'nzhridoy';

        socket.emit('join', username);

        socket.on('receive_message', function(data) {
            const messages = document.getElementById('messages');
            const newMessage = document.createElement('li');
            newMessage.classList.add('message');

            newMessage.innerHTML = `
                <p style="margin-top: 0px;">${data.content.message}</p>
                <small>ID: ${data.content.id} • CHATID: ${data.content.chat_id} • SENDER: ${data.content.sender} • RECEIVER: ${data.content.receiver}</small>
            `;
            messages.appendChild(newMessage);
        });
    </script>
</body>
</html>
