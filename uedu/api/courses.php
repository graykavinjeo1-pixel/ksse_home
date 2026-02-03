<?php
require_once __DIR__ . '/../db_conn.php';
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // 1. Fetch all active courses
    $course_stmt = db()->query("
        SELECT id, title, description AS short_desc, price, thumbnail, category, is_featured
        FROM uedu_courses 
        WHERE is_active = 1 
        ORDER BY is_featured DESC, id DESC
    ");
    $courses = $course_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Fetch all unique categories
    $category_stmt = db()->query("
        SELECT DISTINCT category 
        FROM uedu_courses 
        WHERE category IS NOT NULL AND category != '' AND is_active = 1
        ORDER BY category ASC
    ");
    $categories = $category_stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    // 3. Process courses and add full thumbnail URL
    foreach ($courses as &$course) {
        if (!empty($course['thumbnail'])) {
            $course['thumbnail_url'] = $course['thumbnail'];
        } else {
            $course['thumbnail_url'] = 'https://source.unsplash.com/random/500x300?skill,learn&sig=' . $course['id'];
        }
    }

    // 4. Send JSON response
    echo json_encode([
        'success' => true,
        'data' => [
            'courses' => $courses,
            'categories' => $categories
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch course data.',
        'error' => $e->getMessage() // For debugging, might remove in production
    ]);
}
