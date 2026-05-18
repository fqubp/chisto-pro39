<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Отзывы из БД (последние 3 опубликованных)
$reviews = [];
$res = $conn->query("SELECT * FROM reviews WHERE is_published = 1 ORDER BY created_at DESC LIMIT 3");
if ($res) {
    while ($row = $res->fetch_assoc()) $reviews[] = $row;
}
// Если в БД ещё нет отзывов — показываем заглушки
$fallback_reviews = [
    ['stars' => 5, 'text' => 'Заказывала генеральную уборку двухкомнатной квартиры. Ребята приехали вовремя, работали аккуратно и профессионально. Результат превзошёл ожидания — даже плинтуса блестят!', 'author' => 'Анна К., Калининград', 'service_type' => ''],
    ['stars' => 5, 'text' => 'Вызвали для уборки после ремонта в офисе. Строительная пыль, краска на плитке — всё убрали за один день. Работаем теперь только с Чисто-про39.', 'author' => 'Дмитрий Л., ООО "СтройКом"', 'service_type' => ''],
    ['stars' => 5, 'text' => 'Химчистка дивана — результат отличный, вывели пятно, которое я считала вечным. Цена честная, приехали на следующий день после звонка.', 'author' => 'Марина П., Калининград', 'service_type' => ''],
];

$error_message = '';
if (!empty($_GET['error'])) {
    $error_message = clean_input(urldecode($_GET['error']));
}
$conn->close();
include 'includes/header.php';
?>

<?php if ($error_message): ?>
    <div class="notification notification--error"><?= $error_message ?></div>
<?php endif; ?>

<section class="hero">
    <div class="container hero__container">
        <h1>Профессиональный клининг <br>в Калининграде</h1>
        <p>Уборка квартир, домов, офисов. Химчистка мебели, мойка окон.</p>
        <div class="hero__actions">
            <a href="#callback" class="btn">Оставить заявку</a>
            <a href="calculator.php" class="btn btn--outline"><i class="fas fa-calculator"></i> Рассчитать цену</a>
        </div>
    </div>
</section>

<section class="stats">
    <div class="container">
        <div class="stats__grid">
            <div class="stat fade-in"><div class="stat__number">500+</div><div class="stat__label">Довольных клиентов</div></div>
            <div class="stat fade-in"><div class="stat__number">5</div><div class="stat__label">Лет на рынке</div></div>
            <div class="stat fade-in"><div class="stat__number">24/7</div><div class="stat__label">Работаем без выходных</div></div>
            <div class="stat fade-in"><div class="stat__number">100%</div><div class="stat__label">Гарантия результата</div></div>
        </div>
    </div>
</section>

<section class="services-preview">
    <div class="container">
        <h2>Наши услуги</h2>
        <div class="services-grid">
            <div class="service-card fade-in">
                <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/apartment-cleaning.jpg') ?>')"></div>
                <h3>Уборка квартир</h3>
                <p class="price">от 150 руб/м²</p>
                <a href="private/apartment.php" class="btn">Подробнее</a>
            </div>
            <div class="service-card fade-in">
                <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/furniture-cleaning.jpg') ?>')"></div>
                <h3>Химчистка мебели</h3>
                <p class="price">от 4 500 руб</p>
                <a href="private/cleaning.php" class="btn">Подробнее</a>
            </div>
            <div class="service-card fade-in">
                <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/window-cleaning.jpg') ?>')"></div>
                <h3>Мойка окон</h3>
                <p class="price">от 600 руб/створка</p>
                <a href="private/windows.php" class="btn">Подробнее</a>
            </div>
        </div>
    </div>
</section>

<section class="about">
    <div class="container">
        <h2>Почему выбирают нас</h2>
        <div class="advantages-grid">
            <div class="advantage fade-in">
                <div class="advantage__icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Страхование ответственности</h3>
                <p>Несём материальную ответственность за сохранность имущества. При повреждении — компенсируем ущерб без лишних споров.</p>
            </div>
            <div class="advantage fade-in">
                <div class="advantage__icon"><i class="fas fa-leaf"></i></div>
                <h3>Профессиональная химия</h3>
                <p>Используем сертифицированные средства Grass, Henkel и Diversey. Безопасны для детей, животных и аллергиков. Без резкого запаха.</p>
            </div>
            <div class="advantage fade-in">
                <div class="advantage__icon"><i class="fas fa-clock"></i></div>
                <h3>Точно в срок</h3>
                <p>Приезжаем в согласованное время ±15 минут. Если опаздываем — предупреждаем заранее. Работаем 7 дней в неделю с 8:00 до 22:00.</p>
            </div>
            <div class="advantage fade-in">
                <div class="advantage__icon"><i class="fas fa-redo"></i></div>
                <h3>Гарантия на работы</h3>
                <p>Если после уборки остались недочёты — вернёмся и устраним бесплатно в течение 24 часов. Без условий и лишних вопросов.</p>
            </div>
            <div class="advantage fade-in">
                <div class="advantage__icon"><i class="fas fa-users"></i></div>
                <h3>Проверенные сотрудники</h3>
                <p>Все клинеры прошли личное собеседование, инструктаж и стажировку. Работаем официально — договор, чек, закрывающие документы.</p>
            </div>
            <div class="advantage fade-in">
                <div class="advantage__icon"><i class="fas fa-tools"></i></div>
                <h3>Профессиональное оборудование</h3>
                <p>Промышленные пылесосы Karcher, пароочистители и полотёры. Приезжаем полностью оснащёнными — вам не нужно ничего готовить.</p>
            </div>
        </div>
    </div>
</section>

<section class="how-we-work">
    <div class="container">
        <h2>Как мы работаем</h2>
        <div class="steps__grid">
            <div class="step fade-in">
                <div class="step__num">1</div>
                <h3>Оставьте заявку</h3>
                <p>Заполните форму на сайте, напишите в ВКонтакте или позвоните — ответим за 15 минут.</p>
            </div>
            <div class="step fade-in">
                <div class="step__num">2</div>
                <h3>Согласуем детали</h3>
                <p>Уточним объём работ, площадь, пожелания и назначим удобное для вас время.</p>
            </div>
            <div class="step fade-in">
                <div class="step__num">3</div>
                <h3>Выполним уборку</h3>
                <p>Приедем со всем оборудованием и профессиональными средствами. Работаем аккуратно и в срок.</p>
            </div>
            <div class="step fade-in">
                <div class="step__num">4</div>
                <h3>Примете результат</h3>
                <p>Проверите работу и оплатите после того, как убедитесь в качестве. Гарантия на все услуги.</p>
            </div>
        </div>
    </div>
</section>

<section class="reviews">
    <div class="container">
        <h2>Отзывы клиентов</h2>
        <div class="reviews__grid">
            <?php $display_reviews = !empty($reviews) ? $reviews : $fallback_reviews; ?>
            <?php foreach ($display_reviews as $r): ?>
            <div class="review-card fade-in">
                <div class="review-card__stars">
                    <?php $stars = isset($r['rating']) ? (int)$r['rating'] : $r['stars']; ?>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star<?= $i > $stars ? '-half-alt' : '' ?>"<?= $i > $stars + 0.5 ? ' style="opacity:.3"' : '' ?>></i>
                    <?php endfor; ?>
                </div>
                <p class="review-card__text">«<?= htmlspecialchars(isset($r['review_text']) ? $r['review_text'] : $r['text']) ?>»</p>
                <div class="review-card__author"><?= htmlspecialchars(isset($r['author_name']) ? $r['author_name'] : $r['author']) ?></div>
                <?php if (!empty($r['service_type'])): ?>
                    <div class="review-card__service" style="font-size:12px;color:#aaa;margin-top:4px"><?= htmlspecialchars($r['service_type']) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:32px">
            <a href="<?= route('reviews.php') ?>" class="btn btn--outline">Все отзывы</a>
        </div>
    </div>
</section>

<section class="callback" id="callback">
    <div class="container">
        <h2>Оставить заявку</h2>
        <form action="submit_request.php" method="post" enctype="multipart/form-data" class="callback-form">
            <?= csrf_field() ?>
            <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">
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
                    <option value="Уборка дома">Уборка дома</option>
                    <option value="Химчистка мебели">Химчистка мебели</option>
                    <option value="Мойка окон">Мойка окон</option>
                    <option value="Уборка после ремонта">Уборка после ремонта</option>
                    <option value="Уборка офиса">Уборка офиса</option>
                </select>
            </div>
            <div class="form-group">
                <label for="message">Комментарий</label>
                <textarea id="message" name="message" rows="4" placeholder="Дополнительная информация"></textarea>
            </div>
            <div class="form-group">
                <label for="file">Прикрепить фото/видео (до 5 файлов, общий размер до 30 МБ)</label>
                <input type="file" id="file" name="file[]" accept=".jpg,.jpeg,.png,.mp4,.mov" multiple>
            </div>
            <input type="hidden" name="estimated_price" id="estimated_price" value="">
            <div class="form-group checkbox">
                <input type="checkbox" id="agree" name="agree" required>
                <label for="agree">Я согласен на обработку персональных данных в соответствии с <a href="privacy.php" target="_blank">Политикой конфиденциальности</a></label>
            </div>
            <button type="submit" class="btn">Отправить заявку</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
