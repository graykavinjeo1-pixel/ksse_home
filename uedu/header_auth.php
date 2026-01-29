<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

// [핵심] 세션 데이터를 읽었으므로, 즉시 쓰기 잠금을 해제합니다.
// 이후 코드가 실행되는 동안 다른 요청이 대기하지 않게 됩니다.
session_write_close();

require_once __DIR__ . '/db_conn.php';

// DB 조회 (사용자님 의견대로 DB 접속 유지)
$stmt = db()->prepare("
    SELECT id, username, role, name 
    FROM uedu_users
    WHERE id = ?
");
$stmt->execute([$_SESSION['user_id']]); // 이미 읽은 세션 변수는 계속 사용 가능
$user = $stmt->fetch();

if (!$user) {
    // 로그아웃 처리가 필요한 경우 다시 세션을 열어야 함
    session_start(); 
    session_destroy();
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>UEDU Future</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <h1 class="logo">
            <a href="<?= BASE_URL ?>/index.php">UEDU</a>
        </h1>
        <nav class="gnb">
            <a href="<?= BASE_URL ?>/courses.php">수강신청</a>
            <a href="<?= BASE_URL ?>/myroom.php">나의강의실</a>
            <a href="<?= BASE_URL ?>/board.php?type=qna">1:1문의</a>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
                <a href="<?= BASE_URL ?>/admin/index.php" style="color:#ff4444;">관리자</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/logout.php">로그아웃</a>
        </nav>
        <div class="user-info">
            <?= htmlspecialchars($user['name'] ?: $user['username']) ?> 님
        </div>
    </div>
</header>

<main class="site-content">