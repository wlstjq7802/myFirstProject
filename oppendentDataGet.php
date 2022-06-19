<?php
    include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    $userId = $_POST['userId'];
    $writer = $_POST['writer'];
    
    $sql = "SELECT joinId FROM ChatRoomJoin WHERE userId = '$userId' and roomId = '$writer'";
    $sqlResult = mq($sql);

    $count = mysqli_num_rows($sqlResult);
    if($count > 0){

        $result = mysqli_fetch_array($sqlResult);

        $resultArr['result'] = "success";
        $resultArr['roomId'] = $result['joinId'];

    } else{
        $resultArr['result'] = "fail";

    }
    echo json_encode($resultArr);

?>