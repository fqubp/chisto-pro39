<?php
// Параметры подключения к базе данных (для MAMP по умолчанию)
$host = 'localhost';
$dbname = 'chisto_pro39_db'; // Имя базы данных, созданной ранее
$user = 'root';
$password = 'root'; // Стандартный пароль в MAMP
$port = 8889; // Порт MySQL в MAMP (обычно 8889)

// Создаём соединение
$conn = new mysqli($host, $user, $password, $dbname, $port);

// Проверяем соединение
if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

// Устанавливаем кодировку
$conn->set_charset("utf8mb4");

// Теперь переменная $conn доступна для использования в других файлах
?>