    </main>
    <footer class="footer">
        <div class="container footer__container">
            <div class="footer__col">
                <div class="footer__logo">Чисто-про39</div>
                <p class="footer__tagline">Чисто по ГОСТу в Калининграде</p>
                <div class="footer__social">
                    <a href="#" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-telegram"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-viber"></i></a>
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
                <p><i class="fas fa-phone"></i> <a href="tel:+74011234567">+7 (4012) 123-456</a></p>
                <p><i class="fas fa-envelope"></i> <a href="mailto:info@chisto-pro39.ru">info@chisto-pro39.ru</a></p>
                <p><i class="fas fa-clock"></i> Ежедневно 8:00–22:00</p>
            </div>
        </div>
        <div class="footer__bottom">
            <div class="container">
                <p>&copy; 2025 Чисто-про39. Все права защищены.</p>
                <p><a href="<?= route('privacy.php') ?>">Политика конфиденциальности</a></p>
            </div>
        </div>
    </footer>

    <!-- Подключаем скрипты -->
    <script src="<?= asset('js/script.js') ?>"></script>
</body>
</html>