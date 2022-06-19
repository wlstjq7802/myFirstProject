<?php
    include_once "dbcon.php";   

    $userId = $_POST['userId'];
    $responsArr = array();

    $sql = "SELECT * FROM userInfo WHERE userId='$userId'";

    $result = mq($sql);
    $count = mysqli_num_rows($result);

    if($count > 0){
        $result = mysqli_fetch_array($result);
        if(isset($result['smkCessTime'])&& isset($result['cigaretteCount'])&& isset($result['cigarettePrice'])){
            $responsArr['smkCessTime'] = $result['smkCessTime'];
            $responsArr['cigaretteCount'] = $result['cigaretteCount'];
            $responsArr['cigarettePrice'] = $result['cigarettePrice'];
            $responsArr['smkCessationReason'] = $result['smkCessationReason'];
            $responsArr['result'] = "success";
            echo json_encode($responsArr, JSON_UNESCAPED_UNICODE);
        } else{
            $responsArr['result'] = "fail";
            echo json_encode($responsArr, JSON_UNESCAPED_UNICODE);
        }
        
    } else{
        $responsArr['result'] = "fail";
        echo json_encode($responsArr, JSON_UNESCAPED_UNICODE);
    }


?>
