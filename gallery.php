<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$category_filter = $_GET['cat'] ?? 'all';
$categories = [
    'all'         => 'Все работы',
    'apartment'   => 'Квартиры',
    'office'      => 'Офисы',
    'furniture'   => 'Мебель',
    'windows'     => 'Окна',
    'renovation'  => 'После ремонта',
    'other'       => 'Другое',
];

$items = [];
$where = "WHERE is_published = 1";
$params = [];
$types = '';

if ($category_filter !== 'all' && array_key_exists($category_filter, $categories)) {
    $where .= " AND category = ?";
    $params[] = $category_filter;
    $types = 's';
}

$sql = "SELECT * FROM gallery_items $where ORDER BY sort_order ASC, created_at DESC";

if ($params) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

$counts = ['all' => 0];
$count_result = $conn->query("SELECT category, COUNT(*) as cnt FROM gallery_items WHERE is_published = 1 GROUP BY category");
if ($count_result) {
    while ($row = $count_result->fetch_assoc()) {
        $counts[$row['category']] = (int)$row['cnt'];
        $counts['all'] = ($counts['all'] ?? 0) + (int)$row['cnt'];
    }
}

$conn->close();
include 'includes/header.php';
?>

<section class="page-hero page-hero--gallery">
    <div class="container">
        <h1>Наши работы</h1>
        <p>Реальные результаты — до и после уборки</p>
    </div>
</section>

<section class="gallery">
    <div class="container">

        <div class="gallery__filters">
            <?php foreach ($categories as $key => $label): ?>
                <?php if ($key === 'all' || !empty($counts[$key])): ?>
                <a href="gallery.php<?= $key !== 'all' ? '?cat=' . $key : '' ?>"
                   class="gallery__filter <?= $category_filter === $key ? 'active' : '' ?>">
                    <?= $label ?>
                    <?php if (isset($counts[$key])): ?>
                        <span class="gallery__filter-count"><?= $counts[$key] ?></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if (empty($items)): ?>
            <div class="gallery-empty">
                <div class="gallery-empty__icon">🧹</div>
                <p>В этой категории пока нет работ.</p>
                <a href="gallery.php" class="btn btn--outline">Посмотреть все</a>
            </div>
        <?php else: ?>
        <div class="gallery__grid gallery__grid--new">
            <?php foreach ($items as $item): ?>
            <div class="gallery__item fade-in">
                <div class="before-after">
                    <div class="before-after__slider">
                        <div class="before-after__side before-after__side--before">
                            <img src="<?= route(htmlspecialchars($item['before_image'])) ?>" alt="До — <?= htmlspecialchars($item['title']) ?>" loading="lazy">
                            <span class="before-after__label before-after__label--before">До</span>
                        </div>
                        <div class="before-after__side before-after__side--after">
                            <img src="<?= route(htmlspecialchars($item['after_image'])) ?>" alt="После — <?= htmlspecialchars($item['title']) ?>" loading="lazy">
                            <span class="before-after__label before-after__label--after">После</span>
                        </div>
                        <div class="before-after__divider">
                            <div class="before-after__handle">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M8 5l-6 7 6 7M16 5l6 7-6 7" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="before-after__info">
                        <h3><?= htmlspecialchars($item['title']) ?></h3>
                        <?php if ($item['description']): ?>
                            <p><?= htmlspecialchars($item['description']) ?></p>
                        <?php endif; ?>
                        <span class="before-after__category-badge">
                            <?= $categories[$item['category']] ?? htmlspecialchars($item['category']) ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="gallery__cta">
            <p>Хотите такой же результат?</p>
            <a href="<?= route('index.php#callback') ?>" class="btn">Оставить заявку</a>
        </div>

    </div>
</section>

<script>
document.querySelectorAll('.before-after__slider').forEach(slider => {
    const divider = slider.querySelector('.before-after__divider');
    const afterSide = slider.querySelector('.before-after__side--after');
    let dragging = false;
    let currentPercent = 50;

    function setPosition(percent) {
        percent = Math.max(5, Math.min(95, percent));
        currentPercent = percent;
        afterSide.style.clipPath = 'inset(0 0 0 ' + percent + '%)';
        divider.style.left = percent + '%';
    }

    setPosition(50);

    function getPercent(e) {
        const rect = slider.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        return ((clientX - rect.left) / rect.width) * 100;
    }

    divider.addEventListener('mousedown', e => { dragging = true; e.preventDefault(); });
    divider.addEventListener('touchstart', () => { dragging = true; }, { passive: true });
    document.addEventListener('mousemove', e => { if (dragging) setPosition(getPercent(e)); });
    document.addEventListener('touchmove', e => { if (dragging) setPosition(getPercent(e)); }, { passive: true });
    document.addEventListener('mouseup', () => { dragging = false; });
    document.addEventListener('touchend', () => { dragging = false; });
    slider.addEventListener('click', e => {
        if (!e.target.closest('.before-after__handle')) setPosition(getPercent(e));
    });
});
</script>

<?php include 'includes/footer.php'; ?>
