<?php
// 회원가입
include_once "dbcon.php";   

    $userId = $_POST['userId'];
    $userPass = $_POST['userPass'];
    $userPhone = $_POST['userPhone'];


    $userCodeSql = "SELECT MAX(userCode) AS max_userCode FROM userInfo";
    $userCodeResult = mq($userCodeSql);
    
        $resultArr  = mysqli_fetch_array($userCodeResult);
        if(isset($resultArr) > 0){
            $userCode = ((int)$resultArr['max_userCode'])+1;
        } else{
            $userCode = 1;
        }

    $sql = "
        INSERT INTO userInfo SET
        userId = '$userId',
        password = '$userPass',
        profileImg = 'profile.jpg',
        nick = '금연가$userCode',
        phone = '$userPhone'
    ";


    $result = mq($sql);
    

    if($result){
        // DB에 회원정보 저장
        echo "success";
    }
    else{
        // DB에 회원정보 저장 실패
        echo "fail";
    }


?>