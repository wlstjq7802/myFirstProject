<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    
    // 스크랩 폴더 추가 기능
    // 1.bookmark 테이블에서 userId가 사용자의 id인 레코드가 있는지 조회
    // 2-1.레코드가 있다면 클라이언트에서 post로 보낸 folderData를 사용자 id의 레코드에 update
    // 2-2.레코드가 없다면 userId는 $userId, folder는 $folderData로 insert
    $profile = $_POST['profile']; 
    $userId = $_POST['userId'];

    $stageFileCount = $_POST['stageImgCount'];

    // 완성이미지 저장
    if($profile != "profile.jpg"){
        $imgFile = $_FILES['imgFile'];
        $fileName= $imgFile['name'];
        $tmpName= $imgFile['tmp_name']; //서버에 임시로 할당된 파일명
        $filePath= "/var/www/html/img/". $fileName; // 업로드하려는 위치
        $result=move_uploaded_file($tmpName, $filePath);
        $profile = $fileName;
    }



    // 1.bookmark 테이블에서 userId가 사용자의 id인 레코드가 있는지 조회
    $userChkSql = "SELECT * FROM userInfo where id = '$userId'";
    $userChkResult = mq($userChkSql);
    $userChkResult = mysqli_num_rows($userChkResult);

    if($userChkResult > 0){
        
        $setNickNameSql = "
                update userInfo SET 
                profile_img = '$profile'
                WHERE id = '$userId'
        ";
        $setNickNameResult = mq($setNickNameSql);
        
    } else{
        $setNickNameSql = "
            INSERT INTO userInfo SET 
            profile_img = '$profile',
            id = '$userId'
        ";
        $setNickNameResult = mq($setNickNameSql);
    }


    if($setNickNameResult){
        echo "complete";
    } else{
        echo "fail";
    }
    

?>