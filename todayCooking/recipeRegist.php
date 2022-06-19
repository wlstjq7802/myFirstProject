<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    
    // 요리 소개 이미지 배열
    $cookingDescImg = array();
    $stageImg = array();

    $jsonString = $_POST['jsonData']; // 객체 데이터
    $fileCount = $_POST['cookingDescImg'];
    $stageFileCount = $_POST['stageImgCount'];

    // 완성이미지 저장
    for($i=0; $i < (int)$fileCount; $i++){
        $imgFile = $_FILES['img'.$i]; // img 파일
        $fileName= $imgFile['name']; // 파일명
        $tmpName= $imgFile['tmp_name']; //서버에 임시로 할당된 파일명
        $filePath= "/var/www/html/img/". $fileName; // 업로드하려는 위치
        $result=move_uploaded_file($tmpName, $filePath);
        $cookingDescImg[$i] = $fileName;
    }


    



    // json_decode($jsonString, $assoc, $depth, $options);
    // jsonString: 데이터를 추출하려는 json 인코딩 문자열
    // assoc: 부울 변수. true면 연관 배열을, false이면 함수는 객체를 반환한다.
    // depth: 정수. 지정된 깊이를 지정한다.
    // options: 
    
    if($result){
        $data = json_decode($jsonString, true);
        $stage = json_decode($data['stage'], true);

        for($i=0; $i < count($stage); $i++){
            $stage[$i]['stageImgUri']="";
        }

        // 단계별 이미지 저장
        for($i=0; $i < (int)$stageFileCount; $i++){
            $imgFile = $_FILES['stageImg'.$i];
            $fileName= $imgFile['name']; // 파일명
            $tmpName= $imgFile['tmp_name']; //서버에 임시로 할당된 파일명
            $filePath= "/var/www/html/img/". $fileName;
            $result=move_uploaded_file($tmpName, $filePath);
            $stage[$i]['stageImg'] = $fileName;
        }
        if($result){
            $stage = urlencode(json_encode($stage));
            $jsonCookingDescImg = json_encode($cookingDescImg);
            
            // 레시피 저장 쿼리
            $sql = "
                insert into recipe set
                recipeName =  '$data[recipeName]',
                level = '$data[level]',
                typeCategory = '$data[typeCategory]',
                situationCategory = '$data[situationCategory]',
                ingredientCategory = '$data[ingredientCategory]',
                methodCategory = '$data[methodCategory]',
                number = '$data[number]',
                cookingTime = '$data[cookingTime]',
                ingredient = '$data[ingredient]',
                Stage = '$stage',
                introduction = '$data[introduction]',
                writer = '$data[writer]',
                reportDate = '$data[reportDate]',
                likeCount = '$data[likeCount]',
                bookmark = '$data[bookmark]',
                representativeImg = '$jsonCookingDescImg'
            ";

            $sqveResult = mq($sql);
            if($sqveResult){

               echo "레시피 저장";

                
            }else {
                // 실패
                echo "저장 실패";
                echo("Errormessage:". $con -> error);
            }


        } else{
            echo "실패";
            echo("Errormessage:". $con -> error);
        }
        

        
        
    } else{
        echo "파일 저장 실패\n";
        echo "path: ". $filePath. \n;
        // echo "error: ". $imgFile['error']; 
    }
    

?>