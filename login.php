<?php
session_start();

// 오류 표시 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *'); // CORS 허용
header('Access-Control-Allow-Methods: POST'); // POST 메서드 허용
header('Access-Control-Allow-Headers: Content-Type'); // Content-Type 허용
header('Content-Type: application/json'); // JSON 응답 헤더 추가

try {
    // 기본 사용자 'root'와 비밀번호를 비워둡니다.
    $pdo = new PDO('mysql:host=localhost;dbname=hanbom;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['message' => '데이터베이스 연결 실패: ' . $e->getMessage()]);
    exit();
}

// 로그인 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['username'] ?? '';
    $inputPassword = $_POST['password'] ?? '';

    // 유효성 검사
    if (empty($inputUsername) || empty($inputPassword)) {
        echo json_encode(['message' => '아이디와 비밀번호를 입력하세요.']);
        exit();
    }

    // 사용자 정보 조회
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$inputUsername]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 비밀번호 확인
    if ($user && $inputPassword === $user['password']) {
        // 로그인 성공 시 세션에 사용자 정보 저장
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['department'] = $user['department'];
        $_SESSION['grade'] = $user['grade'];
        $_SESSION['class'] = $user['class'];
        $_SESSION['number'] = $user['number'];

        echo json_encode([
            'message' => '로그인 성공',
            'name' => $user['name'],
            'department' => $user['department'],
            'grade' => $user['grade'],
            'class' => $user['class'],
            'number' => $user['number']
        ]);
    } else {
        echo json_encode(['message' => '아이디 또는 비밀번호가 잘못되었습니다.']);
    }
} else {
    echo json_encode(['message' => '잘못된 요청입니다.']);
}
?>