<?php 
    include "dbcon.php"; // db 연결 파일
    
    $compensationId = $_POST["compensationId"];
    $name = $_POST["name"];
    $price = $_POST["price"];


    // db에 포스트 저장하기
    $sql = "
    update compensation SET
        name = '$name',
        price = '$price'
        WHERE id = '$compensationId'
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