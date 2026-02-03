<?php
/*********************************************************
 * 1. 기본 설정 및 DB, 세션 로딩
 *********************************************************/
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db_conn.php';

/*********************************************************
 * 2. POST 데이터 및 권한 확인
 *********************************************************/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request');
}

$id = intval($_POST['id'] ?? 0);
$type = $_POST['type'] ?? 'qna';
$allowed = ['qna', 'bill'];
if (!in_array($type, $allowed) || $id <= 0) {
    header("Location: board.php?type=notice");
    exit;
}

$stmt = db()->prepare("SELECT * FROM uedu_boards WHERE id = ? AND type = ?");
$stmt->execute([$id, $type]);
$post = $stmt->fetch();

if (!$post) {
    exit('게시글이 없습니다.');
}

$is_author = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id'];
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

if (!$is_author && !$is_admin) {
    exit('글을 수정할 권한이 없습니다.');
}

// 작성자가 암호글을 수정하는 경우, 현재 암호 확인 (관리자는 예외)
if (!empty($post['password']) && $is_author && !$is_admin) {
    if (empty($_POST['current_password']) || !password_verify($_POST['current_password'], $post['password'])) {
        exit('현재 암호가 올바르지 않습니다.');
    }
}

/*********************************************************
 * 3. 데이터 업데이트
 *********************************************************/
$title = $_POST['title'];
$content = $_POST['content'];
$filename = $post['filename'];
$filepath = $post['filepath'];
$password_hash = $post['password'];

// 새 첨부파일 처리
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    // (기존 파일이 있다면 삭제하는 로직을 추가할 수 있습니다)
    $uploadDir = __DIR__ . '/assets/uploads/board/';
    $filename = basename($_FILES['attachment']['name']);
    $safeFilename = preg_replace("/[^A-Za-z0-9\._-]/", '', $filename);
    $extension = pathinfo($safeFilename, PATHINFO_EXTENSION);
    $basename = pathinfo($safeFilename, PATHINFO_FILENAME);
    $uniqueName = $basename . '_' . time() . '.' . $extension;
    $newFilepath = $uploadDir . $uniqueName;

    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $newFilepath)) {
        $filepath = '/assets/uploads/board/' . $uniqueName;
    } else {
        $filename = $post['filename']; // 실패 시 기존 파일 정보 유지
        $filepath = $post['filepath'];
    }
}

// 새 암호 처리
if (!empty($_POST['password'])) {
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
}

// DB 업데이트
$stmt = db()->prepare("
    UPDATE uedu_boards
    SET title = ?, content = ?, filename = ?, filepath = ?, password = ?
    WHERE id = ?
");
$stmt->execute([$title, $content, $filename, $filepath, $password_hash, $id]);

/*********************************************************
 * 4. 상세 페이지로 리다이렉트
 *********************************************************/
header("Location: board_view.php?type={$type}&id={$id}");
exit;
