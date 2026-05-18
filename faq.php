<?php include 'includes/header.php'; ?>

<section class="page-hero page-hero--faq">
    <div class="container">
        <h1>Частые вопросы</h1>
        <p>Отвечаем на самые популярные вопросы о наших услугах</p>
    </div>
</section>

<section class="faq">
    <div class="container">
        <div class="faq__list">

            <div class="faq__item">
                <button class="faq__question">
                    Как заказать уборку?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq__answer">
                    <p>Оставьте заявку на сайте, напишите нам в ВКонтакте или Telegram, или позвоните по номеру <a href="tel:+79222501266">+7 (922) 250-12-66</a>. Мы свяжемся с вами в течение 15 минут, уточним детали и согласуем удобное время.</p>
                </div>
            </div>

            <div class="faq__item">
                <button class="faq__question">
                    Сколько стоит уборка?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq__answer">
                    <p>Стоимость зависит от типа уборки, площади и дополнительных услуг. Поддерживающая уборка 1-комнатной квартиры — от 6 000 руб. Воспользуйтесь нашим <a href="<?= route('calculator.php') ?>">онлайн-калькулятором</a> для предварительного расчёта или позвоните нам.</p>
                </div>
            </div>

            <div class="faq__item">
                <button class="faq__question">
                    Нужно ли мне быть дома во время уборки?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq__answer">
                    <p>Нет, необязательно. Многие клиенты оставляют ключи или код от замка. Мы несём полную материальную ответственность за сохранность имущества. По завершении работ отправим вам фотоотчёт.</p>
                </div>
            </div>

            <div class="faq__item">
                <button class="faq__question">
                    Какие средства вы используете?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq__answer">
                    <p>Только профессиональные сертифицированные средства, безопасные для детей и домашних животных. Используем продукцию Karcher, Grass, Pro-Brite. Если у вас есть аллергия или особые пожелания — сообщите заранее, подберём подходящий состав.</p>
                </div>
            </div>

            <div class="faq__item">
                <button class="faq__question">
                    Сколько времени занимает уборка?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq__answer">
                    <p>Поддерживающая уборка 1-комнатной квартиры — 1,5–2 часа. Генеральная уборка — 3–5 часов в зависимости от площади и состояния квартиры. Уборка после ремонта — от 4 часов. Точное время сообщим при согласовании.</p>
                </div>
            </div>

            <div class="faq__item">
                <button class="faq__question">
                    Работаете ли вы в выходные и праздники?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq__answer">
                    <p>Да, работаем ежедневно с 8:00 до 22:00, без выходных и праздников. Выезд в удобное для вас время.</p>
                </div>
            </div>

            <div class="faq__item">
                <button class="faq__question">
                    Как подготовить квартиру к уборке?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq__answer">
                    <p>Особой подготовки не требуется. Достаточно убрать личные ценные вещи и документы в укромное место. Крупный мусор можно вынести заранее, но это не обязательно — мы справимся сами.</p>
                </div>
            </div>

            <div class="faq__item">
                <button class="faq__question">
                    Что если результат меня не устроит?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq__answer">
                    <p>Мы даём гарантию на все виды работ. Если вы обнаружили недочёты — сообщите нам в течение 24 часов, и мы вернёмся и устраним их бесплатно. Ваша удовлетворённость — наш приоритет.</p>
                </div>
            </div>

            <div class="faq__item">
                <button class="faq__question">
                    Вы работаете с юридическими лицами?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq__answer">
                    <p>Да, работаем с организациями по договору с закрывающими документами (акт, счёт, счёт-фактура). Предоставляем все необходимые документы для бухгалтерии. Звоните: <a href="tel:+79222501266">+7 (922) 250-12-66</a>.</p>
                </div>
            </div>

            <div class="faq__item">
                <button class="faq__question">
                    Вы привозите своё оборудование и средства?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq__answer">
                    <p>Да, полностью. Наши специалисты приезжают со всем необходимым оборудованием и средствами. Вам не нужно ничего готовить или покупать.</p>
                </div>
            </div>

        </div>

        <div class="faq__cta">
            <h3>Не нашли ответ на свой вопрос?</h3>
            <p>Напишите нам — ответим в течение 15 минут</p>
            <div class="faq__cta-btns">
                <a href="https://vk.com/chisto_pro39" class="btn" target="_blank"><i class="fab fa-vk"></i> ВКонтакте</a>
                <a href="https://t.me/chisto_pro39_bot" class="btn btn--outline" target="_blank"><i class="fab fa-telegram"></i> Telegram</a>
            </div>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
