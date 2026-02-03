<?php
/*********************************************************
 * 1. type 정의
 *********************************************************/
$type = $_GET['type'] ?? 'qna';
$allowed = ['qna', 'bill'];
if (!in_array($type, $allowed)) {
    $type = 'qna';
}

/*********************************************************
 * 2. type 이름
 *********************************************************/
$type_names = [
    'qna'    => '1:1문의 작성',
    'bill'   => '계산서 요청'
];
$type_name = $type_names[$type];

/*********************************************************
 * 3. 헤더 (인증 필요)
 *********************************************************/
require __DIR__ . '/header_auth.php';
?>

<div class="container">
    <h2 class="page-title"><?= htmlspecialchars($type_name) ?></h2>

    <div class="board-write-form">
        <form method="POST" action="board.php?type=<?= $type ?>" enctype="multipart/form-data">
            <input type="hidden" name="save_board" value="1">

            <div class="form-group">
                <label for="title" class="form-label">제목</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="content" class="form-label">내용</label>
                <textarea id="content" name="content" class="form-control form-control-textarea" rows="10" required></textarea>
            </div>

            <div class="form-group">
                <label for="attachment" class="form-label">첨부파일</label>
                <input type="file" id="attachment" name="attachment" class="form-control">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">암호</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="비공개로 설정하려면 암호를 입력하세요.">
            </div>

            <div class="form-actions">
                <a href="board.php?type=<?= $type ?>" class="btn btn-secondary">목록으로</a>
                <button type="submit" class="btn btn-primary">저장하기</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/layout_footer.php'; ?>
