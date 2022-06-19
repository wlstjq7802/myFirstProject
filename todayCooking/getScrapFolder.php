<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // 사용자의 스크랩 폴더 데이터를 응답한다.
    // 1. bookmark 테이블에서 userId가 사용자 id인 데이터 조회
    // 2.레코드에서 folder속성의 데이터를 응답한다.

    // 레시피 메뉴에 레시피 데이터 보내기
    $userId = $_POST['userId'];
    $resultJson = array();


    // 1. bookmark 테이블에서 userId가 사용자 id인 데이터 조회
    $scrapDataSql = "SELECT * FROM bookmark WHERE userId = '$userId'";
    $scrapDataResult = mq($scrapDataSql);


    // 사용자 데이터가 있는 경우 
    if($scrapDataResult){
        $scrapDataResult = mysqli_fetch_array($scrapDataResult);

        if(isset($scrapDataResult['folder'])&&count(json_decode($scrapDataResult['folder'])) > 0){
            $folderData = $scrapDataResult['folder'];
        } 
        
        else{
            $folderData = "null";
        }

        if(isset($scrapDataResult['recipeInfolder'])&&count(json_decode($scrapDataResult['recipeInfolder'])) > 0){
            $recipeInFolderData = $scrapDataResult['recipeInfolder'];

            if(isset($scrapDataResult['bookmarkRecipeNum'])){
                $totalRecipeCount = count(json_decode($scrapDataResult['bookmarkRecipeNum']));

            } else{
                $totalRecipeCount = "-1";
            }
        } 
        
        else{
            $recipeInFolderData = "null";
        }
    } 
    
    // 사용자 데이터가 없는 경우
    else{
        $recipeNumArr = array();
        $folder = array();
        $recipeInFolder = array();
    
        $recipeInFolder['기본폴더'] = $recipeNumArr;

        // 폴더명과 레시피번호가 담긴 데이터
        $recipeInFolder = json_encode($recipeInFolder, JSON_UNESCAPED_UNICODE); 
        $recipeNumArr = json_encode($recipeNumArr); //전체 레시피 번호 데이터
        array_push($folder, "기본폴더");             //폴더명 데이터
        $folderData = json_encode($folder, JSON_UNESCAPED_UNICODE);

        $sql2 = "
            INSERT INTO bookmark SET 
            bookmarkRecipeNum = '$recipeNumArr',
            folder = '$folder',
            recipeInfolder = '$recipeInFolder',
            userId = '$userId'
        ";

        $userIdAddResult = mq($sql2);
        $totalRecipeCount = 0;
        $recipeInFolderData = 'null';

        // $folderData = "null";
    }
    
    $resultJson['totalRecipeCount'] = $totalRecipeCount;
    $resultJson['folderData'] = $folderData;
    $resultJson['recipeInFolder'] = $recipeInFolderData;
    
    echo json_encode($resultJson);
    

    
    

?>