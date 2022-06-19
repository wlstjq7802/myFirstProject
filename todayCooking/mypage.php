<?php
include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    $userId = $_POST['userId'];

    $userChk = "SELECT * FROM userInfo WHERE id = '$userId'";
    $search = mq($userChk);
    $recipeRecordCount = 0;
    $output = array();

    $userChkResult = mysqli_fetch_array($search);
    if(isset($userChkResult['recipeRecord']) && $userChkResult['recipeRecord'] !== ""){
        $recipeRecord = json_decode($userChkResult['recipeRecord']); 
        $recipeRecordCount = count($recipeRecord);
    }


    $writingRecipe = "SELECT * FROM recipe WHERE writer = '$userId'";
    $writingRecipeResult = mq($writingRecipe);
    $writingRecipeCount = mysqli_num_rows($writingRecipeResult);

    $bookmarkSql = "SELECT * FROM bookmark WHERE userId = '$userId'";
    $bookmarkResult = mq($bookmarkSql);
    $bookmarkResult = mysqli_fetch_array($bookmarkResult);
    if(isset($bookmarkResult['bookmarkRecipeNum']) && count(json_decode($bookmarkResult['bookmarkRecipeNum'])) >= 0){
        $bookmarkResult = json_decode($bookmarkResult['bookmarkRecipeNum']); 
        $bookmarkdCount = count($bookmarkResult);
    }
    
    array_push($output,
        // output에 넣을 데이터 예시
        array(
            'nickname' => $userChkResult['nick'],
            'image' => $userChkResult['profile_img'],
            'writingRecipeCount' => $writingRecipeCount,
            'recipeRecordCount' => $recipeRecordCount,
            'scrapCount' => $bookmarkdCount
        )
    );




    echo json_encode($output); // array를 json형태로 변환하여 출력

?>