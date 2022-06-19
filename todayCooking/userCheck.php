<?php  
    // 유저를 확인하고 사용자의 닉네임과 프로필 사진을 클라이언트에 전달

    
    include_once "dbcon.php";
    
    $userId = $_POST['userId'];

    // userCode로 사용자가 회원가입을 했는지 확인
    $userChk = "SELECT * FROM userInfo WHERE id = '$userId'";
    $search = mq($userChk);
    $userChkResult = mysqli_num_rows($search);
    
    if($userChkResult != 0){
        // 성공
        while($userChkResult = mysqli_fetch_assoc($search)){
            echo $userChkResult['nick'];
            
        }
        
        exit();

    } else{
        // 실패
        echo 0;
        exit();
    }

?>
