<?php
require_once __DIR__ . '/config.php';

// 세션이 없으면 기본 헤더, 있으면 로그인 헤더
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    require __DIR__ . '/header_auth.php';
} else {
    require __DIR__ . '/header_static.php';
}
?>

<div class="hero">
    <div class="container" style="margin:0 auto;">
        <h2>성장의 시작, UEDU</h2>
        <p>
            체계적인 커리큘럼과 실시간 학습 관리로<br>
            당신의 커리어를 한 단계 업그레이드하세요.
        </p>
        <div class="hero-btns">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a class="btn" href="<?= BASE_URL ?>/register.php">무료로 시작하기</a>
                <a class="btn btn-outline" href="<?= BASE_URL ?>/courses.php">강의 둘러보기</a>
            <?php else: ?>
                <a class="btn" href="<?= BASE_URL ?>/myroom.php">나의 강의실</a>
                <a class="btn btn-outline" href="<?= BASE_URL ?>/courses.php">새로운 과정 찾기</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container">
    <div style="text-align:center; margin-bottom:40px;">
        <h3 style="font-size:24px; margin-bottom:10px;">인기 교육 과정</h3>
        <p style="color:#666;">전문가가 엄선한 추천 강의를 만나보세요.</p>
    </div>

    <?php
    require_once __DIR__ . '/db_conn.php';
    
    // [수정] 공개(is_active=1) 상태이면서 + 추천(is_featured=1)인 강의만 조회
    $stmt = db()->query("
        SELECT * FROM uedu_courses 
        WHERE is_active = 1 AND is_featured = 1
        ORDER BY id DESC 
        LIMIT 6
    ");
    $courses = $stmt->fetchAll();
    ?>

    <?php if(empty($courses)): ?>
        <p style="text-align:center; padding:50px; color:#999;">
            현재 추천 강의가 준비 중입니다.
        </p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
            <?php foreach ($courses as $c): ?>
                <div class="board-view" style="padding:24px; cursor:pointer; transition:0.3s;" 
                     onclick="location.href='enroll.php?course_id=<?= $c['id'] ?>'">
                     <h4 style="font-size:18px; margin:0 0 10px;"><?= htmlspecialchars($c['title']) ?></h4>
                     <p style="color:#888; font-size:14px; margin-bottom:15px;">
                        <?= mb_strimwidth(htmlspecialchars($c['description']), 0, 60, '...') ?>
                     </p>
                     <div style="text-align:right; font-weight:bold; color:var(--primary-color);">
                        <?= intval($c['price']) == 0 ? '무료' : number_format($c['price']).'원' ?>
                     </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div style="text-align:center; margin-top:40px;">
        <a href="courses.php" class="btn btn-outline">전체 강의 보기</a>
    </div>
</div>

<?php require __DIR__ . '/layout_footer.php'; ?>