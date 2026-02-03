<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 로그인한 사용자는 되돌려 보냄
if (isset($_SESSION['user_id'])) {
    header('Location: ' . (BASE_URL ?: '/'));
    exit;
}

require_once __DIR__ . '/db_conn.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 입력 값 받기
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $company = trim($_POST['company'] ?? '');

    // 기본 유효성 검사
    if (empty($username) || empty($password) || empty($name) || empty($email)) {
        $error = '필수 항목(아이디, 비밀번호, 이름, 이메일)을 모두 입력하세요.';
    } elseif ($password !== $password2) {
        $error = '비밀번호가 일치하지 않습니다.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '유효하지 않은 이메일 주소입니다.';
    } else {
        // 아이디 및 이메일 중복 검사
        $stmt = db()->prepare("SELECT 1 FROM uedu_users WHERE username=? OR email=?");
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            $error = '이미 사용 중인 아이디 또는 이메일입니다.';
        } else {
            // DB에 사용자 정보 저장
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = db()->prepare("
                INSERT INTO uedu_users (username, password, name, birthdate, phone, email, company, role, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'student', NOW())
            ");
            $stmt->execute([$username, $hash, $name, $birthdate ?: null, $phone, $email, $company]);
            
            $success = '회원가입이 완료되었습니다! 로그인 페이지로 이동합니다.';
            header('Refresh: 2; url=login.php');
        }
    }
}

// 헤더 포함
require __DIR__ . '/header_static.php';
?>

<div class="login-page">
    <div class="login-form__wrapper">
        <div class="login-form__logo">
            <a href="<?= BASE_URL ?: '/' ?>">UEDU</a>
        </div>
        <h2 class="login-form__title">회원가입</h2>
        <p class="login-form__subtitle">UEDU에 오신 것을 환영합니다. 모든 정보를 입력해주세요.</p>

        <?php if ($error): ?>
            <div class="login-form__error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background-color: #D1FAE5; color: #065F46; border-radius: 8px; padding: 16px; margin-bottom: 24px; font-size: 14px; text-align: center;">
                <i class="fa-solid fa-circle-check"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php else: ?>
            <form method="POST" novalidate>
                 <div class="form-group">
                    <label for="name" class="form-label">이름 <span style="color:red;">*</span></label>
                    <div class="form-input-group">
                        <i class="fa-solid fa-user-pen"></i>
                        <input type="text" id="name" name="name" class="form-control" placeholder="이름" required>
                    </div>
                </div>
                 <div class="form-group">
                    <label for="email" class="form-label">이메일 <span style="color:red;">*</span></label>
                    <div class="form-input-group">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="이메일 주소" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="username" class="form-label">아이디 <span style="color:red;">*</span></label>
                    <div class="form-input-group">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="사용할 아이디" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">비밀번호 <span style="color:red;">*</span></label>
                    <div class="form-input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="비밀번호" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password2" class="form-label">비밀번호 확인 <span style="color:red;">*</span></label>
                    <div class="form-input-group">
                        <i class="fa-solid fa-check-double"></i>
                        <input type="password" id="password2" name="password2" class="form-control" placeholder="비밀번호 재입력" required>
                    </div>
                </div>
                <hr style="border:0; border-top:1px solid #e5e7eb; margin: 25px 0;">
                <div class="form-group">
                    <label for="birthdate" class="form-label">생년월일</label>
                    <div class="form-input-group">
                        <i class="fa-solid fa-calendar-days"></i>
                        <input type="date" id="birthdate" name="birthdate" class="form-control" placeholder="YYYY-MM-DD">
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone" class="form-label">휴대폰 번호</label>
                    <div class="form-input-group">
                        <i class="fa-solid fa-phone"></i>
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="010-1234-5678">
                    </div>
                </div>
                <div class="form-group">
                    <label for="company" class="form-label">소속(사업장)</label>
                    <div class="form-input-group">
                        <i class="fa-solid fa-building"></i>
                        <input type="text" id="company" name="company" class="form-control" placeholder="소속 또는 사업장 이름">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 32px;">회원가입</button>
            </form>
        <?php endif; ?>

        <div class="login-form__footer">
            이미 계정이 있으신가요? <a href="<?= BASE_URL ?>/login.php">로그인</a>
        </div>
    </div>
</div>

<?php
// 푸터 포함
require __DIR__ . '/layout_footer.php';
?>
