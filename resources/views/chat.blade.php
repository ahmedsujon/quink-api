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
        const socket = io('{{ env('SOCKET_SERVER') }}'); // Change to your server URL
        const username = 'nzhridoy';

        socket.emit('join', username);

        socket.on('receive_message', function(data) {
            console.log(data);
            const messages = document.getElementById('messages');
            const newMessage = document.createElement('li');
            newMessage.classList.add('message');

            if (data.content.file_type == 'image') {
                var file = '<img src="' + data.content.file + '" style="height: 70px; width: auto;" /><br><br>';
            } else if (data.content.file_type == 'file') {
                var file = '<a href="' + data.content.file + '" download>Download File</a><br><br>';
            } else {
                var file = '';
            }

            newMessage.innerHTML = `
                ${file}
                <p style="margin-top: 0px;">${data.content.message}</p>
                <small>TIME: ${data.content.time} • ID: ${data.content.id} • CHATID: ${data.content.chat_id} • SENDER: ${data.content.sender} • RECEIVER: ${data.content.receiver}</small>
            `;
            messages.appendChild(newMessage);
        });
    </script>
</body>

</html>
