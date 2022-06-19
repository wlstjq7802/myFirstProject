<?php

include "dbcon.php"; // db 연결 파일


    $userId = $_POST["userId"];
    $startTime = $_POST["startTime"];
    $cigaretteCount = $_POST["cigaretteCount"]; 
    $cigarettePrice = $_POST["cigarettePrice"];

    $reason1 = $_POST["reason1"];
    $reason2 = $_POST["reason2"];
    $reason3 = $_POST["reason3"];

    $reasonArr = array();

    $reasonArr[] = $reason1;
    $reasonArr[] = $reason2;
    $reasonArr[] = $reason3;


    $reasonArr = json_encode($reasonArr, JSON_UNESCAPED_UNICODE);


    $sql = "
    update userInfo SET
        smkCessTime = '$startTime',
        cigaretteCount = '$cigaretteCount',
        cigarettePrice = '$cigarettePrice',
        smkCessationReason = '$reasonArr'
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


    // 배열을 json 문자열로 변환하여 클라이언트에 전달
    echo $result;


?>