        document.querySelector('input[name="search"]').addEventListener('input', function () {
        const maxLength = 12;
        if (this.value.length > maxLength) {
            this.value = this.value.substring(0, maxLength); // 12文字以上をカット
        }
    });

    document.querySelectorAll('.dropdown').forEach(function(dropdown) {
        dropdown.addEventListener('click', function() {
            const content = this.querySelector('.dropdown-content');
            content.classList.toggle('active');
        });
    });
    
    
    document.addEventListener("DOMContentLoaded", () => {
        const dropdownHeaders = document.querySelectorAll(".dropdown-header");
    
        dropdownHeaders.forEach(header => {
            header.addEventListener("click", () => {
                const dropdownContent = header.nextElementSibling; // 次の要素を取得
                if (dropdownContent.classList.contains("active")) {
                    dropdownContent.classList.remove("active"); // 非表示
                } else {
                    dropdownContent.classList.add("active"); // 表示
                }
            });
        });
    });
    