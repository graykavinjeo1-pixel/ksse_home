<?php
require __DIR__ . '/_admin_guard.php';
require __DIR__ . '/_admin_header.php';
require_once __DIR__ . '/../db_conn.php';

// 상태 변경(is_active) 처리
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $course_id = intval($_GET['id']);
    
    // 현재 상태 조회
    $stmt = db()->prepare("SELECT is_active FROM uedu_courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $current_status = $stmt->fetchColumn();
    
    // 상태 변경
    if ($current_status !== false) {
        $new_status = $current_status ? 0 : 1;
        $stmt = db()->prepare("UPDATE uedu_courses SET is_active = ? WHERE id = ?");
        $stmt->execute([$new_status, $course_id]);
    }
    
    header('Location: courses.php');
    exit;
}

// 등록된 모든 과정 조회
$stmt = db()->query("
    SELECT * FROM uedu_courses 
    ORDER BY id DESC
");
$courses = $stmt->fetchAll();
?>

<style>
.toggle-switch {
    position: relative; display: inline-block;
    width: 50px; height: 24px;
}
.toggle-switch input { display: none; }
.toggle-slider {
    position: absolute; cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #ccc;
    border-radius: 24px;
    transition: .4s;
}
.toggle-slider:before {
    position: absolute; content: "";
    height: 16px; width: 16px;
    left: 4px; bottom: 4px;
    background-color: white;
    border-radius: 50%;
    transition: .4s;
}
input:checked + .toggle-slider { background-color: var(--primary-color); }
input:checked + .toggle-slider:before { transform: translateX(26px); }
</style>

<div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h3 style="margin:0;">교육과정 관리</h3>
        <a href="course_edit.php" class="btn btn-green"><i class="fas fa-plus"></i> 과정 등록</a>
    </div>

    <?php if (empty($courses)): ?>
        <p class="muted" style="padding:40px; text-align:center;">
            등록된 과정이 없습니다. 우측 상단의 '+ 과정 등록' 버튼을 눌러보세요.
        </p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:60px;">ID</th>
                    <th>과정명</th>
                    <th>수강료</th>
                    <th>옵션</th>
                    <th style="width:80px;">노출</th>
                    <th style="width:150px;">등록일</th>
                    <th style="width:140px;">관리</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($courses as $c): ?>
                <tr>
                    <td><?= intval($c['id']) ?></td>
                    <td>
                        <strong><?= htmlspecialchars($c['title']) ?></strong>
                        <div class="muted" style="font-size:12px; margin-top:4px;">
                            <?= mb_strimwidth(htmlspecialchars($c['description'] ?? ''), 0, 50, '...') ?>
                        </div>
                    </td>
                    <td>
                        <?= intval($c['price']) == 0 ? '무료' : number_format($c['price']).'원' ?>
                    </td>
                    <td>
                        <?php if(intval($c['sequential_learning'] ?? 0)): ?>
                            <span class="badge on">순차학습</span>
                        <?php endif; ?>
                        <?php if(intval($c['prevent_skip'] ?? 0)): ?>
                            <span class="badge on">스킵방지</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?toggle_status=1&id=<?= $c['id'] ?>">
                            <label class="toggle-switch">
                                <input type="checkbox" <?= $c['is_active'] ? 'checked' : '' ?> disabled>
                                <span class="toggle-slider"></span>
                            </label>
                        </a>
                    </td>
                    <td><?= substr($c['created_at'], 0, 10) ?></td>
                    <td>
                        <a href="course_edit.php?id=<?= $c['id'] ?>" class="btn btn-gray" style="padding:4px 8px; font-size:12px;"><i class="fas fa-edit"></i> 수정</a>
                        <a href="course_delete.php?id=<?= $c['id'] ?>" 
                           class="btn btn-red" 
                           style="padding:4px 8px; font-size:12px;"
                           onclick="return confirm('정말 삭제하시겠습니까? \n삭제 시 관련 수강생 데이터가 꼬일 수 있습니다.')"><i class="fas fa-trash"></i> 삭제</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/_admin_footer.php'; ?>