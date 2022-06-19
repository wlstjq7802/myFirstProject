<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // 레시피 메뉴에 레시피 데이터 보내기

    $userId = $_POST['userId']; // 객체 데이터
    $keyword = $_POST['keyword'];
    $searchCondition = $_POST['searchCondition'];

    if($searchCondition == "레시피명+재료"){
        $sql = "SELECT * FROM recipe WHERE ingredient LIKE '%$keyword%' OR recipeName LIKE '%$keyword%' ORDER BY likeCount desc, recipeNum desc";
    } else if($searchCondition == "레시피명"){
        $sql = "SELECT * FROM recipe WHERE recipeName LIKE '%$keyword%' ORDER BY likeCount desc, recipeNum desc";
    } else if($searchCondition == "재료"){
        $sql = "SELECT * FROM recipe WHERE ingredient LIKE '%$keyword%' ORDER BY likeCount desc, recipeNum desc";
    }
    

    
    $result = mq($sql);

    if($result){
        
            $output = array();

            // 행별로 유저의 정보 output에 넣어주기
            while ($row = mysqli_fetch_assoc($result)) {

                // 작성자의 닉네임과 프로필 사진 받아오기
                $sql2 = "SELECT * FROM userInfo where id = '$row[writer]'";
                $userDataResult = mq($sql2);
                $userData = mysqli_fetch_array($userDataResult);

                // 북마크 상태 확인 
                $sql3 = "SELECT * FROM bookmark where userId = '$userId'";
                $likeCountResult = mq($sql3);
                $bookmark = mysqli_fetch_array($likeCountResult);
                $bookmark = json_decode($bookmark['bookmarkRecipeNum']);
                $bookmarkCount = count($bookmark);

                $bookmarkChk = false;
                for($k = 0; $k < $bookmarkCount; $k++){
                    // 북마크 상태 확인
                    if($bookmark[$k] == $row['recipeNum']){
                        $bookmarkChk = true;
                        break;
                    }
                }

                // 좋아요 상태 확인
                $sql3 = "SELECT * FROM likeDB where recipeNum = '$row[recipeNum]'";
                $likeCountResult = mq($sql3);
                $like = mysqli_fetch_array($likeCountResult);
                $like = json_decode($like['userId'], true);
                $likeChk = false;


                // 좋아요 상태 적용
                if($like[$userId] == "true"){
                    $likeChk = true;
                }

                // 요리 완성 이미지 중 첫번째 이미지 불러오기
                $cookingImg = json_decode($row['representativeImg']);
                

                array_push($output,
                    // output에 넣을 데이터 예시
                    array(
                        'recipeNum' => $row['recipeNum'],
                        'recipeName' => $row['recipeName'],
                        'writer' => $userData['nick'],
                        'recipeImg' => $cookingImg[0],
                        'cookingTime' => $row['cookingTime'],
                        'number' => $row['number'],
                        'level' => $row['level'],
                        'writerProfile' => $userData['profile_img'],
                        'writerId' => $userData['id'],
                        'reportDate' => $row['reportDate'],
                        'bookmarkCheck' => $bookmarkChk,
                        'likeCheck' => $likeChk,
                        'likeCount' => $row['likeCount']
                    )
                );
            }

            echo json_encode($output);

        
    } else{
        echo "레시피 불러오기 실패";
        echo("Errormessage:". $con -> error);
    }
    

?>