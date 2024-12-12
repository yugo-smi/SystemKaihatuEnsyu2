<?php
// セッション開始
session_start();

// ログインしているか確認
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// データベース接続設定
$servername = "localhost:3306";
$dbname = "newlink";
$username = "root";
$password = "root";

$chatUsers = [];

try {
    // PDO接続
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $current_user_id = $_SESSION['user_id']; // ログイン中のユーザーID

    // チャット相手一覧を取得（最新のメッセージ順に並べる）
    $stmt = $conn->prepare("
        SELECT DISTINCT 
            CASE 
                WHEN private_table.send_user_id = :current_user_id THEN private_table.recipient_user_id
                ELSE private_table.send_user_id
            END AS chat_user_id,
            user_table.nickname,
            MAX(private_table.sent_time) AS last_message_time
        FROM 
            private_table
        INNER JOIN 
            user_table 
        ON 
            user_table.id = 
            CASE 
                WHEN private_table.send_user_id = :current_user_id THEN private_table.recipient_user_id
                ELSE private_table.send_user_id
            END
        WHERE 
            private_table.send_user_id = :current_user_id 
            OR private_table.recipient_user_id = :current_user_id
        GROUP BY chat_user_id, user_table.nickname
        ORDER BY last_message_time DESC
    ");
    $stmt->bindParam(':current_user_id', $current_user_id, PDO::PARAM_INT);
    $stmt->execute();

    // 結果を配列に格納
    $chatUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // エラー発生時
    $error_message = $e->getMessage();
}

try {
    // PDO接続
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $current_user_id = $_SESSION['user_id']; // ログイン中のユーザーID

    // チャット相手一覧を取得（最新のメッセージ順に並べる）
    $stmt = $conn->prepare("
        SELECT DISTINCT 
            CASE 
                WHEN private_table.send_user_id = :current_user_id THEN private_table.recipient_user_id
                ELSE private_table.send_user_id
            END AS chat_user_id,
            user_table.nickname,
            MAX(private_table.sent_time) AS last_message_time
        FROM 
            private_table
        INNER JOIN 
            user_table 
        ON 
            user_table.id = 
            CASE 
                WHEN private_table.send_user_id = :current_user_id THEN private_table.recipient_user_id
                ELSE private_table.send_user_id
            END
        WHERE 
            private_table.send_user_id = :current_user_id 
            OR private_table.recipient_user_id = :current_user_id
        GROUP BY chat_user_id, user_table.nickname
        ORDER BY last_message_time DESC
    ");
    $stmt->bindParam(':current_user_id', $current_user_id, PDO::PARAM_INT);
    $stmt->execute();

    // 結果を配列に格納
    $chatUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // エラー発生時
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>トーク履歴</title>
    <link rel="stylesheet" href="css/style_index.css">
    <link rel="stylesheet" href="css/style_talk.css">
</head>

<div id = "header">
    <a href="index.php">
        <img class = "logo"  src="image/logo.png" alt="ロゴ">
    </a>

    <div class="hamburger" id="hamburger">
        <img src="image/hamburger.png" alt="ハンバーガーバー">
    </div>

    <div class = "logotitle">
        <img src="image/logotitle.png" alt="タイトル">
    </div>

    <nav class="menu" id="menu">
        <ul>
            <li><a href="index.php">ホーム</a></li>
            <li><a href="kensaku.php">お相手を検索</a></li>
            <li><a href="talk.php">トーク履歴</a></li>
            <li><a href="favorites.php">お気に入り</a></li>
            <li><a href="profile.php">プロフィール</a></li>
            <?php if ($isLoggedIn): ?>
                
            <?php else: ?>
                <li><a href="logout.php">ログアウト</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <script src="js/index_hamburger.js"></script>
</div>
<script src="js/talk.js"></script>

<body>
    <div id="chat-list-container">
        <h2 class = "page-title">チャットリスト</h2>
        <?php if (!empty($chatUsers)): ?>
            <ul id="chat-list">
                <?php foreach ($chatUsers as $user): ?>
                    <li onclick="location.href='chat.php?partner_id=<?= htmlspecialchars($user['chat_user_id']) ?>'">
                        <strong><?= htmlspecialchars($user['nickname']) ?></strong><br>
                        <small>最終メッセージ: <?= htmlspecialchars($user['last_message_time']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>まだチャットした相手はいません。</p>
        <?php endif; ?>
    </div>
</body>
</html>
