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
$senduser_id = $_SESSION['user_id'];
$recuser_id =  2;
$sql = "SELECT nickname FROM user_table WHERE id = :id";
$sql = "SELECT sent_time, send_user_id,recipient_user_id,message_text,delete_flag FROM user_table WHERE id = :id ORDER BY sent_time DESC" ;

// メッセージ送信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    //$recipient_user_id = $_POST['recipient_user_id'] ?? null;
    $recipient_user_id = 1;
    $message_text = $_POST['message_text'] ?? '';

    if ($recipient_user_id && !empty(trim($message_text))) {
        $sql = "INSERT INTO  private_table(send_user_id, recipient_user_id, message_text) VALUES (:send_user_id, :recipient_user_id, :message_text)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':send_user_id', $senduser_id, PDO::PARAM_INT);
        $stmt->bindParam(':recipient_user_id', $recipient_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':message_text', $message_text, PDO::PARAM_STR);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'メッセージが送信されました']);
        
    } else {
        echo json_encode(['status' => 'error', 'message' => 'メッセージを入力してください']);
        
    }
}
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
            <form method="POST" action="http://localhost/SystemKaihatuEnsyu2/chat.php">
                <input type="text" id="message-input" name="message_text"placeholder="">           
                <button id="send-button">送信</button>          
            </form>
         </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
