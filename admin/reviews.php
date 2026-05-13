<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php'); exit;
}

require_once '../includes/db.php';
require_once '../includes/functions.php';

$msg = '';
$msg_type = '';

// --- Добавление нового отзыва ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $author = trim($_POST['author_name'] ?? '');
    $service = trim($_POST['service_type'] ?? '');
    $rating = (int)($_POST['rating'] ?? 5);
    $text = trim($_POST['review_text'] ?? '');
    $published = isset($_POST['is_published']) ? 1 : 0;

    if ($author && $text && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO reviews (author_name, service_type, rating, review_text, is_published) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisi", $author, $service, $rating, $text, $published);
        $stmt->execute();
        $stmt->close();
        $msg = 'Отзыв добавлен.';
        $msg_type = 'success';
    } else {
        $msg = 'Заполните имя и текст отзыва.';
        $msg_type = 'error';
    }
}

// --- Переключение публикации ---
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("UPDATE reviews SET is_published = 1 - is_published WHERE id = $id");
    header('Location: reviews.php'); exit;
}

// --- Удаление ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->prepare("DELETE FROM reviews WHERE id = ?")->execute() || null;
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: reviews.php'); exit;
}

// --- Редактирование (сохранение) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = intval($_POST['id'] ?? 0);
    $author = trim($_POST['author_name'] ?? '');
    $service = trim($_POST['service_type'] ?? '');
    $rating = (int)($_POST['rating'] ?? 5);
    $text = trim($_POST['review_text'] ?? '');
    $published = isset($_POST['is_published']) ? 1 : 0;

    if ($id && $author && $text) {
        $stmt = $conn->prepare("UPDATE reviews SET author_name=?, service_type=?, rating=?, review_text=?, is_published=? WHERE id=?");
        $stmt->bind_param("ssisii", $author, $service, $rating, $text, $published, $id);
        $stmt->execute();
        $stmt->close();
        $msg = 'Отзыв обновлён.';
        $msg_type = 'success';
    }
}

// --- Получить список ---
$reviews = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$edit_review = null;
if (isset($_GET['edit'])) {
    $eid = intval($_GET['edit']);
    foreach ($reviews as $r) {
        if ($r['id'] === $eid) { $edit_review = $r; break; }
    }
}
$conn->close();

$services = ['Генеральная уборка', 'Поддерживающая уборка', 'Химчистка мебели', 'Мойка окон', 'Уборка после ремонта', 'Уборка офиса', 'Другое'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ — Отзывы</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #f4f7fb; }
        .admin-wrap { max-width: 1200px; margin: 0 auto; padding: 32px 20px; }
        .admin-topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:12px; }
        .admin-topbar h1 { color: var(--navy); font-size:24px; }
        .admin-topbar-links { display:flex; gap:12px; align-items:center; }
        .admin-topbar-links a { font-size:14px; color: var(--teal-dark,#00695c); text-decoration:none; }
        .admin-topbar-links a:hover { text-decoration:underline; }
        .msg { padding:12px 20px; border-radius:var(--radius); margin-bottom:20px; font-size:14px; font-weight:500; }
        .msg--success { background:#d1fae5; color:#065f46; }
        .msg--error { background:#fee2e2; color:#991b1b; }
        .card { background:white; border-radius:var(--radius-lg); box-shadow:0 2px 12px rgba(0,0,0,.08); padding:28px; margin-bottom:28px; }
        .card h2 { font-size:18px; color:var(--navy); margin-bottom:20px; font-weight:700; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
        .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
        .form-group label { font-size:13px; font-weight:600; color:var(--navy); }
        .form-group input, .form-group select, .form-group textarea {
            border:1px solid #cdd5e0; border-radius:var(--radius); padding:9px 14px;
            font-size:14px; font-family:inherit; width:100%; box-sizing:border-box;
            transition:border-color 0.2s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline:none; border-color:var(--teal);
        }
        .form-group textarea { min-height:100px; resize:vertical; }
        .stars-input { display:flex; gap:6px; }
        .stars-input input[type=radio] { display:none; }
        .stars-input label {
            font-size:28px; color:#d1d5db; cursor:pointer; transition:color 0.15s;
            line-height:1;
        }
        .stars-input input[type=radio]:checked ~ label,
        .stars-input label:hover,
        .stars-input label:hover ~ label { color:#f59e0b !important; }
        .stars-input { flex-direction:row-reverse; }
        .stars-input label:hover, .stars-input label:hover ~ label { color:#f59e0b; }
        .form-check { display:flex; align-items:center; gap:8px; font-size:14px; color:var(--navy); cursor:pointer; }
        .form-check input { width:16px; height:16px; cursor:pointer; }
        .btn-row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
        .reviews-table { width:100%; border-collapse:collapse; }
        .reviews-table th, .reviews-table td { padding:10px 14px; border-bottom:1px solid #eef0f5; font-size:13px; text-align:left; }
        .reviews-table th { background:var(--navy); color:white; font-weight:600; white-space:nowrap; }
        .reviews-table tr:last-child td { border-bottom:none; }
        .reviews-table tr:hover td { background:#f8fafc; }
        .stars-display { color:#f59e0b; letter-spacing:2px; }
        .badge { display:inline-block; padding:3px 9px; border-radius:12px; font-size:12px; font-weight:600; }
        .badge--pub { background:#d1fae5; color:#065f46; }
        .badge--hidden { background:#f3f4f6; color:#6b7280; }
        .actions a { font-size:16px; text-decoration:none; margin-right:6px; opacity:.8; }
        .actions a:hover { opacity:1; }
        .table-wrap { overflow-x:auto; }
        @media(max-width:640px) { .form-row { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<div class="admin-wrap">
    <div class="admin-topbar">
        <h1>Управление отзывами</h1>
        <div class="admin-topbar-links">
            <a href="index.php">← Заявки</a>
            <a href="gallery.php">Галерея</a>
            <a href="logout.php" class="btn">Выйти</a>
        </div>
    </div>

    <?php if ($msg): ?>
        <div class="msg msg--<?= $msg_type ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- Форма добавления / редактирования -->
    <div class="card">
        <h2><?= $edit_review ? 'Редактировать отзыв' : 'Добавить отзыв' ?></h2>
        <form method="post" action="reviews.php<?= $edit_review ? '?edit=' . $edit_review['id'] : '' ?>">
            <input type="hidden" name="action" value="<?= $edit_review ? 'edit' : 'add' ?>">
            <?php if ($edit_review): ?>
                <input type="hidden" name="id" value="<?= $edit_review['id'] ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label>Имя клиента *</label>
                    <input type="text" name="author_name" required maxlength="255"
                           value="<?= htmlspecialchars($edit_review['author_name'] ?? '') ?>"
                           placeholder="Иван Иванов">
                </div>
                <div class="form-group">
                    <label>Услуга</label>
                    <select name="service_type">
                        <option value="">— не указана —</option>
                        <?php foreach ($services as $svc): ?>
                            <option value="<?= htmlspecialchars($svc) ?>"
                                <?= ($edit_review['service_type'] ?? '') === $svc ? 'selected' : '' ?>>
                                <?= htmlspecialchars($svc) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Оценка *</label>
                <div class="stars-input">
                    <?php for ($s = 5; $s >= 1; $s--): ?>
                        <input type="radio" name="rating" id="star<?= $s ?>" value="<?= $s ?>"
                               <?= ($edit_review['rating'] ?? 5) == $s ? 'checked' : '' ?>>
                        <label for="star<?= $s ?>">★</label>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Текст отзыва *</label>
                <textarea name="review_text" required placeholder="Текст отзыва клиента..."><?= htmlspecialchars($edit_review['review_text'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="is_published" value="1"
                           <?= ($edit_review['is_published'] ?? 0) ? 'checked' : '' ?>>
                    Опубликовать на сайте
                </label>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn"><?= $edit_review ? 'Сохранить' : 'Добавить отзыв' ?></button>
                <?php if ($edit_review): ?>
                    <a href="reviews.php" class="btn btn--outline">Отмена</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Таблица отзывов -->
    <div class="card">
        <h2>Все отзывы (<?= count($reviews) ?>)</h2>
        <?php if (empty($reviews)): ?>
            <p style="color:#6b7280;text-align:center;padding:32px">Отзывов пока нет.</p>
        <?php else: ?>
        <div class="table-wrap">
        <table class="reviews-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Дата</th>
                    <th>Клиент</th>
                    <th>Услуга</th>
                    <th>Оценка</th>
                    <th>Отзыв</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($reviews as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td style="white-space:nowrap"><?= date('d.m.Y', strtotime($r['created_at'])) ?></td>
                    <td><?= htmlspecialchars($r['author_name']) ?></td>
                    <td><?= htmlspecialchars($r['service_type'] ?: '—') ?></td>
                    <td class="stars-display"><?= str_repeat('★', (int)$r['rating']) ?></td>
                    <td style="max-width:260px"><?= nl2br(htmlspecialchars(mb_strimwidth($r['review_text'], 0, 120, '…'))) ?></td>
                    <td>
                        <span class="badge badge--<?= $r['is_published'] ? 'pub' : 'hidden' ?>">
                            <?= $r['is_published'] ? 'Опубликован' : 'Скрыт' ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="reviews.php?edit=<?= $r['id'] ?>" title="Редактировать">✏️</a>
                        <a href="reviews.php?action=toggle&id=<?= $r['id'] ?>" title="<?= $r['is_published'] ? 'Скрыть' : 'Опубликовать' ?>">
                            <?= $r['is_published'] ? '👁️' : '🚫' ?>
                        </a>
                        <a href="reviews.php?action=delete&id=<?= $r['id'] ?>"
                           title="Удалить" style="color:#dc3545"
                           onclick="return confirm('Удалить отзыв #<?= $r['id'] ?>?')">❌</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
