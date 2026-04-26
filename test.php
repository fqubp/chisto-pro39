<?php
// test.php - проверка настроек
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

echo "<h1>Проверка настроек сайта</h1>";

// Проверка БД
$dbConfig = getDbConfig();
echo "<h2>База данных:</h2>";
echo "Хост: " . $dbConfig['host'] . "<br>";
echo "База: " . $dbConfig['name'] . "<br>";
echo "Пользователь: " . $dbConfig['user'] . "<br>";
echo "Подключение: " . ($conn ? "OK" : "Ошибка") . "<br>";

// Проверка SMTP
$smtpConfig = getSmtpConfig();
echo "<h2>SMTP:</h2>";
echo "Хост: " . $smtpConfig['host'] . "<br>";
echo "Пользователь: " . $smtpConfig['user'] . "<br>";
echo "Пароль: " . (empty($smtpConfig['pass']) ? "Не задан" : "Задан") . "<br>";

// Проверка uploads
echo "<h2>Загрузки:</h2>";
echo "Папка uploads: " . (is_dir('uploads') ? "Существует" : "Не существует") . "<br>";
echo "Права: " . substr(sprintf('%o', fileperms('uploads')), -4) . "<br>";

// Проверка PHPMailer
echo "<h2>PHPMailer:</h2>";
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
    echo "PHPMailer: Загружен<br>";
} else {
    echo "PHPMailer: Не найден<br>";
}

$conn->close();
?>