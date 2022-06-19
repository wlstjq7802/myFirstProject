<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // 레시피 메뉴에 레시피 데이터 보내기
    $userId = $_POST['userId']; // 객체 데이터

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
        echo "기록된 레시피 정보 없음";
    }
    
    

    
    

?>