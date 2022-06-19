<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // 폴더의 레시피 선택 삭제
    // 1.bookmark 테이블에 userId가 사용자 id인 레코드가 있는지 확인한다.
    // 2.있다면 recipeInfolder에 $recipeInfolder를 update한다.
    // 3.bookmark 테이블의 recipeInfolder 데이터를 불러온다.
    // 4.recipeInfolder를 decode하여 키가 folderName인 데이터에 담긴 recipeNum의 데이터를
    // recipe 테이블에서 불러온다.
    // 5.recipe 데이터를 array에 저장 후 클라이언트로 보내준다.

    $userId = $_POST['userId']; // 객체 데이터
    $recipeInfolder = $_POST['recipeInfolder'];
    $folderName = $_POST['folderName'];
    $bookmarkRecipeNum = $_POST['bookmarkRecipeNum'];
    $output = array();
    

    // 1.bookmark 테이블에 userId가 사용자 id인 레코드가 있는지 확인한다.
    $bookmarkSql = "SELECT * FROM bookmark where userId = '$userId'";
    $bokkmarkResult = mq($bookmarkSql);
    $bokkmarkCount = mysqli_num_rows($bokkmarkResult);



    if($bokkmarkCount > 0){
        
        // 2.있다면 recipeInfolder에 $recipeInfolder를 update한다.
        $bookmarkUpdateSql = "
            update bookmark SET 
            recipeInfolder = '$recipeInfolder',
            bookmarkRecipeNum = '$bookmarkRecipeNum'
            WHERE userId = '$userId'
        ";
        $bookmarkUpdateResult = mq($bookmarkUpdateSql);

        
        if($bookmarkUpdateResult){
            // 3.bookmark 테이블의 recipeInfolder 데이터를 불러온다.
            $bookmarkSql = "SELECT * FROM bookmark where userId = '$userId'";
            $bokkmarkResult = mq($bookmarkSql);
            $bokkmarkResult = mysqli_fetch_array($bokkmarkResult);
            
            if(isset($bokkmarkResult['recipeInfolder'])&&count(json_decode($bokkmarkResult['recipeInfolder']))> 0){
                $recipeInfolder = json_decode($bokkmarkResult['recipeInfolder'], true);
                $recipeNumArr = $recipeInfolder[$folderName];

                for($i = 0; $i < count($recipeNumArr); $i++){
                
                    // 내가본 레시피 목록에 있는 레시피 조회
                    $sql = "SELECT * FROM recipe WHERE recipeNum = '$recipeNumArr[$i]'";
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
                echo "null";
            }

        } else{
            echo "null";
        }

    } else{
        echo "null";
    }

    

?>