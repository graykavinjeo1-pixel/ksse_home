<?php
require __DIR__ . '/header_auth.php'; // $user 변수 사용 가능
require_once __DIR__ . '/db_conn.php';

$user_id = $_SESSION['user_id'];

// 수강 중인 과정 목록 (결제 완료)
$stmt = db()->prepare("
    SELECT DISTINCT
        c.id, c.title, c.description, c.thumbnail, c.price
    FROM uedu_orders o
    JOIN uedu_courses c ON c.id = o.course_id
    WHERE o.user_id = ? AND o.status = 'paid'
    ORDER BY c.id DESC
");
$stmt->execute([$user_id]);
$paidCourses = $stmt->fetchAll();

// 입금 대기 중인 과정 목록
$stmt = db()->prepare("
    SELECT DISTINCT
        c.id, c.title, c.description, c.thumbnail, c.price
    FROM uedu_orders o
    JOIN uedu_courses c ON c.id = o.course_id
    WHERE o.user_id = ? AND o.status = 'pending'
    ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$pendingCourses = $stmt->fetchAll();
?>

<div class="container" style="margin-top: 120px;">
    <div class="section-header">
        <span class="section-tag">My Room</span>
        <h1 class="section-title"><?= htmlspecialchars($user['name'] ?: $user['username']) ?>님의 강의실</h1>
        <p class="section-subtitle">수강 신청한 강좌를 확인하고 학습을 시작하세요.</p>
    </div>

    <?php if (empty($paidCourses) && empty($pendingCourses)): ?>
        <div class="empty-state">
            <i class="fas fa-book-open"></i>
            <h3>아직 수강중인 강좌가 없습니다.</h3>
            <p>흥미로운 강좌를 탐색하고 학습 여정을 시작해보세요!</p>
            <a href="<?= BASE_URL ?>/courses.php" class="btn btn-primary">강좌 보러가기</a>
        </div>
    <?php endif; ?>

    <?php if (!empty($paidCourses)): ?>
        <section class="content-section">
            <h2 class="section-title-small">수강중인 과정</h2>
            <div class="course-grid">
                <?php foreach ($paidCourses as $c):
                    $thumbnail_url = !empty($c['thumbnail']) ? BASE_URL . $c['thumbnail'] : 'https://source.unsplash.com/random/500x300?skill,learn&sig=' . $c['id'];
                ?>
                    <div class="course-card">
                        <div class="card-img-wrap">
                            <img src="<?= htmlspecialchars($thumbnail_url) ?>" alt="<?= htmlspecialchars($c['title']) ?>">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($c['title']) ?></h3>
                            <p class="card-text"><?= nl2br(htmlspecialchars($c['description'] ?? '')) ?></p>
                            <div class="card-footer">
                                <span class="card-price-status">학습중</span>
                                <a href="classroom.php?course_id=<?= $c['id'] ?>" class="btn btn-primary btn-sm">강의실 입장</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($pendingCourses)): ?>
        <section class="content-section">
            <h2 class="section-title-small">신청 대기중인 과정</h2>
            <div class="course-grid">
                <?php foreach ($pendingCourses as $c):
                    $thumbnail_url = !empty($c['thumbnail']) ? BASE_URL . $c['thumbnail'] : 'https://source.unsplash.com/random/500x300?cash,money&sig=' . $c['id'];
                ?>
                    <div class="course-card">
                        <div class="card-img-wrap">
                            <img src="<?= htmlspecialchars($thumbnail_url) ?>" alt="<?= htmlspecialchars($c['title']) ?>">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($c['title']) ?></h3>
                            <p class="card-text"><?= nl2br(htmlspecialchars($c['description'] ?? '')) ?></p>
                             <div class="card-footer">
                                <span class="card-price">
                                    <?= intval($c['price']) > 0 ? number_format($c['price']).'원' : '금액 확인중' ?>
                                </span>
                                <span class="btn btn-secondary btn-sm disabled">입금 확인중</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

</div>

<?php require __DIR__ . '/layout_footer.php'; ?>

<style>
/* Page-specific styles for myroom.php */
.section-title-small {
    font-size: 1.75rem;
    color: var(--text-heading);
    margin-bottom: 32px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border-color);
}
.empty-state {
    text-align: center;
    padding: 80px 40px;
    background-color: var(--bg-card);
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
.content-section {
    margin-bottom: 60px;
}
.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border-color);
}
.card-price-status {
    font-weight: 600;
    color: var(--primary-color);
}
.btn.disabled {
    cursor: not-allowed;
    opacity: 0.7;
    background-color: #E2E8F0;
    border-color: #E2E8F0;
    color: #64748B;
}
</style>
