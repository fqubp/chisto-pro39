<?php include '../includes/header.php'; ?>

<section class="catalog">
    <div class="container">
        <h1>Услуги для частных лиц</h1>
        <p>Мы предлагаем профессиональный клининг для квартир, домов и дач. Все работы выполняются с учётом ваших пожеланий.</p>

        <div class="services-grid">
            <div class="service-card fade-in">
                <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/apartment-cleaning.jpg') ?>')"></div>
                <h3>Уборка квартир</h3>
                <p class="price">от 150 руб/м²</p>
                <a href="apartment.php" class="btn">Подробнее</a>
            </div>
            <div class="service-card fade-in">
                <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/apartment-cleaning.jpg') ?>')"></div>
                <h3>Уборка домов и коттеджей</h3>
                <p class="price">от 180 руб/м²</p>
                <a href="house.php" class="btn">Подробнее</a>
            </div>
            <div class="service-card fade-in">
                <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/furniture-cleaning.jpg') ?>')"></div>
                <h3>Химчистка мебели</h3>
                <p class="price">от 4 500 руб</p>
                <a href="cleaning.php" class="btn">Подробнее</a>
            </div>
            <div class="service-card fade-in">
                <div class="service-card__image service-card__image--photo" style="background-image:url('<?= asset('images/window-cleaning.jpg') ?>')"></div>
                <h3>Мойка окон</h3>
                <p class="price">от 600 руб/створка</p>
                <a href="windows.php" class="btn">Подробнее</a>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
