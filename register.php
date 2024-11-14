<?php
// エラー表示を有効にする
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// データベース接続設定
$servername = "localhost:3306";
$dbname = "newlink";
$username = "root";
$password = "root";

// バッファリングを開始
ob_start();

// 新規登録処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("データベース接続エラー: " . $e->getMessage());
    }

    $nickname = $_POST['nickname'];
    $email = $_POST['email'];
    // パスワードをハッシュ化して保存
    $password = $_POST['password'];

    // タグが選択されているかチェック
    if (empty($_POST['tags'])) {
        $error = "少なくとも1つのタグを選択してください。";
    } else {
        $tags = implode(",", $_POST['tags']); // 選択されたタグをカンマ区切りで保存

        // メールアドレスの重複確認
        $stmt = $pdo->prepare("SELECT * FROM user_table WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error = "このメールアドレスは既に登録されています。";
        } else {
            // 新規ユーザーの登録
            $stmt = $pdo->prepare("INSERT INTO user_table(nickname, email, password, tags) VALUES (:nickname, :email, :password, :tags)");
            $stmt->bindParam(':nickname', $nickname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':tags', $tags);

            if ($stmt->execute()) {
                // 成功時、ログインページにリダイレクト
                header("Location: login.php");
                exit;
            } else {
                $error = "登録に失敗しました。";
            }
        }
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEW LINK - アカウント新規登録</title>
    <link rel="stylesheet" href="css/style_register.css">
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <img src="image/logo.png" alt="New Link ロゴ">
        </div>
        <h2>アカウント新規登録</h2>

        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form id="registerForm" action="register.php" method="POST">
            <label for="nickname">ニックネーム</label>
            <input type="text" id="nickname" name="nickname" required>

            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" required
                   pattern="^[A-Za-z]{1}\d{4}@oic\.jp$"
                   title="メールアドレスは英単語1文字+4桁の数字@oic.jpの形式で入力してください">

            <label for="password">パスワード</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required
                       pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$"
                       title="英数字を組み合わせた8文字以上で入力してください">
                <span id="toggle-password" class="toggle-password">
                    <img src="image/eye-icon.png" alt="目のアイコン">
                </span>
            </div>

            <label for="confirm-password">パスワード（確認）</label>
            <div class="password-container">
                <input type="password" id="confirm-password" name="confirm-password" required
                       pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$"
                       title="同じパスワードを入力してください">
                <span id="toggle-confirm-password" class="toggle-password">
                    <img src="image/eye-icon.png" alt="目のアイコン">
                </span>
            </div>

            <label>タグを選んで</label>
            <div class="tag-container">
                <label><input type="checkbox" name="tags[]" value="アウトドア"> アウトドア</label>
                <label><input type="checkbox" name="tags[]" value="インドア"> インドア</label>
                <label><input type="checkbox" name="tags[]" value="旅行"> 旅行</label>
                <label><input type="checkbox" name="tags[]" value="読書"> 読書</label>
                <label><input type="checkbox" name="tags[]" value="音楽"> 音楽</label>
            </div>

            <button type="submit" class="register-button">登録する</button>
        </form>

        <div class="back-button">
            <button onclick="history.back()">戻る</button>
        </div>
        <script src="js/register.js"></script>
    </div>
</body>
</html>







