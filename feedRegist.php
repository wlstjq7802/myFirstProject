<?php 
    include "dbcon.php"; // db 연결 파일


    $title = $_POST["title"];
    $contents = $_POST["contents"];
    $cntImage = (int)$_POST["cntImage"]; // 첨부된 사진 개수
    $writer = $_POST['userId'];
    $wTime = $_POST['wTime'];
    $typeValue = $_POST['type'];


    // 첨부된 사진 파일 받기
    $image = array();
    for($i=0; $i<$cntImage; $i=$i+1) {
        $image[] = $_FILES['image'.$i];
    }
    
    // 클라이언트로 보낼 응답 배열
    // $result = array();

    
    // 서버에 저장된 사진의 uri 리스트
    $uriList = array();


    if($cntImage > 0) {
    
    	// 첨부된 사진이 있을 때
        
        for($i=0; $i < $cntImage; $i=$i+1) {

            $imgFile = $_FILES['image'.$i]; // img 파일
            $tmp_name = $imgFile["tmp_name"];
            $oldName = $imgFile["name"]; //ex) example.jpg
            $type = $imgFile["type"]; // application/octet-stream
            $oldName_array = explode(".", $oldName);
            $type = $oldName_array[(count($oldName_array)-1)]; //ex) jpg
            $name = date("YmdHis").'_'. $i.'.' .$type; //ex) 날짜.jpg
            $path = "/var/www/html/img/". $name;
            // 임시 경로에 저장된 사진을 $path로 옮김
            $uploadResult = move_uploaded_file($tmp_name, $path);
            
            $uriList[] = $name;
            
        }
        
    } else{
        $result = "fail";
    }


    // jsonArray를 문자열로 변환
    $uriList = json_encode($uriList);

    // db에 포스트 저장하기
    $sql = "
        INSERT INTO feed SET
            title = '$title',
            contents = '$contents',
            imgArr = '$uriList',
            writer = '$writer',
            wTime = '$wTime',
            type = '$typeValue',
            replyCnt = 0,
            likeCnt = 0
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