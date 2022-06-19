<?php 
    include "connect_db.php"; // db 연결 파일
//     ini_set('display_errors',1);
// error_reporting(E_ALL);

    // $title = $_POST["title"];
    // $contents = $_POST["contents"];
    $feed = $_POST["feed"]; // json을 문자열로 받음
    $cntImage = $_POST["cntImage"]; // 첨부된 사진 개수
    $cntImage = (int)$cntImage;


    // 첨부된 사진 파일 받기
    $image = array();
    for($i=0; $i<$cntImage; $i=$i+1) {
        $image[] = $_FILES['image'.$i];
    }
    
    // 클라이언트로 보낼 응답 배열
    // $result = array();

    // // 20자 랜덤 문자열 생성하는 메소드
    // function generateRandomString($length = 20) {
    //     $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
    //     $charactersLength = strlen($characters); 
    //     $randomString = ''; 
    //     for($i = 0; $i < $length; $i++) { 
    //         $randomString .= $characters[mt_rand(0, $charactersLength - 1)]; 
    //     } 
    //     return $randomString;
    // }
    
    // // 중복되지 않는 20자리 문자열을 postId로 설정
    // $postId;
    // $num = 1;
    // while ($num > 0) {
    //     $postId = generateRandomString(20);
    //     // 중복되는 id인지 확인
    //     $sql = "select postId from post_tbl where postId = '$postId'";
    //     $res = mysqli_query($connect, $sql);
    //     $num = mysqli_num_rows($res);
    // }
    
    // 서버에 저장된 사진의 uri 리스트
    $uriList = array();
    $uploadResult = false;

    if($cntImage > 0) {
    
    	// 첨부된 사진이 있을 때
        $uploadDir = '/var/www/html/img/'; // 서버에서 사진을 저장할 디렉토리 path
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
            
            // if($uploadResult){
            //     echo "업로드 성공";
            //     break;
            // } else{
            //     switch ($image[$i]['error']){
            //         case 1: echo 'msg: upload_max_filesize 초과';break; 
            //         case 2: echo 'msg: max_file_size 초과';break; 
            //         case 3: echo 'msg: 파일이 부분만 업로드됐습니다.';break; 
            //         case 4: echo 'msg: 파일을 선택해 주세요.';break; 
            //         case 6: echo 'msg: 임시 폴더가 존재하지 않습니다.';break; 
            //         case 7: echo 'msg: 임시 폴더에 파일을 쓸 수 없습니다. 퍼미션을 살펴 보세요.';break; 
            //         case 8: echo 'msg: 확장에 의해 파일 업로드가 중지되었습니다.';break; 
            //    } 
            // }
            $uriList[] = $name;
            
        }
        
    } else{
        echo "이미지 없음";
    }

    echo $uploadResult = json_encode($uriList);

    

    // if($image[0]['error'] > 0){
    //     echo '{result: -1, ';
    //      //오류 타입에 따라 echo 'msg: "오류종류"}';
    //      switch ($image[0]['error']){
    //          case 1: echo 'msg: "upload_max_filesize 초과"}';break; 
    //          case 2: echo 'msg: "max_file_size 초과"}';break; 
    //          case 3: echo 'msg: "파일이 부분만 업로드됐습니다."}';break; 
    //          case 4: echo 'msg: "파일을 선택해 주세요."}';break; 
    //          case 6: echo 'msg: "임시 폴더가 존재하지 않습니다."}';break; 
    //          case 7: echo 'msg: "임시 폴더에 파일을 쓸 수 없습니다. 퍼미션을 살펴 보세요."}';break; 
    //          case 8: echo 'msg: "확장에 의해 파일 업로드가 중지되었습니다."}';break; 
    //     } 
    // } else{
    //     echo "에러 없음";
    // }


    // if($uploadResult){
    //     $uploadResult = json_encode($uriList);
    // } else{
    //     $uploadResult = "fail";
    // }
    

    // echo $uploadResult;
    
    // // jsonArray를 문자열로 변환
    // $uriList = json_encode($uriList);

    // // db에 포스트 저장하기
    // $sql = "insert into post_tbl (postId, post, imageList, urlList, publisher, uploadDate)";
    // $sql.= " values ('$postId', '$post', '$uriList', '$urlList', '$email', now())";
    // $res = mysqli_query($connect, $sql);
    // if($res) {
    //     // 포스트 저장 완료
    //     $result["success"] = true;

    // } else {
    //     // 리뷰 저장 실패
    //     $result["success"] = false;
    // }

    // mysqli_close($connect);

    // // 배열을 json 문자열로 변환하여 클라이언트에 전달
    // echo json_encode($result);

?>