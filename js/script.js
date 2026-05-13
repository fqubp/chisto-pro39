document.addEventListener('DOMContentLoaded', function () {

    // ===== BURGER MENU =====
    const burger = document.getElementById('burger');
    const nav = document.querySelector('.header__nav');

    if (burger && nav) {
        burger.addEventListener('click', function () {
            const isOpen = nav.classList.toggle('header__nav--active');
            burger.classList.toggle('active');
            document.body.style.overflow = isOpen ? 'hidden' : '';
        });

        nav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                nav.classList.remove('header__nav--active');
                burger.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }

    // ===== MOBILE SUBMENU (click-based, touch-friendly) =====
    document.querySelectorAll('.menu-item-has-children').forEach(item => {
        const link = item.querySelector(':scope > a');
        if (!link) return;

        link.addEventListener('click', function (e) {
            if (window.innerWidth <= 992) {
                const submenu = item.querySelector('.submenu');
                if (submenu) {
                    e.preventDefault();
                    item.classList.toggle('open');
                    // close other open items
                    document.querySelectorAll('.menu-item-has-children.open').forEach(other => {
                        if (other !== item) other.classList.remove('open');
                    });
                }
            }
        });
    });

    // ===== HEADER SCROLL SHADOW =====
    const header = document.querySelector('.header');
    if (header) {
        const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 20);
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // ===== SCROLL FADE-IN ANIMATIONS =====
    const fadeEls = document.querySelectorAll('.fade-in');
    if (fadeEls.length) {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

            fadeEls.forEach(el => observer.observe(el));
        } else {
            fadeEls.forEach(el => el.classList.add('visible'));
        }
    }

    // ===== STAGGER DELAY FOR GRIDS =====
    document.querySelectorAll('.services-grid, .advantages-grid, .stats__grid').forEach(grid => {
        grid.querySelectorAll('.service-card, .advantage, .stat').forEach((el, i) => {
            el.style.transitionDelay = (i * 0.1) + 's';
        });
    });

    // ===== COOKIE BANNER =====
    const cookieBanner = document.getElementById('cookieBanner');
    const cookieAccept = document.getElementById('cookieAccept');
    if (cookieBanner && !localStorage.getItem('cookie_ok')) {
        setTimeout(() => cookieBanner.classList.add('show'), 800);
    }
    if (cookieAccept) {
        cookieAccept.addEventListener('click', () => {
            localStorage.setItem('cookie_ok', '1');
            cookieBanner.classList.remove('show');
        });
    }

    // ===== FAQ ACCORDION =====
    document.querySelectorAll('.faq__question').forEach(btn => {
        btn.addEventListener('click', () => {
            const item = btn.closest('.faq__item');
            const isOpen = item.classList.contains('open');
            document.querySelectorAll('.faq__item.open').forEach(i => i.classList.remove('open'));
            if (!isOpen) item.classList.add('open');
        });
    });

    // ===== GALLERY FILTER =====
    const filterBtns = document.querySelectorAll('.gallery__filter');
    if (filterBtns.length) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const filter = btn.dataset.filter;
                document.querySelectorAll('.gallery__item').forEach(item => {
                    if (filter === 'all' || item.dataset.category === filter) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            });
        });
    }

    // ===== CALCULATOR =====
    const calcTotalSpan = document.getElementById('calc-total');
    if (calcTotalSpan) {
        const calcInputs = document.querySelectorAll('.calc-input');
        const calcExtras = document.querySelectorAll('.calc-extra');
        const orderPriceInput = document.getElementById('order-price');

        function calculateTotal() {
            const typeRate = parseFloat(document.getElementById('calc-type').value) || 0;
            const area = parseFloat(document.getElementById('calc-area').value) || 0;
            const rooms = parseFloat(document.getElementById('calc-rooms').value) || 0;

            let total = typeRate * area + rooms * 750;
            document.querySelectorAll('.calc-extra:checked').forEach(cb => {
                total += parseFloat(cb.dataset.price) || 0;
            });

            total = Math.round(total);
            calcTotalSpan.textContent = total.toLocaleString('ru-RU');
            if (orderPriceInput) orderPriceInput.value = total;
        }

        calcInputs.forEach(input => input.addEventListener('input', calculateTotal));
        calcExtras.forEach(extra => extra.addEventListener('change', calculateTotal));
        calculateTotal();
    }

});

    // ===== PHONE MASK =====
    document.querySelectorAll('input[type="tel"]').forEach(input => {
        input.addEventListener('input', function () {
            let val = this.value.replace(/\D/g, '');
            if (val.startsWith('8')) val = '7' + val.slice(1);
            if (!val.startsWith('7')) val = '7' + val;
            val = val.slice(0, 11);
            let formatted = '+7';
            if (val.length > 1) formatted += ' (' + val.slice(1, 4);
            if (val.length > 4) formatted += ') ' + val.slice(4, 7);
            if (val.length > 7) formatted += '-' + val.slice(7, 9);
            if (val.length > 9) formatted += '-' + val.slice(9, 11);
            this.value = formatted;
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && this.value === '+7 (') {
                this.value = '';
            }
        });
    });

    // ===== DYNAMIC PAGE TITLE =====
    const pageTitles = {
        'index.php': 'Чисто-про39 — Профессиональный клининг в Калининграде',
        'services.php': 'Услуги клининга — Чисто-про39',
        'prices.php': 'Цены на уборку — Чисто-про39',
        'calculator.php': 'Калькулятор стоимости уборки — Чисто-про39',
        'gallery.php': 'Наши работы — Чисто-про39',
        'reviews.php': 'Отзывы клиентов — Чисто-про39',
        'contacts.php': 'Контакты — Чисто-про39',
        'about.php': 'О компании — Чисто-про39',
        'faq.php': 'Частые вопросы — Чисто-про39',
    };
    const page = window.location.pathname.split('/').pop() || 'index.php';
    if (pageTitles[page]) document.title = pageTitles[page];
