<?php
require_once __DIR__ . '/config.php';

// 세션 시작
if (session_status() === PHP_SESSION_NONE) session_start();

// 로그인 여부에 따른 헤더 로드
if (isset($_SESSION['user_id'])) {
    require __DIR__ . '/header_auth.php';
} else {
    require __DIR__ . '/header_static.php';
}
?>

<section class="hero-section">
    <div class="hero-background"></div>
    <div class="hero-content">
        <h1 class="hero-title">
            <span class="line">UEDU</span>
            <span class="line">Beyond Learning</span>
        </h1>
        <p class="hero-subtitle">미래를 여는 새로운 교육의 시작</p>
        <a href="<?= BASE_URL ?>/courses.php" class="hero-button">Explore Courses</a>
    </div>
</section>

<section id="vision" class="content-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Our Vision</span>
            <h2 class="section-title">A New Standard in Education</h2>
            <p class="section-subtitle">
                We are setting a new standard in education with a systematic approach and a proven curriculum.
            </p>
        </div>
        <div class="vision-cards">
            <div class="vision-card">
                <div class="card-icon"><i class="fas fa-user-tie"></i></div>
                <h3 class="card-title">Professional</h3>
                <p class="card-text">A practical, hands-on curriculum verified by industry experts.</p>
            </div>
            <div class="vision-card">
                <div class="card-icon"><i class="fas fa-cogs"></i></div>
                <h3 class="card-title">Systematic</h3>
                <p class="card-text">Systematic learning management and data-driven performance analysis.</p>
            </div>
            <div class="vision-card">
                <div class="card-icon"><i class="fas fa-map-marked-alt"></i></div>
                <h3 class="card-title">Anywhere</h3>
                <p class="card-text">An online-optimized environment for learning anytime, anywhere.</p>
            </div>
        </div>
    </div>
</section>

<section id="courses" class="content-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Best-selling</span>
            <h2 class="section-title">Popular Courses</h2>
            <p class="section-subtitle">
                Explore our most popular courses, chosen by students like you.
            </p>
        </div>
        <div class="course-filters">
            <!-- Filter buttons will be dynamically inserted here -->
        </div>
        <div class="course-grid">
            <!-- Course cards will be dynamically inserted here -->
        </div>
        <div class="section-footer">
            <a href="<?= BASE_URL ?>/courses.php" class="btn btn-navy">View All Courses</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/layout_footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // GSAP Hero Animation
    const tl = gsap.timeline();
    tl.from(".hero-background", { scale: 1.2, duration: 1.5, ease: "power2.out" })
      .from(".hero-title .line", { y: "100%", duration: 1, stagger: 0.2, ease: "power3.out" }, "-=1")
      .from(".hero-subtitle", { opacity: 0, y: 20, duration: 0.8, ease: "power2.out" }, "-=0.5")
      .from(".hero-button", { opacity: 0, y: 20, duration: 0.8, ease: "power2.out" }, "-=0.5");

    // Course Filtering Logic
    const courseGrid = document.querySelector('.course-grid');
    const courseFilters = document.querySelector('.course-filters');
    
    if (courseGrid && courseFilters) {
        const apiURL = '<?= BASE_URL ?>/api/courses.php';
        let allCourses = []; // To store all fetched courses

        // Function to render courses
        const renderCourses = (filter = 'all') => {
            courseGrid.innerHTML = '';
            const coursesToRender = (filter === 'all' 
                ? allCourses 
                : allCourses.filter(c => c.category === filter)
            ).slice(0, 6); // Always show max 6 on homepage

            if (coursesToRender.length === 0) {
                courseGrid.innerHTML = '<p>No courses found for this category.</p>';
                return;
            }

            coursesToRender.forEach(course => {
                const price = parseInt(course.price, 10) === 0 ? 'Free' : `${new Intl.NumberFormat().format(course.price)} KRW`;
                const card = `
                    <a href="enroll.php?course_id=${course.id}" class="course-card">
                        <div class="card-img-wrap">
                            <img src="${course.thumbnail_url}" alt="${course.title}">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">${course.title}</h3>
                            <p class="card-text">${course.short_desc || 'No description available.'}</p>
                            <div class="card-price">${price}</div>
                        </div>
                    </a>
                `;
                courseGrid.insertAdjacentHTML('beforeend', card);
            });
        };

        // Function to setup filters
        const setupFilters = (categories) => {
            courseFilters.innerHTML = '<button class="filter-btn active" data-category="all">All</button>';
            categories.forEach(category => {
                const btn = `<button class="filter-btn" data-category="${category}">${category}</button>`;
                courseFilters.insertAdjacentHTML('beforeend', btn);
            });

            courseFilters.addEventListener('click', (e) => {
                if (e.target.classList.contains('filter-btn')) {
                    // It's safer to check if the active button exists before trying to remove a class from it.
                    const currentActive = courseFilters.querySelector('.filter-btn.active');
                    if (currentActive) {
                        currentActive.classList.remove('active');
                    }
                    e.target.classList.add('active');
                    const category = e.target.dataset.category;
                    renderCourses(category);
                }
            });
        };

        // Initial fetch
        courseGrid.innerHTML = '<p>Loading courses...</p>';
        fetch(apiURL)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(result => {
                if (result.success && result.data.courses) {
                    allCourses = result.data.courses;
                    setupFilters(result.data.categories || []);
                    renderCourses(); // Render all courses initially
                } else {
                    courseGrid.innerHTML = `<p>${result.message || 'No courses found.'}</p>`;
                }
            })
            .catch(error => {
                console.error('Error fetching courses:', error);
                courseGrid.innerHTML = '<p>Could not load courses. Please try again later.</p>';
            });
    }
});

// Legacy scroll animation script (can be updated or removed)
$(document).ready(function() {
    var observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.2
    };

    var observer = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                $(entry.target).addClass('animated');
                observer.unobserve(entry.target); 
            }
        });
    }, observerOptions);

    // This script might need an update as the '.area' class is no longer in use in the new sections.
    // For now, let's keep it in case other pages use it.
    $('.area').each(function() {
        observer.observe(this);
    });
});
</script>