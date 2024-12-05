<?php
session_start();
$host = 'localhost';
$dbname = 'newlink';
$username = 'root';
$password = 'root';

// データベース接続
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
    exit();
}

// ユーザー認証確認
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// アクション処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['favorite_user_id'])) {
        $favoriteUserId = $_POST['favorite_user_id'];
        $action = $_POST['action'];

        try {
            if ($action === 'add') {
                // chain追加
                $stmt = $pdo->prepare("INSERT INTO favorite_users (user_id, favorite_user_id) VALUES (:user_id, :favorite_user_id)");
                $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindValue(':favorite_user_id', $favoriteUserId, PDO::PARAM_INT);
                $stmt->execute();
            } elseif ($action === 'remove') {
                // chain解除
                $stmt = $pdo->prepare("DELETE FROM favorite_users WHERE user_id = :user_id AND favorite_user_id = :favorite_user_id");
                $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindValue(':favorite_user_id', $favoriteUserId, PDO::PARAM_INT);
                $stmt->execute();
            }
        } catch (PDOException $e) {
            echo "エラー: " . $e->getMessage();
            exit();
        }

        // リダイレクトしてフォーム再送信防止
        header("Location: index.php");
        exit();
    }
}

// chain一覧表示
try {
    $stmt = $pdo->prepare("
        SELECT u.id, u.nickname, u.bio, u.image_path
        FROM favorite_users f
        JOIN user_table u ON f.favorite_user_id = u.id
        WHERE f.user_id = :user_id
    ");
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
    exit();
}
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEW LINK</title>
    <link rel="stylesheet" href="css/style_index.css">
    <style>
        /* 簡易スタイル */
        .favorites-container { display: flex; flex-wrap: wrap; gap: 20px; }
        .favorite-card { border: 1px solid #ddd; padding: 10px; width: 100px; text-align: center; }
        .favorite-card img { width: 50px; height: 50px; border-radius: 50%; }
        .user-info { margin-top: 10px; }
        .actions { margin-top: 10px; }
        .actions button { margin-right: 5px; }
    </style>
</head>

<body>
    <!-- ヘッダー -->
    <header>
        <div id="header">
            <a href="index.php">
                <img class="logo" src="image/logo.png" alt="ロゴ">
            </a>

            <div class="hamburger" id="hamburger">
                <img src="image/hamburger.png" alt="ハンバーガーメニュー">
            </div>

            <!-- メニュー -->
            <nav class="menu" id="menu">
                <ul>
                    <li><a href="index.php">ホーム</a></li>
                    <li><a href="kensaku.php">お相手を検索</a></li>
                    <li><a href="message.php">スレッド</a></li>
                    <li><a href="chat.php">メッセージ</a></li>
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
    </header>
    <!--/ヘッダー-->

    <!-- メイン -->
    <div class="main-container">
        <div class="slideshow-container">
            <div class="slide">
                <img src="./image/deai.png" alt="Slide 1">
            </div>
            <div class="slide">
                <img src="./image/akasi.png" alt="Slide 2">
            </div>
            <div class="slide">
                <img src="./image/default-pic.png" alt="Slide 3">
            </div>
        </div>
        <div class="dots-container">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
        </div>
    </div>

    <!-- ランダムマッチングボタン -->
    <a href="./partner_profile.php" class="random_matching_button">ランダムマッチングボタン</a>

    <!-- スクリプト -->
    <script src="js/index_hamburger.js"></script>
    <script src="./js/index_slideshow.js"></script>
    <!--/メイン -->

    <!-- フッター -->
    <footer>
        <!-- フッター内容 -->
    </footer>
    <!--/フッター -->
    <h2 class="favorites-title">chain一覧</h2>
    <div class="favorites-container">
        <?php if (empty($favorites)): ?>
            <p class="nofavorite">chainしたユーザーはいません。</p>
        <?php else: ?>
            <?php foreach ($favorites as $user): ?>
                <div class="favorite-card">
                    <img src="<?= htmlspecialchars($user['image_path'] ?: 'image/default-pic.png', ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像">
                    <div class="user-info">
                        <h3><?= htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="actions">
                            <!-- プロフィール表示ボタン -->
                            <form method="GET" action="search_profile.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <button class="favorites-button" type="submit">プロフィールを見る</button>
                            </form>
                            <!-- chain解除フォーム -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="favorite_user_id" value="<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="action" value="remove">
                                <button class="favorites-button" type="submit">chain解除</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
