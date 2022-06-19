<?php
    include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // feed 데이터 받기

    $roomId = (int)$_POST['roomId'];
    $userId = $_POST['userId'];
    $limit = (int)$_POST['limit'];
    $page = (int)$_POST['page'];

    $page = (($page-1)*$limit);
    // LIMIT N(시작 번호), M(불러올 개수)

    /*
        @ 채팅방 입장 시 읽음 표시
        - 입장 전 해당 사용자 id와 room_id 서버로 전달
        - 서버에서 해당 사용자 id와 room_id로 된 레코드 전부 삭제
        - 채팅방에서 목록 조회
    */

    while(true){
        $deleteSql = "DELETE FROM msgReadChk WHERE receiver_id = '$userId' AND room_id = '$roomId'";

        $sqlResult = mq($deleteSql);
    
        $deleteCnt = mysqli_num_rows($sqlResult);
        if($deleteCnt <= 0){
            break;
        }
    }
   

    $sql11 = "SELECT ExitTime FROM ChatRoomJoin WHERE userId = '$userId' AND JoinId = '$roomId'";
    $sqlResult = mq($sql11);
    $count = mysqli_num_rows($sqlResult);

    if($count > 0){

        $joinData = mysqli_fetch_array($sqlResult);
        if(empty($joinData['ExitTime'])){
            $joinData['ExitTime'] = 0;
        }
        $sql = "SELECT * FROM msg WHERE room_id = '$roomId' AND wTime > $joinData[ExitTime] ORDER BY msg_id desc LIMIT $page, $limit";

        
    } else{
        $sql = "SELECT * FROM msg WHERE room_id = '$roomId' ORDER BY msg_id desc LIMIT $page, $limit";
    }
    
    $sqlResult = mq($sql);
    
    if($sqlResult){
        
            $output = array();
            $resultArr['result'] = "success";

            // 행별로 유저의 정보 output에 넣어주기
            while ($row = mysqli_fetch_assoc($sqlResult)) {

                $sender = $row['sender_id'];
                if($sender == $userId){
                    $type = "me";
                } else{
                    $type = "other";
                }

                $sql2 = "SELECT profileImg, nick FROM userInfo where userId = '$row[sender_id]'";
                $userDataResult = mq($sql2);
                $userData = mysqli_fetch_array($userDataResult);


                $sql2 = "SELECT isReaded FROM msgReadChk where msg_id = '$row[msg_id]'";
                $userDataResult = mq($sql2);
                $readCnt = mysqli_num_rows($userDataResult);


                array_push($output,
                    // output에 넣을 데이터 예시
                    array(
                        'msg' => $row['msg'],
                        'sender' => $row['sender_id'],
                        'write_time' => $row['wTime'],
                        'writerNick' => $userData['nick'],
                        'writerProfile' => $userData['profileImg'],
                        'readCnt' => $readCnt,
                        'type' => $type
                    )
                );
            }
            $resultArr['json'] = $output;
            $resultArr['count'] = $count;
            echo json_encode($resultArr);

        
    } else{
        $output['result'] = "fail";
        $resultArr['count'] = $count;
        echo json_encode($output);
    }
    

?>