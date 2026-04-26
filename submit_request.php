<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? clean_input($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? clean_input($_POST['phone']) : '';
    $service_type = isset($_POST['service_type']) ? clean_input($_POST['service_type']) : '';
    $message = isset($_POST['message']) ? clean_input($_POST['message']) : '';
    $estimated_price = isset($_POST['estimated_price']) ? clean_input($_POST['estimated_price']) : '';
    $source = 'site';

    if (empty($phone)) {
        die('Ошибка: телефон обязателен.');
    }

    $file_path = null;
    if (isset($_FILES['file'])) {
        $upload_result = upload_file('file', 'uploads/');
        if ($upload_result) {
            if (isset($upload_result['error'])) {
                die('Ошибка загрузки файла: ' . $upload_result['error']);
            } elseif (isset($upload_result['success'])) {
                $file_path = $upload_result['success'];
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO requests (name, phone, service_type, message, file_path, estimated_price, source) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $phone, $service_type, $message, $file_path, $estimated_price, $source);

    if ($stmt->execute()) {
        $to = 'pomianem@bk.ru';
        $subject = 'Новая заявка с сайта Чисто-про39';
        $body = "Имя: $name\nТелефон: $phone\nТип услуги: $service_type\nПримерная стоимость: $estimated_price\nСообщение: $message\nФайл: $file_path\nИсточник: $source";
        send_notification($to, $subject, $body);

        header('Location: thank_you.php');
        exit;
    } else {
        echo "Ошибка при сохранении заявки: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header('Location: index.php');
    exit;
}
?>