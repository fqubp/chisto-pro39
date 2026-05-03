<?php include '../includes/header.php'; ?>

<section class="service-page">
    <div class="container">
        <h1>Уборка домов и коттеджей</h1>
        <div class="service-page__content">
            <div class="service-page__image service-page__image--photo" style="background-image:url('<?= asset('images/apartment-cleaning.jpg') ?>')"></div>
            <div class="service-page__description">
                <p>Профессиональная уборка частных домов, коттеджей и дач в Калининграде и Калининградской области. Работаем с объектами любой площади — от небольших домиков до загородных коттеджей. Используем безопасные моющие средства и профессиональное оборудование.</p>
                <h3>Что входит:</h3>
                <ul>
                    <li>Влажная и сухая уборка всех комнат</li>
                    <li>Чистка кухни: плита, духовка, холодильник снаружи</li>
                    <li>Санитарная обработка санузлов и ванных комнат</li>
                    <li>Мытьё полов, плинтусов и подоконников</li>
                    <li>Удаление пыли со всех поверхностей</li>
                    <li>Мойка окон (по запросу)</li>
                    <li>Уборка веранды и прилегающей территории (опционально)</li>
                </ul>
                <p class="service-page__price">Стоимость: от 180 руб/м²</p>
                <a href="<?= route('index.php#callback') ?>" class="btn">Заказать</a>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>