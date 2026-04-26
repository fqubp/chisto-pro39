<?php
require_once __DIR__ . '/config.php';

$dbConfig = getDbConfig();

// Создаём соединение
$conn = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['name'], $dbConfig['port']);

// Проверяем соединение
if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

// Устанавливаем кодировку
$conn->set_charset("utf8mb4");

// Теперь переменная $conn доступна для использования в других файлах
?>