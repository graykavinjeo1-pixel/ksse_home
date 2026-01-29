<?php
require __DIR__ . '/header_auth.php';
require_once __DIR__ . '/db_conn.php';

$user_id    = $_SESSION['user_id'];
$course_id  = intval($_GET['course_id'] ?? 0);
$content_id = intval($_GET['content_id'] ?? 0);

if ($course_id <= 0) {
    echo "<div class='container'>잘못된 접근입니다.</div>";
    require __DIR__ . '/layout_footer.php';
    exit;
}

/* 수강 권한 확인 */
$stmt = db()->prepare("
    SELECT 1 FROM uedu_orders
    WHERE user_id=? AND course_id=? AND status='paid'
    LIMIT 1
");
$stmt->execute([$user_id, $course_id]);
if (!$stmt->fetchColumn()) {
    echo "<div class='container'>수강 권한이 없습니다.</div>";
    require __DIR__ . '/layout_footer.php';
    exit;
}

/* 과정 정보 가져오기 */
$stmt = db()->prepare("SELECT title, sequential_learning, prevent_skip FROM uedu_courses WHERE id=?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();
if (!$course) {
    echo "<div class='container'>과정이 없습니다.</div>";
    require __DIR__ . '/layout_footer.php';
    exit;
}

/* 전체 커리큘럼 가져오기 (사이드바 및 네비게이션용) */
$stmt = db()->prepare("
    SELECT 
        uc.chapter_order, 
        c.id AS content_id, 
        c.title, 
        c.duration,
        c.video_url,
        IFNULL(p.is_completed, 0) AS is_completed,
        p.last_position
    FROM uedu_curriculum uc
    JOIN uedu_contents c ON c.id = uc.content_id
    LEFT JOIN uedu_progress p 
      ON p.user_id = ? 
     AND p.course_id = ? 
     AND p.content_id = c.id
    WHERE uc.course_id = ?
    ORDER BY uc.chapter_order ASC
");
$stmt->execute([$user_id, $course_id, $course_id]);
$curriculum = $stmt->fetchAll();

/* 현재 콘텐츠 설정 (없으면 첫 번째 강의로) */
$current_content = null;
$prev_content = null;
$next_content = null;

if ($content_id > 0) {
    foreach ($curriculum as $index => $item) {
        if (intval($item['content_id']) === $content_id) {
            $current_content = $item;
            $prev_content = $curriculum[$index - 1] ?? null;
            $next_content = $curriculum[$index + 1] ?? null;
            break;
        }
    }
} else {
    // content_id가 없으면 첫 번째 강의로 설정
    if (!empty($curriculum)) {
        $current_content = $curriculum[0];
        $next_content = $curriculum[1] ?? null;
        $content_id = intval($current_content['content_id']);
    }
}

if (!$current_content) {
    echo "<div class='container'>강의 콘텐츠를 찾을 수 없습니다.</div>";
    require __DIR__ . '/layout_footer.php';
    exit;
}

/* 선수 학습 체크 (순차 학습 모드일 때) */
if (intval($course['sequential_learning']) === 1) {
    // 현재 강의보다 순서가 빠른 강의들 중 미완료된 것이 있는지 확인
    foreach ($curriculum as $item) {
        if (intval($item['chapter_order']) < intval($current_content['chapter_order'])) {
            if (intval($item['is_completed']) === 0) {
                echo "<script>alert('이전 차시를 먼저 완료해야 합니다.'); location.href='watch.php?course_id={$course_id}&content_id={$item['content_id']}';</script>";
                exit;
            }
        }
    }
}

$last_position = intval($current_content['last_position'] ?? 0);
$is_completed = intval($current_content['is_completed']);
?>

<style>
/* Watch Page Specific Styles */
.watch-container {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    margin-bottom: 50px;
}
.video-section {
    flex: 2;
    min-width: 0; /* Flexbox overflow fix */
}
.sidebar-section {
    flex: 1;
    min-width: 300px;
    background: #fff;
    border: 1px solid #e1e1e1;
    border-radius: 12px;
    height: fit-content;
    max-height: 800px;
    display: flex;
    flex-direction: column;
}

.video-player-wrapper {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    height: 0;
    background: #000;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 20px;
}
.video-player-wrapper video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.lesson-info {
    margin-bottom: 20px;
}
.lesson-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 10px;
}
.course-title-sub {
    color: #666;
    font-size: 14px;
    margin-bottom: 5px;
}

.nav-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

/* Sidebar Styles */
.sidebar-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    font-weight: 700;
    font-size: 16px;
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
}
.curriculum-list {
    overflow-y: auto;
    max-height: 600px;
}
.curriculum-item {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background 0.2s;
}
.curriculum-item:hover {
    background-color: #f9f9f9;
}
.curriculum-item.active {
    background-color: #e3f2fd;
    border-left: 4px solid var(--primary-navy);
}
.curr-status {
    width: 24px;
    margin-right: 10px;
    text-align: center;
}
.curr-info {
    flex: 1;
}
.curr-title {
    font-size: 14px;
    margin-bottom: 4px;
    display: block;
}
.curr-time {
    font-size: 12px;
    color: #888;
}

/* Responsive */
@media (max-width: 900px) {
    .watch-container {
        flex-direction: column;
    }
    .video-section {
        width: 100%;
    }
    .sidebar-section {
        width: 100%;
        max-height: 400px;
    }
}
</style>

<div class="container">
    <div class="watch-container">
        
        <!-- 왼쪽: 비디오 플레이어 -->
        <div class="video-section">
            <div class="course-title-sub"><?= htmlspecialchars($course['title']) ?></div>
            <h2 class="lesson-title"><?= htmlspecialchars($current_content['title']) ?></h2>

            <div class="video-player-wrapper">
                <video id="videoPlayer" controls controlsList="nodownload">
                    <source src="<?= htmlspecialchars($current_content['video_url']) ?>" type="video/mp4">
                    브라우저가 video 태그를 지원하지 않습니다.
                </video>
            </div>

            <div class="nav-buttons">
                <?php if ($prev_content): ?>
                    <a href="watch.php?course_id=<?= $course_id ?>&content_id=<?= $prev_content['content_id'] ?>" class="btn btn-gray">
                        <i class="fas fa-chevron-left"></i> 이전 강의
                    </a>
                <?php else: ?>
                    <button class="btn btn-gray" disabled style="opacity:0.5; cursor:not-allowed;">
                        <i class="fas fa-chevron-left"></i> 이전 강의
                    </button>
                <?php endif; ?>

                <?php if ($next_content): ?>
                    <a href="watch.php?course_id=<?= $course_id ?>&content_id=<?= $next_content['content_id'] ?>" class="btn btn-navy">
                        다음 강의 <i class="fas fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <a href="classroom.php?course_id=<?= $course_id ?>" class="btn btn-navy">
                        강의실로 <i class="fas fa-list"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- 오른쪽: 커리큘럼 리스트 -->
        <aside class="sidebar-section">
            <div class="sidebar-header">
                강의 목차
            </div>
            <div class="curriculum-list">
                <?php foreach ($curriculum as $item): 
                    $isActive = (intval($item['content_id']) === intval($current_content['content_id']));
                    $isDone = (intval($item['is_completed']) === 1);
                ?>
                    <div class="curriculum-item <?= $isActive ? 'active' : '' ?>" 
                         onclick="location.href='watch.php?course_id=<?= $course_id ?>&content_id=<?= $item['content_id'] ?>'">
                        <div class="curr-status">
                            <?php if ($isActive && !$isDone): ?>
                                <i class="fas fa-play-circle" style="color:var(--primary-navy);"></i>
                            <?php elseif ($isDone): ?>
                                <i class="fas fa-check-circle" style="color:green;"></i>
                            <?php else: ?>
                                <i class="far fa-circle" style="color:#ccc;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="curr-info">
                            <span class="curr-title"><?= htmlspecialchars($item['title']) ?></span>
                            <span class="curr-time"><?= gmdate("i:s", intval($item['duration'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>

    </div>
</div>

<script>
const video = document.getElementById('videoPlayer');
const courseId  = <?= $course_id ?>;
const contentId = <?= $content_id ?>;
const duration  = <?= intval($current_content['duration']) ?>;
const preventSkip = <?= intval($course['prevent_skip'] ?? 0) === 1 ? 'true' : 'false' ?>;
let maxWatchedTime = <?= $last_position ?>; 
let isCompleted = <?= $is_completed ?>;

/* 1. 시작 위치 설정 */
video.addEventListener('loadedmetadata', () => {
    if (maxWatchedTime > 0 && maxWatchedTime < video.duration - 1) {
        video.currentTime = maxWatchedTime;
    }
});

/* 2. 스킵 방지 */
video.addEventListener('seeking', () => {
    if (isCompleted || !preventSkip) return;
    if (video.currentTime > maxWatchedTime + 2) { // 2초 오차 허용
        alert("학습하지 않은 구간으로 건너뛸 수 없습니다.");
        video.currentTime = maxWatchedTime;
    }
});

/* 3. 진도율 체크 */
video.addEventListener('timeupdate', () => {
    if (!video.seeking && video.currentTime > maxWatchedTime) {
        maxWatchedTime = video.currentTime;
    }
});

/* 4. 주기적 저장 (5초) */
let saveTimer = null;
video.addEventListener('timeupdate', () => {
    if (saveTimer) return;
    saveTimer = setTimeout(() => {
        saveProgress(Math.floor(maxWatchedTime), 0);
        saveTimer = null;
    }, 5000);
});

/* 5. 완료 처리 */
video.addEventListener('ended', () => {
    isCompleted = 1;
    saveProgress(Math.floor(video.duration), 1).then(() => {
        // 완료 시 다음 강의 자동 이동 여부 물어보거나 바로 이동
        if (confirm("학습을 완료했습니다. 다음 강의로 이동하시겠습니까?")) {
            <?php if ($next_content): ?>
                location.href = 'watch.php?course_id=<?= $course_id ?>&content_id=<?= $next_content['content_id'] ?>';
            <?php else: ?>
                location.href = 'classroom.php?course_id=<?= $course_id ?>';
            <?php endif; ?>
        }
    });
});

function saveProgress(position, completed) {
    return fetch('api_progress.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            course_id: courseId,
            content_id: contentId,
            position: position,
            completed: completed
        })
    }).catch(console.error);
}
</script>

<?php require __DIR__ . '/layout_footer.php'; ?>
