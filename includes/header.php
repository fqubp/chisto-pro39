<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чисто-про39 — Профессиональный клининг в Калининграде</title>
    <meta name="description" content="Клининговая компания Чисто-про39 в Калининграде. Уборка квартир, офисов, домов. Химчистка мебели, мойка окон, уборка после ремонта. Звоните: +7 (922) 250-12-66">
    <meta name="keywords" content="клининг Калининград, уборка квартиры Калининград, химчистка мебели, мойка окон, уборка офиса, клининговая компания">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Чисто-про39 — Профессиональный клининг в Калининграде">
    <meta property="og:description" content="Уборка квартир, офисов, химчистка мебели, мойка окон. Поддерживающая уборка 1-комнатной квартиры — от 6 000 руб.">
    <meta property="og:image" content="<?= asset('images/og-preview.jpg') ?>">
    <meta property="og:url" content="https://chisto-pro39.ru">
    <meta property="og:locale" content="ru_RU">
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">
    <link rel="shortcut icon" href="<?= asset('images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- JSON-LD: LocalBusiness -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "Чисто-про39",
      "description": "Профессиональные клининговые услуги в Калининграде: уборка квартир, офисов, химчистка мебели, мойка окон.",
      "url": "https://chisto-pro39.ru",
      "telephone": "+79222501266",
      "email": "chisto-pro39@bk.ru",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Калининград",
        "addressRegion": "Калининградская область",
        "addressCountry": "RU"
      },
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
        "opens": "08:00",
        "closes": "22:00"
      },
      "priceRange": "от 150 руб/м²",
      "sameAs": ["https://t.me/chisto_pro39_bot"]
    }
    </script>
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
                    <li><a href="<?= route('calculator.php') ?>">Калькулятор</a></li>
                    <li><a href="<?= route('gallery.php') ?>" style="white-space:nowrap">Наши работы</a></li>
                    <li><a href="<?= route('reviews.php') ?>">Отзывы</a></li>
                    <li class="menu-item-has-children">
                        <a href="#" style="white-space:nowrap">О нас</a>
                        <ul class="submenu">
                            <li><a href="<?= route('about.php') ?>">О компании</a></li>
                            <li><a href="<?= route('faq.php') ?>">Вопросы и ответы</a></li>
                            <li><a href="<?= route('track.php') ?>">Статус заявки</a></li>
                        </ul>
                    </li>
                    <li><a href="<?= route('contacts.php') ?>">Контакты</a></li>
                </ul>
            </nav>
            <div class="header__contacts">
                <a href="tel:+79222501266" class="header__phone">+7 (922) 250-12-66</a>
                <div class="header__social">
                    <a href="https://vk.com/chisto_pro39" target="_blank" aria-label="ВКонтакте"><i class="fab fa-vk"></i></a>
                    <a href="https://t.me/chisto_pro39_bot" target="_blank" aria-label="Telegram"><i class="fab fa-telegram"></i></a>
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