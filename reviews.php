<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Получаем опубликованные отзывы
$reviews = [];
$result = $conn->query("SELECT * FROM reviews WHERE is_published = 1 ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}

// Считаем статистику
$stats = ['total' => 0, 'avg' => 0];
$stat_result = $conn->query("SELECT COUNT(*) as total, ROUND(AVG(rating),1) as avg FROM reviews WHERE is_published = 1");
if ($stat_result && $row = $stat_result->fetch_assoc()) {
    $stats = $row;
}

$conn->close();

include 'includes/header.php';
?>

<section class="page-hero page-hero--reviews">
    <div class="container">
        <h1>Отзывы клиентов</h1>
        <p>Более <?= max((int)$stats['total'], 1) ?> отзывов — мнения реальных людей о нашей работе</p>
    </div>
</section>

<section class="reviews-page">
    <div class="container">

        <?php if (!empty($stats['total'])): ?>
        <div class="reviews-summary">
            <div class="reviews-summary__score">
                <span class="reviews-summary__number"><?= $stats['avg'] ?></span>
                <div class="reviews-summary__stars">
                    <?php
                    $avg = round((float)$stats['avg']);
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $avg ? '★' : '☆';
                    }
                    ?>
                </div>
                <span class="reviews-summary__count"><?= (int)$stats['total'] ?> отзывов</span>
            </div>
        </div>
        <?php endif; ?>

        <?php if (empty($reviews)): ?>
            <div class="reviews-empty">
                <p>Пока отзывов нет — будьте первым!</p>
            </div>
        <?php else: ?>
        <div class="reviews__grid reviews__grid--page">
            <?php foreach ($reviews as $review): ?>
            <div class="review-card fade-in">
                <div class="review-card__stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?= $i <= (int)$review['rating'] ? '★' : '☆' ?>
                    <?php endfor; ?>
                </div>
                <p class="review-card__text">"<?= htmlspecialchars($review['review_text']) ?>"</p>
                <div class="review-card__footer">
                    <span class="review-card__author"><?= htmlspecialchars($review['author_name']) ?></span>
                    <?php if ($review['service_type']): ?>
                        <span class="review-card__service"><?= htmlspecialchars($review['service_type']) ?></span>
                    <?php endif; ?>
                    <span class="review-card__date"><?= date('d.m.Y', strtotime($review['created_at'])) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="reviews-cta">
            <p>Пользовались нашими услугами?</p>
            <a href="<?= route('index.php#callback') ?>" class="btn">Оставить заявку снова</a>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
