<?php
// error_reporting( E_ALL );
// ini_set( "display_errors", 1);

require_once './vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
$secret_key = "aabbaa";
$alg = 'HS256';

function decodeJWT($jwt){
    // $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
    
    try { 
        $decoded = JWT::decode($jwt, new Key("aabbaa", 'HS256')); 
    } catch (Exception $e) {
         echo 'Exception catched: ', $e->getMessage(), "\n"; 
    }

    $arr = (array) $decoded;
    

    return $arr;
}

function encodeJWT($userId, $pass){
    $data = array(
        'userId' => $userId,
        'pass' => $pass
    );

    $jwt = JWT::encode($data, "aabbaa", "HS256");
    return $jwt;
}

//   $jwk = encodeJWT("wlstjq12", "user1");
//   echo $jwk. "<br><br>";

//   $decode = decodeJWT($jwk);
//     echo $decode['userId'];


 
// $data = array(
//     'id' => "wlstjq12",
//     'nick' => "user1",
//     'phone' => "010-3333-5555"
// );
 
// $jwt = JWT::encode($data, $secret_key, 'HS256');
// echo "jwt: ". $jwt . "<br><br>";
 
// $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
// $arr = (array) $decoded;

// echo $arr['id'];



?>