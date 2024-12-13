document.querySelectorAll('.dropdown-header').forEach(header => {
    header.addEventListener('click', () => {
        const dropdown = header.parentElement;
        dropdown.classList.toggle('open');
    });
});
