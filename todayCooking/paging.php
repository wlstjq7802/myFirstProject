<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // 1. 사용자의 id를 받아 bookmarkDB의 bookmarkNum에 데이터가 있는지 확인한다.
    // 2. 사용자의 bookmarkNum의 카테고리들의 점유율 순위를 정한다.
    // 3. 점유율이 높은 카테고리의 인기레시피부터  5개씩 순서대로 불러온다.


    // $userId = $_POST['userId'];
    $userId = "wlstjq7802@naver.com";
    $page = (int)$_POST['page'];
    $output = array();
    // $categoryData = array();
    // $recipeNumArr = array();

    // $recipeSql = "SELECT * FROM recipe";
    // $recipeResult = mq($recipeSql);
    // $total_record = mysqli_num_rows($recipeResult);
    $list = 10;
    $page_start = ($page - 1) * $list;
    $output = array();
    
    $recipeSql = "SELECT * FROM recipe ORDER BY recipeNum DESC LIMIT $page_start, $list";
    // $recipeSql = "SELECT * FROM recipe";
    $sql2 = mq($recipeSql);
    $recsult2 = mysqli_num_rows($sql2);


    if($recsult2 > 0){
    
        // 레시피 데이터 불러오기
        while ($row = mysqli_fetch_assoc($sql2)) {
            array_push($output, $row['recipeName']);
        }

        echo json_encode($output);
    } else{
        echo "stop";
    }
    
    
?>