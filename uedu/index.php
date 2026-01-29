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
<section id="mainContent1" class="section">
    <div class="container">
        <div class="area" data-scroll>
            
            <div class="main-prd-top-box">
                <div class="main-tit-box">
                    <strong class="main-tit-en transition-left">Global Leader in Safety</strong>
                    <div class="prd-tit-box">
                        <h4 class="main-tit transition-left">Visions</h4>
                        <p class="main-txt transition-left">
                            체계적인 교육 시스템과 검증된 커리큘럼으로<br>
                            대한민국 안전 교육의 새로운 기준을 제시합니다.
                        </p>
                    </div>
                </div>
                <div class="main-btn-box transition-right">
                    <a href="<?= BASE_URL ?>/courses.php" class="main-btn">
                        <span>View more</span>
                        <i class="fas fa-plus-circle"></i>
                    </a>
                </div>
            </div>

            <div class="main-prd-list transition-bottom">
                
                <div class="list-item">
                    <a href="#">
                        <div class="info-box">
                            <h5 class="tit">Professional</h5>
                            <p class="txt">현업 전문가들이 검증한<br>실무 중심의 커리큘럼</p>
                        </div>
                        <span class="img-box">
                            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Professional">
                        </span>
                    </a>
                </div>

                <div class="list-item">
                    <a href="#">
                        <div class="info-box">
                            <h5 class="tit">Systematic</h5>
                            <p class="txt">체계적인 학습 관리와<br>데이터 기반 성과 분석</p>
                        </div>
                        <span class="img-box">
                            <img src="https://cdn-icons-png.flaticon.com/512/2620/2620986.png" alt="Systematic">
                        </span>
                    </a>
                </div>

                <div class="list-item">
                    <a href="#">
                        <div class="info-box">
                            <h5 class="tit">Anywhere</h5>
                            <p class="txt">언제 어디서나 학습 가능한<br>온라인 최적화 환경</p>
                        </div>
                        <span class="img-box">
                            <img src="https://cdn-icons-png.flaticon.com/512/2991/2991148.png" alt="Anywhere">
                        </span>
                    </a>
                </div>

            </div></div></div>
</section>

<section id="courseSection">
    <span class="bg-shape"></span><div class="container">
        <div class="business-wrap">
            
            <div class="business-left">
                <div class="main-tit-box">
                    <strong class="business-tit-en">Best Curriculum</strong>
                    <h4 class="business-tit">Popular<br>Courses</h4>
                    <p class="business-txt">
                        수강생들이 선택한 최고의 인기 강의를 만나보세요.<br>
                        실무에 즉시 적용 가능한 핵심 노하우를 제공합니다.
                    </p>
                    <div class="main-btn-box">
                        <a href="<?= BASE_URL ?>/courses.php" class="btn btn-navy" style="border-radius:50px; padding:12px 30px;">
                            View More <i class="fas fa-plus-circle" style="margin-left:5px;"></i>
                        </a>
                    </div>
                </div>

                <div class="business-controls">
                    <div class="business-arrow">
                        <button type="button" class="bs-prev"><i class="fas fa-chevron-left"></i></button>
                        <button type="button" class="bs-next"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="business-count">
                        <span class="cur">01</span>
                        <span class="line"></span>
                        <span class="total">06</span>
                    </div>
                </div>
            </div>

            <div class="business-right">
                <div class="business-slider-wrap">
                    <div class="business-slider">
                        <?php
                        require_once __DIR__ . '/db_conn.php';
                        // 슬라이더 효과를 위해 6개 조회
                        try {
                            $stmt = db()->query("SELECT * FROM uedu_courses WHERE is_active=1 ORDER BY is_featured DESC, id DESC LIMIT 6");
                            $courses = $stmt->fetchAll();
                        } catch(Exception $e) { $courses = []; }
                        
                        // 데이터가 없으면 임시 데이터 생성 (디자인 확인용)
                        if(empty($courses)) {
                            for($i=1; $i<=6; $i++) {
                                $courses[] = [
                                    'id' => $i,
                                    'title' => '임시 강의 제목 ' . $i,
                                    'price' => 50000,
                                    'thumbnail' => '' // 이미지 없으면 회색 배경
                                ];
                            }
                        }
                        ?>

                        <?php foreach ($courses as $index => $c): 
                            // 이미지가 없으면 랜덤 이미지 사용 (Unsplash)
                            $bgImg = !empty($c['thumbnail']) ? $c['thumbnail'] : 'https://source.unsplash.com/random/400x500?tech,coding&sig='.$index;
                        ?>
                            <div class="bs-item">
                                <a href="enroll.php?course_id=<?= $c['id'] ?>" class="bs-card" style="background-image:url('<?= htmlspecialchars($bgImg) ?>')">
                                    <div class="bs-info">
                                        <span class="bs-cat">Online Course</span>
                                        <h5 class="bs-title"><?= htmlspecialchars($c['title']) ?></h5>
                                        <div style="margin-top:10px; font-weight:400;">
                                            <?= intval($c['price']) == 0 ? 'Free' : number_format($c['price']).' KRW' ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div></div>
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

// [추가] 추천 강의 섹션 슬라이더
$(document).ready(function() {
    var $bsSlider = $('.business-slider');
    var $bsCur = $('.business-count .cur');
    var $bsTotal = $('.business-count .total');

    // 1. 초기화
    $bsSlider.on('init', function(event, slick){
        var count = slick.slideCount;
        $bsTotal.text(count < 10 ? '0'+count : count);
        $bsCur.text('01');
    });

    // 2. 슬라이드 변경 시 카운터 갱신
    $bsSlider.on('beforeChange', function(event, slick, currentSlide, nextSlide){
        var next = nextSlide + 1;
        $bsCur.text(next < 10 ? '0'+next : next);
    });

    // 3. 슬라이더 실행
    $bsSlider.slick({
        slidesToShow: 1,      // 화면에 보일 개수 (variableWidth 때문에 큰 의미 없음)
        slidesToScroll: 1,
        variableWidth: true,  // [핵심] 카드 너비만큼 자연스럽게 나열
        arrows: false,        // 커스텀 화살표 사용
        dots: false,
        infinite: false,
        autoplay: true,
        autoplaySpeed: 3000,
        speed: 1500,
        pauseOnHover: true,
        cssEase: 'cubic-bezier(0.25, 1, 0.5, 1)' // 부드러운 가감속
    });

    // 4. 컨트롤 버튼 연결
    $('.bs-prev').click(function(){ $bsSlider.slick('slickPrev'); });
    $('.bs-next').click(function(){ $bsSlider.slick('slickNext'); });
});
</script>