<?php
require_once __DIR__ . '/config.php';

$dbConfig = getDbConfig();

// Создаём соединение
$conn = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['name'], $dbConfig['port']);

// Проверяем соединение
if ($conn->connect_error) {
    error_log("DB connection error: " . $conn->connect_error);
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
        echo '<h1>Ошибка сервера</h1>';
        echo '<p>Не удалось подключиться к базе данных. Проверьте файл <code>.env</code> и параметры подключения.</p>';
    }
    exit;
}

// Устанавливаем кодировку
$conn->set_charset("utf8mb4");

// Теперь переменная $conn доступна для использования в других файлах
?>