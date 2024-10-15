<?php
// 데이터베이스 연결
session_start();
$username = $_SESSION['username'];

$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "hanbom";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// POST로 받은 데이터
$name = $_POST['name'];
$birthdate = $_POST['birthdate'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$photo = $_FILES['photo']['name'];  // 파일 업로드는 따로 처리해야 합니다

// 학력사항 1
$entrance_date_1 = $_POST['entrance_date_1'];
$graduation_date_1 = $_POST['graduation_date_1'];
$school_name_1 = $_POST['school_name_1'];
$major_1 = $_POST['major_1'];

// 학력사항 2
$entrance_date_2 = $_POST['entrance_date_2'];
$graduation_date_2 = $_POST['graduation_date_2'];
$school_name_2 = $_POST['school_name_2'];
$major_2 = $_POST['major_2'];

// 자격증
$certificate_date = $_POST['certificate_date'];
$certificate_name = $_POST['certificate_name'];
$certificate_authority = $_POST['certificate_authority'];

// 수상
$award_date = $_POST['award_date'];
$award_name = $_POST['award_name'];
$award_authority = $_POST['award_authority'];

// 행교육 및 연수 활동
$event_date = $_POST['event_date'];
$event_name = $_POST['event_name'];
$event_authority = $_POST['event_authority'];

// 자기소개서
$motivation = $_POST['motivation'];
$experience = $_POST['experience'];
$strengths = $_POST['strengths'];
$growth = $_POST['growth'];

// 파일 저장
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["photo"]["name"]);
move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);

// 데이터베이스 삽입 쿼리
$sql = "INSERT INTO resumes (username, name, birthdate, email, phone, address, photo, entrance_date_1, graduation_date_1, school_name_1, major_1, entrance_date_2, graduation_date_2, school_name_2, major_2, motivation, experience, strengths, growth) 
VALUES ('$username', '$name', '$birthdate', '$email', '$phone', '$address', '$photo', '$entrance_date_1', '$graduation_date_1', '$school_name_1', '$major_1', '$entrance_date_2', '$graduation_date_2', '$school_name_2', '$major_2', '$motivation', '$experience', '$strengths', '$growth')";

if ($conn->query($sql) === TRUE) {
    $resume_id = $conn->insert_id; // 삽입된 이력서의 ID를 가져옴

        // 자격증 정보 삽입 (별도의 certificates 테이블에 저장)
        // 수상 정보 삽입
        if (isset($_POST['award_date']) && is_array($_POST['award_date'])) {
            $award_dates = $_POST['award_date'];
            $award_names = $_POST['award_name'];
            $award_authorities = $_POST['award_authority'];
        
            foreach ($award_dates as $index => $award_date) {
                if (isset($award_names[$index]) && isset($award_authorities[$index])) {
                    $award_name = $award_names[$index];
                    $award_authority = $award_authorities[$index];
                    $sql_award = "INSERT INTO awards (resume_id, award_date, award_name, award_authority)
                                  VALUES ('$resume_id', '$award_date', '$award_name', '$award_authority')";
                    $conn->query($sql_award);
                }
            }
        }
        
        // 자격증 정보 삽입
        if (isset($_POST['certificate_date']) && is_array($_POST['certificate_date'])) {
            $certificate_dates = $_POST['certificate_date'];
            $certificate_names = $_POST['certificate_name'];
            $certificate_authorities = $_POST['certificate_authority'];

            foreach ($certificate_dates as $index => $certificate_date) {
                if (isset($certificate_names[$index]) && isset($certificate_authorities[$index])) {
                    $certificate_name = $certificate_names[$index];
                    $certificate_authority = $certificate_authorities[$index];
                    $sql_certificate = "INSERT INTO certificates (resume_id, certificate_date, certificate_name, certificate_authority)
                                        VALUES ('$resume_id', '$certificate_date', '$certificate_name', '$certificate_authority')";
                    $conn->query($sql_certificate);
                }
            }
        }

        // 행사 정보 삽입
        if (isset($_POST['event_date']) && is_array($_POST['event_date'])) {
            $event_dates = $_POST['event_date'];
            $event_names = $_POST['event_name'];
            $event_authorities = $_POST['event_authority'];

            foreach ($event_dates as $index => $event_date) {
                if (isset($event_names[$index]) && isset($event_authorities[$index])) {
                    $event_name = $event_names[$index];
                    $event_authority = $event_authorities[$index];
                    $sql_event = "INSERT INTO events (resume_id, event_date, event_name, event_authority)
                                        VALUES ('$resume_id', '$event_date', '$event_name', '$event_authority')";
                    $conn->query($sql_event);
                }
            }
        }

    echo "자기소개서가 성공적으로 등록되었습니다.";
} else {
    echo "에러: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
