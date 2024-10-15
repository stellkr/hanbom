<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>자기소개서 세부 정보</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resume_styles.css">
    <style>
        body {font-family: 'Noto Sans KR', sans-serif;}
        .container { max-width: 1200px; margin: auto; padding: 20px; }
        fieldset { border: 2px solid #990000; padding: 10px; margin-top: 20px; }
        legend { font-weight: bold; color: #990000; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #990000; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f5f5f5; }
        p { margin: 0; padding: 5px 0; }
        .field-value { padding: 10px; background-color: #f9f9f9; border: 1px solid #ccc; }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    </head>
<body>

<div class="container">
    <!-- 상세보기 내용 -->
    <div id="resume-content">
        <?php
        session_start();
        if ($_SESSION['username'] !== 'hanbomadmin') {
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

        $resume_id = $_GET['id'];
        // 기본 자기소개서 정보 가져오기
        $sql_resume = "SELECT * FROM resumes WHERE id = $resume_id";
        $result_resume = $conn->query($sql_resume);

        // 자격증, 수상, 교육 및 연수 활동 정보 가져오기
        $sql_certificates = "SELECT * FROM certificates WHERE resume_id = $resume_id";
        $result_certificates = $conn->query($sql_certificates);

        $sql_awards = "SELECT * FROM awards WHERE resume_id = $resume_id";
        $result_awards = $conn->query($sql_awards);

        $sql_events = "SELECT * FROM events WHERE resume_id = $resume_id";
        $result_events = $conn->query($sql_events);

        // 기본 자기소개서 정보 가져오기
        $sql_resume = "SELECT * FROM resumes WHERE id = $resume_id";
        $result_resume = $conn->query($sql_resume);

        if ($result_resume->num_rows > 0) {
            $row = $result_resume->fetch_assoc();
            ?>
            <h1><?php echo $row['name']; ?>님의 자기소개서</h1>

            <!-- 연락사항 -->
            <fieldset>
                <legend>[연락사항]</legend>
                <div class="contact-info" style="display: flex; gap: 20px; align-items: flex-start;">
                    <div class="photo">
                        <label>사진 (3 x 4)</label><br>
                        <?php if ($row['photo']) { ?>
                            <img src="uploads/<?php echo $row['photo']; ?>" alt="사진" style="width: 200px; height: 250px; border: 1px solid #ccc; margin-top: 10px;">
                        <?php } else { ?>
                            <p>사진이 없습니다.</p>
                        <?php } ?>
                    </div>
                    <div class="info" style="flex: 1;">
                        <label>성명</label>
                        <div class="field-value"><?php echo $row['name']; ?></div><br>
                        
                        <label>생년월일</label>
                        <div class="field-value"><?php echo $row['birthdate']; ?></div><br>
                        
                        <label>E-Mail</label>
                        <div class="field-value"><?php echo $row['email']; ?></div><br>
                        
                        <label>휴대폰</label>
                        <div class="field-value"><?php echo $row['phone']; ?></div><br>
                        
                        <label>주소</label>
                        <div class="field-value"><?php echo $row['address']; ?></div><br>
                    </div>
                </div>
            </fieldset>


            <!-- 자격증 및 특기사항 -->
        <fieldset>
            <legend>[자격증 및 특기사항]</legend>
            <h3>자격증</h3>
            <table class="fixed-table">
                <tr>
                    <th>해당년월일</th>
                    <th>관련 내용</th>
                    <th>시험처</th>
                </tr>
                <?php while($certificate = $result_certificates->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $certificate['certificate_date']; ?></td>
                        <td><?php echo $certificate['certificate_name']; ?></td>
                        <td><?php echo $certificate['certificate_authority']; ?></td>
                    </tr>
                <?php } ?>
            </table>

            <h3>수상</h3>
            <table class="fixed-table">
                <tr>
                    <th>해당년월일</th>
                    <th>관련 내용</th>
                    <th>수상처</th>
                </tr>
                <?php while($award = $result_awards->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $award['award_date']; ?></td>
                        <td><?php echo $award['award_name']; ?></td>
                        <td><?php echo $award['award_authority']; ?></td>
                    </tr>
                <?php } ?>
            </table>

            <h3>교육 및 연수 활동</h3>
            <table class="fixed-table">
                <tr>
                    <th>해당년월일</th>
                    <th>관련 내용</th>
                    <th>기관</th>
                </tr>
                <?php while($event = $result_events->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $event['event_date']; ?></td>
                        <td><?php echo $event['event_name']; ?></td>
                        <td><?php echo $event['event_authority']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </fieldset>

            <!-- 학력사항 -->
            <fieldset>
                <legend>[학력사항]</legend>
                <table>
                    <tr>
                        <th>입학년월</th>
                        <th>졸업년월</th>
                        <th>학교명</th>
                        <th>학과</th>
                        <th>비고</th>
                    </tr>
                    <tr>
                        <td><?php echo $row['entrance_date_1']; ?></td>
                        <td><?php echo $row['graduation_date_1']; ?></td>
                        <td><?php echo $row['school_name_1']; ?></td>
                        <td><?php echo $row['major_1']; ?></td>
                        <td>졸업 예정</td>
                    </tr>
                    <tr>
                        <td><?php echo $row['entrance_date_2']; ?></td>
                        <td><?php echo $row['graduation_date_2']; ?></td>
                        <td><?php echo $row['school_name_2']; ?></td>
                        <td><?php echo $row['major_2']; ?></td>
                        <td>졸업</td>
                    </tr>
                </table>
            </fieldset>

            <!-- 자기소개서 -->
            <fieldset>
                <legend>[자기소개서]</legend>
                <p><strong>지원 동기 및 입사 후 포부:</strong></p>
                <div class="field-value"><?php echo nl2br($row['motivation']); ?></div>
                <p><strong>학창 시절의 경험과 역량 개발을 위한 노력:</strong></p>
                <div class="field-value"><?php echo nl2br($row['experience']); ?></div>
                <p><strong>장점과 보완점:</strong></p>
                <div class="field-value"><?php echo nl2br($row['strengths']); ?></div>
                <p><strong>성장 과정과 가치관:</strong></p>
                <div class="field-value"><?php echo nl2br($row['growth']); ?></div>
            </fieldset>
        <?php } else { ?>
            <p>해당 자기소개서를 찾을 수 없습니다.</p>
        <?php } ?>
    </div>

    <!-- 이미지 다운로드 버튼 -->
    <button id="download-png">이미지 다운로드</button>

    <!-- 이미지 다운로드 링크 (숨김 처리된 링크) -->
    <p id="download-link" style="display:none;">이미지 다운로드: <a id="image-link" href="#" download>이미지 파일</a></p>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.getElementById('download-png').addEventListener('click', function() {
    html2canvas(document.getElementById('resume-content'), {
        scale: 2,  // 해상도 향상
        useCORS: true,  // 외부 리소스 문제 해결
        onclone: function(clonedDoc) {
            // 복제된 문서에서 폰트가 적용되었는지 확인
            clonedDoc.fonts.ready.then(function() {
                console.log("폰트 로드 완료 후 캡처 시작");
            });
        }
    }).then(function(canvas) {
        var image = canvas.toDataURL('image/png');

        // 이미지 다운로드 링크 생성
        var downloadLink = document.getElementById('download-link');
        var imageLink = document.getElementById('image-link');

        downloadLink.style.display = 'block';  // 다운로드 링크 표시
        imageLink.href = image;  // 이미지 경로 설정
        imageLink.download = 'resume_image.png';  // 파일명 설정
    }).catch(function(error) {
        console.log("이미지 생성 중 오류:", error);
    });
});
</script>

</body>
</html>
