const chatPartnerId = 2; // チャット相手のIDを動的に設定する

// メッセージ送信
async function sendMessage() {
    const messageInput = document.getElementById('message-input');
    const messageText = messageInput.value;

    if (!messageText.trim()) return; // 空のメッセージは送信しない

    try {
        const response = await fetch('send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `recipient_id=${chatPartnerId}&message_text=${encodeURIComponent(messageText)}`
        });

        const result = await response.json();
        if (result.success) {
            messageInput.value = ''; // 入力欄をクリア
            fetchMessages(); // 最新メッセージを取得
        }
    } catch (error) {
        console.error('Error sending message:', error);
    }
}

// メッセージ取得
async function fetchMessages() {
    try {
        const response = await fetch('fetch_messages.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `chat_partner_id=${chatPartnerId}`
        });

        const messages = await response.json();
        const chatBox = document.querySelector('.chat-box');
        chatBox.innerHTML = ''; // チャットボックスをクリア

        // メッセージを表示
        messages.forEach(msg => {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${msg.sender_name === 'あなたの名前' ? 'sent' : 'received'}`;

            messageDiv.innerHTML = `
                <div class="message-bubble">
                    <div class="chat-sender">${msg.sender_name}</div>
                    <div class="chat-text">${msg.message_text}</div>
                    <div class="chat-time">${msg.sent_time}</div>
                </div>
            `;
            chatBox.appendChild(messageDiv);
        });

        // 最新メッセージへスクロール
       // chatBox.scrollTop = chatBox.scrollHeight;
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
}

// イベントリスナーの設定
document.getElementById('send-button').addEventListener('click', sendMessage);

// メッセージを定期的に取得
setInterval(fetchMessages, 2000);




