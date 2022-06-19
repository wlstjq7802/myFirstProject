<?php
    include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    $userId = $_POST['userId'];
    
    $sql = "SELECT * FROM compensation WHERE userId = '$userId' and status != 'ADelete' ORDER BY id asc";
    $sqlResult = mq($sql);

    $sql2 = "SELECT * FROM compensation WHERE userId = '$userId' and status != 'Bpurchasing' ORDER BY id asc";
    $sqlResult2 = mq($sql2);

    if($sqlResult){

        $resultCount = mysqli_num_rows($sqlResult2);


        $output = array();
        $output2 = array();

        $resultArr['result'] = "success";

        // 행별로 유저의 정보 output에 넣어주기
        while ($row = mysqli_fetch_assoc($sqlResult)) {
            
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


        if($resultCount > 0){

            
            // 행별로 유저의 정보 output에 넣어주기
            while ($row2 = mysqli_fetch_assoc($sqlResult2)) {
                
                array_push($output2,
                    // output에 넣을 데이터 예시
                    array(
                        'name' => $row2['name'],
                        'price' => $row2['price'],
                        'id' => $row2['id'],
                        'status' => $row2['status']
                    )
                );
            }

            $resultArr['purchasingList'] = $output2;
        }
        
            
            
            echo json_encode($resultArr);

        
    } else{
        $output['result'] = "fail";
        echo json_encode($output);
    }
    

?>