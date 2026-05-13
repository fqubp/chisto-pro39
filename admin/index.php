<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';
require_once '../includes/functions.php';

// Change status
if (isset($_GET['action']) && $_GET['action'] === 'change_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];
    $allowed_statuses = ['new', 'in_progress', 'completed'];
    if (in_array($status, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: index.php');
    exit;
}

// Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT file_path FROM requests WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $file_paths = get_file_paths($row['file_path']);
        foreach ($file_paths as $file_path) {
            if ($file_path && file_exists('../' . $file_path)) {
                unlink('../' . $file_path);
            }
        }
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM requests WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: index.php');
    exit;
}

// Filters & pagination
$status_filter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

$allowed_statuses = ['new', 'in_progress', 'completed'];
$where = [];
$params = [];
$types = '';

if ($status_filter && in_array($status_filter, $allowed_statuses)) {
    $where[] = 'status = ?';
    $params[] = $status_filter;
    $types .= 's';
}

if ($search !== '') {
    $where[] = '(name LIKE ? OR phone LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Total count
$count_stmt = $conn->prepare("SELECT COUNT(*) FROM requests $where_sql");
if ($params) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total = (int) $count_stmt->get_result()->fetch_row()[0];
$count_stmt->close();
$total_pages = (int) ceil($total / $per_page);

// Fetch current page
$page_params = $params;
$page_params[] = $per_page;
$page_params[] = $offset;
$page_types = $types . 'ii';

$stmt = $conn->prepare("SELECT * FROM requests $where_sql ORDER BY created_at DESC LIMIT ? OFFSET ?");
if ($page_params) {
    $stmt->bind_param($page_types, ...$page_params);
}
$stmt->execute();
$requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

function build_url($overrides = []) {
    global $status_filter, $search, $page;
    $params = ['status' => $status_filter, 'search' => $search, 'page' => $page];
    $params = array_merge($params, $overrides);
    $params = array_filter($params, fn($v) => $v !== '' && $v !== null);
    return 'index.php' . ($params ? '?' . http_build_query($params) : '');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Заявки</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #f4f7fb; }
        .admin-wrap { max-width: 1400px; margin: 0 auto; padding: 40px 20px; }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .admin-header h1 { color: var(--navy); font-size: 26px; }
        .admin-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .admin-filter input[type="text"] {
            padding: 8px 14px;
            border: 1px solid #cdd5e0;
            border-radius: var(--radius);
            font-size: 14px;
            flex: 1;
            min-width: 200px;
        }
        .admin-filter select {
            padding: 8px 14px;
            border: 1px solid #cdd5e0;
            border-radius: var(--radius);
            font-size: 14px;
            background: white;
        }
        .admin-filter .btn { padding: 8px 18px; font-size: 14px; }
        .admin-stats {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .admin-stat {
            background: white;
            border-radius: var(--radius);
            padding: 14px 20px;
            font-size: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
        }
        .admin-stat strong { display: block; font-size: 22px; color: var(--navy); }
        .admin-table-wrap { overflow-x: auto; }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border-radius: var(--radius);
            overflow: hidden;
        }
        .admin-table th,
        .admin-table td {
            padding: 11px 14px;
            text-align: left;
            border-bottom: 1px solid #eef0f5;
            font-size: 13px;
        }
        .admin-table th {
            background: var(--navy);
            color: white;
            font-weight: 600;
            white-space: nowrap;
        }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover td { background: #f8fafc; }
        .status-badge {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-new { background: #fff3cd; color: #856404; }
        .status-in_progress { background: #cff4fc; color: #0c6278; }
        .status-completed { background: #d1e7dd; color: #0a5235; }
        .file-link { color: var(--teal-dark); text-decoration: underline; font-size: 12px; }
        .actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
        .actions a { font-size: 16px; text-decoration: none; opacity: .8; }
        .actions a:hover { opacity: 1; }
        .pagination {
            display: flex;
            gap: 6px;
            justify-content: center;
            margin-top: 24px;
            flex-wrap: wrap;
        }
        .pagination__link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: var(--radius);
            background: white;
            color: var(--navy);
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 1px 3px rgba(0,0,0,.1);
            transition: background var(--transition);
        }
        .pagination__link:hover,
        .pagination__link.active {
            background: var(--teal);
            color: white;
        }
        .empty-state { text-align: center; padding: 60px 0; color: #6c757d; font-size: 16px; }
    </style>
</head>
<body>
    <div class="admin-wrap">
        <div class="admin-header">
            <h1>Заявки с сайта</h1>
            <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
                <a href="reviews.php" class="btn btn--outline" style="font-size:14px">💬 Отзывы</a>
                <a href="gallery.php" class="btn btn--outline" style="font-size:14px">🖼️ Галерея</a>
                <a href="logout.php" class="btn">Выйти</a>
            </div>
        </div>

        <form method="get" class="admin-filter">
            <input type="text" name="search" placeholder="Поиск по имени или телефону"
                   value="<?= htmlspecialchars($search) ?>">
            <select name="status">
                <option value="">Все статусы</option>
                <option value="new" <?= $status_filter === 'new' ? 'selected' : '' ?>>Новые</option>
                <option value="in_progress" <?= $status_filter === 'in_progress' ? 'selected' : '' ?>>В работе</option>
                <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Выполненные</option>
            </select>
            <button type="submit" class="btn">Применить</button>
            <?php if ($status_filter || $search): ?>
                <a href="index.php" class="btn btn--outline">Сбросить</a>
            <?php endif; ?>
        </form>

        <div class="admin-stats">
            <div class="admin-stat"><strong><?= $total ?></strong>найдено заявок</div>
        </div>

        <?php if (empty($requests)): ?>
            <div class="empty-state">Заявок не найдено.</div>
        <?php else: ?>
            <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Дата</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Услуга</th>
                        <th>Стоимость</th>
                        <th>Комментарий</th>
                        <th>Файл</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><?= $req['id'] ?></td>
                            <td style="white-space:nowrap"><?= date('d.m.Y H:i', strtotime($req['created_at'])) ?></td>
                            <td><?= htmlspecialchars($req['name'] ?: '-') ?></td>
                            <td style="white-space:nowrap"><?= htmlspecialchars($req['phone']) ?></td>
                            <td><?= htmlspecialchars($req['service_type'] ?: '-') ?></td>
                            <td style="white-space:nowrap"><?= htmlspecialchars($req['estimated_price'] ?: '-') ?></td>
                            <td style="max-width:200px"><?= nl2br(htmlspecialchars($req['message'] ?: '-')) ?></td>
                            <td>
                                <?php $file_paths = get_file_paths($req['file_path']); ?>
                                <?php if (!empty($file_paths)): ?>
                                    <?php foreach ($file_paths as $i => $fp): ?>
                                        <div><a href="../<?= htmlspecialchars($fp) ?>" target="_blank" class="file-link">Файл <?= $i + 1 ?></a></div>
                                    <?php endforeach; ?>
                                <?php else: ?>-<?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $req['status'] ?>">
                                    <?php match($req['status']) {
                                        'new'         => print('Новая'),
                                        'in_progress' => print('В работе'),
                                        'completed'   => print('Выполнена'),
                                        default       => print(htmlspecialchars($req['status'])),
                                    }; ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="edit.php?id=<?= $req['id'] ?>" title="Редактировать">✏️</a>
                                <a href="<?= build_url(['action' => 'change_status', 'id' => $req['id'], 'status' => 'new', 'page' => null]) ?>" title="Новая">📄</a>
                                <a href="<?= build_url(['action' => 'change_status', 'id' => $req['id'], 'status' => 'in_progress', 'page' => null]) ?>" title="В работе">⚙️</a>
                                <a href="<?= build_url(['action' => 'change_status', 'id' => $req['id'], 'status' => 'completed', 'page' => null]) ?>" title="Выполнена">✅</a>
                                <a href="<?= build_url(['action' => 'delete', 'id' => $req['id'], 'page' => null]) ?>"
                                   title="Удалить" onclick="return confirm('Удалить заявку #<?= $req['id'] ?>?');"
                                   style="color:#dc3545">❌</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="<?= build_url(['page' => $page - 1]) ?>" class="pagination__link">&#8249;</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="<?= build_url(['page' => $i]) ?>"
                           class="pagination__link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="<?= build_url(['page' => $page + 1]) ?>" class="pagination__link">&#8250;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
