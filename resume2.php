<?php
session_start();

// 데이터베이스 연결
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "hanbom";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 입력 값 받기
    $name = $_POST['name'];
    $birthdate = $_POST['birthdate'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    // 학력사항
    $entrance_date_1 = $_POST['entrance_date_1'];
    $graduation_date_1 = $_POST['graduation_date_1'];
    $school_name_1 = $_POST['school_name_1'];
    $major_1 = $_POST['major_1'];

    $entrance_date_2 = $_POST['entrance_date_2'];
    $graduation_date_2 = $_POST['graduation_date_2'];
    $school_name_2 = $_POST['school_name_2'];
    $major_2 = $_POST['major_2'];

    // 자기소개서 내용 처리
    $motivation = $_POST['motivation'];
    $experience = $_POST['experience'];
    $strengths = $_POST['strengths'];
    $growth = $_POST['growth'];

    // 사진 업로드 처리
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = $_FILES['photo']['name'];
        $photo_tmp = $_FILES['photo']['tmp_name'];

        // 업로드할 폴더 설정 (uploads 폴더가 존재해야 함)
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($photo);

        // 파일을 서버에 저장
        if (move_uploaded_file($photo_tmp, $upload_file)) {
            // 파일 업로드 성공
        } else {
            echo "파일 업로드 실패";
            exit();
        }
    } else {
        $photo = null; // 사진을 선택하지 않았을 경우
    }

    // 이력서 기본 정보 삽입
    $sql = "INSERT INTO resumes (name, birthdate, email, phone, address, photo, entrance_date_1, graduation_date_1, school_name_1, major_1, entrance_date_2, graduation_date_2, school_name_2, major_2, motivation, experience, strengths, growth) 
    VALUES ('$name', '$birthdate', '$email', '$phone', '$address', '$photo', '$entrance_date_1', '$graduation_date_1', '$school_name_1', '$major_1', '$entrance_date_2', '$graduation_date_2', '$school_name_2', '$major_2', '$motivation', '$experience', '$strengths', '$growth')";

    if ($conn->query($sql) === TRUE) {
        $resume_id = $conn->insert_id; // 삽입된 이력서의 ID를 가져옴

        // 자격증 정보 삽입 (별도의 certificates 테이블에 저장)
        if (isset($_POST['certificate_date']) && is_array($_POST['certificate_date'])) {
            $certificate_dates = $_POST['certificate_date'];
            $certificate_names = $_POST['certificate_name'];
            $certificate_authorities = $_POST['certificate_authority'];

            foreach ($certificate_dates as $index => $certificate_date) {
                $certificate_name = $certificate_names[$index];
                $certificate_authority = $certificate_authorities[$index];
                $sql_certificate = "INSERT INTO certificates (resume_id, certificate_date, certificate_name, certificate_authority)
                                    VALUES ('$resume_id', '$certificate_date', '$certificate_name', '$certificate_authority')";
                $conn->query($sql_certificate);
            }
        }

        echo "자기소개서가 성공적으로 등록되었습니다.";
    } else {
        echo "에러: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>






<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>입사 지원서</title>
    <link rel="stylesheet" href="resume_styles.css">
    <script>
        // 자격증 및 특기사항 행 추가 함수
        function addCertificateRow() {
            const table = document.getElementById("certificateTable");
            const newRow = table.insertRow(-1); // 마지막에 행 추가

            const cell1 = newRow.insertCell(0);
            const cell2 = newRow.insertCell(1);
            const cell3 = newRow.insertCell(2);
            const cell4 = newRow.insertCell(3); // 제거 버튼 셀 추가

            cell1.innerHTML = '<input type="date" name="certificate_date[]">';
            cell2.innerHTML = '<input type="text" name="certificate_name[]">';
            cell3.innerHTML = '<input type="text" name="certificate_authority[]">';
            cell4.innerHTML = '<button type="button" onclick="removeCertificateRow(this)">제거</button>'; // 제거 버튼
        }

        // 자격증 및 특기사항 행 제거 함수
        function removeCertificateRow(button) {
            const row = button.parentNode.parentNode;
            row.parentNode.removeChild(row); // 해당 행 삭제
        }
    </script>
</head>
<body>

<div class="container">
    <h1>입 사 지 원 서</h1>
    <form action="submit_resume.php" method="POST" enctype="multipart/form-data">
        <!-- 연락사항 -->
        <fieldset>
            <legend>[연락사항]</legend>
            <div class="contact-info">
                <div class="photo">
                    <label for="photo">사진 (3 x 4)</label>
                    <input type="file" id="photo" name="photo">
                </div>
                <div class="info">
                    <label for="name">성명</label>
                    <input type="text" id="name" name="name" required>
                    
                    <label for="birthdate">생년월일</label>
                    <input type="date" id="birthdate" name="birthdate" required>
                    
                    <label for="email">E-Mail</label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="phone">휴대폰</label>
                    <input type="text" id="phone" name="phone" required>

                    <label for="address">주소</label>
                    <input type="text" id="address" name="address" required>
                </div>
            </div>
        </fieldset>

        <!-- 학력사항 -->
        <fieldset>
            <legend>[학력사항]</legend>
            <table>
                <thead>
                    <tr>
                        <th>입학년월</th>
                        <th>졸업년월</th>
                        <th>학교명</th>
                        <th>학과</th>
                        <th>비고</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="date" name="entrance_date_1" required></td>
                        <td><input type="date" name="graduation_date_1" required></td>
                        <td><input type="text" name="school_name_1" required></td>
                        <td><input type="text" name="major_1" required></td>
                        <td><input type="text" name="note_1" required></td>
                    </tr>
                    <tr>
                        <td><input type="date" name="entrance_date_2"></td>
                        <td><input type="date" name="graduation_date_2"></td>
                        <td><input type="text" name="school_name_2"></td>
                        <td><input type="text" name="major_2"></td>
                        <td><input type="text" name="note_2"></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>

        <!-- 자격증 및 특기사항 -->
        <fieldset>
            <legend>[자격증 및 특기사항]</legend>
            <table id="certificateTable">
                <thead>
                    <tr>
                        <th>해당년월일</th>
                        <th>관련 내용</th>
                        <th>시험처</th>
                        <th>작업</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="date" name="certificate_date[]"></td>
                        <td><input type="text" name="certificate_name[]"></td>
                        <td><input type="text" name="certificate_authority[]"></td>
                        <td><button type="button" onclick="removeCertificateRow(this)">제거</button></td> <!-- 제거 버튼 추가 -->
                    </tr>
                </tbody>
            </table>
            <button type="button" onclick="addCertificateRow()">자격증 추가</button>
        </fieldset>

        <!-- 자기소개서 -->
        <fieldset>
            <legend>[자기소개서]</legend>

            <label for="motivation">지원 동기 및 입사 후 포부:</label>
            <textarea id="motivation" name="motivation" rows="4" cols="50" required></textarea><br>

            <label for="experience">학창 시절의 경험과 역량 개발을 위한 노력:</label>
            <textarea id="experience" name="experience" rows="4" cols="50" required></textarea><br>

            <label for="strengths">장점과 보완점:</label>
            <textarea id="strengths" name="strengths" rows="4" cols="50" required></textarea><br>

            <label for="growth">성장 과정과 가치관:</label>
            <textarea id="growth" name="growth" rows="4" cols="50" required></textarea><br>
        </fieldset>

        <!-- 서명 -->
        <fieldset class="declaration">
            <legend>[서명]</legend>
            <p>위 내용은 사실과 다름이 없습니다.</p>
            <label for="signature_date">날짜:</label>
            <input type="date" id="signature_date" name="signature_date" required>
            <br>
            <label for="signature_name">지원자 이름:</label>
            <input type="text" id="signature_name" name="signature_name" required>
            <br>
            <label for="signature">서명:</label>
            <input type="text" id="signature" name="signature" placeholder="(인)" required>
        </fieldset>

        <!-- 제출 버튼 -->
        <button type="submit">제출</button>
    </form>
</div>

</body>
</html>
