<?php
    $con = mysqli_connect("localhost", "jin", "jin1234", "SmokingCessationHelper");
    mysqli_set_charset($con,"utf8");
    
        // SQL 쿼리문 간단하게 쓰기 위한 함수 mq 선언
        function mq($sql){
            global $con;
            return $con->query($sql);
        }
        
        function getCon(){
            global $con;
            return $con;
        }

?>