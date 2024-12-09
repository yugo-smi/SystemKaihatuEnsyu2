<?php
// セッション開始
session_start();
$_SESSION['previous_url'] = $_SERVER['REQUEST_URI'];

// 現在のユーザーIDをセッションから取得
$currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// データベース接続設定
$servername = "localhost:3306";
$dbname = "newlink";
$username = "root";
$password = "root";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $results = [];
    $searchKeyword = isset($_SESSION['search_keyword']) ? $_SESSION['search_keyword'] : '';
    $tags = isset($_SESSION['tags']) ? $_SESSION['tags'] : [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
        $searchKeyword = isset($_POST['search']) ? $_POST['search'] : '';

        // セッションに保存
        $_SESSION['tags'] = $tags;
        $_SESSION['search_keyword'] = $searchKeyword;

        if (empty($tags) && empty($searchKeyword)) {
            $results = [];
        } else {
            $query = "SELECT id, nickname, bio, image_path FROM user_table WHERE 1";

            if (!empty($tags)) {
                foreach ($tags as $index => $tag) {
                    $query .= " AND tags LIKE :tag$index";
                }
            }

            if (!empty($searchKeyword)) {
                $query .= " AND nickname LIKE :search";
            }

            $stmt = $pdo->prepare($query);

            foreach ($tags as $index => $tag) {
                $stmt->bindValue(":tag$index", '%' . $tag . '%');
            }

            if (!empty($searchKeyword)) {
                $stmt->bindValue(':search', '%' . $searchKeyword . '%');
            }

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 現在のユーザーを検索結果から除外
            $results = array_filter($results, function ($user) use ($currentUserId) {
                return $user['id'] != $currentUserId;
            });

            // 検索結果をセッションに保存
            $_SESSION['results'] = $results;
        }
    } else {
        // POSTリクエストでない場合、セッションから結果を取得
        $results = isset($_SESSION['results']) ? $_SESSION['results'] : [];
    }
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Link</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style_kensaku.css">
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
                    <li><a href="talk.php">トーク履歴</a></li>
                    <li><a href="favorites.php">お気に入り</a></li>
                    <li><a href="profile.php">プロフィール</a></li>
                    <li><a href="logout.php">ログアウト</a></li>
                </ul>
            </nav>

            <div class="logotitle">
                <img src="image/logotitle.png" alt="タイトル">
            </div>
        </div>
    </header>
    <script src="js/kensaku_hamburger.js"></script>

    <!-- メインコンテンツ -->
    <main>
        <!-- Search Section -->
        <form method="POST" action="">
    <div class="buttons">
        <div class="search-input-container">
            <input type="text" name="search" placeholder="検索" value="<?= htmlspecialchars($searchKeyword, ENT_QUOTES, 'UTF-8') ?>">
            <button class="btn search-button"><i class="fas fa-search"></i></button>
        </div>
        <div class="search-input-container">
            <option>条件を絞って検索</option>
            <div class="tag-container">
                <label><input type="checkbox" name="tags[]" value="アウトドア" <?= in_array('アウトドア', $tags) ? 'checked' : '' ?>> アウトドア</label>
                <label><input type="checkbox" name="tags[]" value="インドア" <?= in_array('インドア', $tags) ? 'checked' : '' ?>> インドア</label>
                <label><input type="checkbox" name="tags[]" value="旅行" <?= in_array('旅行', $tags) ? 'checked' : '' ?>> 旅行</label>
                <label><input type="checkbox" name="tags[]" value="読書" <?= in_array('読書', $tags) ? 'checked' : '' ?>> 読書</label>
                <label><input type="checkbox" name="tags[]" value="音楽" <?= in_array('音楽', $tags) ? 'checked' : '' ?>> 音楽</label>
            </div>
            <button class="btn search-button"><i class="fas fa-search"></i></button>
        </div>
    </div>
</form>


        <!-- Results Container for Displaying Search Results -->
        <div class="results-container">
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($results)): ?>
                <p>検索結果はありません。</p>
            <?php elseif (!empty($results)): ?>
                <?php foreach ($results as $user): ?>
                    <!-- カード全体をリンク化 -->
                    <a href="search_profile.php?id=<?= htmlspecialchars($user['id']) ?>" class="user-card">
                        <div class="profile-card">
                            <div class="profile-image">
                                <img src="<?= htmlspecialchars($user['image_path'] ?: 'image/default-pic.png', ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像">
                            </div>
                            <div class="profile-content">
                                <div class="profile-name"><?= htmlspecialchars($user['nickname']) ?></div>
                                <div class="profile-bio"><?= htmlspecialchars($user['bio']) ?></div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <script src="js/hamburger.js"></script>
    <script src="script.js"></script>
</body>
</html>
