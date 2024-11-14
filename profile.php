<?php
// データベース接続設定
$servername = "localhost:3306";
$dbname = "newlink";
$username = "root";
$password = "root";

try {
    // データベースに接続
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "データベース接続に失敗しました: " . $e->getMessage();
    exit();
}

// ユーザーID（ログインしているユーザーのIDがセッションから取得される前提）
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "ログインが必要です。";
    exit();
}
$user_id = $_SESSION['user_id'];

// ユーザー情報の取得
$sql = "SELECT nickname, tags, bio FROM user_table WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームから送信されたデータを取得
    $nickname = $_POST['nickname'];
    $tags = $_POST['tags'];
    $bio = $_POST['bio'];

    // ユーザー情報の更新
    $update_sql = "UPDATE user_table SET nickname = :nickname, tags = :tags, bio = :bio WHERE id = :id";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->bindParam(':nickname', $nickname);
    $update_stmt->bindParam(':tags', $tags);
    $update_stmt->bindParam(':bio', $bio);
    $update_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $update_stmt->execute();

    echo "プロフィールが更新されました。";
    // ページを再読み込みして変更を反映
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール編集</title>
</head>
<body>
    <h2>プロフィール編集</h2>
    <form method="POST" action="profile.php">
        <label>ニックネーム:</label>
        <input type="text" name="nickname" value="<?= htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8') ?>" required><br>

        <label>タグ:</label>
        <input type="text" name="tags" value="<?= htmlspecialchars($user['tags'], ENT_QUOTES, 'UTF-8') ?>"><br>

        <label>自己紹介文:</label>
        <textarea name="bio"><?= htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8') ?></textarea><br>

        <button type="submit">更新</button>
    </form>
</body>
</html>
