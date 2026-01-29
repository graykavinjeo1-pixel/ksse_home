// Mock Data
const courses = [
  {
    id: 1,
    title: "파이썬 기초 마스터",
    instructor: "김코딩",
    progress: 45,
    thumbnailIcon: "fa-brands fa-python",
    modules: [
      {
        title: "섹션 1: 파이썬 시작하기",
        lessons: [
          { id: 101, title: "1-1. 파이썬 설치 및 환경 설정", type: "video", duration: "10:00" },
          { id: 102, title: "1-2. Hello World 출력하기", type: "video", duration: "05:30" },
          { id: 103, title: "1-3. 변수와 자료형", type: "quiz", duration: "03:00" }
        ]
      },
      {
        title: "섹션 2: 제어문",
        lessons: [
          { id: 201, title: "2-1. if 조건문", type: "video", duration: "12:00" },
          { id: 202, title: "2-2. for 반복문", type: "video", duration: "15:00" }
        ]
      }
    ]
  },
  {
    id: 2,
    title: "웹 개발 풀스택 입문",
    instructor: "이웹",
    progress: 10,
    thumbnailIcon: "fa-solid fa-code",
    modules: [
      {
        title: "섹션 1: HTML/CSS 기초",
        lessons: [
          { id: 301, title: "HTML 태그의 이해", type: "video", duration: "08:00" }
        ]
      }
    ]
  },
  {
    id: 3,
    title: "데이터 사이언스 개론",
    instructor: "박데이터",
    progress: 0,
    thumbnailIcon: "fa-solid fa-chart-line",
    modules: []
  }
];

// App Logic
const app = {
  container: document.getElementById('app-container'),
  
  init() {
    this.navigate('home');
  },

  navigate(page, params = {}) {
    this.container.innerHTML = '';
    window.scrollTo(0, 0);

    if (page === 'home') {
      this.renderHome();
    } else if (page === 'my-learning') {
      this.renderMyLearning();
    } else if (page === 'player') {
      this.renderPlayer(params.courseId, params.lessonId);
    }
  },

  renderHome() {
    const section = document.createElement('div');
    section.innerHTML = `
      <h2 class="section-title">추천 강의</h2>
      <div class="course-grid">
        ${courses.map(course => this.createCourseCard(course)).join('')}
      </div>
    `;
    this.container.appendChild(section);
  },

  renderMyLearning() {
    const myCourses = courses.filter(c => c.progress > 0);
    const section = document.createElement('div');
    section.innerHTML = `
      <h2 class="section-title">내 강의실</h2>
      ${myCourses.length > 0 
        ? `<div class="course-grid">${myCourses.map(course => this.createCourseCard(course)).join('')}</div>`
        : '<p>수강 중인 강의가 없습니다.</p>'
      }
    `;
    this.container.appendChild(section);
  },

  createCourseCard(course) {
    return `
      <div class="course-card" onclick="app.navigate('player', { courseId: ${course.id} })">
        <div class="course-thumbnail">
          <i class="${course.thumbnailIcon}"></i>
        </div>
        <div class="course-info">
          <div class="course-title">${course.title}</div>
          <div class="course-instructor">${course.instructor}</div>
          <div class="progress-bar-bg">
            <div class="progress-bar-fill" style="width: ${course.progress}%"></div>
          </div>
          <div class="course-meta">
            <span>진도율 ${course.progress}%</span>
          </div>
        </div>
      </div>
    `;
  },

  renderPlayer(courseId, lessonId) {
    const course = courses.find(c => c.id === courseId);
    if (!course) return;

    // Default to first lesson if not specified
    let currentLesson = null;
    if (lessonId) {
      for (const mod of course.modules) {
        const found = mod.lessons.find(l => l.id === lessonId);
        if (found) {
          currentLesson = found;
          break;
        }
      }
    } else if (course.modules.length > 0 && course.modules[0].lessons.length > 0) {
      currentLesson = course.modules[0].lessons[0];
    }

    const playerLayout = document.createElement('div');
    playerLayout.className = 'player-layout';
    
    // Video Area (Mock)
    const videoArea = document.createElement('div');
    videoArea.className = 'video-container';
    videoArea.innerHTML = `
      <div style="text-align: center;">
        <i class="fas fa-play-circle" style="font-size: 4rem; margin-bottom: 1rem;"></i>
        <h2>${currentLesson ? currentLesson.title : '강의를 선택하세요'}</h2>
        <p>${currentLesson ? currentLesson.duration : ''}</p>
        <p style="margin-top: 1rem; color: #aaa;">(동영상 플레이어 영역)</p>
      </div>
    `;

    // Sidebar
    const sidebar = document.createElement('div');
    sidebar.className = 'curriculum-sidebar';
    sidebar.innerHTML = `
      <div class="sidebar-header">${course.title}</div>
      <ul class="module-list">
        ${course.modules.map(mod => `
          <li class="module-item">
            <div class="module-title">${mod.title}</div>
            <ul class="lesson-list">
              ${mod.lessons.map(lesson => `
                <li class="lesson-item ${currentLesson && currentLesson.id === lesson.id ? 'active' : ''}" 
                    onclick="event.stopPropagation(); app.navigate('player', { courseId: ${course.id}, lessonId: ${lesson.id} })">
                  <i class="lesson-icon fas ${lesson.type === 'video' ? 'fa-play-circle' : 'fa-question-circle'}"></i>
                  <span>${lesson.title}</span>
                </li>
              `).join('')}
            </ul>
          </li>
        `).join('')}
      </ul>
    `;

    playerLayout.appendChild(videoArea);
    playerLayout.appendChild(sidebar);
    this.container.appendChild(playerLayout);
  }
};

// Initialize app
document.addEventListener('DOMContentLoaded', () => {
  app.init();
});
