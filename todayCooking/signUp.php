<?php  
    // error_reporting(E_ALL); 
    // ini_set('display_errors',1);
    include_once "dbcon.php";

    $username = $_POST['username'];
    $passwd = $_POST['password'];

    // 전체 사용자 수를 구해서 닉네임에 적용(ex) 요리사1)
    // 전체 사용자의 수를 조회
    $sql = "SELECT * FROM userInfo";
    $result = mq($sql);
    $userCount = mysqli_num_rows($result);

    // 해당 닉네임이 존재하는지 확인 후 존재하면 숫자에 +1을하여 변경 후 다시 확인
    $i = true;
    while($i == true){
        $sql = "SELECT * FROM userInfo WHERE nick = '요리사'.$userCount. ";
        $result = mq($sql);
        $userChk = mysqli_num_rows($result);
        if($userChk == 0){
            $i = false;
        }
        $userCount++;
    }    

    // 회원정보 DB에 저장
    $sql = "
        insert into userInfo set
        id = '$username',
        passwd = '$passwd',
        profile_img = 'profile.jpg',
        nick = '요리사$userCount'
    ";

    $result = mq($sql);
    
    if($result){

        $recipeInFolder = array();
        $folder = array();
        $recipeInFolder['기본폴더'] = $folder;
        $recipeInFolder = json_encode($recipeInFolder, JSON_UNESCAPED_UNICODE);
        array_push($folder, '기본폴더');
        $folder = json_encode($folder, JSON_UNESCAPED_UNICODE);

        $bookmarkSql = "
            insert into bookmark set
            userId = '$username',
            recipeInfolder = '$recipeInFolder',
            folder = '$folder'
        ";

        $bookmarkResult = mq($bookmarkSql);

        if($bookmarkResult){
            echo 1;
        } else{
            echo 0;
        }

        
    }else {
        // 실패
        echo 0;
    }
    
?>
