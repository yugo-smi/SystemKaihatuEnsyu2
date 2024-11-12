<?php
// データベース接続設定
$servername = "localhost:3306";
$dbname = "newlink";
$username = "root";
$password = "root";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続エラー: " . $e->getMessage());
}

// 新規登録処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nickname = $_POST['nickname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // パスワードのハッシュ化
    $tags = implode(",", $_POST['tags']); // 選択されたタグをカンマ区切りで保存

    // メールアドレスが既に登録されているか確認
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // 既に存在する場合
        $error = "このメールアドレスは既に登録されています。";
    } else {
        // 新規登録処理
        $stmt = $pdo->prepare("INSERT INTO users (nickname, email, password, tags) VALUES (:nickname, :email, :password, :tags)");
        $stmt->bindParam(':nickname', $nickname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':tags', $tags);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit;
        } else {
            $error = "登録に失敗しました。";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEW LINK - アカウント登録完了</title>
</head>
<body>
    <div class="register-complete-container">
        <h2>アカウント登録</h2>
        <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
        <form action="register_complete.php" method="POST">
            <label for="nickname">ニックネーム</label>
            <input type="text" id="nickname" name="nickname" required>
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" required>
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" class="register-button">登録する</button>
        </form>
    </div>
</body>
</html>





