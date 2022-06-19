
<?php 
    include "dbcon.php"; // db 연결 파일
    
    $con = mysqli_connect("localhost", "jin", "jin1234", "SmokingCessationHelper");
    // left join
    /* 
        a, b 두 테이블 중에 a의 값 전체와 a의 해당 key 값과 b의 해당 key 값이
        같은 경우 그 값을 select 해온다.

    */
    // $sql = "
    //     SELECT feed.writer FROM feed LEFT JOIN diary ON feed.writer = diary.writer;
    // ";

    // $sql = "
    //     SELECT diary.writer FROM feed RIGHT JOIN diary ON feed.writer = diary.writer;
    // ";

    // INNER JOIN = 교집합

    // $sql = "
    //     SELECT * FROM msg left outer JOIN msgReadChk on msg.msg_id = msgReadChk.msg_id WHERE msgReadChk.isReaded = 0;
    // ";

    $sql = "
        SELECT * FROM msg left JOIN msgReadChk on msg.msg_id = msgReadChk.msg_id WHERE msgReadChk.isReaded = 0;
    ";


    $sqlResult = mq($sql);
    
    if($sqlResult) {
        while ($row = mysqli_fetch_assoc($sqlResult)) {
            
            echo json_encode($row, JSON_UNESCAPED_UNICODE). "<br><br><br>";
        }
        

    } else {
        // 리뷰 저장 실패
        echo("쿼리오류 발생: " . mysqli_error($con));


    }



?>




