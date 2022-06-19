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
           echo "delete";
            
        } else{
            echo "fail";
        }

    } else{
        echo "fail";
    }

    

?>