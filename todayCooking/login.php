<?php
    // db 연결코드
    include_once "dbcon.php";

    //넘어온 폼 데이터 id, pw
    $id = $_POST['id'];
    $pw = $_POST['pw'];

    $sql = "SELECT * FROM userInfo WHERE id='$id' AND (passwd='$pw')";
    // echo  $sql;
    $result = mq($sql);
    $member = mysqli_fetch_array($result);
    print_r($member);

    // 아이디와 비밀번호가 일치하지 않으면 실패(0)
    if($member==0){
        echo 1;
    // 일치하면 성공(0이 아님)
    }else{
        echo 0;
        exit();
    }


?>