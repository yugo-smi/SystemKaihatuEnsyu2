
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
        header("Location: index.php");
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

try {
    // PDO接続
    $servername = "localhost";
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $current_user_id = $_SESSION['user_id']; // ログイン中のユーザーID

    // チャット相手一覧を取得（最新のメッセージ順に並べて、上位3人を取得する）
    $stmt = $conn->prepare("
        SELECT DISTINCT 
            CASE 
                WHEN private_table.send_user_id = :current_user_id THEN private_table.recipient_user_id
                ELSE private_table.send_user_id
            END AS chat_user_id,
            user_table.nickname,
            MAX(private_table.sent_time) AS last_message_time
        FROM 
            private_table
        INNER JOIN 
            user_table 
        ON 
            user_table.id = 
            CASE 
                WHEN private_table.send_user_id = :current_user_id THEN private_table.recipient_user_id
                ELSE private_table.send_user_id
            END
        WHERE 
            (private_table.send_user_id = :current_user_id 
            OR private_table.recipient_user_id = :current_user_id)
            AND private_table.sent_time >= NOW()-INTERVAL 24 HOUR
        GROUP BY chat_user_id, user_table.nickname
        ORDER BY last_message_time DESC
        

    ");
    $stmt->bindParam(':current_user_id', $current_user_id, PDO::PARAM_INT);
    $stmt->execute();

    // 結果を配列に格納
    $chatUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 結果を出力または利用


} catch (PDOException $e) {
    // エラー発生時
    $error_message = $e->getMessage();
    echo "Error: " . $error_message;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEW LINK</title>
    <link rel="stylesheet" href="css/style_index.css">
    <link rel="stylesheet" href="css/style_favorites.css">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Roboto:wght@400;700&display=swap" rel="stylesheet">

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
            <?php
                session_start();
                $isLoggedIn = false; // デフォルト値を設定
                if (isset($_SESSION['user_id'])) {
                $isLoggedIn = true;
                }
            ?>

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
    </body>
<div id="chat-list-container">
        <h1>最新のメッセージ</h1>
        <?php if (!empty($chatUsers)): ?>
            <ul id="chat-list">
                <?php foreach ($chatUsers as $user): ?>
                    <li onclick="location.href='chat.php?partner_id=<?= htmlspecialchars($user['chat_user_id']) ?>'">
                        <strong><?= htmlspecialchars($user['nickname']) ?></strong><br>
                        <small>最終メッセージ: <?= htmlspecialchars($user['last_message_time']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>まだチャットした相手はいません。</p>
        <?php endif; ?>
    </div>


<!-- チェーンセクション -->
    <h2 class="title">chain</h2>
    <div class="favorites-container">
        <?php if (empty($chains)): ?>
            <p>chainはまだありません。</p>
        <?php else: ?>
            <?php foreach ($chains as $user): ?>
                <div class="chain-card" onclick="location.href='search_profile.php?id=<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>'">
                    <img src="<?= htmlspecialchars($user['image_path'] ?: 'image/default-pic.png', ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像">
                    <div class="user-info">
                        <h2><?= htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="actions">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="favorite_user_id" value="<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="action" value="remove_chain">
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <h2 class="title">link</h2>
    <div class="favorites-container">
    <?php if (empty($favorites)): ?>
        <p>linkしたユーザーはいません。</p>
    <?php else: ?>
        <?php foreach ($favorites as $user): ?>
            <div class="favorite-card" onclick="location.href='search_profile.php?id=<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>'">
                <img src="<?= htmlspecialchars($user['image_path'] ?: 'image/default-pic.png', ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像">
                <div class="user-info">
                    <h2><?= htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <p><?= htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div class="actions">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="favorite_user_id" value="<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="action" value="remove">
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


  



<body>

</body>

    <!--/メイン -->

    <!-- フッター -->
    <footer>
        <!-- フッター内容 -->
    </footer>
    

</html>
