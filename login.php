<?php
session_start();

// データベース接続設定
$servername = "localhost:3306";
$dbname = "newlink";
$username = "root";
$password = "root";

$method = $_SERVER["REQUEST_METHOD"];
echo $method;
// ログイン処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "ad";
    } catch (PDOException $e) {
        die("データベース接続エラー: " . $e->getMessage());
    }
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ユーザー情報の照合
    $stmt = $pdo->prepare("SELECT * FROM user_table WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($user);


    // パスワード検証
    if ($user && $password == $user['password']) {
        $_SESSION["loggedin"] = true;
        $_SESSION["email"] = $email;
        header("Location: index.php"); // ログイン後のページへリダイレクト
        exit;
    } else {
        $error = "メールアドレスまたはパスワードが間違っています。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEW LINK-ログイン</title>
    <link rel="stylesheet" href="css/style_login.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="image/logo.PNG" alt="New Link ロゴ">
        </div>
        <h2>ログイン</h2>

        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form id="loginForm" action="login.php" method="POST">
            <label for="email">OICメールアドレス</label>
            <input type="email" id="email" name="email" required>

            <label for="password">パスワード</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required
                       pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$"
                       title="英数字を組み合わせた8文字以上で入力してください">
                <span id="toggle-password" class="toggle-password">
                    <img src="image/eye-icon.png" alt="目のアイコン">
                </span>
            </div>

            <button type="submit" class="login-button">ログインする</button>
        </form>

        <div class="register-link">
            <a href="register.php">アカウント新規登録</a>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>




