document.addEventListener('DOMContentLoaded', function() {
    // パスワードの表示/非表示を切り替える機能
    document.getElementById('toggle-password').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('img').src = type === 'password' ? 'image/eye-icon.png' : 'image/eye-slash-icon.png';
    });
  
    document.getElementById('toggle-confirm-password').addEventListener('click', function () {
        const confirmPasswordInput = document.getElementById('confirm-password');
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        this.querySelector('img').src = type === 'password' ? 'image/eye-icon.png' : 'image/eye-slash-icon.png';
    });
  
    // フォーム送信時のパスワード確認
    document.getElementById('registerForm').addEventListener('submit', function(event) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const pattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
  
        // パスワードが8文字以上で英数字を含むかをチェック
        if (!pattern.test(password)) {
            alert("パスワードは英数字を組み合わせた8文字以上で入力してください。");
            event.preventDefault();
            return;
        }
  
        // パスワードと確認パスワードが一致するかをチェック
        if (password !== confirmPassword) {
            alert("パスワードが一致しません。もう一度確認してください。");
            event.preventDefault(); // フォーム送信をキャンセル
            return;
        }
    });
  });
  document.getElementById("registerForm").addEventListener("submit", function (event) {
      const tags = document.querySelectorAll("input[name='tags[]']:checked");
      if (tags.length === 0) {
          alert("少なくとも1つの趣味を選択してください。");
          event.preventDefault();
      }
  });
  
  
  document.getElementById("registerForm").addEventListener("submit", function (event) {
    const license = document.querySelectorAll("input[name='license[]']:checked");
    if (license.length === 0) {
        alert("少なくとも1つの資格を選択してください。");
        event.preventDefault();
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const noneCheckbox = document.getElementById("none");
    const licenseCheckboxes = document.querySelectorAll('input[name="license[]"]:not(#none)');

    // "なし"がチェックされた場合、他のチェックボックスを外す
    noneCheckbox.addEventListener("change", function () {
        if (this.checked) {
            licenseCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    });

    // 他のチェックボックスがチェックされた場合、"なし"を外す
    licenseCheckboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            if (this.checked) {
                noneCheckbox.checked = false;
            }
        });
    });
});

