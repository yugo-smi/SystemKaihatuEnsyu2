<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Link Profile</title>
    <link rel="stylesheet" href="css/style_profile.css">
</head>
<body>
    <div class="profile-container">
        <div id = "header">
            <a href="index.php">
                <img class = "logo"  src="image/logo.png" alt="ロゴ">
            </a>
                <img class = "hamburger_bar" src="image/hamburger.png" alt="メニュー"> 
            <div class = "logotitle">
                <img src="image/logotitle.png" alt="タイトル">
            </div>
        </div>

        <div class="profile-section">
            <div class="buttons">
                <button class="btn">プロフィール</button>
                <button class="btn heart">💖</button>
            </div>

            <!-- 画像選択機能を追加 -->
            <div class="profile-info">
                <div class="profile-pic-container">
                    <img src="image/default-pic.png" alt="プロフィール画像" id="profile-pic" class="profile-pic">
                    <label for="profile-pic-input" class="file-label">プロフィール画像を選択</label>
                    <input type="file" id="profile-pic-input" accept="image/*" aria-label="プロフィール画像を選択">
                </div>

                <!-- 名前入力欄 -->
                <div class="name-box">
                    <input type="text" class="name-input" placeholder="名前を入力してください">
                </div>

                <!-- 性別、誕生日、血液型を名前の下に配置 -->
                <div class="details">
                    <label>性別:
                        <select aria-label="性別を選択">
                            <option value="" disabled selected>選択してください</option>
                            <option value="male">男性</option>
                            <option value="female">女性</option>
                            <option value="other">その他</option>
                        </select>
                    </label>
                    <label>誕生日: <input type="date" aria-label="誕生日を選択"></label>
                    <label>血液型:
                        <select aria-label="血液型を選択">
                            <option value="" disabled selected>選択してください</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                        </select>
                    </label>
                </div>
            </div>

            <div class="bio">
                <h3>自己紹介</h3>
                <textarea placeholder="自己紹介を入力してください"></textarea>
            </div>
            <div class = "submit">
                <button class="submit-btn">確定</button>
            </div>
        </div>
    </div>

    <!-- JavaScriptで画像プレビュー機能を追加 -->
    <script>
        const fileInput = document.getElementById("profile-pic-input");
        const profilePic = document.getElementById("profile-pic");
        const label = document.querySelector(".file-label");

        fileInput.addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePic.src = e.target.result;
                    label.style.display = 'none'; // 画像が選択された場合にラベルを見えなくする
                };
                reader.readAsDataURL(file);
            } else {
                label.style.display = 'flex'; // 何も選択されていない場合はラベルを再表示
            }
        });

        // 画像を選択している場合に再度選択できるようにする
        profilePic.addEventListener("click", function() {
            fileInput.click();
        });
    </script>
</body>
</html>
