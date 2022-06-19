<?php

    include_once "dbcon.php";   
    include_once "jwtExample.php";


    $token = $_POST['token'];

    $decoded = decodeJWT($token);

    $decode_array = (array)$decoded;

    $userId =  $decode_array['userId'];
    
    if(is_null($userId)){
        echo "fail";
    } else{
        echo $userId;
    }
    

    

?>