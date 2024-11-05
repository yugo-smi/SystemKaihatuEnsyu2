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

        <!-- ログインフォーム -->
        <form id="loginForm" action="index.php" method="POST">
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
            <a href="register.html">アカウント新規登録</a>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>


