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

// PDF 저장 기능
if (isset($_GET['action']) && $_GET['action'] == 'pdf') {
    require_once('tcpdf/tcpdf.php');
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('관리자');
    $pdf->SetTitle('자기소개서 상세 정보');
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->AddPage();

    // HTML 내용 생성
    ob_start();
    include('resume_detail_content.php'); // PDF 생성용 HTML 콘텐츠 파일 포함
    $html = ob_get_clean();
    
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('자기소개서_상세_정보.pdf', 'I'); // 브라우저에서 PDF로 출력
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>자기소개서 세부 정보</title>
    <link rel="stylesheet" href="resume_styles.css">
    <style>
        .container { max-width: 900px; margin: auto; padding: 20px; }
        fieldset { border: 2px solid #990000; padding: 10px; margin-top: 20px; }
        legend { font-weight: bold; color: #990000; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #990000; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f5f5f5; }
        p { margin: 0; padding: 5px 0; }
        textarea { width: 100%; box-sizing: border-box; padding: 10px; }

        /* Fixed column widths for consistency across tables */
        .fixed-table th, .fixed-table td {
            width: 33.33%;
        }
    </style>
</head>
<body>

<div class="container">
    <h1><?php echo $row['name']; ?>님의 자기소개서</h1>
    <a href="?id=<?php echo $resume_id; ?>&action=pdf" style="display: inline-block; margin-bottom: 20px;">PDF로 저장</a>
    
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
                <input type="text" value="<?php echo $row['name']; ?>" readonly><br>
                
                <label>생년월일</label>
                <input type="date" value="<?php echo $row['birthdate']; ?>" readonly><br>
                
                <label>E-Mail</label>
                <input type="email" value="<?php echo $row['email']; ?>" readonly><br>
                
                <label>휴대폰</label>
                <input type="text" value="<?php echo $row['phone']; ?>" readonly><br>
                
                <label>주소</label>
                <input type="text" value="<?php echo $row['address']; ?>" readonly><br>
            </div>
        </div>
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

    <!-- 자기소개서 -->
    <fieldset>
        <legend>[자기소개서]</legend>
        <p><strong>지원 동기 및 입사 후 포부:</strong></p>
        <textarea readonly><?php echo $row['motivation']; ?></textarea>
        <p><strong>학창 시절의 경험과 역량 개발을 위한 노력:</strong></p>
        <textarea readonly><?php echo $row['experience']; ?></textarea>
        <p><strong>장점과 보완점:</strong></p>
        <textarea readonly><?php echo $row['strengths']; ?></textarea>
        <p><strong>성장 과정과 가치관:</strong></p>
        <textarea readonly><?php echo $row['growth']; ?></textarea>
    </fieldset>

</div>

</body>
</html>

<?php $conn->close(); ?>
