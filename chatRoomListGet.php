<?php
    include_once "dbcon.php";   
    
    /*
        @ 채팅방 목록 조회
        1. 사용자ID 받기
        2. 사용자ID로 사용자가 포함된 JOIN 레코드 조회
        3. 해당 레코드의 상대방을 확인하여 userInfo에서 상대방 정보 조회
        4. 해당 채팅방의 마지막 MSG, 발신날짜, 안읽은 개수 조회
        5. 해당 채팅방의 JOINID, 상대방 정보(ID, 프로필, 닉네임), 
           마지막 MSG, 발신날짜, 안읽은 개수 클라이언트로 응답
    */
    

    // 1. 사용자ID 받기
    $userId = $_POST['userId'];
    $resultArr = array();

    // 2. 사용자ID로 사용자가 포함된 JOIN 레코드 조회
    $sql = "SELECT * FROM ChatRoomJoin WHERE userId='$userId'";

    $result = mq($sql);
    $count = mysqli_num_rows($result);

    if($count > 0){
        $output = array();

            
            while ($row = mysqli_fetch_assoc($result)) {

                if($row['ExitTime'] != 0){
                    // 채팅방 나간지 확인(roomId가 같고, 나간시간 이후의 채팅 있는지 확인)
                    $exitSql = "SELECT msg FROM msg where room_id = '$row[JoinId]' AND wTime > '$row[ExitTime]' ";
                    $exitResult = mq($exitSql);
                    $msgCount = mysqli_num_rows($exitResult);

                    if($msgCount <= 0){
                        continue;
                    }
                }
                


                // 3. 해당 레코드의 상대방을 확인하여 userInfo에서 상대방 정보 조회
                $sql2 = "SELECT profileImg, nick FROM userInfo where userId = '$row[roomId]'";
                $userDataResult = mq($sql2);
                $userData = mysqli_fetch_array($userDataResult);


                // 4. 해당 채팅방의 마지막 MSG(사용자가 참여한 전체 채팅방id 중 가장 큰 값), 
                //    발신날짜, 안읽은 개수 조회
                $msgDataSql = "SELECT * FROM msg where room_id = '$row[JoinId]' order by msg_Id desc limit 1";
                $msgDataResult = mq($msgDataSql);

                $msgData = mysqli_fetch_array($msgDataResult);

                
                // 안읽은 msg 개수
                $msgCntSql = "SELECT * FROM msgReadChk where room_id = '$row[JoinId]' AND receiver_id = '$userId'";
                $msgCntResult = mq($msgCntSql);
                $msgCnt = mysqli_num_rows($msgCntResult);
                
                if($msgData['msg'] != null){
                    array_push($output,
                        // output에 넣을 데이터 예시
                        array(
                            'opponent' => $row['roomId'],
                            'joinId' => $row['JoinId'],
                            'roomName' => $userData['nick'],
                            'profile' => $userData['profileImg'],
                            'currentMsg' => $msgData['msg'],
                            'msg_id' => $msgData['msg_id'],
                            'msgCnt' => $msgCnt,
                            'wTime' => $msgData['wTime']
                        )
                    );
                }

                foreach ((array) $output as $key => $value) {
                    $sort[$key] = $value['msg_id'];
                }
                
                array_multisort($sort, SORT_DESC, $output);


            }

            $resultArr['result'] = "success";
            $resultArr['data'] = $output;
            echo json_encode($resultArr, JSON_UNESCAPED_UNICODE);
        
        

    } else{
        $resultArr['result'] = "fail";
        echo json_encode($resultArr, JSON_UNESCAPED_UNICODE);
    }




?>
