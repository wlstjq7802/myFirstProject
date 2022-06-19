<?php
    include_once "dbcon.php";
    header('Content-Type: application/json; charset=UTF-8');

    // mypage의 스크랩 메뉴
    // 폴더 데이터를 respons
    // bookmark 테이블의 userId가 사용자 id인 레코드의 folder와 recipeInfolder를 조회하여
    // 클라이언트에서 folder명과 해당 폴더에 저장된 레시피 게수를 표시한다.

    // 1. bookmark 테이블의 userId가 사용자 id인 레코드를 조회한다.
    // 2. 배열을 생성하여 folder라는 키에 folder데이터를 저장,
    //    recipeInfolder라는 키에 recipeInfolder 데이터를 저장한다.  
    // 3. 저장을 완료한 배열을 리턴한다.

    // 사용자 id
    $userId = $_POST['userId'];
    $output = array();
    
    
    // bookmark 테이블의 userId가 사용자 id인 레코드를 조회한다.
    $bookmarkSql = "SELECT * FROM bookmark where userId = 'wlstjq22'";
    $bookmarkResult = mq($bookmarkSql);
    $bookmarkResult = mysqli_fetch_array($bookmarkResult);


    // folder 데이터 있는지 확인
    // folder 데이터가 있는 경우
    if(isset($bookmarkResult['folder']) && count(json_decode($bookmarkResult['folder'])) > 0){
        $folderData = $bookmarkResult['folder'];
    } 
    // folder 데이터가 없는 경우
    else{
        $folderData = "null";
    }

    // recipeInFolder 데이터가 있는지 확인
    // recipeInFolder 데이터가 있는 경우
    if(isset($bookmarkResult['recipeInfolder']) && count(json_decode($bookmarkResult['recipeInfolder'])) > 0){
        $recipeInFolderData = $bookmarkResult['recipeInfolder'];
    } 
    // recipeInFolder 데이터가 없는 경우
    else{
        $recipeInFolderData = "null";
    }
    
    $output['folder'] = $folderData;
    $output['recipeInfolder'] = $recipeInFolderData;
    
                
    echo json_encode($output);


?>