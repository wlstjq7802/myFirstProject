<?php
    include_once "mail.php";

    $userId = $_POST['userId'];
    $randomNum = $_POST['randomNum'];



    $result = sendMail($userId, $randomNum);

    echo $result;
    // if($result == "success"){
    //     echo $result;
    // } else{
    //     echo "fail";
    // }

?>