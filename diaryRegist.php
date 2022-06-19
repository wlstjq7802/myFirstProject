<?php 
    include "dbcon.php"; // db 연결 파일

    $writer = $_POST['userId'];
    $degree = $_POST["degree"];
    $method = $_POST["method"];
    $contents = $_POST['contents'];
    $writeTime = $_POST['writeTime'];
    $isShare = $_POST['isShare'];
    $title = $_POST['title'];
    $imgUri = $_POST['imgUri'];
    $imgFile = $_FILES['img'];
    $rateDays = $_POST['rateDays'];
    
    if(isset($imgFile)) {

        $tmp_name = $imgFile["tmp_name"];
        $oldName = $imgFile["name"]; //ex) example.jpg
        $type = $imgFile["type"]; // application/octet-stream
        $oldName_array = explode(".", $oldName);
        $type = $oldName_array[(count($oldName_array)-1)]; //ex) jpg
        $name = date("YmdHis").'_'. $i.'.' .$type; //ex) 날짜.jpg
        $path = "/var/www/html/img/". $name;
        // 임시 경로에 저장된 사진을 $path로 옮김
        if(move_uploaded_file($tmp_name, $path)){
            $imgFile = $name;
            
        } else{

            $imgFile = $imgUri;
            // $imgFile = "저장안됨";
        }
        
    } else{
        $imgFile = $imgUri;
    }

    // db에 포스트 저장하기
    $sql = "
        INSERT INTO diary SET
            writer = '$writer',
            degree = '$degree',
            method = '$method',
            contents = '$contents',
            wTime = '$writeTime',
            isShare = '$isShare', 
            title = '$title',
            img = '$imgFile',
            rateDays = '$rateDays'
    ";

    $sqlResult = mq($sql);
    
    if($sqlResult) {
        // 포스트 저장 완료
        $result = "success";

    } else {
        // 리뷰 저장 실패
        $result = "fail";
    }


    // 배열을 json 문자열로 변환하여 클라이언트에 전달
    echo $result;

?>