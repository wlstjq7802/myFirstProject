
<?php 
    include "dbcon.php"; // db 연결 파일
    
    $con = mysqli_connect("localhost", "jin", "jin1234", "SmokingCessationHelper");

    (int)$page = $_POST['page'];
    (int)$count = $_POST['count'];

    if($page == 0){
        $s_pageNum = 0;
    } else{
        $s_pageNum = ($page * $count);
    }
    
    
    $sql = "
        SELECT * FROM healthCenter ORDER BY id asc LIMIT $s_pageNum, $count;
    ";

    
    $resultArr = array();
    
    $sqlResult = mq($sql);
    
    if($sqlResult) {
        $output = array();
        while ($row = mysqli_fetch_assoc($sqlResult)) {
            
            array_push($output,
                    // output에 넣을 데이터 예시
                    array(
                        'centerName' => $row['centerName'],
                        'phoneNum' => $row['phoneNum'],
                        'address' => $row['address']
                    )
                );
        }

        $resultArr['result'] = "success";
        $resultArr['data'] = $output;
        echo json_encode($resultArr);

    } else {
        // 리뷰 저장 실패
        echo("쿼리오류 발생: " . mysqli_error($con));


    }



?>




