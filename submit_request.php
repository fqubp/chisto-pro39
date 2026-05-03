<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF-проверка
    verify_csrf();

    // Honeypot — боты заполняют скрытое поле, люди нет
    if (!empty($_POST['website'])) {
        header('Location: thank_you.php');
        exit;
    }

    // Rate limiting
    if (!check_rate_limit($conn)) {
        header('Location: index.php?error=' . urlencode('Слишком много заявок. Позвоните нам напрямую: +7 (922) 250-12-66'));
        exit;
    }

    $name    = isset($_POST['name'])            ? clean_input($_POST['name'])            : '';
    $phone   = isset($_POST['phone'])           ? clean_input($_POST['phone'])           : '';
    $service = isset($_POST['service_type'])    ? clean_input($_POST['service_type'])    : '';
    $message = isset($_POST['message'])         ? clean_input($_POST['message'])         : '';
    $price   = isset($_POST['estimated_price']) ? clean_input($_POST['estimated_price']) : '';
    $source  = 'site';
    $ip_hash = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');

    if (empty($phone)) {
        header('Location: index.php?error=' . urlencode('Телефон обязателен.'));
        exit;
    }

    $file_path = null;
    if (isset($_FILES['file'])) {
        $upload_result = upload_files('file');
        if ($upload_result) {
            if (isset($upload_result['error'])) {
                header('Location: index.php?error=' . urlencode('Ошибка загрузки файла: ' . $upload_result['error']));
                exit;
            } elseif (isset($upload_result['success'])) {
                $file_path = json_encode($upload_result['success']);
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO requests (name, phone, service_type, message, file_path, estimated_price, source, source_ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        error_log('DB prepare error: ' . $conn->error);
        header('Location: index.php?error=' . urlencode('Ошибка сервера. Попробуйте позже.'));
        exit;
    }
    $stmt->bind_param("ssssssss", $name, $phone, $service, $message, $file_path, $price, $source, $ip_hash);

    if ($stmt->execute()) {
        $to      = getenv('ADMIN_EMAIL') ?: 'chisto-pro39@bk.ru';
        $subject = 'Новая заявка с сайта Чисто-про39';
        $paths   = get_file_paths($file_path);
        $body    = "Имя: $name\nТелефон: $phone\nТип услуги: $service\nПримерная стоимость: $price\nСообщение: $message\nФайлы: " . implode(', ', $paths);
        send_notification($to, $subject, $body);

        $tg = "🧹 <b>Новая заявка!</b>\n"
            . "👤 Имя: " . ($name ?: '—') . "\n"
            . "📞 Телефон: $phone\n"
            . ($service ? "🔧 Услуга: $service\n" : "")
            . ($price    ? "💰 Стоимость: ~$price руб\n" : "")
            . ($message  ? "💬 Комментарий: $message\n" : "");
        send_telegram_notification($tg);

        header('Location: thank_you.php');
        exit;
    } else {
        error_log('DB execute error: ' . $stmt->error);
        header('Location: index.php?error=' . urlencode('Ошибка сервера. Попробуйте позже.'));
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    header('Location: index.php');
    exit;
}
?>
