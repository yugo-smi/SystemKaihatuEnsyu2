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

// お気に入り一覧表示
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
    <title>お気に入り一覧</title>
    <style>
        /* 簡易スタイル */
        .favorites-container { display: flex; flex-wrap: wrap; gap: 20px; }
        .favorite-card { border: 1px solid #ddd; padding: 10px; width: 300px; text-align: center; }
        .favorite-card img { width: 80px; height: 80px; border-radius: 50%; }
        .user-info { margin-top: 10px; }
        .actions { margin-top: 10px; }
        .actions button { margin-right: 5px; }
    </style>
</head>
<body>
    <h1>お気に入り一覧</h1>
    <div class="favorites-container">
        <?php if (empty($favorites)): ?>
            <p>お気に入りに登録されたユーザーはいません。</p>
        <?php else: ?>
            <?php foreach ($favorites as $user): ?>
                <div class="favorite-card">
                    <img src="<?= htmlspecialchars($user['image_path'] ?: 'image/default-pic.png', ENT_QUOTES, 'UTF-8') ?>" 
                         alt="プロフィール画像">
                    <div class="user-info">
                        <h2><?= htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="actions">
                            <!-- プロフィール表示リンク -->
                            <a href="search_profile.php?id=<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>">プロフィールを見る</a>
                            <!-- お気に入り解除フォーム -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="favorite_user_id" value="<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit">お気に入り解除</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
