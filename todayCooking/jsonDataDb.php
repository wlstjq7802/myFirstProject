<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // 요리 소개 이미지 배열
    $cookingDescImg = array();

    $jsonString = $_POST['jsonData']; // 객체 데이터

    $fileCount = $_POST['cookingDescImg'];
    for($i=0; $i < (int)$fileCount; $i++){
        $imgFile = $_FILES['img'.$i];
        $fileName= $imgFile['name']; // 파일명
        $tmpName= $imgFile['tmp_name']; //서버에 임시로 할당된 파일명
        $filePath= "/var/www/html/img/". $fileName;
        $result=move_uploaded_file($tmpName, $filePath);
        $cookingDescImg[$i] = $fileName;
    }
    
    // $imgFile = $_FILES['img']; // img 파일
    // $fileName= $imgFile['name']; // 파일명
    // $tmpName= $imgFile['tmp_name']; //서버에 임시로 할당된 파일명
    // $saveName = time(). ".png";
    
    //임시 저장소 이미지를 원하는 폴더로 이동
    // $filePath= "/var/www/html/img/". $fileName;
    // $result=move_uploaded_file($tmpName, $filePath);


    // json_decode($jsonString, $assoc, $depth, $options);
    // jsonString: 데이터를 추출하려는 json 인코딩 문자열
    // assoc: 부울 변수. true면 연관 배열을, false이면 함수는 객체를 반환한다.
    // depth: 정수. 지정된 깊이를 지정한다.
    // options: 
    
    if($result){
        $data = json_decode($jsonString, true);

        $jsonCookingDescImg = json_encode($cookingDescImg);

        // 레시피 저장 쿼리
        $sql = "
            insert into recipe set
            recipeName =  '$data[recipeName]',
            level = '$data[level]',
            category = '$data[category]',
            number = '$data[number]',
            cookingTime = '$data[cookingTime]',
            ingredient = '$data[ingredient]',
            Stage = '$data[stage]',
            introduction = '$data[introduction]',
            writer = '$data[writer]',
            reportDate = '$data[reportDate]',
            view = '$data[view]',
            bookmark = '$data[bookmark]',
            representativeImg = '$jsonCookingDescImg'
        ";

        $sqveResult = mq($sql);
        if($sqveResult){

            $sql = "SELECT * FROM recipe WHERE recipeName = '$data[recipeName]'";
            $result = mq($sql);
            $output = array();
            if($result){
                while ($row = mysqli_fetch_assoc($result)) { // 행별로 유저의 정보 output에 넣어주기
                    array_push($output,
                        // output에 넣을 데이터 예시
                        array(
                            'recipeName' => $row['recipeName'],
                            'level' => $row['level'],
                            'category' => $row['category'],
                            'number' => $row['number'],
                            'cookingTime' => $row['cookingTime'],
                            'ingredient' => $row['ingredient'],
                            'stage' => $row['Stage']
                        )
                    );
                }
            }else{
                $output = array('message' => '쿼리 결과 없음');
            }
            echo json_encode($output);

            // // 성공
            // echo "
            // db에 저장되었습니다.\n
            // filePath: $filePath. \n
            // fileName:  $fileName. \n
            // tmpName: $tmpName
            // ";
            
        }else {
            // 실패
            echo "저장 실패";
            echo("Errormessage:". $con -> error);
        }
        
    } else{
        echo "파일 저장 실패\n";
        echo "path: ". $filePath. \n;
        // echo "error: ". $imgFile['error']; 
    }
    

?>