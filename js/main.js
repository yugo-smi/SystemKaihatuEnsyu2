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
    const password = document.getElementById('password').value;
    const pattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;

    if (!pattern.test(password)) {
        alert("パスワードは英数字を組み合わせた8文字以上で入力してください。");
        event.preventDefault(); // フォーム送信をキャンセル
    }
});

