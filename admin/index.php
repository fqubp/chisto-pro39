<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php'); exit;
}

require_once '../includes/db.php';
require_once '../includes/functions.php';

// Назначение рабочих (множественный выбор)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'assign_worker') {
    $id         = intval($_POST['id']);
    $worker_ids = array_map('intval', (array)($_POST['worker_ids'] ?? []));
    $scheduled  = trim($_POST['scheduled_at'] ?? '') ?: null;
    $address    = trim($_POST['address'] ?? '');
    $area       = !empty($_POST['area_sqm']) ? (float)$_POST['area_sqm'] : null;
    $rooms      = !empty($_POST['rooms'])    ? (int)$_POST['rooms']      : null;

    // Обновляем основные поля заявки
    $stmt = $conn->prepare("UPDATE requests SET scheduled_at=?, address=?, area_sqm=?, rooms=? WHERE id=?");
    $stmt->bind_param("ssdii", $scheduled, $address, $area, $rooms, $id);
    $stmt->execute(); $stmt->close();

    // Получаем уже назначенных рабочих (чтобы уведомить только новых)
    $existing_res = $conn->prepare("SELECT worker_id FROM request_workers WHERE request_id=?");
    $existing_res->bind_param("i", $id); $existing_res->execute();
    $existing_ids = array_column($existing_res->get_result()->fetch_all(MYSQLI_ASSOC), 'worker_id');
    $existing_res->close();

    // Удаляем всех старых и вставляем новых
    $del = $conn->prepare("DELETE FROM request_workers WHERE request_id=?");
    $del->bind_param("i", $id); $del->execute(); $del->close();

    // Получаем данные заявки для уведомления
    $req_stmt = $conn->prepare("SELECT * FROM requests WHERE id=?");
    $req_stmt->bind_param("i", $id); $req_stmt->execute();
    $req = $req_stmt->get_result()->fetch_assoc(); $req_stmt->close();
    $req['scheduled_at'] = $scheduled;
    $req['address']      = $address;
    $req['area_sqm']     = $area;
    $req['rooms']        = $rooms;

    foreach ($worker_ids as $wid) {
        if (!$wid) continue;
        $ins = $conn->prepare("INSERT INTO request_workers (request_id, worker_id, notified_at) VALUES (?,?,NOW())");
        $ins->bind_param("ii", $id, $wid); $ins->execute(); $ins->close();
        // Уведомляем только новых рабочих
        if (!in_array($wid, $existing_ids)) {
            notify_worker($conn, $wid, $req);
        }
    }

    header('Location: index.php'); exit;
}

// Смена статуса
if (isset($_GET['action']) && $_GET['action'] === 'change_status' && isset($_GET['id'], $_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];
    if (in_array($status, ['new','in_progress','completed'])) {
        $stmt = $conn->prepare("UPDATE requests SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id); $stmt->execute(); $stmt->close();
    }
    header('Location: index.php'); exit;
}

// Удаление
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT file_path FROM requests WHERE id=?");
    $stmt->bind_param("i", $id); $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc(); $stmt->close();
    if ($row) foreach (get_file_paths($row['file_path']) as $fp) {
        if ($fp && file_exists('../'.$fp)) unlink('../'.$fp);
    }
    $stmt = $conn->prepare("DELETE FROM requests WHERE id=?");
    $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close();
    header('Location: index.php'); exit;
}

// Фильтры
$status_filter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

$where = []; $params = []; $types = '';
if ($status_filter && in_array($status_filter, ['new','in_progress','completed'])) {
    $where[] = 'r.status = ?'; $params[] = $status_filter; $types .= 's';
}
if ($search !== '') {
    $where[] = '(r.name LIKE ? OR r.phone LIKE ?)';
    $like = '%'.$search.'%'; $params[] = $like; $params[] = $like; $types .= 'ss';
}
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';

$count_stmt = $conn->prepare("SELECT COUNT(*) FROM requests r $where_sql");
if ($params) $count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$total = (int)$count_stmt->get_result()->fetch_row()[0];
$count_stmt->close();
$total_pages = (int)ceil($total / $per_page);

$page_params = array_merge($params, [$per_page, $offset]);
$page_types  = $types . 'ii';
$stmt = $conn->prepare("SELECT r.*, GROUP_CONCAT(w.name ORDER BY w.name SEPARATOR ', ') as worker_names, GROUP_CONCAT(w.id ORDER BY w.name SEPARATOR ',') as worker_ids_assigned FROM requests r LEFT JOIN request_workers rw ON r.id = rw.request_id LEFT JOIN workers w ON rw.worker_id = w.id $where_sql GROUP BY r.id ORDER BY r.created_at DESC LIMIT ? OFFSET ?");
if ($page_params) $stmt->bind_param($page_types, ...$page_params);
$stmt->execute();
$requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Статистика
$stats = $conn->query("SELECT status, COUNT(*) as cnt FROM requests GROUP BY status")->fetch_all(MYSQLI_ASSOC);
$stat_map = array_column($stats, 'cnt', 'status');

// Рабочие для выпадающего списка
$workers = $conn->query("SELECT id, name FROM workers WHERE is_active=1 ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$conn->close();

function build_url($overrides = []) {
    global $status_filter, $search, $page;
    $p = array_filter(array_merge(['status'=>$status_filter,'search'=>$search,'page'=>$page], $overrides), fn($v)=>$v!==''&&$v!==null);
    return 'index.php'.($p ? '?'.http_build_query($p) : '');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Админ — Заявки</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body{background:#f4f7fb}
        .admin-wrap{max-width:1500px;margin:0 auto;padding:32px 20px}
        .admin-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
        .admin-header h1{color:var(--navy);font-size:24px}
        .admin-nav{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
        .admin-nav a{font-size:14px;text-decoration:none;padding:7px 14px;border-radius:var(--radius);background:white;color:var(--navy);box-shadow:0 1px 3px rgba(0,0,0,.1);transition:.2s}
        .admin-nav a:hover{background:var(--teal);color:white}
        .admin-stats{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap}
        .admin-stat{background:white;border-radius:var(--radius);padding:14px 20px;font-size:13px;box-shadow:0 1px 4px rgba(0,0,0,.08);text-align:center}
        .admin-stat strong{display:block;font-size:24px;color:var(--navy);font-weight:800}
        .admin-filter{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;background:white;padding:16px;border-radius:var(--radius-lg);box-shadow:0 1px 4px rgba(0,0,0,.08)}
        .admin-filter input,.admin-filter select{padding:8px 14px;border:1px solid #cdd5e0;border-radius:var(--radius);font-size:14px;background:white}
        .admin-filter input{flex:1;min-width:160px}
        .table-wrap{overflow-x:auto}
        .admin-table{width:100%;border-collapse:collapse;background:white;box-shadow:0 2px 10px rgba(0,0,0,.08);border-radius:var(--radius-lg);overflow:hidden}
        .admin-table th,.admin-table td{padding:10px 12px;text-align:left;border-bottom:1px solid #eef0f5;font-size:13px}
        .admin-table th{background:var(--navy);color:white;font-weight:600;white-space:nowrap}
        .admin-table tr:hover td{background:#f8fafc}
        .admin-table tr:last-child td{border-bottom:none}
        .status-badge{display:inline-block;padding:3px 9px;border-radius:12px;font-size:11px;font-weight:700;white-space:nowrap}
        .status-new{background:#fef9c3;color:#854d0e}
        .status-in_progress{background:#dbeafe;color:#1e40af}
        .status-completed{background:#d1fae5;color:#065f46}
        .actions{display:flex;gap:4px;align-items:center}
        .actions a,.actions button{font-size:15px;text-decoration:none;opacity:.75;background:none;border:none;cursor:pointer;padding:2px}
        .actions a:hover,.actions button:hover{opacity:1}
        .pagination{display:flex;gap:6px;justify-content:center;margin-top:24px;flex-wrap:wrap}
        .pagination a{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:var(--radius);background:white;color:var(--navy);font-size:14px;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,.1)}
        .pagination a:hover,.pagination a.active{background:var(--teal);color:white}
        .empty-state{text-align:center;padding:60px;color:#6b7280;background:white;border-radius:var(--radius-lg)}

        /* Модальное окно назначения рабочего */
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center}
        .modal-overlay.open{display:flex}
        .modal{background:white;border-radius:var(--radius-lg);padding:32px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.3);position:relative}
        .modal h3{font-size:18px;color:var(--navy);margin-bottom:20px;font-weight:700}
        .modal .form-group{display:flex;flex-direction:column;gap:6px;margin-bottom:14px}
        .modal .form-group label{font-size:13px;font-weight:600;color:var(--navy)}
        .modal .form-group select,.modal .form-group input{border:1px solid #cdd5e0;border-radius:var(--radius);padding:9px 14px;font-size:14px;width:100%;box-sizing:border-box}
        .modal-close{position:absolute;top:16px;right:16px;background:none;border:none;font-size:22px;cursor:pointer;opacity:.6}
        .modal-close:hover{opacity:1}
        .modal-btns{display:flex;gap:12px;margin-top:20px}
    </style>
</head>
<body>
<div class="admin-wrap">
    <div class="admin-header">
        <h1>Заявки с сайта</h1>
        <nav class="admin-nav">
            <a href="workers.php">👷 Рабочие</a>
            <a href="reviews.php">💬 Отзывы</a>
            <a href="gallery.php">🖼️ Галерея</a>
            <a href="logout.php" class="btn" style="font-size:14px">Выйти</a>
        </nav>
    </div>

    <div class="admin-stats">
        <div class="admin-stat"><strong><?= array_sum(array_column($stats??[], 'cnt')) ?></strong>Всего заявок</div>
        <div class="admin-stat"><strong style="color:#854d0e"><?= $stat_map['new'] ?? 0 ?></strong>Новых</div>
        <div class="admin-stat"><strong style="color:#1e40af"><?= $stat_map['in_progress'] ?? 0 ?></strong>В работе</div>
        <div class="admin-stat"><strong style="color:#065f46"><?= $stat_map['completed'] ?? 0 ?></strong>Выполнено</div>
    </div>

    <form method="get" class="admin-filter">
        <input type="text" name="search" placeholder="Поиск по имени или телефону" value="<?= htmlspecialchars($search) ?>">
        <select name="status">
            <option value="">Все статусы</option>
            <option value="new" <?= $status_filter==='new'?'selected':'' ?>>Новые</option>
            <option value="in_progress" <?= $status_filter==='in_progress'?'selected':'' ?>>В работе</option>
            <option value="completed" <?= $status_filter==='completed'?'selected':'' ?>>Выполненные</option>
        </select>
        <button type="submit" class="btn">Применить</button>
        <?php if ($status_filter||$search): ?><a href="index.php" class="btn btn--outline">Сбросить</a><?php endif; ?>
    </form>

    <?php if (empty($requests)): ?>
        <div class="empty-state">Заявок не найдено.</div>
    <?php else: ?>
    <div class="table-wrap">
    <table class="admin-table">
        <thead><tr>
            <th>ID</th><th>Дата</th><th>Имя</th><th>Телефон</th>
            <th>Услуга</th><th>Адрес</th><th>Дата уборки</th>
            <th>Рабочий</th><th>Стоимость</th><th>Статус</th><th>Действия</th>
        </tr></thead>
        <tbody>
        <?php foreach ($requests as $req): ?>
        <tr>
            <td><?= $req['id'] ?></td>
            <td style="white-space:nowrap"><?= date('d.m.Y H:i', strtotime($req['created_at'])) ?></td>
            <td><?= htmlspecialchars($req['name'] ?: '—') ?></td>
            <td style="white-space:nowrap"><a href="tel:<?= htmlspecialchars($req['phone']) ?>"><?= htmlspecialchars($req['phone']) ?></a></td>
            <td><?= htmlspecialchars($req['service_type'] ?: '—') ?></td>
            <td style="max-width:140px;font-size:12px"><?= htmlspecialchars($req['address'] ?: '—') ?></td>
            <td style="white-space:nowrap;font-size:12px"><?= $req['scheduled_at'] ? date('d.m.Y H:i', strtotime($req['scheduled_at'])) : '—' ?></td>
            <td><?= htmlspecialchars($req['worker_names'] ?: '—') ?></td>
            <td style="white-space:nowrap"><?= htmlspecialchars($req['estimated_price'] ?: '—') ?></td>
            <td>
                <span class="status-badge status-<?= $req['status'] ?>">
                    <?= ['new'=>'Новая','in_progress'=>'В работе','completed'=>'Выполнена'][$req['status']] ?? $req['status'] ?>
                </span>
            </td>
            <td class="actions">
                <!-- Назначить рабочего -->
                <button onclick="openAssign(<?= $req['id'] ?>, '<?= addslashes($req['worker_ids_assigned']??'') ?>', '<?= addslashes($req['scheduled_at']??'') ?>', '<?= addslashes($req['address']??'') ?>', '<?= $req['area_sqm']??'' ?>', '<?= $req['rooms']??'' ?>')" title="Назначить рабочих">👷</button>
                <!-- Редактировать заявку -->
                <a href="edit.php?id=<?= $req['id'] ?>" title="Редактировать заявку и стоимость">✏️</a>
                <!-- Статусы -->
                <a href="<?= build_url(['action'=>'change_status','id'=>$req['id'],'status'=>'new','page'=>null]) ?>" title="→ Новая">📄</a>
                <a href="<?= build_url(['action'=>'change_status','id'=>$req['id'],'status'=>'in_progress','page'=>null]) ?>" title="→ В работе">⚙️</a>
                <a href="<?= build_url(['action'=>'change_status','id'=>$req['id'],'status'=>'completed','page'=>null]) ?>" title="→ Выполнена">✅</a>
                <!-- Удалить -->
                <a href="<?= build_url(['action'=>'delete','id'=>$req['id'],'page'=>null]) ?>" style="color:#dc3545" title="Удалить" onclick="return confirm('Удалить заявку #<?= $req['id'] ?>?')">❌</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?><a href="<?= build_url(['page'=>$page-1]) ?>">&#8249;</a><?php endif; ?>
        <?php for ($i=1;$i<=$total_pages;$i++): ?>
            <a href="<?= build_url(['page'=>$i]) ?>" class="<?= $i===$page?'active':'' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?><a href="<?= build_url(['page'=>$page+1]) ?>">&#8250;</a><?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Модальное окно назначения рабочего -->
<div class="modal-overlay" id="assignModal">
    <div class="modal">
        <button class="modal-close" onclick="closeAssign()">✕</button>
        <h3>Назначить рабочего</h3>
        <form method="post" action="index.php">
            <input type="hidden" name="action" value="assign_worker">
            <input type="hidden" name="id" id="assign_id">
            <div class="form-group">
                <label>Рабочие <span style="font-weight:400;color:#9ca3af">(можно выбрать несколько)</span></label>
                <div class="workers-checklist" id="assign_workers_list">
                    <?php foreach ($workers as $w): ?>
                    <label class="worker-check-item">
                        <input type="checkbox" name="worker_ids[]" value="<?= $w['id'] ?>"
                               class="worker-checkbox" data-id="<?= $w['id'] ?>">
                        <span><?= htmlspecialchars($w['name']) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-group">
                <label>Дата и время уборки</label>
                <input type="datetime-local" name="scheduled_at" id="assign_date">
            </div>
            <div class="form-group">
                <label>Адрес объекта</label>
                <input type="text" name="address" id="assign_address" placeholder="ул. Пушкина, д. 10, кв. 5">
            </div>
            <div class="form-group" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label>Площадь, м²</label>
                    <input type="number" name="area_sqm" id="assign_area" min="1" step="0.1" placeholder="50">
                </div>
                <div>
                    <label>Комнат</label>
                    <input type="number" name="rooms" id="assign_rooms" min="0" placeholder="2">
                </div>
            </div>
            <div class="modal-btns">
                <button type="submit" class="btn">Сохранить и уведомить</button>
                <button type="button" class="btn btn--outline" onclick="closeAssign()">Отмена</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAssign(id, workerIds, scheduledAt, address, area, rooms) {
    document.getElementById('assign_id').value = id;
    document.getElementById('assign_date').value = scheduledAt ? scheduledAt.replace(' ', 'T').slice(0,16) : '';
    document.getElementById('assign_address').value = address || '';
    document.getElementById('assign_area').value = area || '';
    document.getElementById('assign_rooms').value = rooms || '';

    // Отмечаем чекбоксы назначенных рабочих
    const assigned = workerIds ? workerIds.split(',').map(s => s.trim()) : [];
    document.querySelectorAll('.worker-checkbox').forEach(cb => {
        cb.checked = assigned.includes(cb.dataset.id);
    });

    document.getElementById('assignModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeAssign() {
    document.getElementById('assignModal').classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('assignModal').addEventListener('click', function(e) {
    if (e.target === this) closeAssign();
});
</script>
</body></html>
