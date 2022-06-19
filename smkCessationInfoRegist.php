<?php 
    include "dbcon.php"; // db 연결 파일


    $userId = $_POST["userId"];
    $patchName = $_POST["patchName"];
    $patchStartTime = $_POST['patchStartTime'];

    $sql1 = "
        SELECT userId From smkCessationInfo WHERE
        userId = '$userId'
    ";

    $sqlResult = mq($sql1);
    $sqlResult = mysqli_num_rows($sqlResult);
    
    // 기존에 데이터가 있다면
    if($sqlResult > 0){
        $sql2 = "
            update smkCessationInfo SET
            patchName = '$patchName',
            patchStartTime = '$patchStartTime'
            WHERE userId = '$userId'
        ";
    } else{
        // db에 포스트 저장하기
        $sql2 = "
        INSERT INTO smkCessationInfo SET
        userId = '$userId',
        patchName = '$patchName',
        patchStartTime = '$patchStartTime'
        ";
    }

    

    $sqlResult = mq($sql2);
    
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