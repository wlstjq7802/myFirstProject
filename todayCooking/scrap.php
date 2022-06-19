<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    

    // 레시피 메뉴에 레시피 데이터 보내기
    $userId = $_POST['userId'];
    $resultJson = array();
    $output = array();


    $bookmarkSql = "SELECT * FROM bookmark WHERE userId = '$userId'";
    $bookmarkResult = mq($bookmarkSql);
    $bookmarkResult = mysqli_fetch_array($bookmarkResult);

    if(isset($bookmarkResult['bookmarkRecipeNum'])&&count(json_decode($bookmarkResult['bookmarkRecipeNum'])) >= 1){
        $totalRecipeResult = json_decode($bookmarkResult['bookmarkRecipeNum']);

        for($i = 0; $i < count($totalRecipeResult); $i++){
            
            // 내가본 레시피 목록에 있는 레시피 조회
            $sql = "SELECT * FROM recipe WHERE recipeNum = '$totalRecipeResult[$i]'";
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

    } else if(count(json_decode($bookmarkResult['bookmarkRecipeNum'])) == 0){
        $output = "0";
    }
    
    else{
        $output = "null";
    }
    
    if(isset($bookmarkResult['folder'])&&count(json_decode($bookmarkResult['folder'])) > 0){
        $folderCount = count(json_decode($bookmarkResult['folder']));
    } else{
        $folderCount = 0;
    }
    
    $resultJson['recipeData'] = $output;
    $resultJson['folderCount'] = $folderCount;


    echo json_encode($resultJson);
    

    
    

?>