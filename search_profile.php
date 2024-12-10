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
        <script src="js/index_hamburger.js"></script>

        <div class="profile-info">
            <div class="profile-pic-container">
                <img src="<?= htmlspecialchars($user['image_path'] ?: 'image/default-pic.png', ENT_QUOTES, 'UTF-8') ?>" 
                     alt="プロフィール画像" id="profile-pic" class="profile-pic">
            </div>

            <label>ニックネーム:</label>
            <input type="text" name="nickname" value="<?= htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8') ?>" readonly><br>

            <label>タグ:</label>
            <div class="tag-container">
                <?php 
                // タグを選択済み状態で表示
                $tags = ["アウトドア", "インドア", "旅行", "読書", "音楽"];
                $selected_tags = explode(",", $user['tags']);
                foreach ($tags as $tag) {
                    if (in_array($tag, $selected_tags)) {
                        echo "<span class='tag'>$tag</span> ";
                    }
                }
                ?>
            </div>

            <div class="bio">
                <h3>自己紹介</h3>
                <textarea readonly><?= htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            
            <!-- チャットボタン -->
            <div class="chat-or-change">
                <button><a href="chat.php?partner_id=<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>" class="matching_chat">チャットする</a></button>
            </div>

            <!-- お気に入りボタン -->
            <div class="favorite-container">
                <form method="POST" action="search_profile.php?id=<?= htmlspecialchars($userId, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="favorite_user_id" value="<?= htmlspecialchars($userId, ENT_QUOTES, 'UTF-8') ?>">
                    <?php if ($isFavorite): ?>
                        <button type="submit" name="action" value="remove" class="favorite-button remove">お気に入り解除</button>
                    <?php else: ?>
                        <button type="submit" name="action" value="add" class="favorite-button add">お気に入り追加</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="back-to-search">
            <button>
                <a href="kensaku.php?<?= http_build_query($_GET) ?>" class="back-to-search-link matching_chat">検索画面に戻る</a>
            </button>
        </div>

        <?php
        // お気に入り追加/解除処理
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['favorite_user_id'])) {
            $favoriteUserId = $_POST['favorite_user_id'];
            $action = $_POST['action'];

            try {
                if ($action === 'add') {
                    // お気に入り追加
                    $stmt = $pdo->prepare("INSERT INTO favorite_users (user_id, favorite_user_id) VALUES (:user_id, :favorite_user_id)");
                    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    $stmt->bindValue(':favorite_user_id', $favoriteUserId, PDO::PARAM_INT);
                    $stmt->execute();
                } elseif ($action === 'remove') {
                    // お気に入り解除
                    $stmt = $pdo->prepare("DELETE FROM favorite_users WHERE user_id = :user_id AND favorite_user_id = :favorite_user_id");
                    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    $stmt->bindValue(':favorite_user_id', $favoriteUserId, PDO::PARAM_INT);
                    $stmt->execute();
                }
            } catch (PDOException $e) {
                echo "エラー: " . $e->getMessage();
                exit;
            }

            // ページをリロードしてフォーム再送信を防止
            header("Location: search_profile.php?id=" . htmlspecialchars($userId, ENT_QUOTES, 'UTF-8'));
            exit();
        }
        ?>
    </body>
</html>
