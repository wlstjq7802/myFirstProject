<?php
    include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    $userId = $_POST['userId'];
    $selectDays = $_POST['selectDays'];

    // feed 데이터 받기

    $sql = "SELECT * FROM diary WHERE rateDays = '$selectDays' AND isShare = '공개' ORDER BY id desc";
    $sqlResult = mq($sql);
    $resultArr = array();

    if($sqlResult){
        
            $output = array();

            // 행별로 유저의 정보 output에 넣어주기
            while ($row = mysqli_fetch_assoc($sqlResult)) {
               
                $sql2 = "SELECT profileImg, nick, userId, smkCessTime FROM userInfo where userId = '$row[writer]'";
                $userDataResult = mq($sql2);
                $userData = mysqli_fetch_array($userDataResult);

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
                        'img' => $row['img'],
                        'writer' => $row['writer'],
                        'writerNick' => $userData['nick'],
                        'profileImg' => $userData['profileImg']
                    )
                );
            }
            $resultArr['result'] = "success";
            $resultArr['data'] = $output;
            $resultArr['selectDays'] = $selectDays;
            echo json_encode($resultArr);

        
    } else{
        $resultArr['result'] = "fail";
        echo json_encode($resultArr);
    }
    

?>