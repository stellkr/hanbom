document.addEventListener("DOMContentLoaded", function() {
    var applyButton = document.getElementById("applyBtn");

    if (applyButton) {  // 버튼이 존재하는지 확인
        applyButton.addEventListener("click", function(event) {
            event.preventDefault();  // 기본 동작 막기
            window.location.href = "https://www.samsung.com/sec/";
            // 로컬 스토리지에서 로그인 상태 확인
            });
    } else {
        console.error("지원하기 버튼을 찾을 수 없습니다.");
    }
});

document.getElementById("loginBtn").addEventListener("click", function(event) {
    // 로그인 성공 시 로컬 스토리지에 로그인 상태 저장
    localStorage.setItem('loggedIn', 'true');
    window.location.href = "index.html";  // 로그인 후 메인 페이지로 이동
});

document.getElementById("logoutBtn").addEventListener("click", function(event) {
    // 로컬 스토리지에서 로그인 상태 제거
    localStorage.removeItem('loggedIn');
    window.location.href = "index.html";  // 로그아웃 후 메인 페이지로 이동
});
