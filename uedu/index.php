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

<section class="hero" id="mainVisual">
    <div class="main-visual-con">        
        <div class="main-visual-item">
            <div class="main-visual-img" style="background: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070') no-repeat center;"></div>
            <div class="main-visual-txt-con">
                <div class="main-visual-txt-box">
                    <div class="main-visual-txt-inner">
                        <strong class="main-visual-txt1">Global Leader</strong>
                        <p class="main-visual-txt2">세계적인 기업으로 성장하겠습니다.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-visual-item">
            <div class="main-visual-img" style="background: url('https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=2069') no-repeat center;"></div>
            <div class="main-visual-txt-con">
                <div class="main-visual-txt-box">
                    <div class="main-visual-txt-inner">
                        <strong class="main-visual-txt1">Sustainability</strong>
                        <p class="main-visual-txt2">지속 가능한 경영을 실현합니다.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-visual-item">
            <div class="main-visual-img" style="background: url('https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=2015') no-repeat center;"></div>
            <div class="main-visual-txt-con">
                <div class="main-visual-txt-box">
                    <div class="main-visual-txt-inner">
                        <strong class="main-visual-txt1">Innovation</strong>
                        <p class="main-visual-txt2">미래를 여는 혁신적인 기술.</p>
                    </div>
                </div>
            </div>
        </div>
    </div><div class="main-scroll-icon">
        <span class="txt">SCROLL DOWN</span>
        <div class="scroll-arrow-ani">↓</div>
    </div>
    <div class="main-visual-counter">
        <div class="area">
            <div class="main-visual-num-box">
                <div class="main-visual-arrow">
                    <button type="button" class="prev-btn"><i class="fas fa-chevron-left"></i></button>
                    <button type="button" class="next-btn"><i class="fas fa-chevron-right"></i></button>
                </div>
                <span class="cur-num">01</span>
                <span class="middle-line"></span>
                <span class="total-num">03</span>
            </div>
            <div class="main-visual-bar">
                <span class="bar-fill"></span>
            </div>
        </div>
    </div>
</section>

<section id="vision" class="content-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Our Vision</span>
            <h2 class="section-title">A New Standard in Education</h2>
        </div>
        <div class="vision-cards">
             <div class="course-card">
                <div class="card-body">
                    <h3 class="card-title">Professional</h3>
                    <p class="card-text">산업 전문가가 검증한 실무 중심의 커리큘럼</p>
                </div>
            </div>
            <div class="course-card">
                 <div class="card-body">
                    <h3 class="card-title">Systematic</h3>
                    <p class="card-text">체계적인 학습 관리와 데이터 기반 성과 분석</p>
                </div>
            </div>
            <div class="course-card">
                 <div class="card-body">
                    <h3 class="card-title">Anywhere</h3>
                    <p class="card-text">언제 어디서든 학습이 가능한 온라인 최적화 환경</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="courses" class="content-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Best-selling</span>
            <h2 class="section-title">Popular Courses</h2>
        </div>
        <div class="course-filters"></div>
        <div class="course-grid"></div>
        <div class="section-footer">
            <a href="<?= BASE_URL ?>/courses.php" class="btn btn-primary">모든 강좌 보기</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/layout_footer.php'; ?>

<script>
    
$(document).ready(function() {
    var $mainVisual = $(".main-visual-con");
    var $progressBar = $('.main-visual-bar');
    var $curNum = $('.cur-num');
    var $totalNum = $('.total-num');

    // 1. 초기화 이벤트 (init)
    $mainVisual.on('init', function(event, slick) {
        // 첫 번째 슬라이드에 active-item 클래스 강제 주입 (애니메이션 시작)
        $(this).find(".main-visual-item").eq(0).addClass("active-item");
        
        // 프로그레스 바 시작
        $progressBar.addClass('start-progress');
        
        // 총 개수 설정
        $totalNum.text('0' + slick.slideCount);
    });

    // 2. 슬라이드 변경 직전 이벤트
    $mainVisual.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
        
        // 프로그레스 바 리셋 (깜빡임 효과로 재시작)
        $progressBar.removeClass('start-progress');
        setTimeout(function() {
            $progressBar.addClass('start-progress');
        }, 10);

        // 숫자 갱신
        $curNum.text('0' + (nextSlide + 1));

        // [핵심] 다음 슬라이드에 active-item을 미리 주어 애니메이션을 시작시킴 (페이드인 될 때 이미 움직이고 있음)
        $(this).find(".main-visual-item").eq(nextSlide).addClass("active-item");
        
        // [핵심] 현재 슬라이드는 stop-active-item을 주어 애니메이션이 끊기지 않고 끝까지 유지되게 함
        $(this).find(".main-visual-item").eq(currentSlide).addClass("stop-active-item");
    });

    // 3. 슬라이드 변경 완료 이벤트
    $mainVisual.on('afterChange', function() {
        // 전환이 완전히 끝난 후, 이전 슬라이드의 클래스를 제거하여 초기화
        $(this).find(".stop-active-item").removeClass("stop-active-item active-item");
    });

    // 4. 슬라이더 설정
    $mainVisual.slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false, 
        fade: true,    
        dots: false,
        autoplay: true,
        speed: 1000,          // 전환 속도 (부드럽게 겹치는 시간)
        autoplaySpeed: 5000,  // 머무르는 시간
        pauseOnHover: false,
        zIndex: 1,
        cssEase: 'cubic-bezier(0.87, 0.03, 0.41, 0.9)' 
    });

    // 5. 버튼 연결
    $('.prev-btn').click(function() { $mainVisual.slick('slickPrev'); });
    $('.next-btn').click(function() { $mainVisual.slick('slickNext'); });
});

// [추가] 스크롤 애니메이션
$(document).ready(function() {
    var observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.2 // 화면에 20% 보이면 작동
    };

    var observer = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                // 화면에 들어오면 .animated 클래스 추가
                $(entry.target).addClass('animated');
                // 한 번 실행 후 관찰 중지 (반복 원하면 아래 줄 삭제)
                observer.unobserve(entry.target); 
            }
        });
    }, observerOptions);

    // .area 클래스를 가진 요소 관찰 시작
    $('.area').each(function() {
        observer.observe(this);
    });
});
document.addEventListener("DOMContentLoaded", function() {
    // --- Course Filtering Logic ---
    const courseGrid = document.querySelector('.course-grid');
    const courseFilters = document.querySelector('.course-filters');
    if (courseGrid && courseFilters) {
        const apiURL = '<?= BASE_URL ?>/api/courses.php';
        let allCourses = [];

        const renderCourses = (filter = 'all') => {
            courseGrid.innerHTML = '';
            const coursesToRender = (filter === 'all' ? allCourses : allCourses.filter(c => c.category === filter)).slice(0, 6);
            if (coursesToRender.length === 0) {
                courseGrid.innerHTML = '<p style="text-align:center; color: var(--text-muted);">이 카테고리에는 아직 강좌가 없습니다.</p>';
                return;
            }
            coursesToRender.forEach(course => {
                const price = parseInt(course.price, 10) === 0 ? 'Free' : `${new Intl.NumberFormat().format(course.price)}원`;
                const card = `
                    <a href="enroll.php?course_id=${course.id}" class="course-card">
                        <div class="card-img-wrap">
                            <img src="${course.thumbnail_url}" alt="${course.title}">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">${course.title}</h3>
                            <p class="card-text">${course.short_desc || '강좌 설명이 없습니다.'}</p>
                            <div class="card-price">${price}</div>
                        </div>
                    </a>
                `;
                courseGrid.insertAdjacentHTML('beforeend', card);
            });
        };

        const setupFilters = (categories) => {
            courseFilters.innerHTML = '<button class="filter-btn active" data-category="all">All</button>';
            categories.forEach(category => {
                const btn = `<button class="filter-btn" data-category="${category}">${category}</button>`;
                courseFilters.insertAdjacentHTML('beforeend', btn);
            });
            courseFilters.addEventListener('click', (e) => {
                if (e.target.classList.contains('filter-btn')) {
                    const currentActive = courseFilters.querySelector('.filter-btn.active');
                    if (currentActive) currentActive.classList.remove('active');
                    e.target.classList.add('active');
                    renderCourses(e.target.dataset.category);
                }
            });
        };

        courseGrid.innerHTML = '<p style="text-align:center; color: var(--text-muted);">강좌를 불러오는 중...</p>';
        fetch(apiURL)
            .then(response => { if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`); return response.json(); })
            .then(result => {
                if (result.success && result.data.courses) {
                    allCourses = result.data.courses;
                    setupFilters(result.data.categories || []);
                    renderCourses();
                } else {
                    courseGrid.innerHTML = `<p>${result.message || '강좌를 찾을 수 없습니다.'}</p>`;
                }
            })
            .catch(error => { console.error('Error fetching courses:', error); courseGrid.innerHTML = '<p>강좌를 불러오는 데 실패했습니다. 잠시 후 다시 시도해주세요.</p>'; });
    }
});
</script>