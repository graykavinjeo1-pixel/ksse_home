<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_conn.php';

/* 로그인 여부 */
if (isset($_SESSION['user_id'])) {
    require __DIR__ . '/header_auth.php';
    $loggedIn = true;
    $user_id = $_SESSION['user_id'];
} else {
    require __DIR__ . '/header_static.php';
    $loggedIn = false;
    $user_id = 0;
}

/* 검색 필터 */
$keyword = $_GET['keyword'] ?? '';
$where = "WHERE is_active = 1";
$params = [];

if (!empty($keyword)) {
    $where .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

/* 과정 목록 조회 */
$stmt = db()->prepare("
    SELECT id, title, description, price, thumbnail
    FROM uedu_courses
    $where
    ORDER BY id DESC
");
$stmt->execute($params);
$courses = $stmt->fetchAll();

/* 내 신청 내역 조회 (버튼 상태 표시용) */
$myOrders = [];
if ($loggedIn) {
    $stmt = db()->prepare("SELECT course_id, status FROM uedu_orders WHERE user_id=?");
    $stmt->execute([$user_id]);
    foreach ($stmt->fetchAll() as $row) {
        $myOrders[$row['course_id']] = $row['status'];
    }
}
?>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
        <h2 class="page-title" style="margin:0;">교육과정 신청</h2>
        
        <!-- 검색 폼 -->
        <form method="GET" style="display:flex; gap:10px;">
            <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" 
                   placeholder="강의명 검색" 
                   style="padding:10px; border:1px solid #ddd; border-radius:5px; width:200px;">
            <button type="submit" class="btn btn-navy" style="padding:10px 20px;">검색</button>
        </form>
    </div>

    <?php if (empty($courses)): ?>
        <div style="text-align:center; padding:50px; color:#666; background:#f9f9f9; border-radius:10px;">
            <i class="fas fa-exclamation-circle" style="font-size:40px; margin-bottom:15px; display:block; color:#ccc;"></i>
            등록된 강의가 없습니다.
        </div>
    <?php else: ?>
        <div class="course-grid">
            <?php foreach ($courses as $c): ?>
                <div class="course-card">
                    <div class="card-img-wrap">
                        <?php if (!empty($c['thumbnail'])): ?>
                            <img src="<?= htmlspecialchars($c['thumbnail']) ?>" alt="<?= htmlspecialchars($c['title']) ?>">
                        <?php else: ?>
                            <div class="no-img">NO IMAGE</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($c['title']) ?></h3>
                        <div class="card-text">
                            <?= nl2br(htmlspecialchars($c['description'] ?? '')) ?>
                        </div>
                        
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">
                            <span class="card-price">
                                <?= intval($c['price']) > 0 ? number_format($c['price']).'원' : '무료' ?>
                            </span>

                            <?php if ($loggedIn): ?>
                                <?php $status = $myOrders[$c['id']] ?? null; ?>
                                <?php if ($status === 'paid'): ?>
                                    <a href="classroom.php?course_id=<?= $c['id'] ?>" class="btn btn-gray" style="padding:8px 16px; font-size:14px;">강의실</a>
                                <?php elseif ($status === 'pending'): ?>
                                    <a href="enroll.php?course_id=<?= $c['id'] ?>" class="btn btn-gray" style="padding:8px 16px; font-size:14px;">확인중</a>
                                <?php else: ?>
                                    <a href="enroll.php?course_id=<?= $c['id'] ?>" class="btn btn-navy" style="padding:8px 16px; font-size:14px;">신청하기</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php" onclick="return confirm('로그인이 필요합니다.');" class="btn btn-navy" style="padding:8px 16px; font-size:14px;">신청하기</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/layout_footer.php'; ?>
