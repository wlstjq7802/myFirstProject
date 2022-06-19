<?php
    include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // feed 데이터 받기

    $roomId = (int)$_POST['roomId'];
    $nick = $_POST['nick'];
    $limit = (int)$_POST['limit'];
    $page = (int)$_POST['page'];

    $page = (($page-1)*$limit);
    
    // LIMIT N(시작 번호), M(불러올 개수)

    // 채팅방 나간 시간 확인
    $sql11 = "SELECT ExitTime FROM ChatRoomJoin WHERE userId = '$nick' AND JoinId = '$roomId'";
    $sqlResult = mq($sql11);
    $count = mysqli_num_rows($sqlResult);

    if($count > 0){

        $joinData = mysqli_fetch_array($sqlResult); 
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
                if($sender == $nick){
                    $type = "me";
                } else{
                    $type = "other";
                }

                $sql2 = "SELECT profileImg, nick FROM userInfo where userId = '$row[sender_id]'";
                $userDataResult = mq($sql2);
                $userData = mysqli_fetch_array($userDataResult);


                array_push($output,
                    // output에 넣을 데이터 예시
                    array(
                        'msg' => $row['msg'],
                        'sender' => $row['sender_id'],
                        'write_time' => $row['wTime'],
                        'writerNick' => $userData['nick'],
                        'writerProfile' => $userData['profileImg'],
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