<?php

    // error_reporting( E_ALL );
    // ini_set( "display_errors", 1);
    include_once "jwtExample.php";
    include_once "dbcon.php";  

    $token = $_POST['token'];

    
    $decoded = decodeJWT($token);
    $decode_array = (array)$decoded;

    $userId =  $decode_array['userId'];
    $userPass =  $decode_array['pass'];


    $sql = "SELECT userId, nick, profileImg FROM userInfo WHERE userId='$userId' AND password = '$userPass'";

    $result = mq($sql);
    $count = mysqli_num_rows($result);

    if($count > 0){
        $userInfoArr = mysqli_fetch_array($result);

        $responsArr['result'] = "success";
        $responsArr['userId'] = $userId;
        $responsArr['nick'] = $userInfoArr['nick'];
        $responsArr['profile'] = $userInfoArr['profileImg'];

        echo json_encode($responsArr, JSON_UNESCAPED_UNICODE);
    } else{
        $responsArr['result'] = "fail";
        $responsArr['userId'] = "null";
        echo json_encode($responsArr, JSON_UNESCAPED_UNICODE);
    }


?>