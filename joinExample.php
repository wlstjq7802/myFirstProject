<?php 
    include "dbcon.php"; // db 연결 파일



        $sql = "
            SELECT *
            FROM userInfo left join feed
            on userInfo.userId = feed.writer
        ";

    $sqlResult = mq($sql);
    
    if($sqlResult) {
        
        while ($row = mysqli_fetch_assoc($sqlResult)) {
            echo "ID: ".$row['userId']. "<br>";
            echo "NICK: ".$row['nick']. "<br>";
            echo "작성자: ". $row['writer']. "<br>"; 
            echo "제목". $row['title'] . "<br><br>";
        }

    } else {
        echo "fail";
    }




?>