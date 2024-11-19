<?php
session_start();

// ログイン状態を確認
if (!isset($_SESSION['user_id'])) {
    // ログインしていない場合、login.php へリダイレクト
    header("Location: login.php?error=not_logged_in");
    exit;
}
?>

<!-- html -->
<!DOCTYPE html>
 
 <!-- head（ページ概要） -->
 <head>
     <html lang="ja">
     <title>NEW LINK</title>  
     <link rel="stylesheet" href="css/style_index.css">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
 </head>
 <!--/head（ページ概要） -->


 <!-- body（本文） -->
 <body>
     <!-- ヘッダー-->
     <header>
         <div id = "header">
             <a href="index.php">
                 <img class = "logo"  src="image/logo.png" alt="ロゴ">
             </a>

             <div class="hamburger" id="hamburger">
                 <img src="image/hamburger.png" alt="ハンバーガーバー">
             </div>

             <!-- メニュー -->
             <nav class="menu" id="menu">
                 <ul>
                     <li><a href="index.php">ホーム</a></li>
                     <li><a href="kensaku.php">お相手を検索</a></li>
                     <li><a href="message.php">スレッド</a></li>
                     <li><a href="chat.php">メッセージ</a></li>
                     <li><a href="profile.php">プロフィール</a></li>
                 </ul>
             </nav>

             <div class = "logotitle">
                 <img src="image/logotitle.png" alt="タイトル">
             </div>
         </div>
     </header>
     
     <!--/ヘッダー-->
     
     <!-- メイン-->


     <!-- <div class = "main_illust">
         <img src="./image/deai.png" alt="メイン画像">
     </div> -->

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
          <!-- インジケーターのドットを追加 -->
         <div class="dots-container">
             <span class="dot" onclick="currentSlide(1)"></span>
             <span class="dot" onclick="currentSlide(2)"></span>
             <span class="dot" onclick="currentSlide(3)"></span>
         </div>
 </div>
 <script src="js/index_hamburger.js"></script>
 <!-- ランダムマッチングボタン -->
 <a href="./partner_profile.php" class="random_matching_button">ランダムマッチングボタン</a>


     <script src="./js/index_slideshow.js"></script>   
     <!--/メイン -->

     <!-- フッター -->
     <footer>
        
     </footer>
     <!--/フッター -->

 </body>
 <!--/body（本文） -->

</html>
<!--/html -->

<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<nav class="menu" id="menu">
 <ul>
     <li><a href="index.php">ホーム</a></li>
     <?php if ($isLoggedIn): ?>
         <li><a href="profile.php">プロフィール</a></li>
     <?php else: ?>
         <li><a href="login.php">ログインが必要です</a></li>
     <?php endif; ?>
     <li><a href="">PayPay</a></li>
     <li><a href="">QuickPay</a></li>
 </ul>
</nav>
