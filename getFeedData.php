<?php
    include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // feed 데이터 받기
    $userId = $_POST['userId'];
    
    // $sql = "SELECT * FROM feed ORDER BY feedId desc";
    $sql = "
        SELECT * FROM feed  ORDER BY wTime desc;
    ";
    $sqlResult = mq($sql);

    if($sqlResult){
        
            $output = array();

            // 행별로 유저의 정보 output에 넣어주기
            while ($row = mysqli_fetch_assoc($sqlResult)) {

                // 작성자의 닉네임과 프로필 사진 받아오기
                $sql2 = "SELECT profileImg, nick, userId, smkCessTime FROM userInfo where userId = '$row[writer]'";
                $userDataResult = mq($sql2);
                $userData = mysqli_fetch_array($userDataResult);

                $likeDataSql = "SELECT userId FROM likeTable where userId = '$userId' AND feedNum = '$row[feedId]'";
                $likeDataResult = mq($likeDataSql);
                // $likeData = mysqli_fetch_array($likeDataResult);

                $likeData = mysqli_num_rows($likeDataResult);
                if($likeData >= 1){
                    $likeChk = true;
                } else{
                    $likeChk = false;
                }
                
                array_push($output,
                    // output에 넣을 데이터 예시
                    array(
                        'feedId' => $row['feedId'],
                        'title' => $row['title'],
                        'contents' => $row['contents'],
                        'imgArr' => $row['imgArr'],
                        'writer' => $row['writer'],
                        'wTime' => $row['wTime'],
                        'replyCnt' => $row['replyCnt'],
                        'likeCnt' => $row['likeCnt'],
                        'nickName' => $userData['nick'],
                        'profileImg' => $userData['profileImg'],
                        'type' => $row['type'],
                        // 'degree' => $row['degree'],
                        'likeChk' => $likeChk,
                        'smkCessTime' => $userData['smkCessTime']
                    )
                );
            }

            echo json_encode($output);

        
    } else{
        $output['result'] = "fail";
        echo json_encode($output);
    }
    

?>