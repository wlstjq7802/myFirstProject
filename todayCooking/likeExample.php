<?php
    include_once "dbcon.php";
    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력


    $recipeNum = $_POST['recipeNum']; // 레시피 번호
    $userId = $_POST['userId'];       // 사용자 id
    
    // 레시피 번호에 회원이 있는지 확인
    // 레시피 번호 배열
    $userArr = array();

    $sql = "SELECT * FROM likeDB where recipeNum = '$recipeNum'";
    $result = mq($sql);

    $recipeChk = mysqli_num_rows($result);
    
    // 레시피가 좋아요를 한번이라도 눌린경우
    if($recipeChk > 0){
        $likeCheck = true;
        
        $likUserArr = mysqli_fetch_array($result);
        $likUserArr = json_decode($likUserArr['userId']);

        // 좋아요가 이미 체크되어있으면 체크를 해제
        for($i = 0; $i < count($likUserArr); $i++){
            if($likUserArr[$i] == $userId){
                
                $likeCheck = false;
                unset($likUserArr[$i]);
                $likeCount = count($likUserArr);
                $likUserArr = json_encode($likUserArr);

                $sql3 = "
                    update likeDB SET 
                    recipeNum = '$recipeNum',
                    userId = '$likUserArr'
                    WHERE recipeNum = '$recipeNum'
                ";

                $result = mq($sql3);
                if($result){

                    // 레시피 table의 좋아요 수 변경
                    $recipeLikeSql = "
                        update recipe SET 
                        likeCount = '$likeCount'
                        WHERE recipeNum = '$recipeNum'
                    ";

                    $recipeLikeResult = mq($recipeLikeSql);
                    if($recipeLikeResult){
                        echo "unlike";
                    } else{
                        echo "좋아요 취소 저장에 실패하였습니다.";
                    }

                } else {
                    echo "좋아요 취소에 실패하였습니다.";
                }

                break;
            }
        }

        // 좋아요가 안되어 있어 체크
        if($likeCheck){
            // 끝에 추가
            array_push($likUserArr, $userId);
            $likeCount = count($likUserArr);
            $likUserArr = json_encode($likUserArr);

            $sql3 = "
                update likeDB SET 
                recipeNum = '$recipeNum',
                userId = '$likUserArr'
                WHERE recipeNum = '$recipeNum'
            ";

            $result = mq($sql3);
            if($result){

                $recipeLikeSql = "
                    update recipe SET 
                    likeCount = '$likeCount'
                    WHERE recipeNum = '$recipeNum'
                ";

                $recipeLikeResult = mq($recipeLikeSql);
                if($recipeLikeResult){
                    echo "like";
                } else{
                    echo "좋아요 추가 저장에 실패하였습니다.";
                }

            } else {
                echo "좋아요 추가에 실패하였습니다.";
            }

        }

       
        // 레시피 정보가 없는 경우
    } else{

        $userArr[0] = $userId;
        $userArr = json_encode($userArr);

        $sql2 = "
            INSERT INTO likeDB SET 
            recipeNum = '$recipeNum',
            userId = '$userArr'
        ";

        $userIdAddResult = mq($sql2);

        if($userIdAddResult){

            $recipeLikeSql = "
                update recipe SET 
                likeCount = 1
                WHERE recipeNum = '$recipeNum'
            ";

            $recipeLikeResult = mq($recipeLikeSql);
            if($recipeLikeResult){
                echo "like";
            } else{
                echo "좋아요 추가 저장에 실패하였습니다.";
            }

        } else{
            echo "좋아요 추가에 실패하였습니다.2";
        }
    }
    

?>