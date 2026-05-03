    </main>
    <footer class="footer">
        <div class="container footer__container">
            <div class="footer__col">
                <div class="footer__logo">Чисто-про39</div>
                <p class="footer__tagline">Чисто по ГОСТу в Калининграде</p>
                <div class="footer__social">
                    <a href="https://wa.me/79222501266" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://t.me/chisto_pro39_bot" target="_blank" aria-label="Telegram"><i class="fab fa-telegram"></i></a>
                </div>
            </div>
            <div class="footer__col">
                <h4>Навигация</h4>
                <ul>
                    <li><a href="<?= route('index.php') ?>">Главная</a></li>
                    <li><a href="<?= route('services.php') ?>">Услуги</a></li>
                    <li><a href="<?= route('prices.php') ?>">Цены</a></li>
                    <li><a href="<?= route('contacts.php') ?>">Контакты</a></li>
                </ul>
            </div>
            <div class="footer__col">
                <h4>Услуги</h4>
                <ul>
                    <li><a href="<?= route('private/apartment.php') ?>">Уборка квартир</a></li>
                    <li><a href="<?= route('private/cleaning.php') ?>">Химчистка мебели</a></li>
                    <li><a href="<?= route('private/windows.php') ?>">Мойка окон</a></li>
                    <li><a href="<?= route('business/office.php') ?>">Уборка офисов</a></li>
                </ul>
            </div>
            <div class="footer__col">
                <h4>Контакты</h4>
                <p><i class="fas fa-phone"></i> <a href="tel:+79222501266">+7 (922) 250-12-66</a></p>
                <p><i class="fas fa-envelope"></i> <a href="mailto:chisto-pro39@bk.ru">chisto-pro39@bk.ru</a></p>
                <p><i class="fas fa-clock"></i> Ежедневно 8:00–22:00</p>
            </div>
        </div>
        <div class="footer__bottom">
            <div class="container">
                <p>&copy; <?= date('Y') ?> Чисто-про39. Все права защищены.</p>
                <p><a href="<?= route('privacy.php') ?>">Политика конфиденциальности</a></p>
            </div>
        </div>
    </footer>

    <!-- Плавающие кнопки мессенджеров -->
    <div class="float-contacts">
        <a href="https://wa.me/79222501266" class="float-btn float-btn--wa" target="_blank" aria-label="Написать в WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="https://t.me/chisto_pro39_bot" class="float-btn float-btn--tg" target="_blank" aria-label="Написать в Telegram">
            <i class="fab fa-telegram"></i>
        </a>
    </div>

    <script src="<?= asset('js/script.js') ?>"></script>
</body>
</html>