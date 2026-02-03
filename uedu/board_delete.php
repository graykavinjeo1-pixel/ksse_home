<?php
/*********************************************************
 * 1. 기본 설정 및 DB, 헤더 로딩
 *********************************************************/
$type = $_GET['type'] ?? 'qna';
$allowed = ['qna', 'bill'];
if (!in_array($type, $allowed)) {
    header("Location: board.php?type=notice");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: board.php?type={$type}");
    exit;
}

require __DIR__ . '/header_auth.php';
require_once __DIR__ . '/db_conn.php';

/*********************************************************
 * 2. 게시글 조회 및 권한 확인
 *********************************************************/
$stmt = db()->prepare("SELECT * FROM uedu_boards WHERE id = ? AND type = ?");
$stmt->execute([$id, $type]);
$post = $stmt->fetch();

if (!$post) {
    exit('게시글이 없습니다.');
}

$is_author = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id'];
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

if (!$is_author && !$is_admin) {
    echo "<div class='container'><p>글을 삭제할 권한이 없습니다.</p><a href='board.php?type={$type}' class='btn'>목록으로</a></div>";
    require __DIR__ . '/layout_footer.php';
    exit;
}

$error = '';
$needs_password = !empty($post['password']) && $is_author && !$is_admin;

/*********************************************************
 * 3. 삭제 처리 (POST)
 *********************************************************/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // 암호 확인
    if ($needs_password) {
        if (empty($_POST['password']) || !password_verify($_POST['password'], $post['password'])) {
            $error = '암호가 올바르지 않습니다.';
        }
    }

    if (empty($error)) {
        // (첨부파일이 있다면 여기서 삭제하는 로직을 추가할 수 있습니다)
        $stmt = db()->prepare("DELETE FROM uedu_boards WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: board.php?type={$type}");
        exit;
    }
}
?>

<div class="container">
    <h2 class="page-title">게시글 삭제</h2>

    <div class="password-prompt-card">
        <form method="POST" action="board_delete.php?type=<?= $type ?>&id=<?= $id ?>">
            <input type="hidden" name="confirm_delete" value="1">
            
            <h4>'<?= htmlspecialchars($post['title']) ?>'</h4>
            <p>이 게시글을 정말로 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.</p>

            <?php if ($error): ?>
                <div class="login-form__error" style="margin-bottom:15px;"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($needs_password): ?>
            <div class="form-group" style="text-align:left;">
                <label for="password" class="form-label">암호</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <?php endif; ?>

            <div class="form-actions" style="justify-content:center;">
                <a href="board_view.php?type=<?= $type ?>&id=<?= $id ?>" class="btn btn-secondary">취소</a>
                <button type="submit" class="btn btn-danger">삭제하기</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/layout_footer.php'; ?>
