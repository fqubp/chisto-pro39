<?php
require_once __DIR__ . '/config.php';

// Максимальные ограничения для загрузки файлов
define('MAX_UPLOAD_FILES', 5);
define('MAX_UPLOAD_FILE_SIZE', 10 * 1024 * 1024); // 10 МБ на файл
define('MAX_UPLOAD_TOTAL_SIZE', 30 * 1024 * 1024); // 30 МБ общий размер

// Очистка данных от тегов и лишних пробелов
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function normalize_uploaded_files($file_input) {
    $items = [];
    if (!isset($file_input['name'])) {
        return $items;
    }

    if (is_array($file_input['name'])) {
        foreach ($file_input['name'] as $index => $name) {
            if ($file_input['error'][$index] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $items[] = [
                'name' => $name,
                'type' => $file_input['type'][$index],
                'tmp_name' => $file_input['tmp_name'][$index],
                'error' => $file_input['error'][$index],
                'size' => $file_input['size'][$index],
            ];
        }
    } else {
        if ($file_input['error'] !== UPLOAD_ERR_NO_FILE) {
            $items[] = $file_input;
        }
    }

    return $items;
}

function upload_files($file_input_name, $upload_dir = UPLOADS_DIR . '/') {
    if (!isset($_FILES[$file_input_name])) {
        return null;
    }

    $files = normalize_uploaded_files($_FILES[$file_input_name]);
    if (empty($files)) {
        return null;
    }

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (count($files) > MAX_UPLOAD_FILES) {
        return ['error' => 'Можно загрузить не более ' . MAX_UPLOAD_FILES . ' файлов.'];
    }

    $totalSize = 0;
    foreach ($files as $file) {
        $totalSize += $file['size'];
    }

    if ($totalSize > MAX_UPLOAD_TOTAL_SIZE) {
        return ['error' => 'Общий размер файлов не должен превышать ' . (MAX_UPLOAD_TOTAL_SIZE / 1024 / 1024) . ' МБ.'];
    }

    $allowed_types = ['image/jpeg', 'image/png', 'video/mp4', 'video/quicktime'];
    $savedPaths = [];

    foreach ($files as $file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Ошибка загрузки файла: код ' . $file['error']];
        }

        if ($file['size'] > MAX_UPLOAD_FILE_SIZE) {
            return ['error' => 'Файл ' . basename($file['name']) . ' слишком большой. Максимум ' . (MAX_UPLOAD_FILE_SIZE / 1024 / 1024) . ' МБ.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $allowed_types)) {
            return ['error' => 'Недопустимый тип файла ' . basename($file['name']) . '. Разрешены только JPG, PNG, MP4, MOV.'];
        }

        $file_ext = pathinfo(basename($file['name']), PATHINFO_EXTENSION);
        $new_file_name = uniqid() . '_' . time() . '.' . $file_ext;
        $destination = $upload_dir . $new_file_name;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            foreach ($savedPaths as $savedPath) {
                if (file_exists($upload_dir . basename($savedPath))) {
                    unlink($upload_dir . basename($savedPath));
                }
            }
            return ['error' => 'Ошибка при сохранении файла ' . basename($file['name']) . '.'];
        }

        $savedPaths[] = 'uploads/' . $new_file_name;
    }

    return ['success' => $savedPaths];
}

function upload_file($file_input_name, $upload_dir = UPLOADS_DIR . '/') {
    $result = upload_files($file_input_name, $upload_dir);
    if ($result === null) {
        return null;
    }
    if (isset($result['error'])) {
        return $result;
    }
    return ['success' => $result['success'][0]];
}

function get_file_paths($file_path) {
    if (empty($file_path)) {
        return [];
    }

    $decoded = json_decode($file_path, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        return array_filter($decoded, fn($item) => !empty($item));
    }

    return [$file_path];
}

// Имитация отправки письма (на локалке пишет в файл)
function send_notification($to, $subject, $message) {
    // Если на локалке, пишем в файл
    if (getenv('APP_ENV') === 'local' || !getenv('SMTP_HOST')) {
        $log = "To: $to\nSubject: $subject\nMessage: $message\n\n";
        file_put_contents(__DIR__ . '/../mail_log.txt', $log, FILE_APPEND);
        return true;
    }

    // Иначе отправляем через SMTP
    return send_smtp_email($to, $subject, $message);
}

function send_smtp_email($to, $subject, $message) {
    require_once __DIR__ . '/../vendor/autoload.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $smtp = getSmtpConfig();

        $mail->isSMTP();
        $mail->Host = $smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['user'];
        $mail->Password = $smtp['pass'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $smtp['port'];

        $mail->setFrom($smtp['from'], $smtp['from_name']);
        $mail->addAddress($to);

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail error: {$mail->ErrorInfo}");
        return false;
    }
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