<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    



    $comment = $_POST['comment']; // 댓글내용
    $recipeNum = $_POST['recipeNum'];
    $userId = $_POST['userId'];
    $reportDate = $_POST['reportDate'];


    $imgFile = $_FILES['img']; // 파일

    // 파일이 있는지 확인하는 조건문
    if(isset($imgFile)){
        $fileName= $imgFile['name']; // 파일명
        $tmpName= $imgFile['tmp_name']; //서버에 임시로 할당된 파일명
        $filePath= "/var/www/html/img/". $fileName; // 업로드하려는 위치
        $result=move_uploaded_file($tmpName, $filePath);
    
    } else{
        $result = true;
    }
     
    
    
    // 파일 등록 확인 조건문
    if($result){

            
            
            if(isset($imgFile)){
                // 레시피 저장 쿼리
                $sql = "
                    insert into replyDB set
                    comment = '$comment',
                    recipeNum = '$recipeNum',
                    userId = '$userId',
                    reportDate = '$reportDate',
                    img = '$fileName'
                ";
            } else{
                // 레시피 저장 쿼리
                $sql = "
                    insert into replyDB set
                    comment = '$comment',
                    recipeNum = '$recipeNum',
                    userId = '$userId',
                    reportDate = '$reportDate',
                    img = ''
                ";
            }

            $replyRegistResult = mq($sql);


            if($replyRegistResult){
                echo "success";
            } else{
                echo "댓글 등록 실패";
            }

    } else{
        echo "파일 저장 실패\n";
        echo "path: ". $filePath. \n;
    }
    

?>