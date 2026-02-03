<?php
require __DIR__ . '/header_auth.php';
require_once __DIR__ . '/db_conn.php';

$user_id = $_SESSION['user_id'];
$course_id = intval($_GET['course_id'] ?? 0);

if ($course_id <= 0) {
    header('Location: ' . BASE_URL . '/courses.php');
    exit;
}

$stmt = db()->prepare("
    SELECT id, title, price
    FROM uedu_courses
    WHERE id=? AND is_active = 1
    LIMIT 1
");
$stmt->execute([$course_id]);
$course = $stmt->fetch();
if (!$course) {
    header('Location: ' . BASE_URL . '/courses.php');
    exit;
}

/* 이미 수강 중인지 확인 */
$stmt = db()->prepare("
    SELECT id, status
    FROM uedu_orders
    WHERE user_id=? AND course_id=?
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute([$user_id, $course_id]);
$order = $stmt->fetch();
if ($order && $order['status'] === 'paid') {
    header('Location: ' . BASE_URL . '/classroom.php?course_id=' . $course_id);
    exit;
}

$price = intval($course['price']);

/* 무료 수강신청 = 즉시 완료 */
if ($price <= 0) {
    if (!$order) {
        $stmt = db()->prepare("
            INSERT INTO uedu_orders (user_id, course_id, amount, status, created_at)
            VALUES (?, ?, 0, 'paid', NOW())
        ");
        $stmt->execute([$user_id, $course_id]);
    }
    header('Location: ' . BASE_URL . '/myroom.php');
    exit;
}

/* 유료 과정: 입금대기 주문 생성 */
if (!$order) {
    $stmt = db()->prepare("
        INSERT INTO uedu_orders (user_id, course_id, amount, status, payment_key, created_at)
        VALUES (?, ?, ?, 'pending', ?, NOW())
    ");
    $stmt->execute([$user_id, $course_id, $price, 'BANK_' . time()]);
}
?>

<div class="container enroll-page">
    <div class="enroll-card">
        <div class="enroll-header">
            <h2 class="page-title">수강신청 - 입금 안내</h2>
            <p>아래 계좌로 입금하시면 관리자 확인 후 수강이 시작됩니다.</p>
        </div>

        <div class="enroll-body">
            <h3 class="course-title"><?= htmlspecialchars($course['title']) ?></h3>

            <div class="payment-info-box">
                <div class="info-item">
                    <span class="label">입금 계좌</span>
                    <span class="value">국민은행 123-456-789012 (예금주: UEDU)</span>
                </div>
                <div class="info-item">
                    <span class="label">입금 금액</span>
                    <span class="value price"><?= number_format($price) ?>원</span>
                </div>
                <div class="info-item">
                    <span class="label">현재 상태</span>
                    <span class="value"><span class="badge status-pending">입금대기</span></span>
                </div>
            </div>
        </div>

        <div class="enroll-footer">
            <a class="btn btn-secondary" href="<?= BASE_URL ?>/myroom.php">나의강의실로</a>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/courses.php">다른 과정 보기</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/layout_footer.php'; ?>
