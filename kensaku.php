<?php
// セッション開始
session_start();

// ログインしているか確認し、していない場合はログインページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    // ログインしていない場合、login.php へリダイレクト
    header("Location: login.php");
    exit;
}
$isLoggedIn = isset($_SESSION['user_id']);



// データベース接続設定
$servername = "localhost:3306";
$dbname = "newlink";
$username = "root";
$password = "root";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $results = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
        $searchKeyword = isset($_POST['search']) ? $_POST['search'] : '';

        if (empty($tags) && empty($searchKeyword)) {
            $results = [];
        } else {
            $query = "SELECT  id,nickname, bio,image_path FROM user_table WHERE 1";

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
        }
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
    <script src="js/kensaku_hamburger.js"></script>

    <!-- メインコンテンツ -->
    <main>


        <!-- Search Section -->
        <form method="POST" action="">
            <div class="buttons">
                <div class="search-input-container">
                    <input type="text" name="search" placeholder="検索">
                    <button class="btn search-button"><i class="fas fa-search"></i></button>
                </div>
                <div class="search-input-container">
                    <option>条件を絞って検索</option>
                    <div class="tag-container">
                        <label><input type="checkbox" name="tags[]" value="アウトドア"> アウトドア</label>
                        <label><input type="checkbox" name="tags[]" value="インドア"> インドア</label>
                        <label><input type="checkbox" name="tags[]" value="旅行"> 旅行</label>
                        <label><input type="checkbox" name="tags[]" value="読書"> 読書</label>
                        <label><input type="checkbox" name="tags[]" value="音楽"> 音楽</label>
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

    </div>
    <script src="js/hamburger.js"></script>
    <script src="script.js"></script>
</body>
</html>
