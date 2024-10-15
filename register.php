<?php
// 오류 표시 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *'); // CORS 허용
header('Access-Control-Allow-Methods: POST'); // POST 메서드 허용
header('Access-Control-Allow-Headers: Content-Type'); // Content-Type 허용
header('Content-Type: application/json'); // JSON 응답 헤더 추가

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST 요청으로 받은 데이터
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $department = $_POST['department'];
    $grade = $_POST['grade'];
    $class = $_POST['class'];
    $number = $_POST['number'];

    // 데이터베이스 연결 (PDO 예시)
    try {
        // 기본 사용자 'root'와 비밀번호를 비워둡니다.
        $pdo = new PDO('mysql:host=localhost;dbname=hanbom;charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo json_encode(['message' => '데이터베이스 연결 실패: ' . $e->getMessage()]);
        exit();
    }

    // 사용자 이름이 이미 존재하는지 확인
    $checkUser = "SELECT COUNT(*) FROM users WHERE username = ?";
    $stmt = $pdo->prepare($checkUser);
    $stmt->execute([$username]);
    $userExists = $stmt->fetchColumn();

    if ($userExists > 0) {
        echo json_encode(['message' => '이미 존재하는 사용자 이름입니다.']);
        exit();
    }

    // 학과, 학년, 반, 번호가 모두 일치하는지 확인
    $checkExisting = "SELECT COUNT(*) FROM users WHERE department = ? AND grade = ? AND class = ? AND number = ?";
    $stmt = $pdo->prepare($checkExisting);
    $stmt->execute([$department, $grade, $class, $number]);
    $existingCount = $stmt->fetchColumn();

    if ($existingCount > 0) {
        echo json_encode(['message' => '해당 정보가 이미 존재합니다. 회원가입을 거부합니다.']);
        exit();
    }

    // 데이터 삽입 쿼리
    $sql = "INSERT INTO users (username, password, name, department, grade, class, number) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([$username, $password, $name, $department, $grade, $class, $number]);
        echo json_encode(['message' => '회원가입이 완료되었습니다']);
    } catch (PDOException $e) {
        echo json_encode(['message' => '회원가입 실패: ' . $e->getMessage()]);
    }
}
?>