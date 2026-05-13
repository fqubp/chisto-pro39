<?php
// Загрузка переменных окружения из .env файла
function loadEnv($path) {
    if (!file_exists($path)) {
        error_log("Warning: .env file not found at $path");
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

loadEnv(__DIR__ . '/../.env');

// Настройки приложения и URL-помощники

define('APP_ROOT_DIR', dirname(__DIR__));
define('UPLOADS_DIR', APP_ROOT_DIR . '/uploads');

function get_app_root_url() {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptFilename = $_SERVER['SCRIPT_FILENAME'] ?? '';
    $appRootReal = realpath(APP_ROOT_DIR);
    $scriptDir = dirname($scriptName);

    if ($appRootReal && $scriptFilename) {
        $currentDirReal = realpath(dirname($scriptFilename));
        if ($currentDirReal !== false && strpos($currentDirReal, $appRootReal) === 0) {
            $relativeDir = trim(str_replace($appRootReal, '', $currentDirReal), '/\\');
            if ($relativeDir !== '') {
                $depth = substr_count($relativeDir, '/') + 1;
                $base = $scriptDir;
                for ($i = 0; $i < $depth; $i++) {
                    $base = dirname($base);
                }
                $scriptDir = $base;
            }
        }
    }

    $scriptDir = str_replace('\\', '/', $scriptDir);
    return $scriptDir === '' ? '/' : rtrim($scriptDir, '/');
}

function route($path = '') {
    $base = get_app_root_url();
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function asset($path = '') {
    return route($path);
}

// SMTP настройки
function getSmtpConfig() {
    return [
        'host' => getenv('SMTP_HOST') ?: 'smtp.mail.ru',
        'port' => getenv('SMTP_PORT') ?: 465,
        'user' => getenv('SMTP_USER') ?: '',
        'pass' => getenv('SMTP_PASS') ?: '',
        'from' => getenv('SMTP_FROM') ?: '',
        'from_name' => getenv('SMTP_FROM_NAME') ?: 'Чисто-про39',
    ];
}

// База данных настройки
function getDbConfig() {
    return [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'chisto_pro39_db',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: 'root',
        'port' => getenv('DB_PORT') ?: 3306,
    ];
}

