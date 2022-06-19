<?php
    include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // feed 데이터 받기
    $userId = $_POST['userId'];
    $keyword = $_POST['keyword'];
    $category = $_POST['category'];
    $sort = $_POST['sort'];
    $resultArr = array();

     /*
            - 키워드
            - 카테고리
            - 키워드, 카테고리


            - 정렬
            - 키워드 정렬
            - 카테고리, 정렬
            - 키워드, 카테고리, 정렬
         */


    if(isset($keyword) && isset($category)){
        // "키워드/카테고리 있음";

        $keyword = "(title LIKE '%". $keyword. "%' OR contents LIKE '%". $keyword. "%')";
        $category = "WHERE (type = ". "'". $category. "') AND";
    
    } else if(isset($keyword) && !isset($category)){
        // "키워드 만 있음";
        $keyword = "WHERE title LIKE '%". $keyword. "%' OR contents LIKE '%". $keyword. "%'";
    } else if(!isset($keyword) && isset($category)){
        // "카테고리 만 있음";
        $category = "WHERE type = ". "'". $category. "'";
    }

    if($sort == "최신순" || !isset($sort)){
        $sort = "ORDER BY wTime desc";
        // echo "최신순입니다.";
    } else if($sort == "작성순"){
        $sort = "ORDER BY wTime asc";
        // echo "작성순이네요";
    } else if($sort == "좋아요순"){
        $sort = "ORDER BY likeCnt desc, wTime desc";
    } else if($sort == "선택"){
        $sort = null;
    }
    
    $sql = "
        SELECT * FROM feed  $category $keyword  $sort
    ";

    // echo $sql;

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
            $resultArr['result'] = "success";
            $resultArr['data'] = $output;
            echo json_encode($resultArr);

        
    } else{
        $resultArr['result'] = "fail";
        echo json_encode($resultArr);
    }
    

?>