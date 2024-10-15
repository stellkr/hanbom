<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'hanbomadmin') {
    echo "관리자만 접근할 수 있습니다.";
    exit();
}

$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'hanbom';
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

$sql = "SELECT * FROM resumes";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 페이지 - 자기소개서 목록</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #4CAF50;
            padding: 20px;
            text-align: center;
            color: white;
        }
        main {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>관리자 페이지</h1>
    </header>
    <main>
        <h2>등록된 자기소개서 목록</h2>
        <table>
            <thead>
                <tr>
                    <th>이름</th>
                    <th>아이디</th>
                    <th>자소서</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><a href="resume_detail.php?id=<?php echo $row['id']; ?>">자세히보기</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </main>
</body>
</html>

<?php $conn->close(); ?>

<?php
// download_image.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'hanbomadmin') {
    echo "관리자만 접근할 수 있습니다.";
    exit();
}

$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'hanbom';

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit();
}

$resume_id = intval($_GET['id']);
$sql = "SELECT * FROM resumes WHERE id = $resume_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // 출력 버퍼 비우기
    if (ob_get_level()) {
        ob_end_clean();
    }

    // GD 라이브러리를 사용하여 고화질 이미지 생성
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="resume_' . $resume_id . '.png"');
    $image = imagecreatetruecolor(1200, 1600); // 고화질 이미지를 위해 큰 크기 설정

    $background_color = imagecolorallocate($image, 255, 255, 255); // 흰색 배경
    $text_color = imagecolorallocate($image, 0, 0, 0); // 검정색 글자

    imagefilledrectangle($image, 0, 0, 1200, 1600, $background_color);

    // 텍스트 설정
    $text = "이름: " . $row['name'] . "\n" .
            "아이디: " . $row['username'] . "\n" .
            "이메일: " . $row['email'] . "\n" .
            "전화번호: " . $row['phone'] . "\n" .
            "주소: " . $row['address'] . "\n" .
            "\n학력사항:\n" .
            "입학일: " . $row['entrance_date_1'] . "\n" .
            "졸업일: " . $row['graduation_date_1'] . "\n" .
            "학교명: " . $row['school_name_1'] . "\n" .
            "전공: " . $row['major_1'] . "\n";

    $font_path = __DIR__ . '/arial.ttf'; // 폰트 경로 (arial.ttf 파일 필요)
    if (!file_exists($font_path)) {
        echo "폰트 파일을 찾을 수 없습니다.";
        exit();
    }
    imagettftext($image, 24, 0, 50, 100, $text_color, $font_path, $text); // 고화질을 위해 큰 글씨 크기 설정

    // 이미지 출력
    imagepng($image);
    imagedestroy($image);
    exit();
} else {
    echo "해당 자기소개서를 찾을 수 없습니다.";
}

$conn->close();
?>