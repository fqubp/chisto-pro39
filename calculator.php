<?php include 'includes/header.php'; ?>

<section class="calculator-page">
    <div class="container">
        <h1>Калькулятор стоимости уборки</h1>
        <p class="calculator-page__intro">Рассчитайте примерную стоимость уборки онлайн. Точная цена зависит от многих факторов, но вы получите ориентир.</p>

        <div class="calculator">
            <div class="calculator__form">
                <div class="form-group">
                    <label for="calc-type">Тип уборки</label>
                    <select id="calc-type" class="calc-input">
                        <option value="150">Поддерживающая (150 руб/м²)</option>
                        <option value="250" selected>Генеральная (250 руб/м²)</option>
                        <option value="400">После ремонта (400 руб/м²)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="calc-area">Площадь, м²</label>
                    <input type="number" id="calc-area" class="calc-input" min="1" max="1000" value="50">
                </div>

                <div class="form-group">
                    <label for="calc-rooms">Количество комнат</label>
                    <input type="number" id="calc-rooms" class="calc-input" min="0" max="10" value="2">
                </div>

                <div class="form-group">
                    <label>Дополнительные услуги</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" id="calc-windows" class="calc-extra" data-price="1500"> Мытьё окон (+1 500 руб)</label>
                        <label><input type="checkbox" id="calc-fridge" class="calc-extra" data-price="750"> Мытьё холодильника (+750 руб)</label>
                        <label><input type="checkbox" id="calc-oven" class="calc-extra" data-price="1000"> Чистка духовки (+1 000 руб)</label>
                        <label><input type="checkbox" id="calc-chimney" class="calc-extra" data-price="2500"> Химчистка одного предмета мебели (+2 500 руб)</label>
                    </div>
                </div>

                <div class="calculator__result">
                    <strong>Примерная стоимость: <span id="calc-total">0</span> руб</strong>
                </div>
            </div>

            <div class="calculator__order">
                <h2>Отправить заявку с этой ценой</h2>
                <form action="submit_request.php" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">
                    <input type="hidden" name="estimated_price" id="order-price" value="">
                    
                    <div class="form-group">
                        <label for="order-name">Ваше имя</label>
                        <input type="text" id="order-name" name="name" placeholder="Имя">
                    </div>
                    <div class="form-group">
                        <label for="order-phone">Телефон <span class="required">*</span></label>
                        <input type="tel" id="order-phone" name="phone" placeholder="+7 (___) ___-__-__" required>
                    </div>
                    <div class="form-group">
                        <label for="order-type">Тип услуги</label>
                        <select id="order-type" name="service_type">
                            <option value="Уборка квартиры">Уборка квартиры</option>
                            <option value="Химчистка мебели">Химчистка мебели</option>
                            <option value="Мойка окон">Мойка окон</option>
                            <option value="Уборка офиса">Уборка офиса</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="order-message">Комментарий</label>
                        <textarea id="order-message" name="message" rows="3" placeholder="Дополнительная информация"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="order-file">Прикрепить фото/видео (до 10 МБ)</label>
                        <input type="file" id="order-file" name="file" accept=".jpg,.jpeg,.png,.mp4,.mov">
                    </div>
                    <div class="form-group checkbox">
                        <input type="checkbox" id="agree-calc" name="agree" required>
                        <label for="agree-calc">Я согласен на обработку персональных данных</label>
                    </div>
                    <button type="submit" class="btn">Отправить заявку</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>