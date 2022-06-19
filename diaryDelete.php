<?php 
    include "dbcon.php"; // db 연결 파일
    
    $diaryId = $_POST["diaryId"];

    // db에 포스트 저장하기
    $sql = "
        DELETE FROM diary WHERE id = '$diaryId'
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