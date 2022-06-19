<?php
    include_once "dbcon.php";

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    
    // 레시피 번호
    $recipeNum = $_POST['recipeNum'];
    
    $sql = "DELETE FROM recipe WHERE recipeNum = '$recipeNum'";
    $result = mq($sql);

    if($result){

        $sql2 = "SELECT * FROM likeDB where recipeNum = '$recipeNum'";
        $result2 = mq($sql2);
        $result2 = mysqli_num_rows($result2);
        if($result2 > 0){
            $sql2 = "DELETE FROM likeDB WHERE recipeNum = '$recipeNum'";
            $result2 = mq($sql2);
            echo "삭제";
        } else{
            echo "삭제";
        }

    } else{
        echo "실패";
    }
    

?>