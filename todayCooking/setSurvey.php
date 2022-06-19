<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    $userId = $_POST['userId'];
    $level = $_POST['level'];
    $cookingTime = $_POST['cookingTime'];
    $typeData = $_POST['type'];
    $ingredientData = $_POST['ingredient'];
    $situationData = $_POST['situation'];
    $methodData = $_POST['method'];

    $surveyArr = array();
    $surveyArr['level'] = $level;
    $surveyArr['cookingTime'] = $cookingTime;
    $surveyArr['type'] = json_decode($typeData);
    $surveyArr['ingredient'] = json_decode($ingredientData);
    $surveyArr['situation'] = json_decode($situationData);
    $surveyArr['method'] = json_decode($methodData);
    $surveyArr = json_encode($surveyArr, JSON_UNESCAPED_UNICODE);
    

    $Sql = "
        update userInfo SET 
        survey = '$surveyArr'
        WHERE id = '$userId'
    ";
    // $recipeSql = "SELECT * FROM recipe";
    $result = mq($Sql);



    if($result){
        echo "complete";
    } else{
        echo "fail";
    }
    
    
?>