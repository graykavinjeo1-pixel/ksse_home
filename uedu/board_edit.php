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
    header("Location: board.php?type={$type}");
    exit;
}

$is_author = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id'];
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

if (!$is_author && !$is_admin) {
    echo "<div class='container'><p>글을 수정할 권한이 없습니다.</p><a href='board.php?type={$type}' class='btn'>목록으로</a></div>";
    require __DIR__ . '/layout_footer.php';
    exit;
}
?>

<div class="container">
    <h2 class="page-title">게시글 수정</h2>

    <div class="board-write-form">
        <form method="POST" action="board_edit_process.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="type" value="<?= $type ?>">

            <div class="form-group">
                <label for="title" class="form-label">제목</label>
                <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
            </div>

            <div class="form-group">
                <label for="content" class="form-label">내용</label>
                <textarea id="content" name="content" class="form-control form-control-textarea" rows="10" required><?= htmlspecialchars($post['content']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="attachment" class="form-label">첨부파일</label>
                <input type="file" id="attachment" name="attachment" class="form-control">
                <?php if ($post['filename']): ?>
                    <p class="current-file">현재 파일: <?= htmlspecialchars($post['filename']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">암호</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="암호를 변경할 경우에만 입력하세요.">
            </div>

            <?php if (!empty($post['password']) && $is_author && !$is_admin): ?>
            <div class="form-group">
                <label for="current_password" class="form-label">현재 암호</label>
                <input type="password" id="current_password" name="current_password" class="form-control" placeholder="수정하려면 현재 암호를 입력하세요." required>
            </div>
            <?php endif; ?>

            <div class="form-actions">
                <a href="board_view.php?type=<?= $type ?>&id=<?= $id ?>" class="btn btn-secondary">취소</a>
                <button type="submit" class="btn btn-primary">저장하기</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/layout_footer.php'; ?>
