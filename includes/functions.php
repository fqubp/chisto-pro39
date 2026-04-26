<?php
// Очистка данных от тегов и лишних пробелов
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Загрузка файла
function upload_file($file_input_name, $upload_dir = '../uploads/') {
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES[$file_input_name]['tmp_name'];
        $file_name = basename($_FILES[$file_input_name]['name']);
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid() . '_' . time() . '.' . $file_ext;
        $destination = $upload_dir . $new_file_name;

        // Проверка размера (до 10 МБ)
        if ($_FILES[$file_input_name]['size'] > 10 * 1024 * 1024) {
            return ['error' => 'Файл слишком большой. Максимум 10 МБ.'];
        }

        // Разрешённые типы
        $allowed_types = ['image/jpeg', 'image/png', 'video/mp4', 'video/quicktime'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        if (!in_array($mime_type, $allowed_types)) {
            return ['error' => 'Недопустимый тип файла. Разрешены только JPG, PNG, MP4, MOV.'];
        }

        if (move_uploaded_file($file_tmp, $destination)) {
            return ['success' => 'uploads/' . $new_file_name];
        } else {
            return ['error' => 'Ошибка при сохранении файла.'];
        }
    } elseif ($_FILES[$file_input_name]['error'] !== UPLOAD_ERR_NO_FILE) {
        return ['error' => 'Ошибка загрузки файла: код ' . $_FILES[$file_input_name]['error']];
    }
    return null;
}

// Имитация отправки письма (на локалке пишет в файл)
function send_notification($to, $subject, $message) {
    $log = "To: $to\nSubject: $subject\nMessage: $message\n\n";
    file_put_contents(__DIR__ . '/../mail_log.txt', $log, FILE_APPEND);
    return true;
}

// Получение всех заявок (для админки)
function get_requests($conn) {
    $sql = "SELECT * FROM requests ORDER BY created_at DESC";
    $result = $conn->query($sql);
    $requests = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
    }
    return $requests;
}
?>