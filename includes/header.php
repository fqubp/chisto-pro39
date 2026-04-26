<?php require_once __DIR__ . '/config.php'; ?>
<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чисто-про39 - Клининг в Калининграде</title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <!-- Font Awesome для иконок -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container header__container">
            <div class="header__logo">
                <a href="<?= route('index.php') ?>">Чисто-про39</a>
            </div>
            <nav class="header__nav">
                <ul class="header__menu">
                    <li><a href="<?= route('index.php') ?>">Главная</a></li>
                    <li class="menu-item-has-children">
                        <a href="<?= route('services.php') ?>">Услуги</a>
                        <ul class="submenu">
                            <li><a href="<?= route('private/') ?>">Для дома</a></li>
                            <li><a href="<?= route('business/') ?>">Для бизнеса</a></li>
                        </ul>
                    </li>
                    <li><a href="<?= route('prices.php') ?>">Цены</a></li>
                    <!-- НОВЫЙ ПУНКТ МЕНЮ: Калькулятор -->
                    <li><a href="<?= route('calculator.php') ?>">Калькулятор</a></li>
                    <li><a href="<?= route('contacts.php') ?>">Контакты</a></li>
                </ul>
            </nav>
            <div class="header__contacts">
                <a href="tel:+74011234567" class="header__phone">+7 (4012) 123-456</a>
                <div class="header__social">
                    <a href="#" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-telegram"></i></a>
                </div>
                <a href="<?= route('index.php#callback') ?>" class="header__btn btn">Оставить заявку</a>
            </div>
            <button class="header__burger" id="burger">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>
    <main>