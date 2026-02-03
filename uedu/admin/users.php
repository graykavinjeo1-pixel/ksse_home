<?php
require __DIR__ . '/_admin_guard.php';
require __DIR__ . '/_admin_header.php';

// 회원 검색 및 목록 조회
$q = $_GET['q'] ?? '';
$page = intval($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

$where = "WHERE 1=1";
$params = [];
if ($q) {
    $where .= " AND (username LIKE ? OR name LIKE ? OR email LIKE ? OR company LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

// 전체 수
$stmt = db()->prepare("SELECT COUNT(*) FROM uedu_users $where");
$stmt->execute($params);
$total = $stmt->fetchColumn();
$totalPages = ceil($total / $limit);

// 목록
$stmt = db()->prepare("SELECT id, username, name, email, role, created_at FROM uedu_users $where ORDER BY id DESC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<div class="admin-card">
    <h3 style="margin-top:0;">회원 관리</h3>
    
    <form method="GET" style="margin-bottom:20px; display:flex; gap:10px; align-items: center;">
        <input class="input" type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="아이디, 이름, 이메일, 소속 검색" style="max-width:300px;">
        <button type="submit" class="btn btn-gray">검색</button>
        <?php if ($q): ?>
            <a href="users.php">검색 초기화</a>
        <?php endif; ?>
    </form>

    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>아이디</th>
                    <th>이름</th>
                    <th>이메일</th>
                    <th>권한</th>
                    <th>가입일</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($u['email'] ?? 'N/A') ?></td>
                    <td>
                        <?php if ($u['role'] === 'admin'): ?>
                            <span class="badge on">관리자</span>
                        <?php else: ?>
                            <span class="badge">학생</span>
                        <?php endif; ?>
                    </td>
                    <td><?= substr($u['created_at'], 0, 10) ?></td>
                    <td>
                        <a href="user_edit.php?id=<?= $u['id'] ?>" class="btn btn-gray" style="padding:4px 8px; font-size:12px;">
                            <i class="fas fa-edit"></i> 수정
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div style="margin-top:20px; text-align:center;">
        <?php for($i=1; $i<=$totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&q=<?= urlencode($q) ?>" class="btn <?= $i==$page ? 'btn-green':'btn-gray' ?>" style="padding:4px 8px;"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>
<?php require __DIR__ . '/_admin_footer.php'; ?>