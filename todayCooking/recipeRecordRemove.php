<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // 레시피 메뉴에 레시피 데이터 보내기
    $userId = $_POST['userId']; // 객체 데이터
    $recipeRecordList = $_POST['recipeRecordList'];
    $decode = json_decode($recipeRecordList);
    if(count($decode) == 0){
        $recipeRecordList = "";
    }
    $sql2 = "
            update userInfo SET 
            recipeRecord = '$recipeRecordList'
            WHERE id = '$userId'
        ";
    $recipeRecordResult = mq($sql2);
    

    if($recipeRecordResult){
        $recipeRecordSql = "SELECT recipeRecord FROM userInfo WHERE id = '$userId'";
        $recipeRecordResult = mq($recipeRecordSql);
        $recipeRecordResult = mysqli_fetch_array($recipeRecordResult);

        if(isset($recipeRecordResult['recipeRecord'])&&$recipeRecordResult['recipeRecord']!==""){
            $recipeRecordResult = json_decode($recipeRecordResult['recipeRecord']);

            $output = array();

            for($i = 0; $i < count($recipeRecordResult); $i++){
                
                // 내가본 레시피 목록에 있는 레시피 조회
                $sql = "SELECT * FROM recipe WHERE recipeNum = '$recipeRecordResult[$i]'";
                $result = mq($sql);

                if($result){
                    $row = mysqli_fetch_array($result);

                    // 작성자의 닉네임과 프로필 사진 받아오기
                    $sql2 = "SELECT * FROM userInfo where id = '$row[writer]'";
                    $userDataResult = mq($sql2);
                    $userData = mysqli_fetch_array($userDataResult);

                    // 북마크 상태 확인
                    $sql3 = "SELECT * FROM bookmark where bookmarkRecipeNum = '$row[recipeNum]'";
                    $bokkmarkResult = mq($sql3);
                    $bookmark = mysqli_fetch_array($bokkmarkResult);
                    $bookmark = json_decode($bookmark['userId'], true);
                    $bookmarkChk = false;

                    // 북마크 상태 적용
                    if($bookmark[$userId] == "true"){
                        $bookmarkChk = true;
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
                            'writerNick' => $userData['nick'],
                            'recipeImg' => $cookingImg[0],
                            'writerId' => $userData['id'],
                            'reportDate' => $row['reportDate'],
                            'writerProfile' => $userData['profile_img']
                        )
                    );

                } else{
                    echo "레시피 불러오기 실패";
                    echo("Errormessage:". $con -> error);
                }
            }

            echo json_encode($output);
        } else{
            // 저장된 기록이 없음
            echo "error1";
        }
    } else{
        // 기록된 레시피 제거 실패
        echo "error2";
    }
    
    
    

    
    

?>