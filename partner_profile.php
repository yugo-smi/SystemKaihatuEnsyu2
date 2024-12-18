<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// セッションに保存された前のURLを取得
$backURL = isset($_SESSION['previous_url']) ? $_SESSION['previous_url'] : 'index.php';

// partner_profile.php は常に index.php に戻る
$backURL = 'index.php';

// データベース接続
$host = 'localhost';
$dbname = 'newlink';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ランダムで1人のユーザーを取得
    $current_user_id = $_SESSION['user_id'];
    $stmt = $pdo->query("SELECT * FROM user_table WHERE id != $current_user_id ORDER BY RAND() LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "データベースにユーザーが存在しません。";
        exit;
    }

    // 取得したユーザーの資格と趣味を分割して配列に変換
    $licenses = explode(",", $user['license']);
    $tags = explode(",", $user['tags']);

} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <title>パートナープロフィール</title>
    <link rel="stylesheet" href="./css/style_partner_profile.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div id="header">
        <a href="index.php"><img class="logo" src="image/logo.png" alt="ロゴ"></a>
        <div class="hamburger" id="hamburger"><img src="image/hamburger.png" alt="ハンバーガーメニュー"></div>
        <nav class="menu" id="menu">
            <ul>
                <li><a href="index.php">ホーム</a></li>
                <li><a href="kensaku.php">検索</a></li>
                <li><a href="talk.php">トーク</a></li>
                <li><a href="favorites.php">つながり</a></li>
                <li><a href="profile.php">プロフィール</a></li>
                <li><a href="logout.php">ログアウト</a></li>
            </ul>
        </nav>
        <div class="logotitle">
            <img src="image/logotitle.png" alt="タイトル">
        </div>

    </div>
    <script src="js/index_hamburger.js"></script>

    <div class="profile-info">
    <div class="profile-pic-container">
                <img src="<?= htmlspecialchars($user['image_path'] ?: 'image/default-pic.png', ENT_QUOTES, 'UTF-8') ?>" 
                     alt="プロフィール画像" id="profile-pic" class="profile-pic">
            </div>

        <label>ニックネーム:</label>
        <input type="text" name="nickname" value="<?= htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8') ?>" readonly><br>
        
        <label>保有資格:</label>
        <div class="license-container">
            <?php foreach ($licenses as $license): ?>
                <span class="license-item"><?= htmlspecialchars($license, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endforeach; ?>
        </div>

        <label>趣味:</label>
        <div class="tag-container">
            <?php foreach ($tags as $tag): ?>
                <span class="tag-item"><?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endforeach; ?>
        </div>

        <div class="bio">
            <h3>自己紹介</h3>
            <textarea readonly><?= htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>
        
        <div class="buttons">
            <a href="chat.php?partner_id=<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>" class="button chat-button">チャットする</a>
            <button class="button change-button" onclick="reloadPage()">チェンジする</button>
            <button class="button back-button"><a href="index.php">前の画面に戻る</a></button>
        </div>

        <script>
            function reloadPage() {
                location.href = location.pathname + "?t=" + new Date().getTime();
            }
        </script>
    </div>
</body>
</html>
