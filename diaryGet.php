<?php
    include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    $userId = $_POST['userId'];
    // feed 데이터 받기

    $sql = "SELECT * FROM diary WHERE writer = '$userId'";
    $sqlResult = mq($sql);
    $resultArr = array();

    if($sqlResult){
        
            $output = array();

            // 행별로 유저의 정보 output에 넣어주기
            while ($row = mysqli_fetch_assoc($sqlResult)) {
               
                array_push($output,
                    // output에 넣을 데이터 예시
                    array(
                        'contents' => $row['contents'],
                        'method' => $row['method'],
                        'degree' => $row['degree'],
                        'wTime' => $row['wTime'],
                        'isShare' => $row['isShare'],
                        'diaryId' => $row['id'],
                        'title' => $row['title'],
                        'img' => $row['img']
                    )
                );
            }
            $resultArr['result'] = "success";
            $resultArr['data'] = $output;
            echo json_encode($resultArr);

        
    } else{
        $resultArr['result'] = "fail";
        echo json_encode($resultArr);
    }
    

?>