<?php
    include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    

    $roomId = (int)$_POST['roomId'];
    $userId = $_POST['userId'];
    
    
    $msgCntSql = "SELECT msg_id FROM msgReadChk where room_id = '$roomId' AND receiver_id = '$userId'";
    $msgCntResult = mq($msgCntSql);
    $msgCnt = mysqli_num_rows($msgCntResult);
    
    if($msgCnt > 0){
        
            $resultArr['result'] = "success";
            $resultArr['msgCnt'] = $msgCnt;
        
    } else{
        $resultArr['result'] = "fail";
    }

    echo json_encode($resultArr);
    

?>