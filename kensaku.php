<?php
// セッション開始
session_start();
// 検索結果の保持時間（秒単位、例: 1分=60秒）
$session_lifetime = 30;

// 検索タイムスタンプを確認
if (isset($_SESSION['search_timestamp'])) {
    // 現在時刻と比較
    if (time() - $_SESSION['search_timestamp'] > $session_lifetime) {
        // 一定時間が経過していたら検索結果をクリア
        unset($_SESSION['search_keyword'], $_SESSION['tags'], $_SESSION['license'], $_SESSION['results'], $_SESSION['search_timestamp']);
    }
}

// 新たな検索が行われた場合、タイムスタンプを更新
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['search_timestamp'] = time();
}

$_SESSION['previous_url'] = $_SERVER['REQUEST_URI'];

// 現在のユーザーIDをセッションから取得
$currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// データベース接続設定
$servername = "localhost:3306";
$dbname = "newlink";
$username = "root";
$password = "root";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $results = [];
    $searchKeyword = isset($_SESSION['search_keyword']) ? $_SESSION['search_keyword'] : '';
    $tags = isset($_SESSION['tags']) ? $_SESSION['tags'] : [];
    $licenses = isset($_SESSION['license']) ? $_SESSION['license'] : [];

    // 検索フォームが送信された場合
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
        $licenses = isset($_POST['license']) ? $_POST['license'] : [];
        $searchKeyword = isset($_POST['search']) ? $_POST['search'] : '';

        // 12文字以上の検索キーワードを制限
        if (strlen($searchKeyword) > 12) {
            $errorMessage = "\u691c\u7d22\u30ad\u30fc\u30ef\u30fc\u30c9\u306f12\u6587\u5b57\u5185\u3067\u5165\u529b\u3057\u3066\u304f\u3060\u3055\u3044\u3002";
        } else {
            // セッションに保存
            $_SESSION['tags'] = $tags;
            $_SESSION['license'] = $licenses;
            $_SESSION['search_keyword'] = $searchKeyword;

            if (empty($tags) && empty($licenses) && empty($searchKeyword)) {
                $results = [];
            } else {
                $query = "SELECT id, nickname, bio, image_path FROM user_table WHERE 1";

                if (!empty($tags)) {
                    foreach ($tags as $index => $tag) {
                        $query .= " AND tags LIKE :tag$index";
                    }
                }

                if (!empty($licenses)) {
                    foreach ($licenses as $index => $license) {
                        $query .= " AND license LIKE :license$index";
                    }
                }

                if (!empty($searchKeyword)) {
                    $query .= " AND nickname LIKE :search";
                }

                $stmt = $pdo->prepare($query);

                foreach ($tags as $index => $tag) {
                    $stmt->bindValue(":tag$index", '%' . $tag . '%');
                }

                foreach ($licenses as $index => $license) {
                    $stmt->bindValue(":license$index", '%' . $license . '%');
                }

                if (!empty($searchKeyword)) {
                    $stmt->bindValue(':search', '%' . $searchKeyword . '%');
                }

                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 現在のユーザーを検索結果から除外
                $results = array_filter($results, function ($user) use ($currentUserId) {
                    return $user['id'] != $currentUserId;
                });

                // 検索結果をセッションに保存
                $_SESSION['results'] = $results;
            }
        }
    } else {
        // POSTリクエストでない場合、セッションから結果を取得
        $results = isset($_SESSION['results']) ? $_SESSION['results'] : [];
    }
} catch (PDOException $e) {
    echo "\u30c7\u30fc\u30bf\u30d9\u30fc\u30b9\u63a5\u7d9a\u30a8\u30e9\u30fc: " . $e->getMessage();
    exit;
}
?>



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Link</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style_kensaku.css">
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
                    <li><a href="kensaku.php">検索</a></li>
                    <li><a href="talk.php">トーク</a></li>
                    <li><a href="favorites.php">お気に入り</a></li>
                    <li><a href="profile.php">プロフィール</a></li>
                    <li><a href="logout.php">ログアウト</a></li>
                </ul>
            </nav>

            <div class="logotitle">
                <img src="image/logotitle.png" alt="タイトル">
            </div>
        </div>
    </header>
    <script src="js/kensaku_hamburger.js"></script>

    <!-- メインコンテンツ -->
    <main>
        <!-- Search Section -->
        <form method="POST" action="">
    <div class="buttons">
        <div class="search-input-container">
            <input type="text" name="search" placeholder="検索" value="<?= htmlspecialchars($searchKeyword, ENT_QUOTES, 'UTF-8') ?>" maxlength="12">
            <button class="btn search-button"><i class="fas fa-search"></i></button>
        </div>
        <div class="search-input-container">
        <label>※保有資格を選んでください</label>          
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
                        <label><input type="checkbox" name="tags[]" value="料理">料理</label><br>
                        <label><input type="checkbox" name="tags[]" value="ゲーム">ゲーム</label><br>
                        <label><input type="checkbox" name="tags[]" value="釣り">釣り</label><br>
                        <label><input type="checkbox" name="tags[]" value="旅行">旅行</label><br>
                        <label><input type="checkbox" name="tags[]" value="麻雀">麻雀</label><br>
                        <label><input type="checkbox" name="tags[]" value="パチンコ・スロット">パチンコ・スロット</label><br>
                        <label><input type="checkbox" name="tags[]" value="スポーツ">スポーツ</label><br>
                        <label><input type="checkbox" name="tags[]" value="漫画">漫画</label><br>
                        <label><input type="checkbox" name="tags[]" value="アイドル好き">アイドル好き</label><br>
                        <label><input type="checkbox" name="tags[]" value="BL好き">BL好き</label><br>
                        <label><input type="checkbox" name="tags[]" value="絵を描く">絵を描く</label><br>
                        <label><input type="checkbox" name="tags[]" value="外食">外食</label><br>
                    </div>
                </div>
            </div>
            <button class="btn search-button"><i class="fas fa-search"></i></button>
        </div>
    </div>
    <!-- エラーメッセージを表示 -->
    <?php if (isset($errorMessage)): ?>
        <div class="error-message"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
</form>



        <!-- Results Container for Displaying Search Results -->
        <div class="results-container">
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($results)): ?>
                <p>検索結果はありません。</p>
            <?php elseif (!empty($results)): ?>
                <?php foreach ($results as $user): ?>
                    <!-- カード全体をリンク化 -->
                    <a href="search_profile.php?id=<?= htmlspecialchars($user['id']) ?>" class="user-card">
                        <div class="profile-card">
                            <div class="profile-image">
                                <img src="<?= htmlspecialchars($user['image_path'] ?: 'image/default-pic.png', ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像">
                            </div>
                            <div class="profile-content">
                                <div class="profile-name"><?= htmlspecialchars($user['nickname']) ?></div>
                                <div class="profile-bio"><?= htmlspecialchars($user['bio']) ?></div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <script src="js/hamburger.js"></script>
    <script src="js/kensaku.js"></script>
    <script src="js/tag.js"></script>
</body>
</html>
