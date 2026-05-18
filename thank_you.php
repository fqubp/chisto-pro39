<?php
include 'includes/header.php';
$token = clean_input($_GET['token'] ?? '');
$show_tracking = $token && $token !== 'bot';
?>

<section class="thank-you">
    <div class="container">
        <div class="thank-you__card">
            <div class="thank-you__icon">✅</div>
            <h1>Заявка принята!</h1>
            <p>Мы свяжемся с вами в течение <strong>15 минут</strong> для уточнения деталей.</p>

            <?php if ($show_tracking): ?>
            <div class="thank-you__tracking">
                <p>🔎 Вы можете отслеживать статус вашей заявки:</p>
                <a href="<?= route('track.php') ?>?token=<?= urlencode($token) ?>" class="thank-you__track-btn">
                    Отслеживать заявку
                </a>
                <p class="thank-you__track-hint">Или введите номер телефона на <a href="<?= route('track.php') ?>">странице отслеживания</a></p>
            </div>
            <?php endif; ?>

            <p class="thank-you__sub">Если хотите ускорить — напишите нам напрямую:</p>
            <div class="thank-you__contacts">
                <a href="tel:+79222501266" class="thank-you__btn thank-you__btn--phone"><i class="fas fa-phone"></i> +7 (922) 250-12-66</a>
                <a href="https://vk.com/chisto_pro39" target="_blank" class="thank-you__btn thank-you__btn--vk"><i class="fab fa-vk"></i> ВКонтакте</a>
                <a href="https://t.me/chisto_pro39_bot" target="_blank" class="thank-you__btn thank-you__btn--tg"><i class="fab fa-telegram"></i> Telegram</a>
            </div>
            <a href="<?= route('index.php') ?>" class="btn btn--outline" style="margin-top:24px">← На главную</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
