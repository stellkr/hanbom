const departmentToClassMap = {
    "뷰티아트과": ["1반", "2반"],
    "캐릭터창작과": ["1반"],
    "시각디자인과": ["1반", "2반", "3반"],
    "빅데이터정보과": ["1반", "2반"],
    "스마트제어과": ["1반", "2반"],
};

const departmentSelect = document.getElementById('department');
const classSelect = document.getElementById('class');
const errorMessage = document.getElementById('error-message');

// 학과 선택 시 반 목록 업데이트
departmentSelect.addEventListener('change', function() {
    const selectedDepartment = departmentSelect.value;
    classSelect.innerHTML = '<option value="">선택하세요</option>'; // 초기화

    if (selectedDepartment) {
        const classes = departmentToClassMap[selectedDepartment];
        classes.forEach(cls => {
            const option = document.createElement('option');
            option.value = cls;
            option.textContent = cls;
            classSelect.appendChild(option);
        });
    }
});

document.getElementById('registrationForm').addEventListener('submit', function(event) {
    event.preventDefault(); // 기본 제출 이벤트 방지

    const password = document.getElementById('password').value;
    const username = document.getElementById('username').value;
    const name = document.getElementById('name').value;
    const department = departmentSelect.value;
    const grade = document.getElementById('grade').value;
    const classValue = classSelect.value;
    const number = document.getElementById('number').value;

    // 오류 메시지 초기화
    errorMessage.style.display = 'none';
    const inputFields = [username, password, name, department, grade, classValue, number];
    const inputs = [
        document.getElementById('username'),
        document.getElementById('password'),
        document.getElementById('name'),
        departmentSelect,
        document.getElementById('grade'),
        classSelect,
        document.getElementById('number')
    ];
    
    let hasError = false;
    const errorMessages = []; // 오류 메시지를 저장할 배열

    // 입력값 검사
    inputs.forEach((input, index) => {
        if (!inputFields[index]) {
            input.classList.add('error'); // 오류 시 클래스 추가
            hasError = true;
            errorMessages.push(`${inputs[index].previousElementSibling.textContent} 필드를 입력하세요.`); // 필드 이름 추가
        } else {
            input.classList.remove('error'); // 정상 입력 시 클래스 제거
        }
    });

    // 아이디 유효성 검사
    if (username.length < 6 || /[\u3131-\u3163\uac00-\ud7a3]/.test(username)) {
        errorMessages.push('아이디는 6글자 이상이어야 하며 한글을 포함할 수 없습니다.');
        hasError = true;
    }

    // 비밀번호 유효성 검사
    if (password.length < 6) {
        errorMessages.push('비밀번호는 6글자 이상이어야 합니다.');
        hasError = true;
    }

    // 기호 포함 여부 검사
    const specialCharPattern = /[!@#$%^&*(),.?":{}|<>]/; // 기호 패턴
    if (!specialCharPattern.test(password)) {
        errorMessages.push("비밀번호는 하나 이상의 기호를 포함해야 합니다.");
        hasError = true;
    }

    if (!/[a-z]/.test(password)) {
        errorMessages.push("비밀번호는 최소 하나의 영문자를 포함해야 합니다.");
        hasError = true;
    }
    if (!/[0-9]/.test(password)) {
        errorMessages.push("비밀번호는 최소 하나의 숫자를 포함해야 합니다.");
        hasError = true;
    }

    if (hasError) {
        errorMessage.innerHTML = errorMessages.join('<br>'); // 오류 메시지를 줄바꿈으로 연결
        errorMessage.style.display = 'block'; // 오류 메시지 표시
    } else {
        // 데이터 객체 생성
        const data = {
            username,
            password,
            name,
            department,
            grade,
            class: classValue,
            number
        };

        // fetch 요청
        console.log('서버 요청 전');
        fetch('http://localhost/register.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data), // URLSearchParams로 변환
        })
        .then(response => {
            console.log('응답 수신:', response); // 응답 로그
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json(); // JSON으로 변환
        })
        .then(data => {
            console.log('서버 응답 데이터:', data); // 서버 응답 데이터 로그
            if (data.message === '회원가입이 완료되었습니다') {
                // 알림 표시
                alert('회원가입이 완료되었습니다.');

                // 1초 후 로그인 페이지로 이동
                setTimeout(() => {
                    window.location.href = '../login/login.html'; // 로그인 페이지로 이동
                }, 1000); // 1000ms = 1초
            } else {
                errorMessage.innerHTML = data.message; // 서버에서 전달된 메시지
                errorMessage.style.display = 'block'; // 오류 메시지 표시
            }
        })
        .catch(error => {
            console.error('Error:', error); // 에러 메시지를 콘솔에 출력
        });
    }
});
