<?php
    include_once "dbcon.php";   

    
    $responsArr = array();

    $sql = "SELECT * FROM healthCenter";

    $sqlResult = mq($sql);

    if($sqlResult){
        
            $output = array();

            // 행별로 유저의 정보 output에 넣어주기
            while ($row = mysqli_fetch_assoc($sqlResult)) {
                
                array_push($output,
                    // output에 넣을 데이터 예시
                    array(
                        'centerName' => $row['centerName'],
                        'phoneNum' => $row['phoneNum'],
                        'address' => $row['address'],
                        'longitude' => $row['longitude'],
                        'latitude' => $row['latitude']
                    )
                );
            }

            $responsArr['result'] = "success";
            $responsArr['data'] = $output;
            echo json_encode($responsArr);

        
    } else{
        $responsArr['result'] = "fail";
        echo json_encode($responsArr);
    }
    

?>