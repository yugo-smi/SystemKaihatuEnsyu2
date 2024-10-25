document.addEventListener('DOMContentLoaded', function() {
    // パスワードの表示/非表示を切り替える機能
    document.getElementById('toggle-password').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // アイコンの変更
        this.querySelector('img').src = type === 'password' ? 'image/eye-icon.png' : 'image/eye-slash-icon.png';
    });
});

document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // デフォルトのフォーム送信を防ぐ
    // バリデーションやAPIリクエストなどの処理をここに追加

    // 成功した場合にページ遷移
    window.location.href = 'index.php'; // 遷移先ページ
});
