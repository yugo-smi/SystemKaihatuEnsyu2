<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>NEW LINK</title>  
        <link rel="stylesheet" href="./css/search_pofile.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <!-- body（本文） -->
    <body>
    <?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// セッションに保存された前のURLを取得
$backURL = isset($_SESSION['previous_url']) ? $_SESSION['previous_url'] : 'index.php'; // デフォルトはindex.php

// データベース接続
$host = 'localhost'; 
$dbname = 'newlink'; 
$username = 'root'; 
$password = 'root'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $userId = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM user_table WHERE id = :id");
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$user) {
        echo "データベースにユーザーが存在しません。";
        exit;
    }

    // 資格と趣味を取得
    $licenses = explode(",", $user['license']); // 資格を配列に変換
    $tags = explode(",", $user['tags']); // 趣味を配列に変換

    // お気に入り状態を確認
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM favorite_users WHERE user_id = :current_user_id AND favorite_user_id = :profile_user_id");
    $stmt->bindValue(':current_user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':profile_user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $isFavorite = $stmt->fetchColumn() > 0;
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
    exit;
}
?>
        <!-- ヘッダー -->
        <div id="header">
            <a href="index.php">
                <img class="logo" src="image/logo.png" alt="ロゴ">
            </a>

            <div class="hamburger" id="hamburger">
                <img src="image/hamburger.png" alt="ハンバーガーバー">
            </div>

            <!-- メニュー -->
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

            <!-- 保有資格 -->
            <label>保有資格:</label>
            <div class="license-container">
                <?php foreach ($licenses as $license): ?>
                    <span class="license-item"><?= htmlspecialchars($license, ENT_QUOTES, 'UTF-8') ?></span>
                <?php endforeach; ?>
            </div>

            <!-- 趣味 -->
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
            
            <!-- チャットボタン -->
            <div class="chat-or-change">
                <button class="button chat-button">
                    <a href="chat.php?partner_id=<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>">チャットする</a>
                </button>
            </div>

            <!-- お気に入りボタン -->
            <div class="favorite-container">
                <form method="POST" action="search_profile.php?id=<?= htmlspecialchars($userId, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="favorite_user_id" value="<?= htmlspecialchars($userId, ENT_QUOTES, 'UTF-8') ?>">
                <?php if ($isFavorite): ?>
                <button type="submit" name="action" value="remove" class="button favorite-button">お気に入り解除</button>
                <?php else: ?>
                    <button type="submit" name="action" value="add" class="button favorite-button">お気に入り追加</button>
                <?php endif; ?>
                </form>
            </div>

            <div class="back-to-search">
                <button class="button back-button">
                    <a href="<?= htmlspecialchars($backURL, ENT_QUOTES, 'UTF-8') ?>">前の画面に戻る</a>
                </button>
            </div>

            <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['favorite_user_id'])) {
    $favoriteUserId = $_POST['favorite_user_id'];
    $action = $_POST['action'];

    try {
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO favorite_users (user_id, favorite_user_id) VALUES (:user_id, :favorite_user_id)");
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':favorite_user_id', $favoriteUserId, PDO::PARAM_INT);
            $stmt->execute();
        } elseif ($action === 'remove') {
            $stmt = $pdo->prepare("DELETE FROM favorite_users WHERE user_id = :user_id AND favorite_user_id = :favorite_user_id");
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':favorite_user_id', $favoriteUserId, PDO::PARAM_INT);
            $stmt->execute();
        }
    } catch (PDOException $e) {
        echo "エラー: " . $e->getMessage();
        exit;
    }

    // リダイレクト処理
    header("Location: search_profile.php?id=" . htmlspecialchars($userId, ENT_QUOTES, 'UTF-8'));
    exit();
}
?>
    </body>
</html>
