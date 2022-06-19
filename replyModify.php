<?php 
    include "dbcon.php"; // db 연결 파일
    
    $comment = $_POST["comment"];
    $replyId = $_POST["replyId"];


    // db에 포스트 저장하기
    $sql = "
    update reply SET
        comment = '$comment',
        isModified = true
        WHERE replyId = '$replyId'
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