<?php
// id 중복확인

include_once "dbcon.php";

    $userId = $_POST['userId'];

    $sql = "SELECT * FROM userInfo WHERE userId='$userId'";

    $result = mq($sql);
    $count = mysqli_num_rows($result);

    if($count == 0){
        // 중복 ID 없음(사용 가능)
        echo "success";
    }
    else{
        // 중복 ID있음(사용 불가)
        echo "fail";
    }


?>