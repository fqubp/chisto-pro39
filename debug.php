<?php
// УДАЛИ ЭТОТ ФАЙЛ ПОСЛЕ ДИАГНОСТИКИ
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>PHP версия: " . PHP_VERSION . "</h2>";

// Проверяем .env
echo "<h3>.env файл:</h3>";
$env = file_exists(__DIR__ . '/.env') ? 'НАЙДЕН ✅' : 'НЕ НАЙДЕН ❌';
echo $env . "<br>";

// Проверяем vendor
echo "<h3>vendor/:</h3>";
echo (is_dir(__DIR__ . '/vendor') ? 'НАЙДЕН ✅' : 'НЕ НАЙДЕН ❌ (причина 500!)') . "<br>";

// Проверяем подключение к БД
echo "<h3>БД:</h3>";
require_once __DIR__ . '/includes/config.php';
$db = getDbConfig();
echo "host={$db['host']} name={$db['name']} user={$db['user']}<br>";
$conn = @new mysqli($db['host'], $db['user'], $db['pass'], $db['name'], $db['port']);
echo $conn->connect_error ? "❌ Ошибка: " . $conn->connect_error : "✅ Подключение OK";

// Проверяем таблицы
if (!$conn->connect_error) {
    echo "<h3>Таблицы:</h3>";
    $r = $conn->query("SHOW TABLES");
    while ($row = $r->fetch_row()) echo $row[0] . "<br>";
}
