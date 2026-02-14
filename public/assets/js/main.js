document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.querySelector('#menuToggle');
    const categoryMenu = document.querySelector('.category-menu');
    if (menuBtn && categoryMenu) {
        menuBtn.addEventListener('click', () => {
            categoryMenu.classList.toggle('open');
        });
    }

    document.querySelectorAll('[data-mask="phone"]').forEach(input => {
        input.addEventListener('input', () => {
            let value = input.value.replace(/[^0-9+]/g, '');
            if (!value.startsWith('+998')) {
                value = '+998' + value.replace(/^\+?998/, '');
            }
            if (value.length > 13) {
                value = value.slice(0, 13);
            }
            input.value = value;
        });
    });
});
