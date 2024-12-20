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
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
    exit();
}

// ログインしているユーザーのIDを取得
$user_id = $_SESSION['user_id'];

// ユーザー情報の取得
$sql = "SELECT nickname, license,tags, bio,image_path FROM user_table WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 入力データを取得
    $nickname = !empty($_POST['nickname']) ? $_POST['nickname'] : $user['nickname'];
    $tags = !empty($_POST['tags']) ? implode(",", $_POST['tags']) : $user['tags']; // 選択されたタグをカンマ区切りに
    $license = !empty($_POST['license']) ? implode(",", $_POST['license']) : $user['license']; 
    $bio = !empty($_POST['bio']) ? $_POST['bio'] : $user['bio'];


    // 古い画像の削除用パス
    $current_image_path = $user['image_path'];

    // 画像投稿の処理
    $imagepath = $current_image_path; // 既存画像を初期値に設定
    if (!empty($_FILES['image']['tmp_name']) && exif_imagetype($_FILES['image']['tmp_name'])) { 
        // ファイル名を user_id に基づいて作成
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION); // 拡張子取得
        $file = "profile-image/$user_id.$extension";

        // 古い画像が存在すれば削除
        if (file_exists($current_image_path)) {
            unlink($current_image_path);
        }

        // 新しい画像を保存
        move_uploaded_file($_FILES['image']['tmp_name'], $file);
        $imagepath = $file;
    }

    // データベース更新
    // var_dump($tags);exit;
    $update_sql = "UPDATE user_table SET nickname = :nickname, tags = :tags, bio = :bio, image_path = :image_path ,license = :license WHERE id = :id";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->bindParam(':nickname', $nickname);
    $update_stmt->bindParam(':tags', $tags);
    $update_stmt->bindParam(':bio', $bio);
    $update_stmt->bindParam(':image_path', $imagepath, PDO::PARAM_STR);
    $update_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $update_stmt->bindParam('license',$license);
    $update_stmt->execute();

    // 更新メッセージ表示とリロード
    echo "<p style='color:green;'>プロフィールが更新されました。</p>";
    header("Refresh:0"); // ページをリロードして更新内容を反映
    exit();
}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/style_partner_profile.css">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール編集</title>
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
    </header>
    <script src="js/kensaku_hamburger.js"></script>
    <h2>プロフィール編集</h2>
    <form method="POST" action="profile.php" enctype="multipart/form-data">
        <!-- 画像選択機能を追加 -->
        <div class="profile-info">
                <div class="profile-pic-container">
                <img src="<?= htmlspecialchars($user['image_path'] ?: 'image/default-pic.png', ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像" id="profile-pic" class="profile-pic">
                    <label for="profile-pic-input" class="file-label">プロフィール画像を選択</label>
                    <input type="file" name="image" id="profile-pic-input" aria-label="プロフィール画像を選択">
                </div>

        <label>ニックネーム</label>
        <input type="text" name="nickname" value="<?= htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8') ?>" required><br>

        <label></label>
        <div class="dropdown">
            <h5 class="dropdown-header">資格
            </h5>
            <div class="dropdown-content">
                <div class="tag-container">
                    <?php
                    [$tags_hobby,$tags_license] = require 'tags_data.php';
                            $selected_license = explode(",", $user['license']);
                            echo "<div>";
                                foreach ($tags_license as $tag) {
                                $checked = in_array($tag, $selected_license) ? "checked" : "";
                                echo "<div><label><input type='checkbox' name='license[]' value='$tag' $checked> $tag</label></div> ";
                            }
                            echo "</div>"
                    ?>



  
                </div>
            </div>
        </div>
        <label></label>
        <div class="dropdown">
            <h5 class="dropdown-header">趣味</h5>
            <div class="dropdown-content">
                <div class="tag-container">
                    <?php
                        include('tags_data.php');
                        $selected_tags = explode(",", $user['tags']);
                        echo "<div>";
                            foreach ($tags_hobby as $tag) {
                                $checked = in_array($tag, $selected_tags) ? "checked" : "";
                                echo "<div><label><input type='checkbox' name='tags[]' value='$tag' $checked> $tag</label></div>";
                            }
                        echo "</div>";
                    ?>
                </div>
            </div>
        </div>

        <label>自己紹介文</label>
        <textarea name="bio"><?= htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8') ?></textarea><br>
        <button type="submit" class="submit-btn">更新</button>
    </form>
    <script src="js/profile.js"></script>
    <script src="js/tag.js"></script>
</body>
</html>

