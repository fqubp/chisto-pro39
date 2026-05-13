<?php http_response_code(404); include 'includes/header.php'; ?>

<section class="not-found">
    <div class="container">
        <div class="not-found__card">
            <div class="not-found__code">404</div>
            <h1>Страница не найдена</h1>
            <p>Возможно, страница была удалена или вы перешли по неверной ссылке.</p>
            <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;margin-top:32px">
                <a href="<?= route('index.php') ?>" class="btn">На главную</a>
                <a href="<?= route('services.php') ?>" class="btn btn--outline">Наши услуги</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
