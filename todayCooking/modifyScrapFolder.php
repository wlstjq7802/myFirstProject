<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    
    // 스크랩 폴더 추가 기능
    // 1.bookmark 테이블에서 userId가 사용자의 id인 레코드가 있는지 조회
    // 2-1.레코드가 있다면 클라이언트에서 post로 보낸 folderData를 사용자 id의 레코드에 update
    // 2-2.레코드가 없다면 userId는 $userId, folder는 $folderData로 insert
    // 3. 클라이언트에서 post로 보낸 recipeInFolderData를 사용자의 id의 레코드에 update
    $folderData = $_POST['folderData']; // 객체 데이터
    $userId = $_POST['userId'];
    $recipeInFolderData = $_POST['recipeInFolder'];
    $resultArr = array();

    // 1.bookmark 테이블에서 userId가 사용자의 id인 레코드가 있는지 조회
    $userChkSql = "SELECT * FROM bookmark where userId = '$userId'";
    $userChkResult = mq($userChkSql);
    $userChkResult = mysqli_num_rows($userChkResult);

    //2-1.레코드가 있다면 클라이언트에서 post로 보낸 folderData를 사용자 id의 레코드에 update
    if($userChkResult > 0){
        $scrapUpdateSql = "
                update bookmark SET 
                folder = '$folderData',
                recipeInfolder = '$recipeInFolderData'
                WHERE userId = '$userId'
        ";
        $scrapResult = mq($scrapUpdateSql);
        
    } else{
        $scrapInsertSql = "
            INSERT INTO bookmark SET 
            folder = '$folderData',
            userId = '$userId',
            recipeInfolder = '$recipeInFolderData'
        ";
        $scrapResult = mq($scrapInsertSql);
    }


    if($scrapResult){
        
        echo "complete";

    } else{
        echo "fail";
    }
    

?>