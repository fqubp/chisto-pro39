<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php'); exit;
}

require_once '../includes/db.php';
require_once '../includes/functions.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header('Location: index.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM requests WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$request) { header('Location: index.php'); exit; }

$msg = ''; $msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name            = clean_input($_POST['name'] ?? '');
    $phone           = clean_input($_POST['phone'] ?? '');
    $service_type    = clean_input($_POST['service_type'] ?? '');
    $message         = clean_input($_POST['message'] ?? '');
    $estimated_price = clean_input($_POST['estimated_price'] ?? '');
    $status          = clean_input($_POST['status'] ?? 'new');

    $file_path = $request['file_path'];
    if (!empty($_FILES['new_file']['name'][0])) {
        $upload_result = upload_files('new_file', '../uploads/');
        if (isset($upload_result['success'])) {
            foreach (get_file_paths($file_path) as $old) {
                if ($old && file_exists('../' . $old)) unlink('../' . $old);
            }
            $file_path = json_encode($upload_result['success']);
        }
    }

    $stmt = $conn->prepare("UPDATE requests SET name=?, phone=?, service_type=?, message=?, estimated_price=?, status=?, file_path=? WHERE id=?");
    $stmt->bind_param("sssssssi", $name, $phone, $service_type, $message, $estimated_price, $status, $file_path, $id);
    if ($stmt->execute()) {
        // Обновляем данные для отображения
        $request = array_merge($request, [
            'name' => $name, 'phone' => $phone, 'service_type' => $service_type,
            'message' => $message, 'estimated_price' => $estimated_price, 'status' => $status,
        ]);
        $msg = 'Заявка обновлена.'; $msg_type = 'success';
    } else {
        $msg = 'Ошибка: ' . $conn->error; $msg_type = 'error';
    }
    $stmt->close();
}

$conn->close();

$services = ['Уборка квартиры','Уборка дома','Химчистка мебели','Мойка окон','Уборка после ремонта','Уборка офиса','Другое'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование заявки #<?= $id ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #f4f7fb; }
        .admin-wrap { max-width: 760px; margin: 0 auto; padding: 32px 20px; }
        .admin-topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:12px; }
        .admin-topbar h1 { color:var(--navy); font-size:22px; }
        .card { background:white; border-radius:var(--radius-lg); box-shadow:0 2px 12px rgba(0,0,0,.08); padding:32px; }
        .msg { padding:12px 20px; border-radius:var(--radius); margin-bottom:20px; font-size:14px; font-weight:500; }
        .msg--success { background:#d1fae5; color:#065f46; }
        .msg--error { background:#fee2e2; color:#991b1b; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
        .form-group label { font-size:13px; font-weight:600; color:var(--navy); }
        .form-group input, .form-group select, .form-group textarea {
            border:1px solid #cdd5e0; border-radius:var(--radius); padding:10px 14px;
            font-size:14px; font-family:inherit; width:100%; box-sizing:border-box;
            transition:border-color .2s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline:none; border-color:var(--teal);
        }
        .form-group textarea { min-height:90px; resize:vertical; }
        .price-field input { font-size:18px; font-weight:700; color:var(--navy); border-color:var(--teal); }
        .price-hint { font-size:12px; color:#9ca3af; margin-top:4px; }
        .form-divider { border:none; border-top:1px solid #e5e7eb; margin:20px 0; }
        .btn-row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-top:8px; }
        .current-files { display:flex; gap:8px; flex-wrap:wrap; margin-top:8px; }
        .current-files a { font-size:13px; color:var(--teal-dark,#00695c); text-decoration:none; border:1px solid #a7f3d0; padding:4px 10px; border-radius:8px; }
        .current-files a:hover { background:#f0fdf9; }
        @media(max-width:640px) { .form-grid { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<div class="admin-wrap">
    <div class="admin-topbar">
        <h1>✏️ Редактирование заявки #<?= $id ?></h1>
        <a href="index.php" class="btn btn--outline">← Назад</a>
    </div>

    <?php if ($msg): ?>
        <div class="msg msg--<?= $msg_type ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="post" enctype="multipart/form-data">

            <!-- Стоимость — самое важное поле, сверху -->
            <div class="form-group price-field">
                <label>💰 Согласованная стоимость (руб.)</label>
                <input type="text" name="estimated_price"
                       value="<?= htmlspecialchars($request['estimated_price'] ?? '') ?>"
                       placeholder="Введите сумму после обсуждения с клиентом">
                <span class="price-hint">Заполните после согласования стоимости с клиентом по телефону</span>
            </div>

            <div class="form-group">
                <label>Статус заявки</label>
                <select name="status">
                    <option value="new"         <?= $request['status'] === 'new'         ? 'selected' : '' ?>>🆕 Новая</option>
                    <option value="in_progress" <?= $request['status'] === 'in_progress' ? 'selected' : '' ?>>⚙️ В работе</option>
                    <option value="completed"   <?= $request['status'] === 'completed'   ? 'selected' : '' ?>>✅ Выполнена</option>
                </select>
            </div>

            <hr class="form-divider">

            <div class="form-grid">
                <div class="form-group">
                    <label>Имя клиента</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($request['name'] ?? '') ?>" placeholder="Имя">
                </div>
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="tel" name="phone" id="phone-edit" value="<?= htmlspecialchars($request['phone'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Тип услуги</label>
                <select name="service_type">
                    <option value="">— не указана —</option>
                    <?php foreach ($services as $s): ?>
                        <option value="<?= $s ?>" <?= ($request['service_type'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                    <?php if ($request['service_type'] && !in_array($request['service_type'], $services)): ?>
                        <option value="<?= htmlspecialchars($request['service_type']) ?>" selected><?= htmlspecialchars($request['service_type']) ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Комментарий клиента</label>
                <textarea name="message"><?= htmlspecialchars($request['message'] ?? '') ?></textarea>
            </div>

            <?php $existing_files = get_file_paths($request['file_path']); ?>
            <?php if (!empty($existing_files)): ?>
            <div class="form-group">
                <label>Прикреплённые файлы</label>
                <div class="current-files">
                    <?php foreach ($existing_files as $i => $file): ?>
                        <a href="../<?= htmlspecialchars($file) ?>" target="_blank">📎 Файл <?= $i + 1 ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Заменить файлы (необязательно)</label>
                <input type="file" name="new_file[]" accept=".jpg,.jpeg,.png,.mp4,.mov" multiple>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn">Сохранить изменения</button>
                <a href="index.php" class="btn btn--outline">Отмена</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
