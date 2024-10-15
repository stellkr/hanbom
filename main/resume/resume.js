document.getElementById('resumeForm').addEventListener('submit', function(event) {
    event.preventDefault(); // 기본 제출 이벤트 방지

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const education = document.getElementById('education').value;
    const experience = document.getElementById('experience').value;
    const skills = document.getElementById('skills').value;

    // 로그인한 사용자 정보 가져오기
    const loggedInUserName = ''; // PHP에서 세션으로 가져온 사용자 이름
    const loggedInUserId = ''; // PHP에서 세션으로 가져온 사용자 ID

    const errorMessage = document.getElementById('error-message');
    errorMessage.style.display = 'none'; // 오류 메시지 초기화

    // 입력값 검사
    if (!name || !email || !phone || !education || !experience || !skills) {
        errorMessage.innerHTML = '모든 필드를 입력하세요.';
        errorMessage.style.display = 'block'; // 오류 메시지 표시
        return;
    }

    // 데이터 객체 생성
    const data = {
        name,
        email,
        phone,
        education,
        experience,
        skills,
        username: loggedInUserName, // 로그인한 사용자 이름 추가
        userId: loggedInUserId // 로그인한 사용자 ID 추가
    };

    // fetch 요청
    fetch('http://localhost/submit_resume.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(data), // URLSearchParams로 변환
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json(); // JSON으로 변환
    })
    .then(data => {
        if (data.message === '이력서가 등록되었습니다.') {
            alert('이력서가 등록되었습니다.');
            setTimeout(() => {
                window.location.href = 'index.html'; // 메인 페이지로 이동
            }, 1000);
        } else {
            errorMessage.innerHTML = data.message; // 서버에서 전달된 메시지
            errorMessage.style.display = 'block'; // 오류 메시지 표시
        }
    })
    .catch(error => {
        console.error('Error:', error); // 에러 메시지를 콘솔에 출력
    });
});
