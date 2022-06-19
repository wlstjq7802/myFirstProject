<?php  

    include_once "dbcon.php";
    
    $nickName = $_POST['nick'];
    $profileImg = $_POST['profileImg'];
    $kakaoUserCode = $_POST['userId'];


    // userCode로 사용자가 회원가입을 했는지 확인
    $userChk = "SELECT * FROM userInfo WHERE userId = '$kakaoUserCode'";
    $search = mq($userChk);
    $userChkResult = mysqli_num_rows($search);
    if($userChkResult == 0){
        // 회원정보 DB에 저장
        $sql = "
            insert into userInfo set
            userId = '$kakaoUserCode',
            password = '-',
            nick = '$nickName',
            profileImg = '$profileImg'
        ";

        $result = mq($sql);

        if($result){
            // 성공
            echo "success";
            
        }else {
            // 실패
            echo "fail";
        }
    } else{
        echo "aleady";
        exit();
    }

?>
