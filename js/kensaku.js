
    document.querySelector('input[name="search"]').addEventListener('input', function () {
        const maxLength = 12;
        if (this.value.length > maxLength) {
            this.value = this.value.substring(0, maxLength); // 12文字以上をカット
        }
    });
