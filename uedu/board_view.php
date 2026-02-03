<?php
/*********************************************************
 * 1. 기본 설정 및 DB, 헤더 로딩
 *********************************************************/
$type = $_GET['type'] ?? 'notice';
$allowed = ['notice', 'faq', 'qna', 'bill'];
if (!in_array($type, $allowed)) $type = 'notice';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: board.php?type={$type}");
    exit;
}

if (in_array($type, ['notice', 'faq'])) {
    require __DIR__ . '/header_static.php';
} else {
    require __DIR__ . '/header_auth.php';
}
require_once __DIR__ . '/db_conn.php';

/*********************************************************
 * 2. 게시글 조회
 *********************************************************/
$stmt = db()->prepare("
    SELECT b.*, u.username, u.name
    FROM uedu_boards b
    LEFT JOIN uedu_users u ON b.user_id = u.id
    WHERE b.id = ? AND b.type = ?
    LIMIT 1
");
$stmt->execute([$id, $type]);
$post = $stmt->fetch();

if (!$post) {
    header("Location: board.php?type={$type}");
    exit;
}

/*********************************************************
 * 3. 접근 권한 확인
 *********************************************************/
$is_author = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id'];
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$is_secret = !empty($post['password']);
$has_access = false;
$password_error = '';

// 1:1 문의는 본인과 관리자만 접근 가능 (암호 여부와 무관)
if ($type === 'qna' && !$is_author && !$is_admin) {
    echo "<div class='container'><p>글을 볼 권한이 없습니다.</p><a href='board.php?type={$type}' class='btn'>목록으로</a></div>";
    require __DIR__ . '/layout_footer.php';
    exit;
}

// 암호 글 접근 로직
if (!$is_secret) {
    $has_access = true;
} else {
    if ($is_author || $is_admin) {
        $has_access = true;
    }
    // 세션에 암호 통과 정보 확인
    if (isset($_SESSION['unlocked_posts']) && in_array($id, $_SESSION['unlocked_posts'])) {
        $has_access = true;
    }
}

// 암호 제출 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_password'])) {
    if (password_verify($_POST['post_password'], $post['password'])) {
        $_SESSION['unlocked_posts'][] = $id;
        $has_access = true;
    } else {
        $password_error = "암호가 올바르지 않습니다.";
    }
}
?>

<div class="container">
    <h2 class="page-title"><?= htmlspecialchars($post['title']) ?></h2>

    <?php if ($has_access): ?>
        
        <!-- 게시글 내용 -->
        <div class="board-view-card">
            <div class="card-header">
                <div class="author-info">
                    <strong>작성자:</strong> <?= htmlspecialchars($post['name'] ?: $post['username']) ?>
                </div>
                <div class="date-info">
                    <strong>등록일:</strong> <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?>
                </div>
            </div>
            <div class="card-body">
                <div class="post-content">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </div>

                <?php if (!empty($post['filepath']) && !empty($post['filename'])): ?>
                <div class="post-attachment">
                    <strong>첨부파일:</strong>
                    <a href="<?= htmlspecialchars($post['filepath']) ?>" download><?= htmlspecialchars($post['filename']) ?></a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 관리자 답변 -->
        <?php if (!empty($post['answer'])): ?>
        <div class="admin-answer-box">
            <div class="answer-header"><span class="admin-badge">관리자 답변</span></div>
            <div class="answer-content"><?= nl2br(htmlspecialchars($post['answer'])) ?></div>
        </div>
        <?php endif; ?>

        <!-- 버튼 -->
        <div class="form-actions" style="margin-top:24px;">
            <a href="board.php?type=<?= $type ?>" class="btn btn-secondary">목록으로</a>
            <?php if ($is_author || $is_admin): ?>
                <a href="board_edit.php?type=<?= $type ?>&id=<?= $id ?>" class="btn btn-secondary">수정</a>
                <a href="board_delete.php?type=<?= $type ?>&id=<?= $id ?>" class="btn btn-danger">삭제</a>
            <?php endif; ?>
        </div>

    <?php else: ?>

        <!-- 암호 입력 폼 -->
        <div class="password-prompt-card">
            <form method="POST" action="board_view.php?type=<?= $type ?>&id=<?= $id ?>">
                <h4>🔒 비밀글입니다</h4>
                <p>글을 확인하려면 암호를 입력하세요.</p>
                
                <?php if ($password_error): ?>
                    <div class="login-form__error" style="margin-bottom:15px;"><?= $password_error ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="post_password" class="form-label">암호</label>
                    <input type="password" name="post_password" id="post_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">확인</button>
            </form>
        </div>

    <?php endif; ?>
</div>

<?php require __DIR__ . '/layout_footer.php'; ?>
