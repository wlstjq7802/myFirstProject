<?php
    include_once "dbcon.php";
    $id = $_POST['id'];

    $sql = "SELECT * FROM userInfo WHERE id='$id'";

    $result = mq($sql);
    $count = mysqli_num_rows($result);

    if($count == 0){
        // 중복 ID있음
        echo 1;
    }
    else{
        // 중복 ID 없음
        echo 0;
    }

    

?>