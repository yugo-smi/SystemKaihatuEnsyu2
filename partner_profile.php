<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>NEW LINK</title>  
        <link rel="stylesheet" href="./css/style_partner_profile.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <!-- body（本文） -->
    <body>
        <!-- ヘッダー -->
        <div id="header">
            <a href="index.php">
                <img class="logo" src="image/logo.png" alt="ロゴ">
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

            <div class="logotitle">
                <img src="image/logotitle.png" alt="タイトル">
            </div>
        </div>
        <script src="js/index_hamburger.js"></script>

        <h2>プロフィール</h2>
        <div class="profile-info">
            <div class="profile-pic-container">
                <img src="<?= htmlspecialchars($user['image_path'] ?: 'image/default-pic.png', ENT_QUOTES, 'UTF-8') ?>" 
                     alt="プロフィール画像" id="profile-pic" class="profile-pic">
            </div>

            <label>ニックネーム:</label>
            <input type="text" name="nickname" value="<?= htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8') ?>" readonly><br>

            <label>タグ:</label>
            <div class="tag-container">
                <?php 
                // タグを選択済み状態で表示
                $tags = ["アウトドア", "インドア", "旅行", "読書", "音楽"];
                $selected_tags = explode(",", $user['tags']);
                foreach ($tags as $tag) {
                    if (in_array($tag, $selected_tags)) {
                        echo "<span class='tag'>$tag</span> ";
                    }
                }
                ?>
            </div>

            <div class="bio">
                <h3>自己紹介</h3>
                <textarea readonly><?= htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="chat-or-change">
                <button class="matching_chat">チャットする</button>
                <button class="matching_change">チェンジする</button>
            </div>
        </div>
    </body>
</html>
