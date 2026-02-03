<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . (BASE_URL ?: '/') . 'login.php');
    exit;
}

session_write_close();
require_once __DIR__ . '/db_conn.php';

$stmt = db()->prepare("SELECT id, username, role, name FROM uedu_users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_start(); 
    session_destroy();
    header('Location: ' . (BASE_URL ?: '/') . 'login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>UEDU Future</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <h1 class="logo">
            <a href="<?= BASE_URL ?: '/' ?>">UEDU</a>
        </h1>
        <nav class="gnb">
            <a href="<?= BASE_URL ?>/courses.php">수강신청</a>
            <a href="<?= BASE_URL ?>/myroom.php">나의강의실</a>
            <a href="<?= BASE_URL ?>/board.php?type=qna">1:1문의</a>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
                <a href="<?= BASE_URL ?>/admin/index.php" class="link-admin">관리자</a>
            <?php endif; ?>
        </nav>
        <div class="header-actions">
            <div class="user-info">
                <strong><?= htmlspecialchars($user['name'] ?: $user['username']) ?></strong>님
            </div>
            <a href="<?= BASE_URL ?>/logout.php" class="btn btn-secondary btn-sm">로그아웃</a>
        </div>
        <button class="mobile-menu-trigger">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<main class="site-content">