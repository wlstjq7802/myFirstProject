<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    
    // 스크랩 폴더 추가 기능
    // 1.bookmark 테이블에서 userId가 사용자의 id인 레코드가 있는지 조회
    // 2-1.레코드가 있다면 클라이언트에서 post로 보낸 folderData를 사용자 id의 레코드에 update
    // 2-2.레코드가 없다면 userId는 $userId, folder는 $folderData로 insert
    $folderData = $_POST['folderData']; // 객체 데이터
    $userId = $_POST['userId'];


    // 1.bookmark 테이블에서 userId가 사용자의 id인 레코드가 있는지 조회
    $userChkSql = "SELECT * FROM bookmark where userId = '$userId'";
    $userChkResult = mq($userChkSql);
    $userChkResult = mysqli_num_rows($userChkResult);


    //2-1.레코드가 있다면 클라이언트에서 post로 보낸 folderData를 사용자 id의 레코드에 update
    if($userChkResult > 0){
        
        $scrapUpdateSql = "
                update bookmark SET 
                folder = '$folderData'
                WHERE userId = '$userId'
        ";
        $scrapResult = mq($scrapUpdateSql);
        
    } else{
        $scrapInsertSql = "
            INSERT INTO bookmark SET 
            folder = '$folderData',
            userId = '$userId'
        ";
        $scrapResult = mq($scrapInsertSql);
    }


    if($scrapResult){
        $scrapDataSql = "SELECT * FROM bookmark where userId = '$userId'";
        $scrapDataResult = mq($scrapDataSql);
        $scrapDataResult = mysqli_fetch_array($scrapDataResult);
        echo $scrapDataResult['folder'];

    } else{
        echo "fail";
    }
    

?>