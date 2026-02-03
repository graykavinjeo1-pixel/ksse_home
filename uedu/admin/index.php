<?php
require __DIR__ . '/_admin_guard.php';
require __DIR__ . '/_admin_header.php';

// 1. 핵심 지표
$totalUsers = db()->query("SELECT COUNT(*) FROM uedu_users")->fetchColumn();
$totalCourses = db()->query("SELECT COUNT(*) FROM uedu_courses")->fetchColumn();
$totalOrders = db()->query("SELECT COUNT(*) FROM uedu_orders WHERE status = 'paid'")->fetchColumn();

// 2. 답변 대기중인 Q&A (예외 처리 포함)
$pendingQnaCount = 0;
try {
    // reply_content 컬럼이 존재한다고 가정
    $pendingQnaCount = db()->query("SELECT COUNT(*) FROM uedu_boards WHERE type='qna' AND (reply_content IS NULL OR reply_content = '')")->fetchColumn();
} catch (PDOException $e) {
    // 컬럼이 없는 경우 등 쿼리 실패 시, 오류를 기록하고 0으로 계속 진행
    error_log('Admin Dashboard Q&A Count Error: ' . $e->getMessage());
}

// 3. 최근 주문 5건
$recentOrders = db()->query("
    SELECT o.id, o.status, c.title as course_title, u.name as user_name, o.created_at
    FROM uedu_orders o
    JOIN uedu_courses c ON o.course_id = c.id
    JOIN uedu_users u ON o.user_id = u.id
    ORDER BY o.id DESC
    LIMIT 5
")->fetchAll();

// 4. 최근 가입 회원 5건
$recentUsers = db()->query("
    SELECT id, name, username, created_at
    FROM uedu_users
    ORDER BY id DESC
    LIMIT 5
")->fetchAll();

?>

<style>
.dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; }
.stat-card { background: #fff; border-radius: 8px; padding: 24px; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 20px; }
.stat-card-icon { font-size: 32px; color: var(--primary-color); width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: #eef2ff; border-radius: 50%; }
.stat-card-info .label { font-size: 14px; color: #666; margin-bottom: 4px; }
.stat-card-info .value { font-size: 28px; font-weight: 700; color: #111; }
.stat-card-info .value sup { font-size: 14px; font-weight: 500; color: var(--primary-dark); margin-left: 5px; }
.list-card .list-header { font-weight: 600; padding-bottom: 10px; border-bottom: 1px solid #eee; margin-bottom: 10px; }
.list-card .list-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 4px; font-size: 14px; border-bottom: 1px solid #f9f9f9; }
.list-card .list-item:last-child { border-bottom: none; }
.list-card .card-footer { margin-top: 16px; text-align: right; }
.status-badge { padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500; }
.status-paid { background: #e8f5e9; color: #2e7d32; }
.status-pending { background: #fffde7; color: #f57f17; }
</style>

<div class="dashboard-grid">
    <!-- 핵심 지표 -->
    <div class="stat-card">
        <div class="stat-card-icon"><i class="fas fa-users"></i></div>
        <div class="stat-card-info">
            <div class="label">총 회원 수</div>
            <div class="value"><?= number_format($totalUsers) ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon"><i class="fas fa-book"></i></div>
        <div class="stat-card-info">
            <div class="label">총 강좌 수</div>
            <div class="value"><?= number_format($totalCourses) ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-card-info">
            <div class="label">누적 수강 건수</div>
            <div class="value"><?= number_format($totalOrders) ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon"><i class="fas fa-question-circle"></i></div>
        <div class="stat-card-info">
            <div class="label">답변 대기중인 문의</div>
            <div class="value"><?= number_format($pendingQnaCount) ?> <sup>건</sup></div>
        </div>
    </div>
</div>

<div class="dashboard-grid" style="margin-top: 24px; grid-template-columns: 1fr 1fr;">
    <!-- 최근 주문 -->
    <div class="admin-card list-card">
        <div class="list-header">최근 주문</div>
        <?php foreach($recentOrders as $order): ?>
            <div class="list-item">
                <div>
                    <strong><?= htmlspecialchars($order['user_name']) ?></strong>
                    <div class="muted" style="font-size: 12px;"><?= htmlspecialchars($order['course_title']) ?></div>
                </div>
                <div>
                    <span class="status-badge status-<?= $order['status'] ?>"><?= $order['status'] === 'paid' ? '결제완료' : '입금대기' ?></span>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="card-footer">
            <a href="orders.php" class="btn btn-gray">주문 관리 바로가기 &rarr;</a>
        </div>
    </div>

    <!-- 최근 가입 회원 -->
    <div class="admin-card list-card">
        <div class="list-header">최근 가입 회원</div>
        <?php foreach($recentUsers as $user): ?>
            <div class="list-item">
                <div>
                    <strong><?= htmlspecialchars($user['name'] ?: $user['username']) ?></strong>
                    <div class="muted" style="font-size: 12px;"><?= substr($user['created_at'], 0, 10) ?> 가입</div>
                </div>
                <a href="users.php?q=<?= urlencode($user['username']) ?>" class="btn btn-gray" style="padding: 4px 8px; font-size: 12px;">정보 보기</a>
            </div>
        <?php endforeach; ?>
        <div class="card-footer">
            <a href="users.php" class="btn btn-gray">회원 관리 바로가기 &rarr;</a>
        </div>
    </div>
</div>
