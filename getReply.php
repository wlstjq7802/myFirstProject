<?php
    include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // feed 데이터 받기

    $feeedId = $_POST['feedId'];
    
    $sql = "SELECT * FROM reply WHERE feedId = '$feeedId' AND groupLayer = '1' ORDER BY groupOrd desc";
    $sqlResult = mq($sql);
    

    $replyCntSql = "SELECT replyId FROM reply WHERE feedId = '$feeedId'";
    $replyCntResult = mq($replyCntSql);
    $totalReplyCnt = mysqli_num_rows($replyCntResult);

    if($sqlResult){
        
            $output = array();
            $resultArr['result'] = "success";

            // 행별로 유저의 정보 output에 넣어주기
            while ($row = mysqli_fetch_assoc($sqlResult)) {

                
                if($row['isModified'] == 1){
                    $isModified = true;
                } else{
                    $isModified = false;
                }

                // 작성자의 닉네임과 프로필 사진 받아오기
                $sql2 = "SELECT profileImg, nick, userId FROM userInfo where userId = '$row[writer]'";
                $userDataResult = mq($sql2);
                $userData = mysqli_fetch_array($userDataResult);

                $replyCountSql = "SELECT * FROM reply WHERE 
                    feedId = '$feeedId' AND 
                    groupOrd= '$row[groupOrd]' AND 
                    groupLayer != '1'
                ";
                $replyCountResult = mq($replyCountSql);
                $replyCount = mysqli_num_rows($replyCountResult);
                
                array_push($output,
                    // output에 넣을 데이터 예시
                    array(
                        'groupOrd' => $row['groupOrd'],
                        'comment' => $row['comment'],
                        'writer' => $row['writer'],
                        'wTime' => $row['wTime'],
                        'feedId' => $row['feedId'],
                        'replyId' => $row['replyId'],
                        'nick' => $userData['nick'],
                        'isModified' => $isModified,
                        'profileImg' => $userData['profileImg'],
                        'replyCount' => $replyCount
                    )
                );
            }
            $resultArr['json'] = $output;
            $resultArr['totalReplyCnt'] = $totalReplyCnt;
            echo json_encode($resultArr);

        
    } else{
        $output['result'] = "fail";
        echo json_encode($output);
    }
    

?>