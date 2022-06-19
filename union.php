<?php 
    include "dbcon.php"; // db 연결 파일
    

    // feed와 diary의 데이터를 모두 불러와서 작성 날짜 내림차순으로 정렬한다.
    

 

    $sql = "
        SELECT feedId, writer, contents, wTime, replyCnt  FROM feed  UNION ALL
        SELECT id, writer, contents, wTime, title FROM diary 
        ORDER BY wTime desc;
    ";




    $sqlResult = mq($sql);
    
    if($sqlResult) {
        while ($row = mysqli_fetch_assoc($sqlResult)) {
            
            // echo $row['wTime']. "<br>";
            echo json_encode($row, JSON_UNESCAPED_UNICODE). "<br>";
        }
        // echo json_encode(mysqli_fetch_array($sqlResult)); 
        

    } else {
        // 리뷰 저장 실패
        echo "fail";
    }



?>



