<?php
    // 로그인
    include_once "dbcon.php";  
    include_once "jwtExample.php";

    $userId = $_POST['userId'];
    $userPass = $_POST['userPass'];
    $responsArr = array();

    $sql = "SELECT * FROM userInfo WHERE userId='$userId' AND password = '$userPass'";

    $result = mq($sql);
    $count = mysqli_num_rows($result);
    $result = mysqli_fetch_array($result);

    if($count != 0){
        $responsArr['result'] = "success";
        $responsArr['token'] = encodeJWT($userId, $userPass);
        $responsArr['userId'] = $userId;
        $responsArr['nick'] = $result['nick'];
        $responsArr['profile'] = $result['profileImg'];

        echo json_encode($responsArr, JSON_UNESCAPED_UNICODE);
    } else{
        $responsArr['result'] = "fail";
        $responsArr['token'] = "null";
        echo json_encode($responsArr, JSON_UNESCAPED_UNICODE);
    }


     
    
    

    

?>
