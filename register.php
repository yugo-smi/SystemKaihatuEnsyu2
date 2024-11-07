<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEW LINK - アカウント新規登録</title>
    <link rel="stylesheet" href="css/style_register.css">
    <link rel="shortcut icon" href="image/logo.png">
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <img src="image/logo.png" alt="New Link ロゴ">
        </div>
        <h2>アカウント新規登録</h2>

        <!-- 新規登録フォーム -->
        <form id="registerForm" action="register_complete.php" method="POST">
            <label for="name">名前</label>
            <input type="text" id="name" name="name" required>

            <label for="email">メールアドレス</label>
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

            <label for="confirm-password">パスワード（確認）</label>
            <div class="password-container">
                <input type="password" id="confirm-password" name="confirm-password" required
                       pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$"
                       title="同じパスワードを入力してください">
                <span id="toggle-confirm-password" class="toggle-password">
                    <img src="image/eye-icon.png" alt="目のアイコン">
                </span>
            </div>

            <button type="submit" class="register-button">登録する</button>
        </form>

        <!-- 戻るボタン -->
        <div class="back-button">
            <button onclick="history.back()">戻る</button>
        </div>
    </div>

    <script src="js/register.js"></script> <!-- JavaScriptファイルの読み込み -->
</body>
</html>

