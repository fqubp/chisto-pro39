<?php include 'includes/header.php'; ?>

<section class="hero">
    <div class="container hero__container">
        <h1>Профессиональный клининг <br>в Калининграде</h1>
        <p>Уборка квартир, домов, офисов. Химчистка мебели, мойка окон.</p>
        <a href="#callback" class="btn">Оставить заявку</a>
    </div>
</section>

<section class="services-preview">
    <div class="container">
        <h2>Наши услуги</h2>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-card__image">[Фото: уборка квартиры]</div>
                <h3>Уборка квартир</h3>
                <p>от 90 руб/м²</p>
                <a href="/chisto-pro39/private/apartment.php" class="btn">Подробнее</a>
            </div>
            <div class="service-card">
                <div class="service-card__image">[Фото: химчистка]</div>
                <h3>Химчистка мебели</h3>
                <p>от 4500 руб</p>
                <a href="/chisto-pro39/private/cleaning.php" class="btn">Подробнее</a>
            </div>
            <div class="service-card">
                <div class="service-card__image">[Фото: мойка окон]</div>
                <h3>Мойка окон</h3>
                <p>от 500 руб</p>
                <a href="/chisto-pro39/private/windows.php" class="btn">Подробнее</a>
            </div>
        </div>
    </div>
</section>

<section class="about">
    <div class="container">
        <h2>Почему выбирают нас</h2>
        <div class="advantages-grid">
            <div class="advantage">
                <div class="advantage__icon">[Иконка: сертификат]</div>
                <h3>Работаем по ГОСТу</h3>
                <p>Соблюдаем стандарты качества и безопасности.</p>
            </div>
            <div class="advantage">
                <div class="advantage__icon">[Иконка: часы]</div>
                <h3>Быстро и в срок</h3>
                <p>Выезжаем в удобное для вас время.</p>
            </div>
            <div class="advantage">
                <div class="advantage__icon">[Иконка: эко]</div>
                <h3>Эко-средства</h3>
                <p>Используем безопасную химию.</p>
            </div>
        </div>
    </div>
</section>

<section class="callback" id="callback">
    <div class="container">
        <h2>Оставить заявку</h2>
        <form action="submit_request.php" method="post" enctype="multipart/form-data" class="callback-form">
            <div class="form-group">
                <label for="name">Ваше имя</label>
                <input type="text" id="name" name="name" placeholder="Имя">
            </div>
            <div class="form-group">
                <label for="phone">Телефон <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" placeholder="+7 (___) ___-__-__" required>
            </div>
            <div class="form-group">
                <label for="service_type">Тип услуги</label>
                <select id="service_type" name="service_type">
                    <option value="">Выберите услугу</option>
                    <option value="Уборка квартиры">Уборка квартиры</option>
                    <option value="Химчистка мебели">Химчистка мебели</option>
                    <option value="Мойка окон">Мойка окон</option>
                    <option value="Уборка офиса">Уборка офиса</option>
                </select>
            </div>
            <div class="form-group">
                <label for="message">Комментарий</label>
                <textarea id="message" name="message" rows="4" placeholder="Дополнительная информация"></textarea>
            </div>
            <div class="form-group">
                <label for="file">Прикрепить фото/видео (до 10 МБ)</label>
                <input type="file" id="file" name="file" accept=".jpg,.jpeg,.png,.mp4,.mov">
            </div>
            <!-- Скрытое поле для примерной стоимости (заполнится позже калькулятором) -->
            <input type="hidden" name="estimated_price" id="estimated_price" value="">
            
            <div class="form-group checkbox">
                <input type="checkbox" id="agree" name="agree" required>
                <label for="agree">Я согласен на обработку персональных данных в соответствии с <a href="/chisto-pro39/privacy.php" target="_blank">Политикой конфиденциальности</a></label>
            </div>
            
            <button type="submit" class="btn">Отправить заявку</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>