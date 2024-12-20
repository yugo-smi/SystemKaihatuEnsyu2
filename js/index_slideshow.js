let slideIndex = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');

// スライド表示用の関数
function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.style.display = i === index ? 'block' : 'none';
        dots[i].classList.toggle('active', i === index);
    });
}

// 自動スライドの設定
function autoSlide() {
    slideIndex = (slideIndex + 1) % slides.length;
    showSlide(slideIndex);
    setTimeout(autoSlide, 3000); // 3秒ごとにスライドを切り替える
}

// 初期表示
showSlide(slideIndex);
autoSlide();

// ドットクリックでスライド変更
dots.forEach((dot, i) => {
    dot.addEventListener('click', () => {
        slideIndex = i;
        showSlide(slideIndex);
    });
});

// タッチスワイプの設定
let startX;

function touchStart(e) {
    startX = e.touches ? e.touches[0].clientX : e.clientX;
}

function touchMove(e) {
    if (!startX) return;
    const endX = e.touches ? e.touches[0].clientX : e.clientX;
    const diffX = startX - endX;

    if (diffX > 50) { // 左スワイプ
        nextSlide();
        startX = null;
    } else if (diffX < -50) { // 右スワイプ
        prevSlide();
        startX = null;
    }
}

function nextSlide() {
    slideIndex = (slideIndex + 1) % slides.length;
    showSlide(slideIndex);
}

function prevSlide() {
    slideIndex = (slideIndex - 1 + slides.length) % slides.length;
    showSlide(slideIndex);
}

// スワイプイベントの設定（PCとモバイル両対応）
slides.forEach(slide => {
    slide.addEventListener('touchstart', touchStart);
    slide.addEventListener('touchmove', touchMove);
    slide.addEventListener('mousedown', touchStart);
    slide.addEventListener('mouseup', touchMove);
});
// モーダルを開く関数
function openModal(imageSrc) {
    const modal = document.getElementById('modal');
    const modalImage = document.getElementById('modalImage');
    modal.style.display = 'block';
    modalImage.src = imageSrc;
}

// モーダルを閉じる関数
function closeModal() {
    const modal = document.getElementById('modal');
    modal.style.display = 'none';
}

// モーダル外をクリックしたら閉じる
window.onclick = function(event) {
    const modal = document.getElementById('modal');
    if (event.target === modal) {
        closeModal();
    }
};

async function fetchLatestMessage() {
    try {
        const response = await fetch('chat.php?action=fetch_latest&partner_id=' + partnerId);
        const latestMessage = await response.json();

        const latestMessageBox = document.getElementById('latest-message');
        if (latestMessage) {
            latestMessageBox.innerHTML = `
                <div>
                    <p><strong>${latestMessage.send_user_id == currentUserId ? 'あなた' : latestMessage.sender_name}:</strong></p>
                    <p>${latestMessage.message_text}</p>
                    <p><small>${latestMessage.sent_time}</small></p>
                </div>
            `;
        } else {
            latestMessageBox.innerHTML = '<p>最新メッセージはありません。</p>';
        }
    } catch (error) {
        console.error('Error fetching the latest message:', error);
    }
}