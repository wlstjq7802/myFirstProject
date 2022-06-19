<?php

    include "dbcon.php"; // db 연결 파일


    $userId = $_POST["userId"];
    $name = $_POST["compensationName"];
    $price = $_POST["compensationPrice"]; 
    // echo $userId. " - ". $name. " - ". $price;
    $resultArr = array();

    // Bpurchasing = 구매전
    // Apurchasing = 구매후
    // ADelete = 구매 후 삭제

    $sql = "
        INSERT INTO compensation SET
            name = '$name',
            price = '$price',
            status = 'Bpurchasing',
            userId = '$userId'
    ";


    
    if(mq($sql)) {
        // 포스트 저장 완료
        $datasql = "SELECT * FROM compensation WHERE userId = '$userId' and status != 'ADelete' ORDER BY id asc";
        $sqlResult2 = mq($datasql);
        $resultCount = mysqli_num_rows($sqlResult2);

        if($resultCount > 0){
            $output = array();
            // 행별로 유저의 정보 output에 넣어주기
            while ($row = mysqli_fetch_assoc($sqlResult2)) {
                        
                array_push($output,
                    // output에 넣을 데이터 예시
                    array(
                        'name' => $row['name'],
                        'price' => $row['price'],
                        'id' => $row['id'],
                        'status' => $row['status']
                    )
                );
            }
            $resultArr['data'] = $output;
        }
        

        $resultArr['result'] = "success";

        echo json_encode($resultArr);

    } else {
        // 리뷰 저장 실패
        $resultArr['result'] = getcon()->error;
        echo json_encode($resultArr);
    }





?>