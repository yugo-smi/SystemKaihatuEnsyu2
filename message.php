<!-- html -->
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

<!-- head（ページ概要） -->
<head>
    <html lang="ja">
    <title>NEW LINK</title>  
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="image/logo.png">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<!--/head（ページ概要） -->


<!-- body（本文） -->
<body>
    <!-- ヘッダー-->
    <header>
        <div id = "header">
            
            <a class ="header-inner">
                <a class="header-logo" href="./index.html">
                    <img class = "logo"  src="image/logo.png" alt="ロゴ"></h1>
                <a class="header-connect" href="./connect.html"></a>
                    <img class = "connect" image="/connect.png" alt="お問い合わせ"></h1>
            </a> 
        </div>
        
        <nav class="menu" id="menu">
            <ul>
                <li><a href="index.php">ホーム</a></li>
                <li><a href="kensaku.php">お相手を検索</a></li>
                <li><a href="talk.php">トーク履歴</a></li>
                <li><a href="favorites.php">お気に入り</a></li>
                <li><a href="profile.php">プロフィール</a></li>
                <li><a href="logout.php">ログアウト</a></li>
            </ul>
        </nav>
    </header>
    <!--/ヘッダー-->

    
    <!-- メイン画像-->
    <div>
        <p><img class="main_img" src="./image/deai.png" alt="メイン画像"></p>
    </div>
    <!--/メイン画像-->


    <!-- メイン -->
    <div id="wappaer">
        <div id="main">
            <section id="point">
                <h2>最近の話題</h2>
                <section>
                    <h3>
                        出会いの証
                    </h3>
                    <figure>
                        <img class = "akasi_img" src="image/akasi.png" alt="出会いの証">
                        <figcaption>デートの様子</figcaption>
                        <p>出会った方々が一緒に遊んでいる様子です。<br>
                        このアプリで、様々な方が出会っています。</p>
                    </figure>
                </section>
            </section>
        </div>
    </div>
    <!--/メイン -->

    <!-- フッター -->
    <footer>
    <nav class="menu" id="menu">
                 <ul>
                 <li><a href="index.php">ホーム</a></li>
                    <li><a href="kensaku.php">お相手を検索</a></li>
                    
                    <li><a href="talk.php">トーク履歴</a></li>
                    <li><a href="favorites.php">つながり</a></li>
                    <li><a href="profile.php">プロフィール</a></li>
                    <?php if ($isLoggedIn): ?>
                        
                    <?php else: ?>
                        <li><a href="logout.php">ログアウト</a></li>
                    <?php endif; ?>
                 </ul>
             </nav>
    </footer>
    <!--/フッター -->

</body>
<!--/body（本文） -->

</html>
<!--/html -->