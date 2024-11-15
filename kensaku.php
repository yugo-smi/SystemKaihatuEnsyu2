<?php
session_start();

// データベース接続設定
$servername = "localhost:3306";
$dbname = "newlink";
$username = "root";
$password = "root";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 検索のタグとキーワードがフォームから送信されているかを確認
    $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
    $searchKeyword = isset($_POST['search']) ? $_POST['search'] : '';

    // SQLクエリでタグや検索キーワードに一致するユーザーを取得
    $query = "SELECT * FROM user_table WHERE 1";

    // タグフィルタリング
    if (!empty($tags)) {
        $tagsPlaceholders = implode(',', array_fill(0, count($tags), '?'));
        $query .= " AND tags LIKE '%" . implode("%' OR tags LIKE '%", $tags) . "%'";
    }

    // 検索キーワードでフィルタリング
    if (!empty($searchKeyword)) {
        $query .= " AND username LIKE :search";
    }

    $stmt = $pdo->prepare($query);
    if (!empty($searchKeyword)) {
        $stmt->bindValue(':search', '%' . $searchKeyword . '%');
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                    <li><a href="kensaku.php">お相手を検索</a></li>
                    <li><a href="message.php">スレッド</a></li>
                    <li><a href="chat.php">メッセージ</a></li>
                    <li><a href="profile.php">プロフィール</a></li>
                </ul>
            </nav>

            <div class = "logotitle">
                <img src="image/logotitle.png" alt="タイトル">
            </div>
        </div>

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
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $user): ?>
                    <div class="profile-card">
                        <div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
                        <div class="profile-button btn">プロフィール</div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>検索結果はありません。</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
