<?php
session_start();

// ログイン状態を確認
if (!isset($_SESSION['user_id'])) {
    // ログインしていない場合、login.php へリダイレクト
    header("Location: login.php");
    exit;
}
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEW LINK</title>
    <link rel="stylesheet" href="css/style_index.css">
</head>

<body>
    <!-- ヘッダー -->
    <header>
        <div id="header">
            <a href="index.php">
                <img class="logo" src="image/logo.png" alt="ロゴ">
            </a>

            <div class="hamburger" id="hamburger">
                <img src="image/hamburger.png" alt="ハンバーガーメニュー">
            </div>

            <!-- メニュー -->
            <nav class="menu" id="menu">
                <ul>
                    <li><a href="index.php">ホーム</a></li>
                    <li><a href="kensaku.php">お相手を検索</a></li>
                    <li><a href="message.php">スレッド</a></li>
                    <li><a href="chat.php">メッセージ</a></li>
                    <?php if ($isLoggedIn): ?>
                        <li><a href="profile.php">プロフィール</a></li>
                        <li><a href="logout.php">ログアウト</a></li>
                    <?php else: ?>
                        <li class="logout"><a href="login.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="logotitle">
                <img src="image/logotitle.png" alt="タイトル">
            </div>
        </div>
    </header>
    <!--/ヘッダー-->

    <!-- メイン -->
    <div class="main-container">
        <div class="slideshow-container">
            <div class="slide">
                <img src="./image/deai.png" alt="Slide 1">
            </div>
            <div class="slide">
                <img src="./image/akasi.png" alt="Slide 2">
            </div>
            <div class="slide">
                <img src="./image/default-pic.png" alt="Slide 3">
            </div>
        </div>
        <div class="dots-container">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
        </div>
    </div>

    <!-- ランダムマッチングボタン -->
    <a href="./partner_profile.php" class="random_matching_button">ランダムマッチングボタン</a>

    <!-- スクリプト -->
    <script src="js/index_hamburger.js"></script>
    <script src="./js/index_slideshow.js"></script>
    <!--/メイン -->

    <!-- フッター -->
    <footer>
        <!-- フッター内容 -->
    </footer>
    <!--/フッター -->

</body>

</html>
