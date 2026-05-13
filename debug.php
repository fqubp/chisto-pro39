<?php
// УДАЛИ ЭТОТ ФАЙЛ ПОСЛЕ ДИАГНОСТИКИ
if (session_status() === PHP_SESSION_NONE) session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Debug</title>
<style>body{font-family:monospace;padding:20px} .ok{color:green} .err{color:red} .warn{color:orange}</style>
</head><body>
<h2>🔍 Диагностика сайта</h2>

<h3>PHP</h3>
<p>Версия: <b><?= PHP_VERSION ?></b></p>

<h3>Сессии</h3>
<?php
$_SESSION['test'] = 'ok';
echo $_SESSION['test'] === 'ok'
    ? '<p class="ok">✅ Сессии работают</p>'
    : '<p class="err">❌ Сессии не работают</p>';

// CSRF тест
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$token = csrf_token();
echo '<p class="ok">✅ CSRF токен: ' . substr($token, 0, 16) . '...</p>';
?>

<h3>.env файл</h3>
<?php
echo file_exists(__DIR__ . '/.env')
    ? '<p class="ok">✅ .env найден</p>'
    : '<p class="err">❌ .env НЕ найден — создай его!</p>';
?>

<h3>База данных</h3>
<?php
$db = getDbConfig();
echo "<p>host=<b>{$db['host']}</b> | name=<b>{$db['name']}</b> | user=<b>{$db['user']}</b></p>";
$conn = @new mysqli($db['host'], $db['user'], $db['pass'], $db['name'], (int)$db['port']);
if ($conn->connect_error) {
    echo '<p class="err">❌ Ошибка подключения: ' . $conn->connect_error . '</p>';
} else {
    echo '<p class="ok">✅ Подключение к БД успешно</p>';
    $r = $conn->query("SHOW TABLES");
    echo '<p>Таблицы: ';
    $tables = [];
    while ($row = $r->fetch_row()) $tables[] = $row[0];
    echo implode(', ', $tables) ?: 'нет таблиц — выполни database.sql';
    echo '</p>';

    // Проверяем нужные таблицы
    foreach (['requests','reviews','gallery_items'] as $t) {
        echo in_array($t, $tables)
            ? "<p class='ok'>✅ Таблица <b>$t</b> есть</p>"
            : "<p class='err'>❌ Таблица <b>$t</b> отсутствует</p>";
    }
}
?>

<h3>Папки uploads</h3>
<?php
foreach (['uploads', 'uploads/gallery'] as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (!is_dir($path)) {
        echo "<p class='err'>❌ Папка <b>$dir</b> не существует</p>";
    } elseif (!is_writable($path)) {
        echo "<p class='warn'>⚠️ Папка <b>$dir</b> есть, но нет прав на запись (поставь 755)</p>";
    } else {
        echo "<p class='ok'>✅ Папка <b>$dir</b> доступна для записи</p>";
    }
}
?>

<h3>vendor / PHPMailer</h3>
<?php
echo file_exists(__DIR__ . '/vendor/autoload.php')
    ? '<p class="ok">✅ vendor/autoload.php найден</p>'
    : '<p class="err">❌ vendor/autoload.php отсутствует</p>';
echo file_exists(__DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php')
    ? '<p class="ok">✅ PHPMailer найден</p>'
    : '<p class="err">❌ PHPMailer не найден</p>';
?>

<hr>
<p style="color:red"><b>⚠️ Удали этот файл после проверки!</b></p>
</body></html>
