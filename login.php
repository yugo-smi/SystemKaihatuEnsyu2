<?php
session_start();

// データベース接続設定
$servername = "localhost:3306";
$dbname = "newlink";
$username = "root";
$password = "root";

// セッション初期化
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['last_attempt_time'])) {
    $_SESSION['last_attempt_time'] = time();
}

// ログイン試行回数とロックアウト設定
$max_attempts = 5; // 最大試行回数
$lockout_time = 300; // ロックアウト時間（秒）
$remaining_time = 0;

if ($_SESSION['login_attempts'] >= $max_attempts) {
    $time_since_last_attempt = time() - $_SESSION['last_attempt_time'];
    if ($time_since_last_attempt < $lockout_time) {
        $remaining_time = $lockout_time - $time_since_last_attempt;
    } else {
        // ロック解除
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = time();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRFトークンの検証
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("不正なリクエストです。");
    }

    // データベース接続
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("データベース接続エラー: " . $e->getMessage());
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // メールアドレスの形式を検証
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "無効なメールアドレス形式です。";
    } else {
        // ユーザーを検索
        $stmt = $pdo->prepare("SELECT * FROM user_table WHERE email = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // パスワード検証
        if ($user && password_verify($password, $user['password'])) {
            // ログイン成功
            session_regenerate_id(true); // セッション固定攻撃対策
            $_SESSION["loggedin"] = true;
            $_SESSION["user_id"] = $user['id'];
            $_SESSION['login_attempts'] = 0; // 試行回数リセット
            header("Location: index.php");
            exit;
        } else {
            // ログイン失敗
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();

            if ($_SESSION['login_attempts'] >= $max_attempts) {
                $remaining_time = $lockout_time;
            } else {
                $remaining_attempts = $max_attempts - $_SESSION['login_attempts'];
                $error = "メールアドレスまたはパスワードが間違っています。残り試行回数: {$remaining_attempts}";
            }
        }
    }
}

// CSRFトークン生成
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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

        <?php if (isset($remaining_time) && $remaining_time > 0): ?>
            <p style="color:red;">現在、ロックされています。<span id="countdown"></span>後に再試行できます。</p>
        <?php elseif (isset($error)): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form id="loginForm" action="login.php" method="POST" <?php if (isset($remaining_time) && $remaining_time > 0) echo 'style="display:none;"'; ?>>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <label for="email">OICメールアドレス</label>
            <input type="email" id="email" name="email" required>

            <label for="password">パスワード</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required
                       pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$"
                       title="英数字を組み合わせた8文字以上で入力してください">
                <span src="js/main.js" id="toggle-password" class="toggle-password">
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
    <script>
        // PHPから残り秒数を受け取る
        let remainingTime = <?php echo isset($remaining_time) ? $remaining_time : 0; ?>;

        if (remainingTime > 0) {
            const countdownElement = document.getElementById('countdown');

            // カウントダウン処理
            const interval = setInterval(() => {
                if (remainingTime <= 0) {
                    clearInterval(interval);
                    countdownElement.textContent = 'ロックが解除されました。ページを更新してください。';
                    // フォームを再表示（オプション）
                    document.getElementById('loginForm').style.display = 'block';
                } else {
                    const minutes = Math.floor(remainingTime / 60);
                    const seconds = remainingTime % 60;
                    countdownElement.textContent = `${minutes}分${seconds}秒`;
                    remainingTime--;
                }
            }, 1000);
        }
    </script>
</body>
</html>


