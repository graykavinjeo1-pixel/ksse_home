<?php
/*********************************************************
 * 1. 기초 설정 및 세션 (출력 없음)
 *********************************************************/
$type = $_GET['type'] ?? 'notice';
$allowed = ['notice', 'faq', 'qna', 'bill'];
if (!in_array($type, $allowed)) {
    $type = 'notice';
}

$type_names = [
    'notice' => '공지사항',
    'faq'    => 'FAQ',
    'qna'    => '1:1문의',
    'bill'   => '계산서요청'
];
$type_name = $type_names[$type];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*********************************************************
 * 2. DB 로딩 (출력 없음)
 *********************************************************/
require_once __DIR__ . '/db_conn.php';

/*********************************************************
 * 3. 게시글 저장 처리 (★헤더 출력 전 수행 필수)
 *********************************************************/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['save_board']) &&
    in_array($type, ['qna', 'bill']) &&
    isset($_SESSION['user_id'])
) {
    $filename = null;
    $filepath = null;

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/assets/uploads/board/';
        $filename = basename($_FILES['attachment']['name']);
        
        $safeFilename = preg_replace("/[^A-Za-z0-9\._-]/", '', $filename);
        $extension = pathinfo($safeFilename, PATHINFO_EXTENSION);
        $basename = pathinfo($safeFilename, PATHINFO_FILENAME);
        $uniqueName = $basename . '_' . time() . '.' . $extension;
        
        $filepath = $uploadDir . $uniqueName;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $filepath)) {
            $filepath = '/assets/uploads/board/' . $uniqueName;
        } else {
            $filename = null;
            $filepath = null;
        }
    }

    $password_hash = null;
    if (!empty($_POST['password'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    $stmt = db()->prepare("
        INSERT INTO uedu_boards
        (type, title, content, user_id, created_at, filename, filepath, password)
        VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)
    ");
    $stmt->execute([
        $type,
        $_POST['title'],
        $_POST['content'],
        $_SESSION['user_id'],
        $filename,
        $filepath,
        $password_hash
    ]);

    // 이제 헤더가 전송되기 전이므로 리다이렉트가 정상 작동합니다.
    header("Location: board.php?type={$type}");
    exit;
}

/*********************************************************
 * 4. FAQ 캐시 및 헤더 출력 (여기서부터 HTML 시작)
 *********************************************************/
if ($type === 'faq' && !isset($_SESSION['user_id'])) {
    $cacheFile = __DIR__ . '/cache/faq.html';
    if (is_file($cacheFile)) {
        readfile($cacheFile);
        exit;
    }
    ob_start();
}

if (in_array($type, ['notice', 'faq'])) {
    require __DIR__ . '/header_static.php';
} else {
    require __DIR__ . '/header_auth.php';
}

/*********************************************************
 * 5. 목록 데이터 조회
 *********************************************************/
$page = intval($_GET['page'] ?? 1);
$search = $_GET['search'] ?? '';
$limit = 15;
$offset = ($page - 1) * $limit;

$whereSql = "WHERE b.type = ?";
$params = [$type];
if ($search) {
    $whereSql .= " AND (b.title LIKE ? OR b.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$countStmt = db()->prepare("SELECT COUNT(*) FROM uedu_boards b $whereSql");
$countStmt->execute($params);
$totalCount = $countStmt->fetchColumn();
$totalPages = ceil($totalCount / $limit);

$sql = "
    SELECT b.*, u.username, u.name
    FROM uedu_boards b
    LEFT JOIN uedu_users u ON b.user_id = u.id
    $whereSql
    ORDER BY b.id DESC
    LIMIT $limit OFFSET $offset
";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$list = $stmt->fetchAll();
?>

<div class="container">
    <h2 class="page-title">고객센터 - <?= htmlspecialchars($type_name) ?></h2>
    
    <div class="board-actions">
        <form method="GET" class="search-form">
            <input type="hidden" name="type" value="<?= $type ?>">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="검색어 입력" class="form-control" style="width:200px; display:inline-block;">
            <button class="btn btn-secondary">검색</button>
        </form>
        <?php if (in_array($type, ['qna', 'bill'])): ?>
            <a href="board_write.php?type=<?= $type ?>" class="btn btn-primary">글쓰기</a>
        <?php endif; ?>
    </div>

    <table class="board-table">
        <thead>
            <tr>
                <th style="width:10%;">번호</th>
                <th>제목</th>
                <th style="width:15%;">작성자</th>
                <th style="width:15%;">등록일</th>
            </tr>
        </thead>
        <tbody>
        <?php if(empty($list)): ?>
            <tr><td colspan="4" style="text-align:center; padding:30px;">게시글이 없습니다.</td></tr>
        <?php else: ?>
            <?php foreach($list as $row): ?>
            <tr>
                <td class="post-id"><?= htmlspecialchars($row['id']) ?></td>
                <td class="post-title">
                    <a href="board_view.php?type=<?= $type ?>&id=<?= $row['id'] ?>">
                        <?= htmlspecialchars($row['title']) ?>
                        <?php if (!empty($row['password'])): ?>
                            <span class="lock-icon">🔒</span>
                        <?php endif; ?>
                        <?php if($row['created_at'] > date('Y-m-d H:i:s', strtotime('-1 day'))): ?>
                            <span class="new-post-badge">N</span>
                        <?php endif; ?>
                    </a>
                </td>
                <td class="post-author"><?= htmlspecialchars($row['name'] ?: $row['username']) ?></td>
                <td class="post-date"><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="text-align:center; margin-top:20px;">
        <?php for($i=1; $i<=$totalPages; $i++): ?>
            <a href="?type=<?= $type ?>&page=<?= $i ?>&search=<?= urlencode($search) ?>" 
               class="btn <?= $i==$page ? 'btn-green':'btn-gray' ?>" style="padding:5px 10px;"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>

<?php
require __DIR__ . '/layout_footer.php';

// FAQ 캐시 저장 마무리
if ($type === 'faq' && isset($cacheFile)) {
    file_put_contents($cacheFile, ob_get_contents());
    ob_end_flush();
}