<!DOCTYPE html>
<html lang="ja">
    <head>
        <html lang="ja">
        <title>NEW LINK</title>  
        <link rel="stylesheet" href="./css/style_partner_profile.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <!--/head（ページ概要） -->


    <!-- body（本文） -->
    <body>
        <!-- ヘッダー-->
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
    <script src="js/index_hamburger.js"></script>
    <!-- <div class="profile-section">
         <div class="buttons">
            <button class="btn">プロフィール</button>
            <button class="btn heart">💖</button>
        </div> -->

        <!-- 画像選択機能を追加 -->
        <div class="partner_profile-info">
            <div class="profile-pic-container">
                <!--マッチング相手のプロフィール画像をデータベースから引っ張ってアイコン画像に設定する-->
            </div>

            <!-- 名前入力欄 -->
            <div class="name-box">
                 <!--マッチング相手の名前をデータベースから引っ張って名前のテキストボックスに格納する-->
            </div>

            <!-- 性別、誕生日、血液型を名前の下に配置 -->
            <div class="details">
                <label>性別:
                    <select aria-label="性別を選択">
                        <!--マッチング相手の性別をデータベースから引っ張ってプルダウンメニューに格納する-->
                    </select>
                </label>
                <label>誕生日: <input type="text" aria-label="誕生日を選択">
                    <!--マッチング相手の誕生日をデータベースから引っ張ってプルダウンメニューに格納する-->
                </label>
                <label>血液型:
                    <select aria-label="血液型を選択">
                        <!--マッチング相手の血液型をデータベースから引っ張ってプルダウンメニューに格納する-->
                    </select>
                </label>
            </div>
        </div>

        <div class="bio">
            <h3>自己紹介</h3>
            <textarea placeholder="自己紹介を入力してください">
<!--マッチング相手の自己紹介をデータベースから引っ張ってテキストボックスに格納する-->
            </textarea>
        </div>
        <div class = "chat-or-change">
            <button class = "matching_chat">チャットする</button>
            <button class = "matching_change">チェンジする</button>
        </div>
    </div>
</body>
</html>
