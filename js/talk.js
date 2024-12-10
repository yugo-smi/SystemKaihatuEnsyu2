document.addEventListener("DOMContentLoaded", function () {
    const chatMessages = document.getElementById("chat-messages");
    const chatForm = document.getElementById("chat-form");
    const messageInput = document.getElementById("message-input");
    const partnerId = new URLSearchParams(window.location.search).get("partner_id");

    // トーク履歴を取得して表示
    function fetchMessages() {
        fetch(`chat.php?action=fetch&partner_id=${partnerId}`)
            .then(response => response.json())
            .then(messages => {
                chatMessages.innerHTML = ""; // メッセージ表示をリセット
                messages.forEach(message => {
                    const messageDiv = document.createElement("div");
                    messageDiv.classList.add("message");
                    messageDiv.classList.add(message.send_user_id == partnerId ? "partner" : "me");
                    messageDiv.textContent = `${message.sender_name}: ${message.message_text}`;
                    chatMessages.appendChild(messageDiv);
                });
                chatMessages.scrollTop = chatMessages.scrollHeight; // 自動スクロール
            });
    }

    // メッセージ送信処理
    chatForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const messageText = messageInput.value;
        fetch(`chat.php?partner_id=${partnerId}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ message_text: messageText })
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    messageInput.value = ""; // 入力フィールドをクリア
                    fetchMessages(); // トーク履歴を更新
                }
            });
    });

    // 定期的にメッセージを更新
    setInterval(fetchMessages, 3000); // 3秒ごとにトーク履歴を更新
});