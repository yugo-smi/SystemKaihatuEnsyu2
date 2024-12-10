
        const currentUserId =  json_encode($current_user_id) ;
        const partnerId = 1= json_encode($partner_id) ;

        // メッセージ送信
        async function sendMessage() {
            const messageInput = document.getElementById('message-input');
            const messageText = messageInput.value.trim();
            if (!messageText) return;

            try {
                const response = await fetch('chat.php?partner_id=' + partnerId, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `message_text=${encodeURIComponent(messageText)}`
                });
                const result = await response.json();

                if (result.success) {
                    messageInput.value = '';
                    fetchMessages(); // メッセージ更新
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }
        }

        // メッセージ取得
        async function fetchMessages() {
            try {
                const response = await fetch('chat.php?action=fetch&partner_id=' + partnerId);
                const messages = await response.json();

                const chatBox = document.getElementById('chat-box');
                chatBox.innerHTML = ''; // チャットボックスをリセット

                messages.forEach(msg => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `chat-message ${msg.send_user_id == currentUserId ? 'sent' : 'received'}`;
                    messageDiv.innerHTML = `
                        <p class="chat-sender">${msg.send_user_id == currentUserId ? 'あなた' : msg.sender_name}:</p>
                        <p class="chat-text">${msg.message_text}</p>
                        <span class="chat-time">${msg.sent_time}</span>
                    `;
                    chatBox.appendChild(messageDiv);
                });
                console.log(document.cookie);
                // スクロールを最新メッセージに移動
                if(document.cookie.split(";").some((item)=>item.includes("scrollTop="))){
                    const scrollTop =document.cookie.split("; ").find((row)=>row.startsWith("scrollTop=")).split("=")[1];
                                    
                    chatBox.scrollTop = scrollTop;
                    console.log(scrollTop);
                }
                else {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
        
            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        }

        // 初期化とイベントリスナーの追加
        document.addEventListener('DOMContentLoaded', () => {
            fetchMessages(); // 初回メッセージ取得
            document.getElementById('send-button').addEventListener('click', event => {
                event.preventDefault();
                sendMessage();
            });
            setInterval(fetchMessages, 2000); // メッセージを2秒ごとに更新
        });






        document.addEventListener('DOMContentLoaded', () => {
    const chatBox = document.getElementById('chat-box');
    const scrollToBottomButton = document.getElementById('scroll-to-bottom');

    // チャットボックスのスクロール監視
    chatBox.addEventListener('scroll', () => {
        const isAtBottom = chatBox.scrollHeight - chatBox.scrollTop === chatBox.clientHeight;
        scrollToBottomButton.style.display = isAtBottom ? 'none' : 'block';
    });

    // ボタンのクリックイベント
    scrollToBottomButton.addEventListener('click', () => {
        chatBox.scrollTo({
            top: chatBox.scrollHeight,
            behavior: 'smooth',
        });
    });
});
