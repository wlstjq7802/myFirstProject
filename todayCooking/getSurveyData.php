<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    $userId = $_POST['userId'];

    $Sql = "SELECT * FROM userInfo WHERE id = '$userId'";
    // $recipeSql = "SELECT * FROM recipe";
    $result = mq($Sql);
    $result = mysqli_fetch_array($result);
    $recsult2 = mysqli_num_rows($sql2);


    if(isset($result['survey'])&& $result['survey']!=""){
        echo "true";
    } else{
        echo "false";
    }
    
    
?>