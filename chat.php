<?php
// セッション開始
session_start();

// メッセージが送信された場合
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $message_content = $_POST['message_content'];

    // メッセージをファイルに保存
    $message = "送信者ID: $sender_id\n受信者ID: $receiver_id\nメッセージ内容: $message_content\n\n";
    
    file_put_contents('messages.txt', $message, FILE_APPEND);

    echo "メッセージが送信されました！";
}
?>

<!-- メッセージ送信フォーム -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メッセージ送信</title>
</head>
<body>
    <h2>メッセージ送信</h2>
    <form method="POST" action="send_message.php">
        <label for="sender_id">送信者ID:</label>
        <input type="text" name="sender_id" required><br><br>
        
        <label for="receiver_id">受信者ID:</label>
        <input type="text" name="receiver_id" required><br><br>
        
        <label for="message_content">メッセージ内容:</label><br>
        <textarea name="message_content" rows="5" cols="50" required></textarea><br><br>
        
        <button type="submit">送信</button>
    </form>

    <br><a href="view_messages.php">メッセージ履歴を見る</a>
</body>
</html>

<?php
// メッセージファイルを読み込む
if (file_exists('messages.txt')) {
    $messages = file_get_contents('messages.txt');
} else {
    $messages = "メッセージはありません";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メッセージ履歴</title>
</head>
<body>
    <h2>メッセージ履歴</h2>

    <pre><?php echo $messages; ?></pre>

    <br><a href="send_message.php">メッセージを送る</a>
</body>
</html>
