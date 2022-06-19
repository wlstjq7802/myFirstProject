<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    
    // 요리 소개 이미지 배열
    $stageImg = array();

    $jsonString = $_POST['jsonData']; // 객체 데이터
    $fileCount = $_POST['cookingDescImg'];     // 완성 이미지 개수
    $stageFileCount = $_POST['stageImgCount']; // 단계 이미지 개수
    $recipeNum = (int)$_POST['recipeNum'];

    $data = json_decode($jsonString, true);
    $cookingDescImg = json_decode($data['representativeImg'], true);

    for($j=0; $j < 5; $j++){
        if($cookingDescImg[$j] == 'null'){
            unset($cookingDescImg[$j]);
            continue;
        } 
    }

    if($fileCount > 0){
        // 완성 이미지 파일 저장
        for($i=0; $i < (int)$fileCount; $i++){
            $imgFile = $_FILES['img'.$i]; // img 파일
            $fileName= $imgFile['name']; // 파일명
            $tmpName= $imgFile['tmp_name']; //서버에 임시로 할당된 파일명
            $filePath= "/var/www/html/img/". $fileName; // 업로드하려는 위치
            $result=move_uploaded_file($tmpName, $filePath);

            // 완성 이미지 파일명을 db에 저장
            for($j=0; $j < 5; $j++){
                
                if(strpos($cookingDescImg[$j], '/') !== false) {  
                    $cookingDescImg[$j] = $fileName;
                    break;
                }
            }
        }
    } else{
        $result = true;
    }
    
    
    // 완성 이미지 파일명 배열
    $jsonCookingDescImg = json_encode($cookingDescImg);
    
    
    if($result){
        
        $stage = json_decode($data['stage'], true);

        for($i=0; $i < count($stage); $i++){
            $stage[$i]['stageImgUri']="";
        }

        for($i=0; $i < (int)$stageFileCount; $i++){
            $imgFile = $_FILES['stageImg'.$i];
            $fileName= $imgFile['name']; // 파일명
            $tmpName= $imgFile['tmp_name']; //서버에 임시로 할당된 파일명
            $filePath= "/var/www/html/img/". $fileName;
            $result = move_uploaded_file($tmpName, $filePath);
            
            for($j=0; $j < count($stage); $j++){
                if(strpos($stage[$j]['stageImg'], '/') !== false) {  
                    $stage[$j]['stageImg'] = $fileName;
                    break;
                } 
            }
        }

        if($result){
            $stage = urlencode(json_encode($stage));
            
            // 레시피 저장 쿼리
            $sql = "
                update recipe set
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
                likeCount = '$data[likeCount]',
                bookmark = '$data[bookmark]',
                representativeImg = '$jsonCookingDescImg'
                WHERE recipeNum = '$recipeNum'
            ";

            $sqveResult = mq($sql);
            if($sqveResult){
                echo "레시피 수정 완료";
                
            }else {
                // 실패
                echo "저장 실패 $recipeNum";
                echo("Errormessage:". $con -> error);
            }


        } else{
            echo "실패";
        }
        

        
        
    } else{
        echo "파일 저장 실패\n";
        echo "path: ". $filePath. \n;
        // echo "error: ". $imgFile['error']; 
    }
    

?>