<?php
    include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    
    // $sql = "
    //     SELECT * FROM healthCenter WHERE centerName LIKE '%서구%'
    // ";


    $sql = "
        SELECT * FROM healthCenter WHERE MATCH (centerName) AGAINST ('서구')
    ";

    
    $sqlResult = mq($sql);
    $sqlResult = mysqli_fetch_array($sqlResult);

    echo count($sqlResult);

    // if($sqlResult){
        
    //     $sqlResult = mysqli_fetch_array($sqlResult);
    //         // // 행별로 유저의 정보 output에 넣어주기
    //         // while ($row = mysqli_fetch_assoc($sqlResult)) {

                
    //         // }

    //         echo json_encode($sqlResult);

        
    // } else{
        
    // }
    

?>