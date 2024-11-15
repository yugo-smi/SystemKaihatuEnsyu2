<?php
session_start();

// ログイン状態をJSON形式で返す
echo json_encode(['loggedin' => isset($_SESSION['user_id'])]);
?>
