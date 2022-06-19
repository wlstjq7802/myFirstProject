<?php 
    include "dbcon.php"; // db 연결 파일


    $jsonData = $_POST["jsonData"];

    $jsonData = json_decode($jsonData, true);

    $sqlResult = "";

    for($i = 0;  $i < count($jsonData); $i++){
        $centerName = $jsonData[$i]["centerName"];
        $address = $jsonData[$i]["address"]; // 첨부된 사진 개수
        $phoneNum = $jsonData[$i]['phoneNum'];

        // db에 포스트 저장하기
        $sql = "
            INSERT INTO healthCenter SET
                centerName = '$centerName',
                address = '$address',
                phoneNum = '$phoneNum'
        ";
        $sqlResult = mq($sql);
        if($sqlResult){

        }else{
            break;
        }

    }

    // $sqlResult = mq($sql);
    
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