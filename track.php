<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$request = null;
$requests = [];
$error = '';
$searched = false;

$status_labels = [
    'new'         => ['label' => 'Новая',       'icon' => '🆕', 'color' => '#3b82f6'],
    'in_progress' => ['label' => 'В работе',    'icon' => '🔧', 'color' => '#f59e0b'],
    'completed'   => ['label' => 'Выполнена',   'icon' => '✅', 'color' => '#10b981'],
];

// Поиск по токену (прямая ссылка из SMS/письма)
if (!empty($_GET['token']) && $_GET['token'] !== 'bot') {
    $token = clean_input($_GET['token']);
    $stmt = $conn->prepare("SELECT r.*, w.name as worker_name FROM requests r LEFT JOIN workers w ON r.worker_id = w.id WHERE r.tracking_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $searched = true;
    if (!$request) $error = 'Заявка не найдена.';
}

// Поиск по телефону
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['phone'])) {
    $phone_raw = clean_input($_POST['phone']);
    // Нормализуем телефон — оставляем только цифры
    $phone_digits = preg_replace('/\D/', '', $phone_raw);
    $searched = true;

    $stmt = $conn->prepare("SELECT r.*, w.name as worker_name FROM requests r LEFT JOIN workers w ON r.worker_id = w.id WHERE REGEXP_REPLACE(r.phone, '[^0-9]', '') LIKE ? ORDER BY r.created_at DESC LIMIT 10");
    $like = '%' . substr($phone_digits, -10); // последние 10 цифр
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $requests[] = $row;
    $stmt->close();

    if (empty($requests)) $error = 'Заявок с таким номером телефона не найдено.';
}

$conn->close();
include 'includes/header.php';
?>

<section class="page-hero page-hero--track">
    <div class="container">
        <h1>Статус заявки</h1>
        <p>Введите номер телефона чтобы найти ваши заявки</p>
    </div>
</section>

<section class="track-page">
    <div class="container">

        <!-- Форма поиска -->
        <div class="track-search">
            <form method="post" action="track.php" class="track-form">
                <div class="track-form__row">
                    <input type="tel" name="phone" id="phone-track"
                           placeholder="+7 (___) ___-__-__"
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                           required>
                    <button type="submit" class="btn">Найти заявку</button>
                </div>
            </form>
        </div>

        <?php if ($error): ?>
            <div class="track-empty">
                <div class="track-empty__icon">🔍</div>
                <p><?= htmlspecialchars($error) ?></p>
                <p style="font-size:14px;color:#9ca3af">Проверьте номер телефона или <a href="<?= route('index.php#callback') ?>">оставьте новую заявку</a></p>
            </div>

        <?php elseif ($request): ?>
            <!-- Одна заявка по токену -->
            <div class="track-card">
                <?php $st = $status_labels[$request['status']] ?? $status_labels['new']; ?>
                <div class="track-card__header">
                    <div>
                        <h2>Заявка №<?= $request['id'] ?></h2>
                        <span class="track-card__date"><?= date('d.m.Y в H:i', strtotime($request['created_at'])) ?></span>
                    </div>
                    <div class="track-card__status" style="background:<?= $st['color'] ?>20;color:<?= $st['color'] ?>">
                        <?= $st['icon'] ?> <?= $st['label'] ?>
                    </div>
                </div>

                <div class="track-card__body">
                    <?php if ($request['service_type']): ?>
                    <div class="track-detail"><span>Услуга</span><b><?= htmlspecialchars($request['service_type']) ?></b></div>
                    <?php endif; ?>
                    <?php if ($request['address']): ?>
                    <div class="track-detail"><span>Адрес</span><b><?= htmlspecialchars($request['address']) ?></b></div>
                    <?php endif; ?>
                    <?php if ($request['area_sqm']): ?>
                    <div class="track-detail"><span>Площадь</span><b><?= $request['area_sqm'] ?> м²</b></div>
                    <?php endif; ?>
                    <?php if ($request['rooms']): ?>
                    <div class="track-detail"><span>Комнат</span><b><?= $request['rooms'] ?></b></div>
                    <?php endif; ?>
                    <?php if ($request['scheduled_at']): ?>
                    <div class="track-detail"><span>Дата уборки</span><b><?= date('d.m.Y H:i', strtotime($request['scheduled_at'])) ?></b></div>
                    <?php endif; ?>
                    <?php if ($request['estimated_price']): ?>
                    <div class="track-detail"><span>Стоимость</span><b>~<?= htmlspecialchars($request['estimated_price']) ?> руб</b></div>
                    <?php endif; ?>
                    <?php if ($request['worker_name']): ?>
                    <div class="track-detail"><span>Исполнитель</span><b><?= htmlspecialchars($request['worker_name']) ?></b></div>
                    <?php endif; ?>
                </div>

                <?php if ($request['message']): ?>
                <div class="track-card__comment">
                    <span>Ваш комментарий:</span>
                    <p><?= nl2br(htmlspecialchars($request['message'])) ?></p>
                </div>
                <?php endif; ?>

                <!-- Прогресс-бар статуса -->
                <div class="track-progress">
                    <div class="track-progress__step <?= in_array($request['status'], ['new','in_progress','completed']) ? 'done' : '' ?>">
                        <div class="track-progress__dot"></div><span>Принята</span>
                    </div>
                    <div class="track-progress__line <?= in_array($request['status'], ['in_progress','completed']) ? 'done' : '' ?>"></div>
                    <div class="track-progress__step <?= in_array($request['status'], ['in_progress','completed']) ? 'done' : '' ?>">
                        <div class="track-progress__dot"></div><span>В работе</span>
                    </div>
                    <div class="track-progress__line <?= $request['status'] === 'completed' ? 'done' : '' ?>"></div>
                    <div class="track-progress__step <?= $request['status'] === 'completed' ? 'done' : '' ?>">
                        <div class="track-progress__dot"></div><span>Выполнена</span>
                    </div>
                </div>
            </div>

        <?php elseif (!empty($requests)): ?>
            <!-- Список заявок по телефону -->
            <div class="track-list">
                <p style="color:var(--gray);margin-bottom:20px">Найдено заявок: <?= count($requests) ?></p>
                <?php foreach ($requests as $req): ?>
                <?php $st = $status_labels[$req['status']] ?? $status_labels['new']; ?>
                <div class="track-card">
                    <div class="track-card__header">
                        <div>
                            <h2>Заявка №<?= $req['id'] ?></h2>
                            <span class="track-card__date"><?= date('d.m.Y в H:i', strtotime($req['created_at'])) ?></span>
                        </div>
                        <div class="track-card__status" style="background:<?= $st['color'] ?>20;color:<?= $st['color'] ?>">
                            <?= $st['icon'] ?> <?= $st['label'] ?>
                        </div>
                    </div>
                    <div class="track-card__body">
                        <?php if ($req['service_type']): ?>
                        <div class="track-detail"><span>Услуга</span><b><?= htmlspecialchars($req['service_type']) ?></b></div>
                        <?php endif; ?>
                        <?php if ($req['scheduled_at']): ?>
                        <div class="track-detail"><span>Дата уборки</span><b><?= date('d.m.Y H:i', strtotime($req['scheduled_at'])) ?></b></div>
                        <?php endif; ?>
                        <?php if ($req['worker_name']): ?>
                        <div class="track-detail"><span>Исполнитель</span><b><?= htmlspecialchars($req['worker_name']) ?></b></div>
                        <?php endif; ?>
                    </div>
                    <div class="track-progress">
                        <div class="track-progress__step <?= in_array($req['status'], ['new','in_progress','completed']) ? 'done' : '' ?>">
                            <div class="track-progress__dot"></div><span>Принята</span>
                        </div>
                        <div class="track-progress__line <?= in_array($req['status'], ['in_progress','completed']) ? 'done' : '' ?>"></div>
                        <div class="track-progress__step <?= in_array($req['status'], ['in_progress','completed']) ? 'done' : '' ?>">
                            <div class="track-progress__dot"></div><span>В работе</span>
                        </div>
                        <div class="track-progress__line <?= $req['status'] === 'completed' ? 'done' : '' ?>"></div>
                        <div class="track-progress__step <?= $req['status'] === 'completed' ? 'done' : '' ?>">
                            <div class="track-progress__dot"></div><span>Выполнена</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
