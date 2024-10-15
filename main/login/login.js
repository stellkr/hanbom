document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // 기본 제출 이벤트 방지

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('error-message');

    // 오류 메시지 초기화
    errorMessage.style.display = 'none';

    // 데이터 객체 생성
    const data = {
        username,
        password
    };

    // fetch 요청
    fetch('http://localhost/login.php', {
        method: 'POST', // POST 요청
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
        if (data.message === '로그인 성공') {
            // 맞춤형 알림 메시지
            const welcomeMessage = `${data.name} 학생 어서오세요.\n${data.department} ${data.grade}학년 ${data.class}반 ${data.number}번`;
            alert(`로그인이 완료되었습니다.\n${welcomeMessage}`);
    
            // 1초 후 메인 페이지로 이동
            setTimeout(() => {
                window.location.href = 'http://localhost/index.php'; // 로그인 페이지로 이동
            }, 1000); // 1000ms = 1초
        } else {
            errorMessage.innerHTML = data.message; // 서버에서 전달된 메시지
            errorMessage.style.display = 'block'; // 오류 메시지 표시
        }
    })
    
    .catch(error => {
        console.error('Error:', error); // 에러 메시지를 콘솔에 출력
        errorMessage.innerHTML = '서버에 연결할 수 없습니다. 나중에 다시 시도해주세요.';
        errorMessage.style.display = 'block'; // 오류 메시지 표시
    });
});
/*.
then(data => {
    console.log('서버 응답 데이터:', data); // 서버 응답 데이터 로그
    if (data.message === '회원가입이 완료되었습니다') {
        // 알림 표시
        alert('회원가입이 완료되었습니다.');

        // 1초 후 로그인 페이지로 이동
        setTimeout(() => {
            window.location.href = 'login/login.html'; // 로그인 페이지로 이동
        }, 1000); // 1000ms = 1초
    } else {
        errorMessage.innerHTML = data.message; // 서버에서 전달된 메시지
        errorMessage.style.display = 'block'; // 오류 메시지 표시
    }
})
        
        */