<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New　Link</title>
    <link rel="stylesheet" href="./css/chat.css">
</head>
<body>
    <div class="chat-container">
        <header>
            <h2>New Link [User]</h2>
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
                        <li><a href="profile.php">プロフィール</a></li>
                        <li><a href="">PayPay</a></li>
                        <li><a href="">QuickPay</a></li>
                    </ul>
                </nav>
        
                <div class = "logotitle">
                    <img src="image/logotitle.png" alt="タイトル">
                </div>
            </div>
        
        </header>
        <div id="chat-box" class="chat-box"></div>
        <div class="input-area">
            
            <input type="text" id="message-input" placeholder="">
            <button id="send-button">送信</button>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
