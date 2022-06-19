<?php 
    include "dbcon.php"; // db 연결 파일
    
    $compensationId = $_POST["compensationId"];
    $status = $_POST["status"];

    // Bpurchasing = 구매전
    // Apurchasing = 구매후
    // ADelete = 구매 후 삭제

    if($status == "Bpurchasing"){
        $sql = "
            DELETE FROM compensation WHERE id = '$compensationId'
        ";
    } else if($status == "Apurchasing"){
        $sql = "
        update compensation SET
            status = 'ADelete'
            WHERE id = '$compensationId'
        ";
    }

    
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