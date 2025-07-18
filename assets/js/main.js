document.addEventListener('DOMContentLoaded', function() {
    const chatUsers = document.querySelectorAll('.user');
    const chatWindow = document.querySelector('.chat-window');
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const receiverIdInput = document.getElementById('receiver_id');
    const chatWith = document.getElementById('chat-with');
    let activeUserId = null;
    let lastTimestamp = 0;

    chatUsers.forEach(user => {
        user.addEventListener('click', () => {
            activeUserId = user.dataset.id;
            receiverIdInput.value = activeUserId;
            chatWith.textContent = `Chat with ${user.textContent}`;
            chatMessages.innerHTML = '';
            lastTimestamp = 0;
            loadMessages();
        });
    });

    function loadMessages() {
        if (!activeUserId) return;

        fetch(`controllers/chat_controller.php?receiver_id=${activeUserId}&last_timestamp=${lastTimestamp}`)
            .then(response => response.json())
            .then(data => {
                if (data.messages.length > 0) {
                    data.messages.forEach(message => {
                        const messageElement = document.createElement('div');
                        messageElement.innerHTML = `<strong>${message.sender_username}</strong>: ${message.message}`;
                        chatMessages.appendChild(messageElement);
                    });
                    lastTimestamp = data.last_timestamp;
                }
                setTimeout(loadMessages, 2000);
            });
    }

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('controllers/chat_controller.php', {
            method: 'POST',
            body: formData
        }).then(() => {
            this.reset();
        });
    });
});
