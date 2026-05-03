<?php include 'includes/header.php'; ?>

<section class="page-hero page-hero--about">
    <div class="container">
        <h1>О компании</h1>
        <p>Профессиональный клининг в Калининграде с 2019 года</p>
    </div>
</section>

<section class="about-story">
    <div class="container">
        <div class="about-story__grid">
            <div class="about-story__text">
                <h2>Наша история</h2>
                <p>Компания Чисто-про39 основана в 2019 году в Калининграде. Мы начинали с небольшой команды из трёх человек и нескольких постоянных клиентов — сегодня нам доверяют уборку более 500 семей и десятки компаний города.</p>
                <p>Мы верим, что чистота дома и рабочего пространства напрямую влияет на качество жизни и продуктивность. Именно поэтому к каждому объекту подходим индивидуально: учитываем особенности помещения, пожелания хозяев и используем только безопасные средства.</p>
                <p>За 5 лет работы мы накопили опыт в уборке квартир, коттеджей, офисов, торговых центров и промышленных объектов. Работаем по ГОСТу, соблюдаем технику безопасности, даём гарантию на результат.</p>
            </div>
            <div class="about-story__image" style="background-image:url('<?= asset('images/apartment-cleaning.jpg') ?>')"></div>
        </div>
    </div>
</section>

<section class="about-stats">
    <div class="container">
        <div class="stats__grid">
            <div class="stat fade-in">
                <div class="stat__number">500+</div>
                <div class="stat__label">Довольных клиентов</div>
            </div>
            <div class="stat fade-in">
                <div class="stat__number">5</div>
                <div class="stat__label">Лет на рынке</div>
            </div>
            <div class="stat fade-in">
                <div class="stat__number">24/7</div>
                <div class="stat__label">Работаем без выходных</div>
            </div>
            <div class="stat fade-in">
                <div class="stat__number">100%</div>
                <div class="stat__label">Гарантия результата</div>
            </div>
        </div>
    </div>
</section>

<section class="about-team">
    <div class="container">
        <h2>Наша команда</h2>
        <p class="about-team__sub">Профессионалы с опытом и любовью к своему делу</p>
        <div class="team__grid">
            <div class="team-card fade-in">
                <div class="team-card__photo" style="background-image:url('<?= asset('images/team-1.jpg') ?>')"></div>
                <h3>Максим</h3>
                <p class="team-card__role">Руководитель</p>
                <p class="team-card__desc">Основатель компании. Отвечает за качество каждого выезда и работает с корпоративными клиентами.</p>
            </div>
            <div class="team-card fade-in">
                <div class="team-card__photo" style="background-image:url('<?= asset('images/team-2.jpg') ?>')"></div>
                <h3>Анна</h3>
                <p class="team-card__role">Старший специалист</p>
                <p class="team-card__desc">5 лет в клининге. Специализируется на генеральных уборках и химчистке мебели.</p>
            </div>
            <div class="team-card fade-in">
                <div class="team-card__photo" style="background-image:url('<?= asset('images/team-3.jpg') ?>')"></div>
                <h3>Елена</h3>
                <p class="team-card__role">Специалист по окнам</p>
                <p class="team-card__desc">Эксперт по мойке окон и фасадов. Работает на высоте с соблюдением техники безопасности.</p>
            </div>
        </div>
    </div>
</section>

<section class="about-values">
    <div class="container">
        <h2>Наши принципы</h2>
        <div class="values__grid">
            <div class="value-card fade-in">
                <div class="value-card__icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Безопасность</h3>
                <p>Используем только сертифицированные средства, безопасные для детей, животных и аллергиков.</p>
            </div>
            <div class="value-card fade-in">
                <div class="value-card__icon"><i class="fas fa-clock"></i></div>
                <h3>Пунктуальность</h3>
                <p>Приезжаем точно в назначенное время. Ценим ваше время так же, как своё.</p>
            </div>
            <div class="value-card fade-in">
                <div class="value-card__icon"><i class="fas fa-star"></i></div>
                <h3>Гарантия качества</h3>
                <p>Если результат вас не устроил — вернёмся и переделаем бесплатно.</p>
            </div>
            <div class="value-card fade-in">
                <div class="value-card__icon"><i class="fas fa-lock"></i></div>
                <h3>Надёжность</h3>
                <p>Все сотрудники проверены. Работаем официально, заключаем договор по запросу.</p>
            </div>
        </div>
    </div>
</section>

<section class="callback" id="callback">
    <div class="container">
        <h2>Закажите уборку прямо сейчас</h2>
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
            <div class="form-group checkbox">
                <input type="checkbox" id="agree" name="agree" required>
                <label for="agree">Я согласен на обработку персональных данных в соответствии с <a href="privacy.php" target="_blank">Политикой конфиденциальности</a></label>
            </div>
            <button type="submit" class="btn">Отправить заявку</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
