<?php
// 세션 시작
session_start();

// 세션 변수 제거
$_SESSION = [];

// 세션 종료
session_destroy();

// 로그아웃 후 리다이렉트
header('Location: main/index.html'); // 로그인 페이지로 리다이렉트
exit();
?>