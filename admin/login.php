<?php
session_start();
// Если уже авторизован, перенаправляем в админку
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    // Пароль, который ты указал
    if ($password === 'LiTNa3I%') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверный пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель</title>
    <link rel="stylesheet" href="/chisto-pro39/css/style.css">
    <style>
        .login-form {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: var(--light-bg);
            border-radius: 8px;
        }
        .login-form h1 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--dark-blue);
        }
        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h1>Вход в админ-панель</h1>
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Войти</button>
            </form>
        </div>
    </div>
</body>
</html>