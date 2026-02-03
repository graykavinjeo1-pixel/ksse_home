$(document).ready(function() {
    // 1. 모바일 메뉴 컨테이너 생성 및 GNB 복제
    // '.site-header' 바깥에 'mobile-gnb' 컨테이너를 만듭니다.
    $('body').append('<div class="mobile-gnb"></div>');

    // 데스크탑의 GNB 메뉴를 복제해서 모바일 컨테이너에 넣습니다.
    var $desktopGnb = $('.gnb').clone();
    $('.mobile-gnb').html($desktopGnb.html());

    // 2. 모바일 메뉴에 닫기 버튼 추가
    $('.mobile-gnb').append('<button class="mobile-menu-close">&times;</button>');

    // 3. 모바일 메뉴 트리거 이벤트
    $('.mobile-menu-trigger').on('click', function() {
        // 'is-mobile-menu-active' 클래스를 body에 토글합니다.
        $('body').toggleClass('is-mobile-menu-active');

        // 스크롤을 막거나 허용합니다.
        if ($('body').hasClass('is-mobile-menu-active')) {
            // body 스크롤을 막습니다.
            $(window).on('scroll.mobileMenu, mousewheel.mobileMenu, touchmove.mobileMenu', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            });
        } else {
            // body 스크롤을 다시 허용합니다.
            $(window).off('.mobileMenu');
        }
    });

    // 4. 모바일 메뉴의 링크 또는 닫기 버튼 클릭 시 메뉴 닫기
    $('.mobile-gnb').on('click', 'a, .mobile-menu-close', function() {
        $('body').removeClass('is-mobile-menu-active');
        // body 스크롤을 다시 허용합니다.
        $(window).off('.mobileMenu');
    });
});