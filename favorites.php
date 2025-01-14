<?php
session_start();
$_SESSION['previous_url'] = $_SERVER['REQUEST_URI'];

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
                // お気に入り追加
                $stmt = $pdo->prepare("INSERT INTO favorite_users (user_id, favorite_user_id) VALUES (:user_id, :favorite_user_id)");
                $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindValue(':favorite_user_id', $favoriteUserId, PDO::PARAM_INT);
                $stmt->execute();
            } elseif ($action === 'remove') {
                // お気に入り解除
                $stmt = $pdo->prepare("DELETE FROM favorite_users WHERE user_id = :user_id AND favorite_user_id = :favorite_user_id");
                $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindValue(':favorite_user_id', $favoriteUserId, PDO::PARAM_INT);
                $stmt->execute();
            } elseif ($action === 'remove_chain') {
                // チェーン解除（相互お気に入り解除）
                $stmt = $pdo->prepare("
                    DELETE FROM favorite_users
                    WHERE (user_id = :user_id AND favorite_user_id = :favorite_user_id)
                       OR (user_id = :favorite_user_id AND favorite_user_id = :user_id)
                ");
                $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindValue(':favorite_user_id', $favoriteUserId, PDO::PARAM_INT);
                $stmt->execute();
            }
        } catch (PDOException $e) {
            echo "エラー: " . $e->getMessage();
            exit();
        }

        // リダイレクトしてフォーム再送信防止
        header("Location: favorites.php");
        exit();
    }
}

// チェーン（相互お気に入り）の抽出
try {
    $stmt = $pdo->prepare("
        SELECT u.id, u.nickname, u.bio, u.image_path
        FROM favorite_users f1
        JOIN favorite_users f2 ON f1.user_id = f2.favorite_user_id AND f2.user_id = f1.favorite_user_id
        JOIN user_table u ON f1.favorite_user_id = u.id
        WHERE f1.user_id = :user_id
    ");
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $chains = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // チェーンのユーザーIDを配列で取得
    $chainUserIds = array_column($chains, 'id');
} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
    exit();
}

// お気に入り一覧表示（チェーンを除外）
try {
    $query = "
        SELECT u.id, u.nickname, u.bio, u.image_path
        FROM favorite_users f
        JOIN user_table u ON f.favorite_user_id = u.id
        WHERE f.user_id = :user_id
    ";

    // チェーンに含まれるユーザーを除外
    if (!empty($chainUserIds)) {
        $namedPlaceholders = [];
        foreach ($chainUserIds as $index => $id) {
            $namedPlaceholders[] = ":chain_id_$index";
        }
        $query .= " AND u.id NOT IN (" . implode(',', $namedPlaceholders) . ")";
    }

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

    // チェーンのIDをバインド
    if (!empty($chainUserIds)) {
        foreach ($chainUserIds as $index => $id) {
            $stmt->bindValue(":chain_id_$index", $id, PDO::PARAM_INT);
        }
    }

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
    <link rel="stylesheet" href="css/style_favorites.css">
    <title>お気に入り一覧</title>
    <style>
        /* 簡易スタイル */
        .favorites-container { display: flex; flex-wrap: wrap; gap: 20px; }
        .favorite-card { border: 1px solid #ddd; padding: 10px; width: 100px; text-align: center; }
        .favorite-card img { width: 50px; height: 50px; border-radius: 50%; }
        .chain-card { border: 1px solid #ddd; padding: 10px; width: 100px; text-align: center; }
        .chain-card img { width: 50px; height: 50px; border-radius: 50%; }
        .user-info { margin-top: 10px; }
        .actions { margin-top: 10px; }
        .actions button { margin-right: 5px; }
    </style>
</head>
<body>
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

<h1 class="title"></h1>

<!-- チェーンセクション -->
<h2 class="title1">chain</h2>
<div class="favorites-container">
    <?php if (empty($chains)): ?>
        <p>chainがいません</p>
    <?php else: ?>
        <?php foreach ($chains as $user): ?>
            <div class="chain-card" onclick="location.href='search_profile.php?id=<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>'">
                <img src="<?= htmlspecialchars($user['image_path'] ?: 'image/default-pic.png', ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像">
                <div class="user-info">
                    <h2><?= htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8') ?></h2>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- 通常のお気に入り一覧 -->
<h2 class="title2">link</h2>
<div class="favorites-container">
    <?php if (empty($favorites)): ?>
        <p>linkがいません</p>
    <?php else: ?>
        <?php foreach ($favorites as $user): ?>
            <div class="favorite-card" onclick="location.href='search_profile.php?id=<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>'">
                <img src="<?= htmlspecialchars($user['image_path'] ?: 'image/default-pic.png', ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像">
                <div class="user-info">
                    <h2><?= htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8') ?></h2>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>


