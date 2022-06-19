<?php
    include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); 


    $userId = $_POST['userId'];
    $output = array();

    $sql = "SELECT * FROM userInfo WHERE userId = '$userId'";
    $sqlResult = mq($sql);
    $count = mysqli_num_rows($sqlResult);
    
    if($count > 0){
        $userInfo = mysqli_fetch_array($sqlResult);
        $output['result'] = "success";
        $output['nick'] = $userInfo['nick'];
        $output['profileImg'] = $userInfo['profileImg'];
        $output['smkCessTime'] = $userInfo['smkCessTime'];

    } else{
        $output['result'] = "fail";
    }

    echo json_encode($output);
    

?>