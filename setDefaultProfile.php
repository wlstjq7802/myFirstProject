<?php 
    include "dbcon.php"; // db 연결 파일
    
    $userId = $_POST["userId"];
    
    $sql = "
    update userInfo SET
        profileImg = 'profile.jpg'
        WHERE userId = '$userId'
    ";

    $sqlResult = mq($sql);
    
    if($sqlResult) {
        // 포스트 저장 완료
        $result = "success";

    } else {
        // 리뷰 저장 실패
        $result = "fail";
    }

    echo $result;

?>