<?php include 'includes/header.php'; ?>

<section class="page-hero page-hero--gallery">
    <div class="container">
        <h1>Наши работы</h1>
        <p>Реальные результаты — до и после уборки</p>
    </div>
</section>

<section class="gallery">
    <div class="container">

        <div class="gallery__filters">
            <button class="gallery__filter active" data-filter="all">Все работы</button>
            <button class="gallery__filter" data-filter="apartment">Квартиры</button>
            <button class="gallery__filter" data-filter="office">Офисы</button>
            <button class="gallery__filter" data-filter="furniture">Мебель</button>
            <button class="gallery__filter" data-filter="windows">Окна</button>
        </div>

        <div class="gallery__grid">

            <div class="gallery__item fade-in" data-category="apartment">
                <div class="before-after">
                    <div class="before-after__images">
                        <div class="before-after__img" style="background-image:url('<?= asset('images/before-1.jpg') ?>')">
                            <span class="before-after__label before-after__label--before">До</span>
                        </div>
                        <div class="before-after__img" style="background-image:url('<?= asset('images/after-1.jpg') ?>')">
                            <span class="before-after__label before-after__label--after">После</span>
                        </div>
                    </div>
                    <div class="before-after__info">
                        <h3>Генеральная уборка квартиры</h3>
                        <p>2-комнатная квартира, 54 м²</p>
                    </div>
                </div>
            </div>

            <div class="gallery__item fade-in" data-category="windows">
                <div class="before-after">
                    <div class="before-after__images">
                        <div class="before-after__img" style="background-image:url('<?= asset('images/before-2.jpg') ?>')">
                            <span class="before-after__label before-after__label--before">До</span>
                        </div>
                        <div class="before-after__img" style="background-image:url('<?= asset('images/after-2.jpg') ?>')">
                            <span class="before-after__label before-after__label--after">После</span>
                        </div>
                    </div>
                    <div class="before-after__info">
                        <h3>Мойка окон</h3>
                        <p>Частный дом, 12 створок</p>
                    </div>
                </div>
            </div>

            <div class="gallery__item fade-in" data-category="furniture">
                <div class="before-after">
                    <div class="before-after__images">
                        <div class="before-after__img" style="background-image:url('<?= asset('images/before-3.jpg') ?>')">
                            <span class="before-after__label before-after__label--before">До</span>
                        </div>
                        <div class="before-after__img" style="background-image:url('<?= asset('images/after-3.jpg') ?>')">
                            <span class="before-after__label before-after__label--after">После</span>
                        </div>
                    </div>
                    <div class="before-after__info">
                        <h3>Химчистка дивана</h3>
                        <p>Угловой диван, светлая обивка</p>
                    </div>
                </div>
            </div>

            <div class="gallery__item fade-in" data-category="office">
                <div class="before-after">
                    <div class="before-after__images">
                        <div class="before-after__img" style="background-image:url('<?= asset('images/renovation-cleaning.jpg') ?>')">
                            <span class="before-after__label before-after__label--before">До</span>
                        </div>
                        <div class="before-after__img" style="background-image:url('<?= asset('images/office-cleaning.jpg') ?>')">
                            <span class="before-after__label before-after__label--after">После</span>
                        </div>
                    </div>
                    <div class="before-after__info">
                        <h3>Уборка офиса после ремонта</h3>
                        <p>Офисное помещение, 120 м²</p>
                    </div>
                </div>
            </div>

            <div class="gallery__item fade-in" data-category="apartment">
                <div class="before-after">
                    <div class="before-after__images">
                        <div class="before-after__img" style="background-image:url('<?= asset('images/before-3.jpg') ?>')">
                            <span class="before-after__label before-after__label--before">До</span>
                        </div>
                        <div class="before-after__img" style="background-image:url('<?= asset('images/apartment-cleaning.jpg') ?>')">
                            <span class="before-after__label before-after__label--after">После</span>
                        </div>
                    </div>
                    <div class="before-after__info">
                        <h3>Уборка после ремонта</h3>
                        <p>1-комнатная квартира, 36 м²</p>
                    </div>
                </div>
            </div>

            <div class="gallery__item fade-in" data-category="furniture">
                <div class="before-after">
                    <div class="before-after__images">
                        <div class="before-after__img" style="background-image:url('<?= asset('images/before-1.jpg') ?>')">
                            <span class="before-after__label before-after__label--before">До</span>
                        </div>
                        <div class="before-after__img" style="background-image:url('<?= asset('images/furniture-cleaning.jpg') ?>')">
                            <span class="before-after__label before-after__label--after">После</span>
                        </div>
                    </div>
                    <div class="before-after__info">
                        <h3>Химчистка кресел</h3>
                        <p>Офисные кресла, 6 штук</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="gallery__cta">
            <p>Хотите такой же результат?</p>
            <a href="<?= route('index.php#callback') ?>" class="btn">Оставить заявку</a>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
