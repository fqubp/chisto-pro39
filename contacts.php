<?php include 'includes/header.php'; ?>

<section class="contacts">
    <div class="container">
        <h1>Контакты</h1>
        <div class="contacts__grid">
            <div class="contacts__info">
                <h2>Свяжитесь с нами</h2>
                <p><i class="fas fa-phone"></i> <a href="tel:+74011234567">+7 (4012) 123-456</a></p>
                <p><i class="fas fa-envelope"></i> <a href="mailto:info@chisto-pro39.ru">info@chisto-pro39.ru</a></p>
                <p><i class="fab fa-whatsapp"></i> <a href="#" target="_blank">WhatsApp</a></p>
                <p><i class="fab fa-telegram"></i> <a href="#" target="_blank">Telegram</a></p>
                <p><i class="fas fa-clock"></i> Режим работы: ежедневно 8:00–22:00</p>
                <p><i class="fas fa-map-marker-alt"></i> Калининград (работаем по всему городу и области)</p>
            </div>
            <div class="contacts__form">
                <h2>Напишите нам</h2>
                <form action="submit_request.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Ваше имя">
                    </div>
                    <div class="form-group">
                        <input type="tel" name="phone" placeholder="Телефон*" required>
                    </div>
                    <div class="form-group">
                        <textarea name="message" rows="4" placeholder="Ваше сообщение"></textarea>
                    </div>
                    <div class="form-group checkbox">
                        <input type="checkbox" id="agree-contacts" name="agree" required>
                        <label for="agree-contacts">Согласие на обработку персональных данных</label>
                    </div>
                    <button type="submit" class="btn">Отправить</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>