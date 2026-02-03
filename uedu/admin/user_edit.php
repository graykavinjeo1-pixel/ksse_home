<?php
require __DIR__ . '/_admin_guard.php';
require __DIR__ . '/_admin_header.php';

$user_id = intval($_GET['id'] ?? 0);

if ($user_id <= 0) {
    die("잘못된 접근입니다.");
}

// 사용자 정보 조회
$stmt = db()->prepare("SELECT * FROM uedu_users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("존재하지 않는 회원입니다.");
}

// 회원 정보 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $role = $_POST['role'];
    
    $sql = "UPDATE uedu_users SET name=?, email=?, phone=?, birthdate=?, company=?, role=? WHERE id=?";
    $args = [$name, $email, $phone, $birthdate ?: null, $company, $role, $user_id];
    
    // 비밀번호 변경 요청이 있을 경우
    if (!empty($_POST['new_password'])) {
        $sql = "UPDATE uedu_users SET name=?, email=?, phone=?, birthdate=?, company=?, role=?, password=? WHERE id=?";
        $args = [$name, $email, $phone, $birthdate ?: null, $company, $role, password_hash($_POST['new_password'], PASSWORD_DEFAULT), $user_id];
    }
    
    db()->prepare($sql)->execute($args);
    
    // 수정 후 목록 페이지로 리디렉션
    header("Location: users.php?q=" . urlencode($user['username']));
    exit;
}
?>

<div class="admin-card">
    <h3 style="margin-top:0;">회원 정보 수정 (<?= htmlspecialchars($user['name'] ?: $user['username']) ?>)</h3>
    
    <form method="POST" class="form-grid">
        <div class="form-group">
            <label for="username">아이디</label>
            <input type="text" id="username" class="input" value="<?= htmlspecialchars($user['username']) ?>" readonly disabled>
            <small class="muted">아이디는 변경할 수 없습니다.</small>
        </div>
        
        <div class="form-group">
            <label for="name">이름</label>
            <input type="text" id="name" name="name" class="input" value="<?= htmlspecialchars($user['name'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="email">이메일</label>
            <input type="email" id="email" name="email" class="input" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="phone">연락처</label>
            <input type="text" id="phone" name="phone" class="input" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="birthdate">생년월일</label>
            <input type="date" id="birthdate" name="birthdate" class="input" value="<?= htmlspecialchars($user['birthdate'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="company">소속(사업장)</label>
            <input type="text" id="company" name="company" class="input" value="<?= htmlspecialchars($user['company'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="role">권한</label>
            <select id="role" name="role" class="input">
                <option value="student" <?= ($user['role'] ?? '') === 'student' ? 'selected' : '' ?>>학생</option>
                <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>관리자</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="new_password">비밀번호 변경</label>
            <input type="password" id="new_password" name="new_password" class="input" placeholder="새 비밀번호를 입력할 경우에만 변경됩니다.">
        </div>
        
        <div class="form-actions">
            <a href="users.php" class="btn btn-gray">목록으로</a>
            <button type="submit" class="btn btn-green">저장</button>
        </div>
    </form>
</div>

<style>
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 30px; }
.form-group { display: flex; flex-direction: column; }
.form-group label { font-weight: 500; margin-bottom: 8px; }
.form-actions { grid-column: 2 / -1; text-align: right; }
@media (max-width: 768px) {
    .form-grid { grid-template-columns: 1fr; }
    .form-actions { grid-column: 1; }
}
</style>

<?php require __DIR__ . '/_admin_footer.php'; ?>
