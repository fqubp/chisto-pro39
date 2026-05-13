<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php'); exit;
}
require_once '../includes/db.php';
require_once '../includes/functions.php';

$msg = ''; $msg_type = '';

// Добавление
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $tg_id   = trim($_POST['telegram_chat_id'] ?? '');
    $active  = isset($_POST['is_active']) ? 1 : 0;
    if ($name) {
        $stmt = $conn->prepare("INSERT INTO workers (name, phone, telegram_chat_id, is_active) VALUES (?,?,?,?)");
        $stmt->bind_param("sssi", $name, $phone, $tg_id, $active);
        $stmt->execute(); $stmt->close();
        $msg = 'Рабочий добавлен.'; $msg_type = 'success';
    }
}

// Редактирование
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $id    = intval($_POST['id']);
    $name  = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $tg_id = trim($_POST['telegram_chat_id'] ?? '');
    $active= isset($_POST['is_active']) ? 1 : 0;
    if ($id && $name) {
        $stmt = $conn->prepare("UPDATE workers SET name=?,phone=?,telegram_chat_id=?,is_active=? WHERE id=?");
        $stmt->bind_param("sssii", $name, $phone, $tg_id, $active, $id);
        $stmt->execute(); $stmt->close();
        $msg = 'Сохранено.'; $msg_type = 'success';
    }
}

// Удаление
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM workers WHERE id=?");
    $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close();
    header('Location: workers.php'); exit;
}

// Тест уведомления
if (isset($_GET['action']) && $_GET['action'] === 'test' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM workers WHERE id=?");
    $stmt->bind_param("i", $id); $stmt->execute();
    $w = $stmt->get_result()->fetch_assoc(); $stmt->close();
    if ($w && $w['telegram_chat_id']) {
        $ok = send_telegram_notification("✅ Тест уведомления для <b>{$w['name']}</b>. Всё работает!", $w['telegram_chat_id']);
        $msg = $ok ? 'Тестовое сообщение отправлено!' : 'Ошибка отправки. Проверьте Telegram Chat ID.';
        $msg_type = $ok ? 'success' : 'error';
    } else {
        $msg = 'У рабочего не указан Telegram Chat ID.'; $msg_type = 'error';
    }
}

$workers = $conn->query("SELECT * FROM workers ORDER BY is_active DESC, name ASC")->fetch_all(MYSQLI_ASSOC);
$edit_worker = null;
if (isset($_GET['edit'])) {
    $eid = intval($_GET['edit']);
    foreach ($workers as $w) { if ($w['id'] === $eid) { $edit_worker = $w; break; } }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Админ — Рабочие</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body{background:#f4f7fb}
        .admin-wrap{max-width:1000px;margin:0 auto;padding:32px 20px}
        .admin-topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;flex-wrap:wrap;gap:12px}
        .admin-topbar h1{color:var(--navy);font-size:24px}
        .admin-topbar-links{display:flex;gap:12px;align-items:center}
        .admin-topbar-links a{font-size:14px;color:var(--teal-dark,#00695c);text-decoration:none}
        .msg{padding:12px 20px;border-radius:var(--radius);margin-bottom:20px;font-size:14px;font-weight:500}
        .msg--success{background:#d1fae5;color:#065f46}.msg--error{background:#fee2e2;color:#991b1b}
        .card{background:white;border-radius:var(--radius-lg);box-shadow:0 2px 12px rgba(0,0,0,.08);padding:28px;margin-bottom:28px}
        .card h2{font-size:18px;color:var(--navy);margin-bottom:20px;font-weight:700}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
        .form-group{display:flex;flex-direction:column;gap:6px;margin-bottom:16px}
        .form-group label{font-size:13px;font-weight:600;color:var(--navy)}
        .form-group input{border:1px solid #cdd5e0;border-radius:var(--radius);padding:9px 14px;font-size:14px;width:100%;box-sizing:border-box}
        .form-group input:focus{outline:none;border-color:var(--teal)}
        .form-check{display:flex;align-items:center;gap:8px;font-size:14px;color:var(--navy);cursor:pointer}
        .btn-row{display:flex;gap:12px;align-items:center;flex-wrap:wrap}
        .workers-table{width:100%;border-collapse:collapse}
        .workers-table th,.workers-table td{padding:10px 14px;border-bottom:1px solid #eef0f5;font-size:13px;text-align:left}
        .workers-table th{background:var(--navy);color:white;font-weight:600}
        .workers-table tr:hover td{background:#f8fafc}
        .badge{display:inline-block;padding:3px 9px;border-radius:12px;font-size:12px;font-weight:600}
        .badge--active{background:#d1fae5;color:#065f46}.badge--inactive{background:#f3f4f6;color:#6b7280}
        .actions a{font-size:16px;text-decoration:none;margin-right:6px;opacity:.8}
        .actions a:hover{opacity:1}
        .hint{font-size:12px;color:#9ca3af;margin-top:4px}
        @media(max-width:640px){.form-row{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="admin-wrap">
    <div class="admin-topbar">
        <h1>Рабочие</h1>
        <div class="admin-topbar-links">
            <a href="index.php">← Заявки</a>
            <a href="reviews.php">Отзывы</a>
            <a href="gallery.php">Галерея</a>
            <a href="logout.php" class="btn">Выйти</a>
        </div>
    </div>

    <?php if ($msg): ?>
        <div class="msg msg--<?= $msg_type ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?= $edit_worker ? 'Редактировать рабочего' : 'Добавить рабочего' ?></h2>
        <form method="post" action="workers.php<?= $edit_worker ? '?edit='.$edit_worker['id'] : '' ?>">
            <input type="hidden" name="action" value="<?= $edit_worker ? 'edit' : 'add' ?>">
            <?php if ($edit_worker): ?>
                <input type="hidden" name="id" value="<?= $edit_worker['id'] ?>">
            <?php endif; ?>
            <div class="form-row">
                <div class="form-group">
                    <label>Имя *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($edit_worker['name'] ?? '') ?>" placeholder="Иван Иванов">
                </div>
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($edit_worker['phone'] ?? '') ?>" placeholder="+7 (___) ___-__-__">
                </div>
            </div>
            <div class="form-group">
                <label>Telegram Chat ID</label>
                <input type="text" name="telegram_chat_id" value="<?= htmlspecialchars($edit_worker['telegram_chat_id'] ?? '') ?>" placeholder="123456789">
                <span class="hint">Узнать Chat ID: написать @userinfobot в Telegram. Нужен для уведомлений о назначении на заказ.</span>
            </div>
            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="is_active" value="1" <?= ($edit_worker['is_active'] ?? 1) ? 'checked' : '' ?>>
                    Активный (доступен для назначения)
                </label>
            </div>
            <div class="btn-row">
                <button type="submit" class="btn"><?= $edit_worker ? 'Сохранить' : 'Добавить' ?></button>
                <?php if ($edit_worker): ?><a href="workers.php" class="btn btn--outline">Отмена</a><?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Список рабочих (<?= count($workers) ?>)</h2>
        <?php if (empty($workers)): ?>
            <p style="color:#6b7280;text-align:center;padding:32px">Рабочих пока нет.</p>
        <?php else: ?>
        <table class="workers-table">
            <thead><tr><th>Имя</th><th>Телефон</th><th>Telegram ID</th><th>Статус</th><th>Действия</th></tr></thead>
            <tbody>
            <?php foreach ($workers as $w): ?>
            <tr>
                <td><b><?= htmlspecialchars($w['name']) ?></b></td>
                <td><?= htmlspecialchars($w['phone'] ?: '—') ?></td>
                <td><?= htmlspecialchars($w['telegram_chat_id'] ?: '—') ?></td>
                <td><span class="badge badge--<?= $w['is_active'] ? 'active' : 'inactive' ?>"><?= $w['is_active'] ? 'Активен' : 'Неактивен' ?></span></td>
                <td class="actions">
                    <a href="workers.php?edit=<?= $w['id'] ?>" title="Редактировать">✏️</a>
                    <?php if ($w['telegram_chat_id']): ?>
                    <a href="workers.php?action=test&id=<?= $w['id'] ?>" title="Тест Telegram">📨</a>
                    <?php endif; ?>
                    <a href="workers.php?action=delete&id=<?= $w['id'] ?>" style="color:#dc3545" title="Удалить" onclick="return confirm('Удалить рабочего?')">❌</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
</body></html>
