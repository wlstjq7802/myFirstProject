<?php

    include_once "dbcon.php";  
    
    $sql = "
        SELECT MAX(Column1) AS max_price FROM ExamTable;
    ";

    $sqlResult = mq($sql);
    $sqlResult = mysqli_fetch_array($sqlResult);

    echo $sqlResult['max_price'];


    
    
    

?>