<?php include 'includes/header.php'; ?>

<section class="catalog">
    <div class="container">
        <h1>Наши услуги</h1>
        <p>Мы предоставляем полный спектр клининговых услуг для частных лиц и организаций в Калининграде и области.</p>

        <div class="catalog__section">
            <h2>Для частных лиц</h2>
            <div class="services-grid">
                <div class="service-card fade-in">
                    <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/apartment-cleaning.jpg') ?>')"></div>
                    <h3>Уборка квартир</h3>
                    <p class="price">от 150 руб/м²</p>
                    <a href="private/apartment.php" class="btn">Подробнее</a>
                </div>
                <div class="service-card fade-in">
                    <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/renovation-cleaning.jpg') ?>')"></div>
                    <h3>Уборка домов и коттеджей</h3>
                    <p class="price">от 180 руб/м²</p>
                    <a href="private/house.php" class="btn">Подробнее</a>
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

        <div class="catalog__section">
            <h2>Для бизнеса</h2>
            <div class="services-grid">
                <div class="service-card fade-in">
                    <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/office-cleaning.jpg') ?>')"></div>
                    <h3>Уборка офисов</h3>
                    <p class="price">от 60 руб/м²</p>
                    <a href="business/office.php" class="btn">Подробнее</a>
                </div>
                <div class="service-card fade-in">
                    <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/renovation-cleaning.jpg') ?>')"></div>
                    <h3>Уборка после ремонта</h3>
                    <p class="price">от 150 руб/м²</p>
                    <a href="business/after-renovation.php" class="btn">Подробнее</a>
                </div>
                <div class="service-card fade-in">
                    <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/carpet-cleaning.jpg') ?>')"></div>
                    <h3>Химчистка ковровых покрытий</h3>
                    <p class="price">от 110 руб/м²</p>
                    <a href="business/cleaning.php" class="btn">Подробнее</a>
                </div>
                <div class="service-card fade-in">
                    <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/facade-cleaning.jpg') ?>')"></div>
                    <h3>Мойка фасадов и витрин</h3>
                    <p class="price">от 90 руб/м²</p>
                    <a href="business/facades.php" class="btn">Подробнее</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
