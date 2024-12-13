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
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if(mb_strlen($nickname)<1 | mb_strlen($nickname) >= 12){
        $error = 'ニックネームは1文字以上12文字以下で入力してください。';
    }


    // タグが選択されているかチェック

    if (empty($_POST['license'])) {
        $error = "少なくとも1つの資格を選択してください。";
    }
    else{
        $license = implode(",", $_POST['license']); // 選択されたタグをカンマ区切りで保存
    }

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
            $stmt = $pdo->prepare("INSERT INTO user_table(nickname, email, password, license, tags) VALUES (:nickname, :email, :password, :license, :tags)");
            $stmt->bindParam(':nickname', $nickname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':license', $license);
            $stmt->bindParam(':tags', $tags);
            

            if ($stmt->execute()) {
                $_SESSION["user_id"] = $pdo->lastInsertId();  // 新しく作成されたユーザーIDをセッションに保存
                header("Location: login.php"); // 登録完了後、index.phpにリダイレクト
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
            <label for="nickname">※ニックネーム</label>
            <input type="text" minlength="1" maxlength="12"  id="nickname" name="nickname" required pattern="{1,12}">


            <label for="email">※メールアドレス</label>
            <input type="email" id="email" name="email" required
                   pattern="^[A-Za-z]{1}\d{4}@oic\.jp$"
                   title="メールアドレスは英単語1文字+4桁の数字@oic.jpの形式で入力してください">

            <label for="password">※パスワード</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required
                       pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$"
                       title="英数字を組み合わせた8文字以上で入力してください">
                <span id="toggle-password" class="toggle-password">
                    <img src="image/eye-icon.png" alt="目のアイコン">
                </span>
            </div>

            <label for="confirm-password">※パスワード（確認）</label>
            <div class="password-container">
                <input type="password" id="confirm-password" name="confirm-password" required
                       pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$"
                       title="同じパスワードを入力してください">
                <span id="toggle-confirm-password" class="toggle-password">
                    <img src="image/eye-icon.png" alt="目のアイコン">
                </span>
            </div>

            <label>※保有資格を選んでください</label>
            <div class="tag-container">
                <div class="dropdown">
                    <h5 class="dropdown-header">情報処理IT系</h5>
                    <div class="dropdown-content">
                        <label><input type="checkbox" name="license[]" value="ITパスポート">ITパスポート</label><br>
                        <label><input type="checkbox" name="license[]" value="基本情報技術者試験">基本情報技術者試験</label><br>
                        <label><input type="checkbox" name="license[]" value="応用情報技術者試験">応用情報技術者試験</label><br>
                        <label><input type="checkbox" name="license[]" value="情報セキュリティマネジメント試験">情報セキュリティマネジメント試験</label><br>
                        <label><input type="checkbox" name="license[]" value="ネットワークスペシャリスト試験">ネットワークスペシャリスト試験</label><br>
                        <label><input type="checkbox" name="license[]" value="情報処理安全確保支援士試験">情報処理安全確保支援士試験</label><br>
                        <label><input type="checkbox" name="license[]" value="データベーススペシャリスト試験">データベーススペシャリスト試験</label><br>
                        <label><input type="checkbox" name="license[]" value="システムアーキテクト試験">システムアーキテクト試験</label><br>
                        <label><input type="checkbox" name="license[]" value="プロジェクトマネージャ試験">プロジェクトマネージャ試験</label><br>
                        <label><input type="checkbox" name="license[]" value="マイクロソフト認定技術者">マイクロソフト認定技術者</label><br>
                        <label><input type="checkbox" name="license[]" value="シスコ認定ネットワーク技術者">シスコ認定ネットワーク技術者</label><br>
                        <label><input type="checkbox" name="license[]" value="オラクル認定ネットワーク技術者">オラクル認定ネットワーク技術者</label><br>
                        <label><input type="checkbox" name="license[]" value="マイクロソフトオフィススペシャリストマスター">マイクロソフトオフィススペシャリストマスター</label><br>
                        <label><input type="checkbox" name="license[]" value="マイクロソフトオフィススペシャリスト">マイクロソフトオフィススペシャリスト</label><br>
                        <label><input type="checkbox" name="license[]" value="word expert">word expert</label><br>
                        <label><input type="checkbox" name="license[]" value="word Specialist">word Specialist</label><br>
                        <label><input type="checkbox" name="license[]" value="Excel expert">Excel expert</label><br>
                        <label><input type="checkbox" name="license[]" value="Excel Specialist">Excel Specialist</label><br>
                        <label><input type="checkbox" name="license[]" value="Access expert">Access expert</label><br>
                        <label><input type="checkbox" name="license[]" value="Access Specialist">Access Specialist</label><br>
                        <label><input type="checkbox" name="license[]" value="PowerPoint expert">PowerPoint expert</label><br>
                        <label><input type="checkbox" name="license[]" value="PowerPoint Specialist">PowerPoint Specialist</label><br>
                    </div>
                </div>

                <div class="dropdown">
                    <h5 class="dropdown-header">ビジネス系</h5>
                    <div class="dropdown-content">
                        <label><input type="checkbox" name="license[]" value="ITパスポート">ITパスポート</label><br>
                        <label><input type="checkbox" name="license[]" value="マイクロソフトオフィススペシャリストマスター">マイクロソフトオフィススペシャリストマスター</label><br>
                        <label><input type="checkbox" name="license[]" value="マイクロソフトオフィススペシャリスト">マイクロソフトオフィススペシャリスト</label><br>
                        <label><input type="checkbox" name="license[]" value="word expert">word expert</label><br>
                        <label><input type="checkbox" name="license[]" value="word Specialist">word Specialist</label><br>
                        <label><input type="checkbox" name="license[]" value="Excel expert">Excel expert</label><br>
                        <label><input type="checkbox" name="license[]" value="Excel Specialist">Excel Specialist</label><br>
                        <label><input type="checkbox" name="license[]" value="Access expert">Access expert</label><br>
                        <label><input type="checkbox" name="license[]" value="Access Specialist">Access Specialist</label><br>
                        <label><input type="checkbox" name="license[]" value="PowerPoint expert">PowerPoint expert</label><br>
                        <label><input type="checkbox" name="license[]" value="PowerPoint Specialist">PowerPoint Specialist</label><br>
                        <label><input type="checkbox" name="license[]" value="ビジネス能力ジョブパス２級">ビジネス能力ジョブパス２級</label><br>
                        <label><input type="checkbox" name="license[]" value="ビジネス能力ジョブパス３級">ビジネス能力ジョブパス３級</label><br>
                        <label><input type="checkbox" name="license[]" value="日商簿記２級">日商簿記２級</label><br>
                        <label><input type="checkbox" name="license[]" value="日商簿記３級">日商簿記３級</label><br>
                        <label><input type="checkbox" name="license[]" value="会計ソフト実務能力試験">会計ソフト実務能力試験</label><br>
                        <label><input type="checkbox" name="license[]" value="秘書技能検定試験">秘書技能検定試験</label><br>
                    </div>
                </div>

                <div class="dropdown">
                    <h5 class="dropdown-header">ゲーム系</h5>
                    <div class="dropdown-content">
                        <label><input type="checkbox" name="license[]" value="応用情報技術者試験">応用情報技術者試験</label><br>
                        <label><input type="checkbox" name="license[]" value="基本情報技術者試験">基本情報技術者試験</label><br>
                        <label><input type="checkbox" name="license[]" value="CGクリエイター試験">CGクリエイター試験</label><br>
                        <label><input type="checkbox" name="license[]" value="色彩検定">色彩検定</label><br>
                        <label><input type="checkbox" name="license[]" value="マルチメディア検定">マルチメディア検定</label><br>
                        <label><input type="checkbox" name="license[]" value="Webデザイナー検定">Webデザイナー検定</label><br>
                        <label><input type="checkbox" name="license[]" value="画像処理エンジニア検定">画像処理エンジニア検定</label><br>
                    </div>
                </div>



                <div class="dropdown">
                    <h5 class="dropdown-header">全分野共通資格</h5><br>
                    <div class="dropdown-content">
                        <label><input type="checkbox" name="license[]" value="マイクロソフトオフィススペシャリストマスター">マイクロソフトオフィススペシャリストマスター</label><br>
                        <label><input type="checkbox" name="license[]" value="マイクロソフトオフィススペシャリスト">マイクロソフトオフィススペシャリスト</label><br>
                        <label><input type="checkbox" name="license[]" value="word expert">word expert</label><br>
                        <label><input type="checkbox" name="license[]" value="word Specialist">word Specialist</label><br>
                        <label><input type="checkbox" name="license[]" value="Excel expert">Excel expert</label><br>
                        <label><input type="checkbox" name="license[]" value="Excel Specialist">Excel Specialist</label><br>
                        <label><input type="checkbox" name="license[]" value="Access expert">Access expert</label><br>
                        <label><input type="checkbox" name="license[]" value="Access Specialist">Access Specialist</label><br>
                        <label><input type="checkbox" name="license[]" value="PowerPoint expert">PowerPoint expert</label><br>
                        <label><input type="checkbox" name="license[]" value="PowerPoint Specialist">PowerPoint Specialist</label><br>
                    </div>
                </div>


                <div class="dropdown">
                    <h5 class="dropdown-header">アニメーション・イラスト系<br>
                    CG・映像系<br>
                    デザイン・Web系</h5><br>
                    <div class="dropdown-content">
                        <label><input type="checkbox" name="license[]" value="CGクリエイター検定">CGクリエイター検定</label><br>
                        <label><input type="checkbox" name="license[]" value="色彩検定">色彩検定</label><br>
                        <label><input type="checkbox" name="license[]" value="Webクリエイター検定">Webクリエイター検定</label><br>
                    </div>
                </div>
                <label><input id = 'none' type="checkbox" name="license[]" value="勉強中">勉強中</label><br>
                <label>※趣味を選んでください</label>
                <div class="dropdown">
                    <h5 class="dropdown-header">趣味</h5>
                    <div class="dropdown-content">
                        <label><input type="checkbox" name="tags[]" value="アウトドア">アウトドア</label><br>
                        <label><input type="checkbox" name="tags[]" value="インドア">インドア</label><br>
                        <label><input type="checkbox" name="tags[]" value="旅行">旅行</label><br>
                        <label><input type="checkbox" name="tags[]" value="読書">読書</label><br>
                        <label><input type="checkbox" name="tags[]" value="音楽">音楽</label><br>
                    </div>
                </div>
            </div>
            









            <button type="submit" class="register-button">登録する</button>
        </form>

        <div class="back-button">
            <button onclick="history.back()">戻る</button>
        </div>
        <script src="js/register.js"></script>
        <script src="js/tag.js"></script>
    </div>
</body>
</html>







