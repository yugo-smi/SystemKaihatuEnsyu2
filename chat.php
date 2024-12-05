<?php
// セッション開始
session_start();
$servername = "localhost";
$dbname = "newlink";
$username = "root";
$password = "root";

try {
    // データベース接続
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $current_user_id = $_SESSION['user_id']; // ログイン中のユーザーID
    $partner_id = $_GET['partner_id']; // 相手のユーザーID

    // リクエスト処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $message_text = $_POST['message_text'];

        // メッセージ送信処理
        $stmt = $conn->prepare("
            INSERT INTO private_table (send_user_id, recipient_user_id, message_text, delete_flag)
            VALUES (:send_user_id, :recipient_user_id, :message_text, 0)
        ");
        $stmt->bindParam(':send_user_id', $current_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':recipient_user_id', $partner_id, PDO::PARAM_INT);
        $stmt->bindParam(':message_text', $message_text, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode(['success' => true]);
        exit;
    } elseif (isset($_GET['action']) && $_GET['action'] === 'fetch') {
        // メッセージ取得処理
        $stmt = $conn->prepare("
            SELECT 
                private_table.message_text,
                private_table.sent_time,
                sender.nickname AS sender_name,
                recipient.nickname AS recipient_name,
                private_table.send_user_id
            FROM 
                private_table
            INNER JOIN 
                user_table AS sender ON private_table.send_user_id = sender.id
            INNER JOIN 
                user_table AS recipient ON private_table.recipient_user_id = recipient.id
            WHERE 
                (private_table.send_user_id = :current_user_id AND private_table.recipient_user_id = :partner_id)
                OR
                (private_table.send_user_id = :partner_id AND private_table.recipient_user_id = :current_user_id)
            ORDER BY 
                private_table.sent_time ASC
        ");
        $stmt->bindParam(':current_user_id', $current_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }
    
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}



?>

<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/chat.css">
    <title>チャット</title>
    <script>
        const currentUserId = <?= json_encode($current_user_id) ?>;
        const partnerId = <?= json_encode($partner_id) ?>;

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
    </script>
    <script>
	    document.addEventListener('DOMContentLoaded', function(){

			document.getElementById('chat-box').addEventListener('scroll', function(){
				console.log("横スクロール量:"+ document.getElementById('chat-box').scrollX);
				console.log("縦スクロール量：" + document.getElementById('chat-box').scrollY);
                console.log("横スクロール位置：" + document.getElementById('chat-box').scrollTop);
				console.log("縦スクロール位置：" + document.getElementById('chat-box').scrollLeft);
                document.cookie = "scrollTop="+document.getElementById('chat-box').scrollTop;
			});

		});


	</script>
</head>
<header>
         <div id = "header">
             <a href="index.php">
                 <img class = "logo"  src="image/logo.png" alt="ロゴ">
             </a>

             <div class="hamburger" id="hamburger">
                 <img src="image/hamburger.png" alt="ハンバーガーバー">
                 
             </div>

             <!-- メニュー -->
             <nav class="menu" id="menu">
                 <ul>
                     <li><a href="index.php">ホーム</a></li>
                     <li><a href="kensaku.php">お相手を検索</a></li>
                     <li><a href="message.php">スレッド</a></li>
                     <li><a href="chat.php">メッセージ</a></li>
                     <li><a href="profile.php">プロフィール</a></li>
                 </ul>
             </nav>

             <div class = "logotitle">
                 <img src="image/logotitle.png" alt="タイトル">
             </div>
         </div>
     </header>
<body>
    <div class="chat-container">
        <div id="chat-box" class="chat-box"></div>
        <div class="input-area">
            <input type="text" id="message-input" placeholder="メッセージを入力してください">
            <button id="send-button">送信</button>
        </div>
    </div>
</body><div class="chat-box">
  <div id="box" class="xhat-box">BOX</div>
</div>
<script type="text/javascript" src="js/test.js"></script>



</html>
