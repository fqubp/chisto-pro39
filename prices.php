<?php include 'includes/header.php'; ?>

<section class="page-hero page-hero--prices">
    <div class="container">
        <h1>Цены на услуги</h1>
        <p>Прозрачные цены без скрытых доплат. Точная стоимость — после осмотра или по фотографиям.</p>
    </div>
</section>

<section class="prices">
    <div class="container">

        <div class="prices__tabs">
            <button class="prices__tab active" data-tab="private">Для частных лиц</button>
            <button class="prices__tab" data-tab="business">Для бизнеса</button>
        </div>

        <!-- Частные -->
        <div class="prices__panel" id="tab-private">
            <div class="prices__cards">

                <div class="price-card fade-in">
                    <div class="price-card__icon"><i class="fas fa-broom"></i></div>
                    <h3>Поддерживающая уборка</h3>
                    <div class="price-card__price">от 150 <span>руб/м²</span></div>
                    <ul class="price-card__list">
                        <li><i class="fas fa-check"></i> Пылесос и мытьё полов</li>
                        <li><i class="fas fa-check"></i> Уборка пыли с поверхностей</li>
                        <li><i class="fas fa-check"></i> Санузел и кухня</li>
                        <li><i class="fas fa-check"></i> Вынос мусора</li>
                    </ul>
                    <a href="<?= route('index.php#callback') ?>" class="btn">Заказать</a>
                </div>

                <div class="price-card price-card--featured fade-in">
                    <div class="price-card__badge">Популярное</div>
                    <div class="price-card__icon"><i class="fas fa-star"></i></div>
                    <h3>Генеральная уборка</h3>
                    <div class="price-card__price">от 200 <span>руб/м²</span></div>
                    <ul class="price-card__list">
                        <li><i class="fas fa-check"></i> Всё из поддерживающей</li>
                        <li><i class="fas fa-check"></i> Мытьё окон изнутри</li>
                        <li><i class="fas fa-check"></i> Холодильник и духовка</li>
                        <li><i class="fas fa-check"></i> Плинтуса и труднодоступные места</li>
                        <li><i class="fas fa-check"></i> Шкафы внутри</li>
                    </ul>
                    <a href="<?= route('index.php#callback') ?>" class="btn">Заказать</a>
                </div>

                <div class="price-card fade-in">
                    <div class="price-card__icon"><i class="fas fa-hard-hat"></i></div>
                    <h3>После ремонта</h3>
                    <div class="price-card__price">от 220 <span>руб/м²</span></div>
                    <ul class="price-card__list">
                        <li><i class="fas fa-check"></i> Удаление строительной пыли</li>
                        <li><i class="fas fa-check"></i> Очистка плитки и сантехники</li>
                        <li><i class="fas fa-check"></i> Отмывка окон от шпаклёвки</li>
                        <li><i class="fas fa-check"></i> Вынос строительного мусора</li>
                    </ul>
                    <a href="<?= route('index.php#callback') ?>" class="btn">Заказать</a>
                </div>

            </div>

            <div class="prices__extras">
                <h3>Дополнительные услуги</h3>
                <div class="prices__extras-grid">
                    <div class="prices__extra-item"><span>Химчистка мягкой мебели (диван)</span><span>от 4 500 руб</span></div>
                    <div class="prices__extra-item"><span>Мойка окон</span><span>от 600 руб/створка</span></div>
                    <div class="prices__extra-item"><span>Мытьё холодильника</span><span>750 руб</span></div>
                    <div class="prices__extra-item"><span>Чистка духовки</span><span>1 000 руб</span></div>
                    <div class="prices__extra-item"><span>Химчистка ковра</span><span>от 110 руб/м²</span></div>
                    <div class="prices__extra-item"><span>Глажка белья (1 час)</span><span>600 руб</span></div>
                </div>
            </div>
        </div>

        <!-- Бизнес -->
        <div class="prices__panel" id="tab-business" style="display:none">
            <div class="prices__cards">

                <div class="price-card fade-in">
                    <div class="price-card__icon"><i class="fas fa-building"></i></div>
                    <h3>Ежедневная уборка офиса</h3>
                    <div class="price-card__price">от 60 <span>руб/м²</span></div>
                    <ul class="price-card__list">
                        <li><i class="fas fa-check"></i> Пылесос и мытьё полов</li>
                        <li><i class="fas fa-check"></i> Протирка рабочих поверхностей</li>
                        <li><i class="fas fa-check"></i> Санузлы и кухня</li>
                        <li><i class="fas fa-check"></i> Вынос мусора</li>
                    </ul>
                    <a href="<?= route('index.php#callback') ?>" class="btn">Заказать</a>
                </div>

                <div class="price-card price-card--featured fade-in">
                    <div class="price-card__badge">Популярное</div>
                    <div class="price-card__icon"><i class="fas fa-hard-hat"></i></div>
                    <h3>После ремонта (офис)</h3>
                    <div class="price-card__price">от 150 <span>руб/м²</span></div>
                    <ul class="price-card__list">
                        <li><i class="fas fa-check"></i> Строительная пыль и мусор</li>
                        <li><i class="fas fa-check"></i> Очистка окон и витрин</li>
                        <li><i class="fas fa-check"></i> Полы и плинтуса</li>
                        <li><i class="fas fa-check"></i> Сантехника и кухня</li>
                        <li><i class="fas fa-check"></i> Полный фотоотчёт</li>
                    </ul>
                    <a href="<?= route('index.php#callback') ?>" class="btn">Заказать</a>
                </div>

                <div class="price-card fade-in">
                    <div class="price-card__icon"><i class="fas fa-store"></i></div>
                    <h3>Витрины и фасады</h3>
                    <div class="price-card__price">от 60 <span>руб/м²</span></div>
                    <ul class="price-card__list">
                        <li><i class="fas fa-check"></i> Мойка витринных стёкол</li>
                        <li><i class="fas fa-check"></i> Фасады зданий</li>
                        <li><i class="fas fa-check"></i> Профессиональное оборудование</li>
                        <li><i class="fas fa-check"></i> Работа на высоте</li>
                    </ul>
                    <a href="<?= route('index.php#callback') ?>" class="btn">Заказать</a>
                </div>

            </div>

            <div class="prices__extras">
                <h3>Дополнительные услуги для бизнеса</h3>
                <div class="prices__extras-grid">
                    <div class="prices__extra-item"><span>Химчистка ковровых покрытий</span><span>от 110 руб/м²</span></div>
                    <div class="prices__extra-item"><span>Мойка фасадов</span><span>от 60 руб/м²</span></div>
                    <div class="prices__extra-item"><span>Дезинфекция помещений</span><span>от 25 руб/м²</span></div>
                    <div class="prices__extra-item"><span>Уборка складских помещений</span><span>от 40 руб/м²</span></div>
                </div>
            </div>
        </div>

        <div class="prices__cta">
            <div class="prices__cta-inner">
                <div>
                    <h3>Не знаете сколько будет стоить?</h3>
                    <p>Воспользуйтесь калькулятором или оставьте заявку — мы рассчитаем бесплатно</p>
                </div>
                <div class="prices__cta-btns">
                    <a href="<?= route('calculator.php') ?>" class="btn btn--outline"><i class="fas fa-calculator"></i> Калькулятор</a>
                    <a href="<?= route('index.php#callback') ?>" class="btn">Оставить заявку</a>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
document.querySelectorAll('.prices__tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.prices__tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.prices__panel').forEach(p => p.style.display = 'none');
        tab.classList.add('active');
        document.getElementById('tab-' + tab.dataset.tab).style.display = 'block';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
