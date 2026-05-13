<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php'); exit;
}

require_once '../includes/db.php';
require_once '../includes/functions.php';

$msg = ''; $msg_type = '';

$categories = [
    'apartment'  => 'Квартиры',
    'office'     => 'Офисы',
    'furniture'  => 'Мебель',
    'windows'    => 'Окна',
    'renovation' => 'После ремонта',
    'other'      => 'Другое',
];

// --- Удаление ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT before_image, after_image FROM gallery_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row) {
        foreach ([$row['before_image'], $row['after_image']] as $path) {
            if ($path && file_exists('../' . $path)) unlink('../' . $path);
        }
    }
    $stmt = $conn->prepare("DELETE FROM gallery_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: gallery.php'); exit;
}

// --- Переключение публикации ---
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("UPDATE gallery_items SET is_published = 1 - is_published WHERE id = $id");
    header('Location: gallery.php'); exit;
}

// --- Добавление ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? 'other';
    $sort = intval($_POST['sort_order'] ?? 0);
    $published = isset($_POST['is_published']) ? 1 : 0;

    if (!array_key_exists($category, $categories)) $category = 'other';

    $before_path = null; $after_path = null;

    // Загрузка before
    if (!empty($_FILES['before_image']['name'])) {
        $res = upload_file('before_image', '../uploads/gallery/');
        if (isset($res['success'])) {
            $before_path = 'uploads/gallery/' . basename($res['success']);
        } else {
            $msg = 'Ошибка загрузки фото "До": ' . ($res['error'] ?? '');
            $msg_type = 'error';
        }
    }

    // Загрузка after
    if (!$msg && !empty($_FILES['after_image']['name'])) {
        $res = upload_file('after_image', '../uploads/gallery/');
        if (isset($res['success'])) {
            $after_path = 'uploads/gallery/' . basename($res['success']);
        } else {
            $msg = 'Ошибка загрузки фото "После": ' . ($res['error'] ?? '');
            $msg_type = 'error';
        }
    }

    if (!$msg && $title && $before_path && $after_path) {
        $stmt = $conn->prepare("INSERT INTO gallery_items (title, description, category, before_image, after_image, sort_order, is_published) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssii", $title, $desc, $category, $before_path, $after_path, $sort, $published);
        $stmt->execute();
        $stmt->close();
        $msg = 'Работа добавлена в галерею.';
        $msg_type = 'success';
    } elseif (!$msg) {
        $msg = 'Заполните название и загрузите оба фото.';
        $msg_type = 'error';
    }
}

// --- Редактирование (сохранение) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? 'other';
    $sort = intval($_POST['sort_order'] ?? 0);
    $published = isset($_POST['is_published']) ? 1 : 0;

    if (!array_key_exists($category, $categories)) $category = 'other';

    $stmt = $conn->prepare("SELECT before_image, after_image FROM gallery_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $before_path = $existing['before_image'];
    $after_path = $existing['after_image'];

    if (!empty($_FILES['before_image']['name'])) {
        $res = upload_file('before_image', '../uploads/gallery/');
        if (isset($res['success'])) {
            if ($before_path && file_exists('../' . $before_path)) unlink('../' . $before_path);
            $before_path = 'uploads/gallery/' . basename($res['success']);
        }
    }
    if (!empty($_FILES['after_image']['name'])) {
        $res = upload_file('after_image', '../uploads/gallery/');
        if (isset($res['success'])) {
            if ($after_path && file_exists('../' . $after_path)) unlink('../' . $after_path);
            $after_path = 'uploads/gallery/' . basename($res['success']);
        }
    }

    if ($id && $title) {
        $stmt = $conn->prepare("UPDATE gallery_items SET title=?, description=?, category=?, before_image=?, after_image=?, sort_order=?, is_published=? WHERE id=?");
        $stmt->bind_param("sssssiii", $title, $desc, $category, $before_path, $after_path, $sort, $published, $id);
        $stmt->execute();
        $stmt->close();
        $msg = 'Работа обновлена.';
        $msg_type = 'success';
    }
}

// --- Список ---
$items = $conn->query("SELECT * FROM gallery_items ORDER BY sort_order ASC, created_at DESC")->fetch_all(MYSQLI_ASSOC);
$edit_item = null;
if (isset($_GET['edit'])) {
    $eid = intval($_GET['edit']);
    foreach ($items as $it) {
        if ($it['id'] === $eid) { $edit_item = $it; break; }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ — Галерея</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #f4f7fb; }
        .admin-wrap { max-width: 1300px; margin: 0 auto; padding: 32px 20px; }
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
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .form-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
        .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
        .form-group label { font-size:13px; font-weight:600; color:var(--navy); }
        .form-group input[type=text], .form-group select, .form-group textarea, .form-group input[type=number] {
            border:1px solid #cdd5e0; border-radius:var(--radius); padding:9px 14px;
            font-size:14px; font-family:inherit; width:100%; box-sizing:border-box; transition:border-color 0.2s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline:none; border-color:var(--teal); }
        .form-group textarea { min-height:70px; resize:vertical; }
        .file-upload { border:2px dashed #cdd5e0; border-radius:var(--radius); padding:16px; text-align:center; cursor:pointer; transition:border-color 0.2s; }
        .file-upload:hover { border-color:var(--teal); }
        .file-upload input[type=file] { display:none; }
        .file-upload__label { font-size:13px; color:#6b7280; cursor:pointer; }
        .file-preview { max-width:100%; max-height:120px; border-radius:8px; margin-top:8px; display:none; }
        .form-check { display:flex; align-items:center; gap:8px; font-size:14px; color:var(--navy); cursor:pointer; }
        .form-check input { width:16px; height:16px; cursor:pointer; }
        .btn-row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
        .gallery-admin-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:20px; }
        .gallery-admin-card { background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; transition:box-shadow 0.2s; }
        .gallery-admin-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); }
        .gallery-admin-card__images { display:grid; grid-template-columns:1fr 1fr; height:140px; }
        .gallery-admin-card__img { background-size:cover; background-position:center; position:relative; }
        .gallery-admin-card__img span { position:absolute; bottom:4px; left:6px; font-size:10px; font-weight:700; background:rgba(0,0,0,0.55); color:white; padding:2px 7px; border-radius:8px; }
        .gallery-admin-card__body { padding:14px 16px; }
        .gallery-admin-card__title { font-size:14px; font-weight:700; color:var(--navy); margin-bottom:4px; }
        .gallery-admin-card__meta { font-size:12px; color:#9ca3af; margin-bottom:10px; }
        .gallery-admin-card__actions { display:flex; gap:8px; }
        .gallery-admin-card__actions a { font-size:18px; text-decoration:none; opacity:.8; }
        .gallery-admin-card__actions a:hover { opacity:1; }
        .badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:600; }
        .badge--pub { background:#d1fae5; color:#065f46; }
        .badge--hidden { background:#f3f4f6; color:#6b7280; }
        @media(max-width:640px) { .form-row, .form-row-3 { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<div class="admin-wrap">
    <div class="admin-topbar">
        <h1>Управление галереей</h1>
        <div class="admin-topbar-links">
            <a href="index.php">← Заявки</a>
            <a href="reviews.php">Отзывы</a>
            <a href="logout.php" class="btn">Выйти</a>
        </div>
    </div>

    <?php if ($msg): ?>
        <div class="msg msg--<?= $msg_type ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- Форма -->
    <div class="card">
        <h2><?= $edit_item ? 'Редактировать работу' : 'Добавить работу в галерею' ?></h2>
        <form method="post" enctype="multipart/form-data" action="gallery.php<?= $edit_item ? '?edit=' . $edit_item['id'] : '' ?>">
            <input type="hidden" name="action" value="<?= $edit_item ? 'edit' : 'add' ?>">
            <?php if ($edit_item): ?>
                <input type="hidden" name="id" value="<?= $edit_item['id'] ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label>Название работы *</label>
                    <input type="text" name="title" required maxlength="255"
                           value="<?= htmlspecialchars($edit_item['title'] ?? '') ?>"
                           placeholder="Генеральная уборка квартиры">
                </div>
                <div class="form-group">
                    <label>Категория *</label>
                    <select name="category">
                        <?php foreach ($categories as $k => $v): ?>
                            <option value="<?= $k ?>" <?= ($edit_item['category'] ?? 'other') === $k ? 'selected' : '' ?>><?= $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Описание (необязательно)</label>
                <textarea name="description" placeholder="2-комнатная квартира, 54 м²"><?= htmlspecialchars($edit_item['description'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Фото ДО <?= $edit_item ? '(оставьте пустым, чтобы не менять)' : '*' ?></label>
                    <div class="file-upload" onclick="document.getElementById('before_img').click()">
                        <input type="file" id="before_img" name="before_image" accept="image/jpeg,image/png" onchange="previewImage(this,'prev_before')">
                        <label class="file-upload__label" for="before_img">📷 Выбрать фото</label>
                        <?php if ($edit_item && $edit_item['before_image']): ?>
                            <img src="../<?= htmlspecialchars($edit_item['before_image']) ?>" style="max-width:100%;max-height:100px;border-radius:8px;margin-top:8px">
                        <?php endif; ?>
                        <img id="prev_before" class="file-preview">
                    </div>
                </div>
                <div class="form-group">
                    <label>Фото ПОСЛЕ <?= $edit_item ? '(оставьте пустым, чтобы не менять)' : '*' ?></label>
                    <div class="file-upload" onclick="document.getElementById('after_img').click()">
                        <input type="file" id="after_img" name="after_image" accept="image/jpeg,image/png" onchange="previewImage(this,'prev_after')">
                        <label class="file-upload__label" for="after_img">📷 Выбрать фото</label>
                        <?php if ($edit_item && $edit_item['after_image']): ?>
                            <img src="../<?= htmlspecialchars($edit_item['after_image']) ?>" style="max-width:100%;max-height:100px;border-radius:8px;margin-top:8px">
                        <?php endif; ?>
                        <img id="prev_after" class="file-preview">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Порядок сортировки (меньше — выше)</label>
                    <input type="number" name="sort_order" value="<?= (int)($edit_item['sort_order'] ?? 0) ?>" min="0">
                </div>
                <div class="form-group" style="justify-content:flex-end;padding-bottom:8px">
                    <label class="form-check" style="margin-top:auto">
                        <input type="checkbox" name="is_published" value="1"
                               <?= ($edit_item['is_published'] ?? 1) ? 'checked' : '' ?>>
                        Опубликовать на сайте
                    </label>
                </div>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn"><?= $edit_item ? 'Сохранить' : 'Добавить' ?></button>
                <?php if ($edit_item): ?>
                    <a href="gallery.php" class="btn btn--outline">Отмена</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Список работ -->
    <div class="card">
        <h2>Все работы в галерее (<?= count($items) ?>)</h2>
        <?php if (empty($items)): ?>
            <p style="color:#6b7280;text-align:center;padding:40px">Галерея пуста — добавьте первую работу выше.</p>
        <?php else: ?>
        <div class="gallery-admin-grid">
            <?php foreach ($items as $item): ?>
            <div class="gallery-admin-card">
                <div class="gallery-admin-card__images">
                    <div class="gallery-admin-card__img" style="background-image:url('../<?= htmlspecialchars($item['before_image']) ?>')">
                        <span>До</span>
                    </div>
                    <div class="gallery-admin-card__img" style="background-image:url('../<?= htmlspecialchars($item['after_image']) ?>')">
                        <span>После</span>
                    </div>
                </div>
                <div class="gallery-admin-card__body">
                    <div class="gallery-admin-card__title"><?= htmlspecialchars($item['title']) ?></div>
                    <div class="gallery-admin-card__meta">
                        <?= $categories[$item['category']] ?? $item['category'] ?> •
                        порядок: <?= $item['sort_order'] ?> •
                        <span class="badge badge--<?= $item['is_published'] ? 'pub' : 'hidden' ?>">
                            <?= $item['is_published'] ? 'Опубликовано' : 'Скрыто' ?>
                        </span>
                    </div>
                    <div class="gallery-admin-card__actions">
                        <a href="gallery.php?edit=<?= $item['id'] ?>" title="Редактировать">✏️</a>
                        <a href="gallery.php?action=toggle&id=<?= $item['id'] ?>" title="<?= $item['is_published'] ? 'Скрыть' : 'Опубликовать' ?>">
                            <?= $item['is_published'] ? '👁️' : '🚫' ?>
                        </a>
                        <a href="gallery.php?action=delete&id=<?= $item['id'] ?>"
                           style="color:#dc3545" title="Удалить"
                           onclick="return confirm('Удалить эту работу из галереи?')">❌</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
