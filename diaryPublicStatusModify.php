<?php 
    include "dbcon.php"; // db 연결 파일


    $isShare = $_POST['isShare'];
    $diaryId = $_POST['diaryId'];
    

    // db에 포스트 저장하기
    $sql = "
        UPDATE diary SET
            isShare = '$isShare'
            WHERE id = '$diaryId'
    ";

    $sqlResult = mq($sql);
    
    if($sqlResult) {
        // 포스트 저장 완료
        $result = "success";

    } else {
        // 리뷰 저장 실패
        $result = "fail";
    }


    // 배열을 json 문자열로 변환하여 클라이언트에 전달
    echo $result;

?>