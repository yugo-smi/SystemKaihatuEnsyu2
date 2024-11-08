let slideIndex = 0;

// 自動スライドショーを開始
showSlides();

// スライドの表示を更新する関数
function showSlides() {
  let slides = document.getElementsByClassName("slide");

  // すべてのスライドを非表示にする
  for (let i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }

  // 次のスライドに進む
  slideIndex++;
  if (slideIndex > slides.length) {
    slideIndex = 1; // 最初のスライドに戻る
  }

  // 現在のスライドを表示
  slides[slideIndex - 1].style.display = "block";

  // 3秒ごとにshowSlides()を呼び出してスライドを切り替える
  setTimeout(showSlides, 3000);
}

// 次・前ボタンでスライドを切り替える関数
function changeSlide(n) {
  let slides = document.getElementsByClassName("slide");

  // スライドインデックスを更新
  slideIndex += n;

  // インデックスの範囲をチェック
  if (slideIndex > slides.length) {
    slideIndex = 1;
  } else if (slideIndex < 1) {
    slideIndex = slides.length;
  }

  // スライドを表示
  for (let i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  slides[slideIndex - 1].style.display = "block";
}