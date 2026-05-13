<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

// Защита от брутфорса
if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;
if (!isset($_SESSION['login_lockout']))  $_SESSION['login_lockout']  = 0;

$locked = $_SESSION['login_lockout'] > time();
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$locked) {
    $password       = $_POST['password'] ?? '';
    $admin_password = getenv('ADMIN_PASSWORD') ?: 'LiTNa3I%';

    if (hash_equals($admin_password, $password)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['login_attempts']  = 0;
        $_SESSION['admin_ip']        = $_SERVER['REMOTE_ADDR'];
        session_regenerate_id(true);
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['login_lockout'] = time() + 900; // блокировка 15 минут
            $error = 'Слишком много попыток. Вход заблокирован на 15 минут.';
        } else {
            $remaining = 5 - $_SESSION['login_attempts'];
            $error = "Неверный пароль. Осталось попыток: $remaining";
        }
    }
}

if ($locked) {
    $wait = ceil(($_SESSION['login_lockout'] - time()) / 60);
    $error = "Вход заблокирован. Повторите через $wait мин.";
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — Чисто-про39</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: var(--light-bg); }
        .login-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: var(--white); border-radius: var(--radius-lg); padding: 40px 36px; width: 100%; max-width: 380px; box-shadow: var(--shadow-lg); }
        .login-box h1 { text-align: center; color: var(--navy); margin-bottom: 28px; font-size: 22px; }
        .login-error { background: #fee2e2; color: #b91c1c; border-radius: var(--radius); padding: 10px 14px; margin-bottom: 18px; font-size: 14px; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-box">
        <h1>🔒 Вход в панель</h1>
        <?php if ($error): ?>
            <div class="login-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!$locked): ?>
        <form method="post">
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required autofocus>
            </div>
            <button type="submit" class="btn" style="width:100%;margin-top:8px">Войти</button>
        </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
