<?php include 'includes/header.php'; ?>

<section class="page-hero page-hero--contacts">
    <div class="container">
        <h1>Контакты</h1>
        <p>Свяжитесь с нами любым удобным способом — ответим за 15 минут</p>
    </div>
</section>

<section class="contacts">
    <div class="container">
        <div class="contacts__grid">

            <div class="contacts__info">
                <h2>Как с нами связаться</h2>

                <div class="contact-item">
                    <div class="contact-item__icon"><i class="fas fa-phone"></i></div>
                    <div>
                        <div class="contact-item__label">Телефон</div>
                        <a href="tel:+79222501266" class="contact-item__value">+7 (922) 250-12-66</a>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-item__icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="contact-item__label">Email</div>
                        <a href="mailto:chisto-pro39@bk.ru" class="contact-item__value">chisto-pro39@bk.ru</a>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-item__icon" style="background:#25D366"><i class="fab fa-whatsapp"></i></div>
                    <div>
                        <div class="contact-item__label">WhatsApp</div>
                        <a href="https://wa.me/79222501266" target="_blank" class="contact-item__value">Написать в WhatsApp</a>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-item__icon" style="background:#229ED9"><i class="fab fa-telegram"></i></div>
                    <div>
                        <div class="contact-item__label">Telegram</div>
                        <a href="https://t.me/chisto_pro39_bot" target="_blank" class="contact-item__value">@chisto_pro39_bot</a>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-item__icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="contact-item__label">Режим работы</div>
                        <div class="contact-item__value">Ежедневно 8:00–22:00</div>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-item__icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <div class="contact-item__label">Зона работы</div>
                        <div class="contact-item__value">Калининград и Калининградская область</div>
                    </div>
                </div>

                <!-- Яндекс.Карта -->
                <div class="contacts__map">
                    <iframe
                        src="https://yandex.ru/map-widget/v1/?ll=20.515734,54.707840&z=12&l=map&pt=20.515734,54.707840,pm2rdm"
                        width="100%"
                        height="280"
                        frameborder="0"
                        allowfullscreen
                        style="border-radius:12px;display:block;margin-top:20px">
                    </iframe>
                </div>
            </div>

            <div class="contacts__form">
                <h2>Напишите нам</h2>
                <form action="submit_request.php" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">
                    <div class="form-group">
                        <label>Ваше имя</label>
                        <input type="text" name="name" placeholder="Имя">
                    </div>
                    <div class="form-group">
                        <label>Телефон <span class="required">*</span></label>
                        <input type="tel" name="phone" id="phone-contacts" placeholder="+7 (___) ___-__-__" required>
                    </div>
                    <div class="form-group">
                        <label>Тип услуги</label>
                        <select name="service_type">
                            <option value="">Выберите услугу</option>
                            <option value="Уборка квартиры">Уборка квартиры</option>
                            <option value="Уборка дома">Уборка дома</option>
                            <option value="Химчистка мебели">Химчистка мебели</option>
                            <option value="Мойка окон">Мойка окон</option>
                            <option value="Уборка после ремонта">Уборка после ремонта</option>
                            <option value="Уборка офиса">Уборка офиса</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Сообщение</label>
                        <textarea name="message" rows="4" placeholder="Ваше сообщение или вопрос"></textarea>
                    </div>
                    <div class="form-group checkbox">
                        <input type="checkbox" id="agree-contacts" name="agree" required>
                        <label for="agree-contacts">Согласен на обработку <a href="privacy.php" target="_blank">персональных данных</a></label>
                    </div>
                    <button type="submit" class="btn" style="width:100%">Отправить сообщение</button>
                </form>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
