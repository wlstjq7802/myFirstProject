<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // 레시피 메뉴에 레시피 데이터 보내기

    $recipeNum = $_POST['recipeNum']; // 레시피 번호

    
    $sql = "SELECT * FROM replyDB WHERE recipeNum = '$recipeNum' ORDER BY commentNum asc";
    $result = mq($sql);

    $resultCount = mysqli_num_rows($result);

    if($resultCount > 0){
        
            $output = array();

            // 행별로 유저의 정보 output에 넣어주기
            while ($row = mysqli_fetch_assoc($result)) {


                // 작성자의 닉네임과 프로필 사진 받아오기
                $sql2 = "SELECT * FROM userInfo where id = '$row[userId]'";
                $userDataResult = mq($sql2);
                $userData = mysqli_fetch_array($userDataResult);


                array_push($output,
                    // output에 넣을 데이터 예시
                    array(
                        'comment' => $row['comment'],
                        'nickName' => $userData['nick'],
                        'userId' => $row['userId'],
                        'reportDate' => $row['reportDate'],
                        'profileImg' => $userData['profile_img'],
                        'img' => $row['img'],
                        'recipeNum' => $row['recipeNum']
                    )
                );
            }

            echo json_encode($output);
        
    } else{
        echo "댓글 없음";
    }
    

?>