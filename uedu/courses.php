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
$stmt = db()->prepare("SELECT id, title, description, price, thumbnail FROM uedu_courses $where ORDER BY id DESC");
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

<div class="container" style="margin-top: 120px;">
    <div class="section-header">
        <span class="section-tag">Courses</span>
        <h1 class="section-title">교육과정 신청</h1>
        <p class="section-subtitle">다양한 강좌를 탐색하고 지금 바로 학습을 시작하세요.</p>
        
        <form method="GET" class="page-search-form">
            <input type="text" name="keyword" class="form-control" value="<?= htmlspecialchars($keyword) ?>" placeholder="관심 있는 강좌를 검색해보세요...">
            <button type="submit" class="btn btn-primary">검색</button>
        </form>
    </div>

    <?php if (empty($courses)): ?>
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <h3>'<?= htmlspecialchars($keyword) ?>'에 대한 검색 결과가 없습니다.</h3>
            <p>다른 검색어를 입력하시거나, 전체 강좌 목록을 확인해보세요.</p>
            <a href="<?= BASE_URL ?>/courses.php" class="btn btn-secondary">모든 강좌 보기</a>
        </div>
    <?php else: ?>
        <div class="course-grid">
            <?php foreach ($courses as $c): 
                $thumbnail_url = !empty($c['thumbnail']) ? $c['thumbnail'] : 'https://source.unsplash.com/random/500x300?skill,learn&sig=' . $c['id'];
                $short_desc = $c['description'];
            ?>
                <div class="course-card">
                    <div class="card-img-wrap">
                        <img src="<?= htmlspecialchars($thumbnail_url) ?>" alt="<?= htmlspecialchars($c['title']) ?>">
                    </div>
                    
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($c['title']) ?></h3>
                        <p class="card-text"><?= nl2br(htmlspecialchars($short_desc ?? '')) ?></p>
                        
                        <div class="card-footer">
                            <span class="card-price">
                                <?= intval($c['price']) > 0 ? number_format($c['price']).'원' : '무료' ?>
                            </span>

                            <?php if ($loggedIn):
                                $status = $myOrders[$c['id']] ?? null;
                                if ($status === 'paid'): ?>
                                    <a href="classroom.php?course_id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm">강의실 입장</a>
                                <?php elseif ($status === 'pending'): ?>
                                    <span class="btn btn-secondary btn-sm disabled">신청 확인중</span>
                                <?php else: ?>
                                    <a href="enroll.php?course_id=<?= $c['id'] ?>" class="btn btn-primary btn-sm">수강 신청</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php" onclick="return confirm('로그인이 필요합니다.');" class="btn btn-primary btn-sm">수강 신청</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/layout_footer.php'; ?>

<style>
/* Page-specific styles for courses.php */
.page-search-form {
    display: flex;
    gap: 10px;
    max-width: 600px;
    margin: 40px auto 0;
}
.page-search-form .form-control {
    flex-grow: 1;
    height: 48px;
    padding-left: 20px;
}
.empty-state {
    text-align: center;
    padding: 80px 40px;
    background-color: #fff;
    border-radius: var(--card-radius);
    border: 1px solid var(--border-color);
}
.empty-state i {
    font-size: 40px;
    color: var(--primary-color);
    margin-bottom: 24px;
}
.empty-state h3 {
    font-size: 1.5rem;
    color: var(--text-heading);
}
.empty-state p {
    color: var(--text-muted);
    margin-bottom: 24px;
}
.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: 16px;
    border-top: 1px solid var(--border-color);
}
.btn.disabled {
    cursor: not-allowed;
    opacity: 0.7;
}
</style>
