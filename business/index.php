<?php include '../includes/header.php'; ?>

<section class="catalog">
    <div class="container">
        <h1>Услуги для бизнеса</h1>
        <p>Комплексное обслуживание офисов, торговых центров, складов и производственных помещений. Работаем с юридическими лицами по договору.</p>

        <div class="services-grid">
            <div class="service-card fade-in">
                <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/office-cleaning.jpg') ?>')"></div>
                <h3>Уборка офисов</h3>
                <p class="price">от 60 руб/м²</p>
                <a href="office.php" class="btn">Подробнее</a>
            </div>
            <div class="service-card fade-in">
                <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/renovation-cleaning.jpg') ?>')"></div>
                <h3>Уборка после ремонта</h3>
                <p class="price">от 150 руб/м²</p>
                <a href="after-renovation.php" class="btn">Подробнее</a>
            </div>
            <div class="service-card fade-in">
                <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/carpet-cleaning.jpg') ?>')"></div>
                <h3>Химчистка ковровых покрытий</h3>
                <p class="price">от 110 руб/м²</p>
                <a href="cleaning.php" class="btn">Подробнее</a>
            </div>
            <div class="service-card fade-in">
                <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/facade-cleaning.jpg') ?>')"></div>
                <h3>Мойка фасадов и витрин</h3>
                <p class="price">от 90 руб/м²</p>
                <a href="facades.php" class="btn">Подробнее</a>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
