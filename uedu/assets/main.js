document.addEventListener('DOMContentLoaded', function() {
    // 1. 모바일 메뉴 컨테이너 생성 및 GNB 복제
    const mobileGnb = document.createElement('div');
    mobileGnb.classList.add('mobile-gnb');
    document.body.appendChild(mobileGnb);

    const desktopGnb = document.querySelector('.gnb');
    if (desktopGnb) {
        mobileGnb.innerHTML = desktopGnb.innerHTML;
    }

    // 2. 모바일 메뉴에 닫기 버튼 추가
    const closeButton = document.createElement('button');
    closeButton.classList.add('mobile-menu-close');
    closeButton.innerHTML = '&times;';
    mobileGnb.appendChild(closeButton);

    // 3. 모바일 메뉴 토글 함수
    const trigger = document.querySelector('.mobile-menu-trigger');

    function toggleMobileMenu() {
        document.body.classList.toggle('is-mobile-menu-active');

        if (document.body.classList.contains('is-mobile-menu-active')) {
            // 스크롤 막기 (더 간단한 방법)
            document.body.style.overflow = 'hidden';
        } else {
            // 스크롤 허용
            document.body.style.overflow = '';
        }
    }

    // 4. 이벤트 핸들러
    if (trigger) {
        trigger.addEventListener('click', toggleMobileMenu);
    }

    closeButton.addEventListener('click', toggleMobileMenu);

    mobileGnb.addEventListener('click', function(e) {
        if (e.target.tagName === 'A') {
            toggleMobileMenu();
        }
    });
});
