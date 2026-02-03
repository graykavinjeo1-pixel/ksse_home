<?php
// config.php를 포함하여 BASE_URL과 같은 전역 설정을 사용합니다.
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_conn.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare("
        SELECT id, username, password, name, role
        FROM uedu_users
        WHERE username = ?
        LIMIT 1
    ");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['name']      = $user['name'];
        $_SESSION['role']      = $user['role'];

        // BASE_URL을 사용하여 리다이렉션 경로를 동적으로 생성합니다.
        header("Location: " . BASE_URL . "/myroom.php");
        exit;
    } else {
        $error = '아이디 또는 비밀번호가 올바르지 않습니다.';
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>로그인 - UEDU</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">

<div class="login-form__wrapper">
    <div class="login-form__logo">
        <a href="<?= BASE_URL ?: '/' ?>">UEDU</a>
    </div>
    <h2 class="login-form__title">로그인</h2>
    <p class="login-form__subtitle">서비스를 이용하려면 로그인하세요.</p>

    <?php if ($error): ?>
        <div class="login-form__error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="login-form">
        <div class="form-group">
            <label for="username" class="form-label">아이디</label>
            <div class="form-input-group">
                <i class="fas fa-user"></i>
                <input type="text" id="username" name="username" class="form-control" placeholder="아이디를 입력하세요" required>
            </div>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">비밀번호</label>
            <div class="form-input-group">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" class="form-control" placeholder="비밀번호를 입력하세요" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">로그인</button>
    </form>
    <div class="login-form__footer">
        아직 회원이 아니신가요? <a href="<?= BASE_URL ?>/register.php">회원가입</a>
    </div>
</div>

</body>
</html>