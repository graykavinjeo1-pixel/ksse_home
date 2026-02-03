<?php
// /admin/_admin_header.php
require_once __DIR__ . '/_admin_guard.php'; // guard에서 session start 및 $me 설정

// 캐시 무력화를 위한 파일 수정 시간
$css_version = filemtime(__DIR__ . '/../assets/admin_style.css');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>LMS Admin System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/admin_style.css?v=<?= $css_version ?>">
</head>
<body class="admin-body">

  <aside class="admin-sidebar">
    <div class="admin-logo">
      <a href="/admin/index.php">
        <i class="fas fa-graduation-cap"></i> KSSE LMS
      </a>
    </div>
    <nav class="admin-menu">
      <a href="/admin/index.php"
         class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
         <i class="fas fa-tachometer-alt fa-fw"></i> 대시보드
      </a>
      <a href="/admin/users.php"
         class="<?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
         <i class="fas fa-users fa-fw"></i> 회원 관리
      </a>
      <a href="/admin/courses.php"
         class="<?= in_array(basename($_SERVER['PHP_SELF']), ['courses.php', 'course_edit.php']) ? 'active' : '' ?>">
         <i class="fas fa-book fa-fw"></i> 교육과정 관리
      </a>
      <a href="/admin/contents.php"
         class="<?= basename($_SERVER['PHP_SELF']) == 'contents.php' ? 'active' : '' ?>">
         <i class="fas fa-video fa-fw"></i> 영상 콘텐츠
      </a>
      <a href="/admin/curriculum.php"
         class="<?= basename($_SERVER['PHP_SELF']) == 'curriculum.php' ? 'active' : '' ?>">
         <i class="fas fa-sitemap fa-fw"></i> 커리큘럼 구성
      </a>
      <a href="/admin/exams.php"
         class="<?= basename($_SERVER['PHP_SELF']) == 'exams.php' ? 'active' : '' ?>">
         <i class="fas fa-file-alt fa-fw"></i> 시험/평가
      </a>
      <a href="/admin/orders.php"
         class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">
         <i class="fas fa-shopping-cart fa-fw"></i> 주문/결제
      </a>
      <a href="/admin/completions.php"
         class="<?= basename($_SERVER['PHP_SELF']) == 'completions.php' ? 'active' : '' ?>">
         <i class="fas fa-certificate fa-fw"></i> 수료/발급
      </a>
    </nav>
  </aside>

  <main class="admin-main">
    <header class="admin-header-bar">
      <div style="font-weight:600; font-size:16px; color:#333;">
        <?= htmlspecialchars($me['name'] ?? $me['username']) ?> 관리자님, 환영합니다.
      </div>
      <div>
        <a class="btn btn-gray" href="/index.php" target="_blank"><i class="fas fa-home"></i> 사용자 홈</a>
        <a class="btn btn-gray" href="/logout.php" style="margin-left:8px;"><i class="fas fa-sign-out-alt"></i> 로그아웃</a>
      </div>
    </header>

    <div class="admin-content-wrap">