<?php include 'includes/header.php'; ?>

<section class="contacts">
    <div class="container">
        <h1>Контакты</h1>
        <div class="contacts__grid">
            <div class="contacts__info">
                <h2>Свяжитесь с нами</h2>
                <p><i class="fas fa-phone"></i> <a href="tel:+79222501266">+7 (922) 250-12-66</a></p>
                <p><i class="fas fa-envelope"></i> <a href="mailto:chisto-pro39@bk.ru">chisto-pro39@bk.ru</a></p>
                <p><i class="fab fa-whatsapp"></i> <a href="https://wa.me/79222501266" target="_blank">WhatsApp</a></p>
                <p><i class="fab fa-telegram"></i> <a href="https://t.me/chisto_pro39_bot" target="_blank">Telegram</a></p>
                <p><i class="fas fa-clock"></i> Режим работы: ежедневно 8:00–22:00</p>
                <p><i class="fas fa-map-marker-alt"></i> Калининград (работаем по всему городу и области)</p>
            </div>
            <div class="contacts__form">
                <h2>Напишите нам</h2>
                <form action="submit_request.php" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">
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