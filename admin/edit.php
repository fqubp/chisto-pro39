<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';
require_once '../includes/functions.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Получаем данные заявки
$stmt = $conn->prepare("SELECT * FROM requests WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();
$stmt->close();

if (!$request) {
    header('Location: index.php');
    exit;
}

// Обработка формы сохранения
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $service_type = clean_input($_POST['service_type'] ?? '');
    $message = clean_input($_POST['message'] ?? '');
    $estimated_price = clean_input($_POST['estimated_price'] ?? '');
    $status = clean_input($_POST['status'] ?? 'new');
    $source = clean_input($_POST['source'] ?? 'site');

    // Если загружен новый файл
    $file_path = $request['file_path']; // по умолчанию старый
    if (isset($_FILES['new_file']) && $_FILES['new_file']['error'] === UPLOAD_ERR_OK) {
        // Удаляем старый файл, если есть
        if ($file_path && file_exists('../' . $file_path)) {
            unlink('../' . $file_path);
        }
        // Загружаем новый
        $upload_result = upload_file('new_file', '../uploads/');
        if ($upload_result && isset($upload_result['success'])) {
            $file_path = $upload_result['success'];
        }
    }

    // Обновляем запись
    $stmt = $conn->prepare("UPDATE requests SET name=?, phone=?, service_type=?, message=?, estimated_price=?, status=?, source=?, file_path=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $name, $phone, $service_type, $message, $estimated_price, $status, $source, $file_path, $id);
    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    } else {
        $error = "Ошибка обновления: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование заявки</title>
    <link rel="stylesheet" href="/chisto-pro39/css/style.css">
    <style>
        .edit-form {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: var(--light-bg);
            border-radius: 8px;
        }
        .edit-form h1 {
            margin-bottom: 30px;
            color: var(--dark-blue);
        }
        .current-file {
            margin-top: 5px;
            font-size: 14px;
        }
        .current-file a {
            color: var(--accent-blue);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="edit-form">
            <h1>Редактирование заявки #<?php echo $id; ?></h1>
            <?php if (isset($error)): ?>
                <div style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Имя</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($request['name']); ?>">
                </div>
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($request['phone']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Тип услуги</label>
                    <input type="text" name="service_type" value="<?php echo htmlspecialchars($request['service_type']); ?>">
                </div>
                <div class="form-group">
                    <label>Комментарий</label>
                    <textarea name="message" rows="4"><?php echo htmlspecialchars($request['message']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Примерная стоимость</label>
                    <input type="text" name="estimated_price" value="<?php echo htmlspecialchars($request['estimated_price']); ?>">
                </div>
                <div class="form-group">
                    <label>Статус</label>
                    <select name="status">
                        <option value="new" <?php echo $request['status'] == 'new' ? 'selected' : ''; ?>>Новая</option>
                        <option value="in_progress" <?php echo $request['status'] == 'in_progress' ? 'selected' : ''; ?>>В работе</option>
                        <option value="completed" <?php echo $request['status'] == 'completed' ? 'selected' : ''; ?>>Выполнена</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Источник</label>
                    <input type="text" name="source" value="<?php echo htmlspecialchars($request['source']); ?>">
                </div>
                <div class="form-group">
                    <label>Текущий файл:</label>
                    <?php if ($request['file_path']): ?>
                        <div class="current-file">
                            <a href="/chisto-pro39/<?php echo $request['file_path']; ?>" target="_blank">Посмотреть</a>
                        </div>
                    <?php else: ?>
                        <p>Нет файла</p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Заменить файл (оставьте пустым, если не нужно)</label>
                    <input type="file" name="new_file" accept=".jpg,.jpeg,.png,.mp4,.mov">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Сохранить</button>
                    <a href="index.php" class="btn" style="background: #6c757d;">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>