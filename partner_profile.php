<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>NEW LINK</title>  
        <link rel="stylesheet" href="./css/style_partner_profile.css">
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

if (!isset($_SESSION['user_id'])) {
    // ログインしていない場合、login.php へリダイレクト
    header("Location: login.php");
    exit;
}
$isLoggedIn = isset($_SESSION['user_id']);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ランダムで1人のユーザーを取得
    $current_user_id = $_SESSION['user_id'];
    $stmt = $pdo->query("SELECT * FROM user_table Where id != $current_user_id ORDER BY RAND() LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $user['id'];

    if (!$user) {
        echo "データベースにユーザーが存在しません。";
        exit;
    }
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
                    <?php if ($isLoggedIn): ?>
                        <li><a href="profile.php">プロフィール</a></li>
                        <li><a href="logout.php">ログアウト</a></li>
                    <?php else: ?>
                        <li><a href="login.php">ログイン</a></li>
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
            <div class="chat-or-change">
                <!-- チャットボタン: 抽選されたユーザーのIDをクエリパラメータに含める -->
                <button><a href="chat.php?partner_id=<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>" class="matching_chat">チャットする</a></button>

                <!-- チェンジボタン -->
                <button class="matching_change" onclick="reloadPage()">チェンジする</button>
                <script>
                    function reloadPage() {
                        // URLに現在の時刻を追加してキャッシュを回避
                        location.href = location.pathname + "?t=" + new Date().getTime();
                    }
                </script>
            </div>  
        </div>
    </body>
</html>
