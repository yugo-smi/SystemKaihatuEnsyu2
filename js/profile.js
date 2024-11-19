
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