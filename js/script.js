// Бургер-меню для мобильных устройств
document.addEventListener('DOMContentLoaded', function() {
    const burger = document.getElementById('burger');
    const nav = document.querySelector('.header__nav');
    
    if (burger && nav) {
        burger.addEventListener('click', function() {
            // Переключаем классы для открытия/закрытия меню
            nav.classList.toggle('header__nav--active');
            burger.classList.toggle('active');
            
            // Блокируем прокрутку body при открытом меню (опционально)
            if (nav.classList.contains('header__nav--active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
        
        // Закрытие меню при клике на ссылку внутри навигации
        const navLinks = nav.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                nav.classList.remove('header__nav--active');
                burger.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }

    // Калькулятор стоимости (если он есть на странице)
    const calcTotalSpan = document.getElementById('calc-total');
    if (calcTotalSpan) {
        const calcInputs = document.querySelectorAll('.calc-input');
        const calcExtras = document.querySelectorAll('.calc-extra');
        const orderPriceInput = document.getElementById('order-price');

        function calculateTotal() {
            const typeRate = parseFloat(document.getElementById('calc-type').value) || 0;
            const area = parseFloat(document.getElementById('calc-area').value) || 0;
            const rooms = parseFloat(document.getElementById('calc-rooms').value) || 0;

            let total = typeRate * area + rooms * 500;

            document.querySelectorAll('.calc-extra:checked').forEach(function(cb) {
                total += parseFloat(cb.dataset.price) || 0;
            });

            total = Math.round(total);
            calcTotalSpan.textContent = total;
            if (orderPriceInput) orderPriceInput.value = total;
        }

        calcInputs.forEach(input => input.addEventListener('input', calculateTotal));
        calcExtras.forEach(extra => extra.addEventListener('change', calculateTotal));
        calculateTotal();
    }
});