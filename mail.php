<?php
// composer로 다운로드된 라이브러리 참조하기
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


function sendMail($address, $randomNum){
    // PHPMailer 선언
    $mail = new PHPMailer(true);
    // 디버그 모드(production 환경에서는 주석 처리한다.)
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    // SMTP 서버 세팅
    $mail->isSMTP();
    try {
    // 구글 smtp 설정
    $mail->Host = "smtp.naver.com";
    // SMTP 암호화 여부
    $mail->SMTPAuth = true;
    // SMTP 포트
    $mail->Port = 465;
    // SMTP 보안 프초트콜
    $mail->SMTPSecure = "ssl";
    // naver 유저 아이디
    $mail->Username = "wlstjq7802@naver.com";
    // naver 패스워드
    $mail->Password ="wkd345926!";
    // 인코딩 셋
    $mail->CharSet = 'utf-8';
    $mail->Encoding = "base64";
    // 보내는 사람
    $mail->setFrom('wlstjq7802@naver.com', '금연도우미');
    // 받는 사람
    $mail->AddAddress($address);
    // 본문 html 타입 설정
    $mail->isHTML(true);
    // 제목
    $mail->Subject = '인증번호';
    // 본문 (HTML 전용)
    $mail->Body = '인증번호는 '. $randomNum. " 입니다.";
    // 본문 (non-HTML 전용)
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    $mail->Send();

    echo "success";
    } catch (phpmailerException $e) {
    echo $e->errorMessage();
    } catch (Exception $e) {
    echo $e->getMessage();
    }
}




?>