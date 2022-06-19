<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    

    // 폴더 activity로 폴더 내의 recipe데이터 보내기
    // 1. bookmark 테이블에 userId가 사용자 $userId인 레코드를 불러온다.
    // 2. 있다면 해당 레코드의 recipeInfolder 데이터가 있는지 확인
    // 3. 있다면 recipeInfolder를 decode하고 $folderName을 키로하는 데이터가 있는지 확인 
    // 4. 있다면 decode하고 recipe 테이블에 array에 담긴 recipe번호에 맞는 데이터를 불러온다.
    // 5. recipe 데이터를 array에 저장 후 encode하여 클라이언트로 보낸다. 

    $userId = $_POST['userId'];
    $folderName = $_POST['folderName'];
    $result = array();
    $output = array();



    $bookmarkSql = "SELECT * FROM bookmark WHERE userId = '$userId'";
    $bookmarkResult = mq($bookmarkSql);
    $bookmarkResult = mysqli_fetch_array($bookmarkResult);

    if(isset($bookmarkResult['recipeInfolder'])&&count(json_decode($bookmarkResult['recipeInfolder'])) > 0){
        $RecipeFolderResult = json_decode($bookmarkResult['recipeInfolder'], true);


        if(isset($RecipeFolderResult[$folderName])&&count($RecipeFolderResult[$folderName]) > 0){
            $recipeArr = $RecipeFolderResult[$folderName];

            for($i = 0; $i < count($recipeArr); $i++){
            
                // 내가본 레시피 목록에 있는 레시피 조회
                $sql = "SELECT * FROM recipe WHERE recipeNum = '$recipeArr[$i]'";
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
        } else{
            $output = "null2";
        }

    } else{
        $output = "null1";
    }
    
    if($output == "null1" || $output == "null2"){
        echo $output;
    } else{
        $resultArr = array();
        $resultArr['bookmarkRecipeNum'] = json_decode($bookmarkResult['bookmarkRecipeNum']);
        $resultArr['recipeData'] = $output;
        $resultArr['folderNameArr'] = json_decode($bookmarkResult['folder']);

        echo json_encode($resultArr);
    }

    
    

    
    

?>