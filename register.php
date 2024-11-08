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

            <!-- タグを選択するセクション -->
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

        <!-- 戻るボタン -->
        <div class="back-button">
            <button onclick="history.back()">戻る</button>
        </div>
    </div>

    <!-- JavaScriptでタグの選択を確認 -->
    <script>
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            // タグが1つ以上選択されているかを確認
            const tags = document.querySelectorAll('input[name="tags[]"]:checked');
            if (tags.length === 0) {
                alert("少なくとも1つのタグを選択してください。");
                event.preventDefault(); // フォーム送信をキャンセル
            }
        });
    </script>

    <script src="js/register.js"></script> <!-- JavaScriptファイルの読み込み -->
</body>
</html>




