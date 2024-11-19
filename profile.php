<?php
// セッション開始
session_start();

// ログインしているか確認し、していない場合はログインページにリダイレクト
if (!isset($_SESSION['user_id']) || !isset($_SERVER['HTTP_REFERER'])) {
    // ログインしていない、またはリファラーが不正な場合
    header("Location: login.php");
    exit();
}

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
$sql = "SELECT nickname, tags, bio FROM user_table WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// プロフィール更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = $_POST['nickname'];
    $tags = implode(",", $_POST['tags']);  // 選択されたタグをカンマ区切りに
    $bio = $_POST['bio'];

    //画像投稿の処理
    $image = $_SESSION['user_id'];//ファイル名を設定
    $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得
    $file = "profile-image/$image";
    if (!empty($_FILES['image']['name'])) {//ファイルが選択されていれば$imageにファイル名を代入
        move_uploaded_file($_FILES['image']['tmp_name'], 'profile-image/' . $image);//imagesディレクトリにファイル保存
        if (exif_imagetype($file)) {//画像ファイルかのチェック
            $imagepath = $file;
        } else {
            $imagepath = null;
        }
    }
    // ユーザー情報の更新
    $update_sql = "UPDATE user_table SET nickname = :nickname, tags = :tags, bio = :bio ,image_path = :image_path WHERE id = :id";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->bindParam(':nickname', $nickname);
    $update_stmt->bindParam(':tags', $tags);
    $update_stmt->bindParam(':bio', $bio);
    $update_stmt->bindParam(':image_path', $imagepath, PDO::PARAM_STR);
    $update_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
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
    <h2>プロフィール編集</h2>
    <form method="POST" action="profile.php" enctype="multipart/form-data">
        <!-- 画像選択機能を追加 -->
        <div class="profile-info">
                <div class="profile-pic-container">
                    <img src="image/default-pic.png" alt="プロフィール画像" id="profile-pic" class="profile-pic">
                    <label for="profile-pic-input" class="file-label">プロフィール画像を選択</label>
                    <input type="file" name="image" id="profile-pic-input" aria-label="プロフィール画像を選択">
                </div>

        <label>ニックネーム:</label>
        <input type="text" name="nickname" value="<?= htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8') ?>" required><br>

        <label>タグ:</label>
        <div class="tag-container">
            <?php 
            // チェックボックス形式でタグを表示
            $tags = ["アウトドア", "インドア", "旅行", "読書", "音楽"];
            $selected_tags = explode(",", $user['tags']);
            foreach ($tags as $tag) {
                $checked = in_array($tag, $selected_tags) ? "checked" : "";
                echo "<label><input type='checkbox' name='tags[]' value='$tag' $checked> $tag</label> ";
            }
            ?>
        </div>

        <label>自己紹介文:</label>
        <textarea name="bio"><?= htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8') ?></textarea><br>

        <button type="submit">更新</button>
        <?php echo "<p style='color:green;'>プロフィールが更新されました。</p>"; ?>
    </form>
    <script src="js/profile.js"></script>
</body>
</html>

