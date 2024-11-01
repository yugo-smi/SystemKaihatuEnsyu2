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
});
