<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>UEDU</title>
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
            <a href="<?= BASE_URL ?>/index.php">홈</a>
            <a href="<?= BASE_URL ?>/courses.php">교육과정</a>
            <a href="<?= BASE_URL ?>/board.php?type=notice">공지</a>
            <a href="<?= BASE_URL ?>/board.php?type=faq">FAQ</a>
            <a href="<?= BASE_URL ?>/login.php">로그인</a>
            <a href="<?= BASE_URL ?>/register.php">회원가입</a>
        </nav>
    </div>
</header>

<main class="site-content">
