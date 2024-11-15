document.addEventListener("DOMContentLoaded", function () {
  const profileLink = document.querySelector("a[href='profile.php']");

  // サーバーでセッションが有効か確認
  fetch("session_check.php")
      .then((response) => response.json())
      .then((data) => {
          if (!data.loggedin) {
              // セッションが無効の場合、プロフィールリンクを非表示または無効化
              profileLink.href = "login.php";
              profileLink.textContent = "ログインが必要です";
          }
      })
      .catch((error) => console.error("セッション確認エラー:", error));
});



