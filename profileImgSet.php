<?php 
    include "dbcon.php"; // db 연결 파일
    
    $userId = $_POST["userId"];
    $nick = $_POST["nick"];
    $imgFile = $_FILES['image'];
    $isDefaultImg = $_POST['isDefaultImg'];
    $isImgNull = $_POST['isImgNull'];
    $filterImg = $_POST['filterImg'];
    

    if($isImgNull == "true"){
        $tmp_name = $imgFile["tmp_name"];
        $oldName = $imgFile["name"]; //ex) example.jpg
        $type = $imgFile["type"]; // application/octet-stream
        $oldName_array = explode(".", $oldName);
        $type = $oldName_array[(count($oldName_array)-1)]; //ex) jpg
        $name = date("YmdHis").'_'. $i.'.' .$type; //ex) 날짜.jpg
        $path = "/var/www/html/img/". $name;
        // 임시 경로에 저장된 사진을 $path로 옮김
        $uploadResult = move_uploaded_file($tmp_name, $path);

        if($uploadResult){
            $profilImg = $name;
        } else{
            $profilImg = $imgFile['error'];
        }
        
        

        $sql = "
            update userInfo SET
            nick = '$nick',
            profileImg = '$profilImg'
            WHERE userId = '$userId'
        ";
    } else if($isDefaultImg == "true"){

        $sql = "
            update userInfo SET
            profileImg = 'profile.jpg',
            nick = '$nick'
            WHERE userId = '$userId'
        ";
    } else if($filterImg != null){
        // 파일명에 임의의 난수를 부여해(rand()) jpg 확장자로 저장한다
        $profilImg = "IMG".rand().".jpg";
        /**
         * file_put_contents() : PHP에서 데이터 / 텍스트를 파일에 쓰는 데 사용되는 함수
         * int file_put_contents ( string $filename , mixed $data [, int $flags = 0 [, resource $context ]] )
         */
        file_put_contents("img/".$profilImg, base64_decode($filterImg));
        $sql = "
        update userInfo SET
        profileImg = '$profilImg',
        nick = '$nick'
        WHERE userId = '$userId'
    ";

    } else{
        $profilImg = "null";
        $sql = "
            update userInfo SET
            nick = '$nick'
            WHERE userId = '$userId'
        ";
    }

    $resultArr = array();

    $sqlResult = mq($sql);
    
    if($sqlResult) {
        // 포스트 저장 완료
        $resultArr["result"] = "success";
        $resultArr["profile"] = $profilImg;

    } else {
        // 리뷰 저장 실패
        $resultArr["result"] = "fail";
    }

    echo json_encode($resultArr);


?>