<?php
//// セッション開始
session_start();

// ログインしているか確認し、していない場合はログインページにリダイレクト
if (!isset($_SESSION['user_id']) || !isset($_SERVER['HTTP_REFERER'])) {
    // ログインしていない、またはリファラーが不正な場合
    header("Location: login.php");
    exit();
}

// データベース接続設定
$servername = "localhost:3306";
$dbname = "newlink";
$username = "root";
$password = "root";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
    exit();
}

// ログインしているユーザーのIDを取得
$user_id = $_SESSION['user_id'];

$sql = "SELECT nickname FROM user_table WHERE id = :id";
$sql = "SELECT sent_time, send_user_id,recipient_user_id,message_text,delete_flag FROM user_table WHERE id = :id";

?>
<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/chat.css">
</head>
<body>
    <div class="chat-container">
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
                        <li><a href="profile.php">プロフィール</a></li>
                        <li><a href="">PayPay</a></li>
                        <li><a href="">QuickPay</a></li>
                    </ul>
                </nav>
        
                <div class = "logotitle">
                    <img src="image/logotitle.png" alt="タイトル">
                </div>
            </div>
        
        </header>
        <div id="chat-box" class="chat-box"></div>
        <div class="input-area">
            
            <input type="text" id="message-input" placeholder="">
            <button id="send-button">送信</button>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
