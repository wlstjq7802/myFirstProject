<?php
    include_once "dbcon.php";
    header('Content-Type: application/json; charset=UTF-8');

    $recipeNumber = $_POST['recipeNum'];
    $userId = $_POST['userId'];
    $output = array();
    
    $UpdateResult = true;


    // 사용자의 내가 본 레시피 데이터를 불러온다.
    $recipeRecordSql = "SELECT * FROM userInfo where id = '$userId'";
    $recipeRecordResult = mq($recipeRecordSql);
    $recipeRecordResult = mysqli_fetch_array($recipeRecordResult);


    if(isset($recipeRecordResult['recipeRecord']) && $recipeRecordResult['recipeRecord'] !== ""){
        $recipeRecord = json_decode($recipeRecordResult['recipeRecord']);
        // array_push($recipeRecord, 2);
        $size = count($recipeRecord);

        for($i = 0; $i < $size; $i++){
            if($recipeRecord[$i] == $recipeNumber){
                    unset($recipeRecord[$i]);
                    array_unshift($recipeRecord, $recipeNumber);
                    break;
            } else{
                    if($i == $size-1){
                        array_unshift($recipeRecord, $recipeNumber);  
                    }
            }
        }
        $recipeRecordValue = json_encode($recipeRecord);
    } else{
        $recipeRecord = array();
        array_push($recipeRecord, $recipeNumber);
        $recipeRecordValue = json_encode($recipeRecord);
    }

    
        $sql2 = "
            update userInfo SET 
            recipeRecord = '$recipeRecordValue'
            WHERE id = '$userId'
        ";
        $recipeRecordResult = mq($sql2);

        if($recipeRecordResult){

            $UpdateResult = true;
            

        } else{
            $UpdateResult = false;
        }

    if($UpdateResult){
        // 레시피 데이터 불러오기
        $sql = "SELECT * FROM recipe where recipeNum = '$recipeNumber'";
        $result = mq($sql);


        if($result){

            $replyCountSql = "SELECT * FROM replyDB WHERE recipeNum = '$recipeNumber'";
            $replyCountResult = mq($replyCountSql);
            $replyCount = mysqli_num_rows($replyCountResult);

            
            while ($row = mysqli_fetch_assoc($result)){ // 행별로 레시피 정보 output에 넣어주기
                
                // 북마크 상태 확인 
                $sql3 = "SELECT * FROM bookmark where userId = '$userId'";
                $likeCountResult = mq($sql3);
                $bookmark = mysqli_fetch_array($likeCountResult);
                $bookmark = json_decode($bookmark['bookmarkRecipeNum']);
                $bookmarkCount = count($bookmark);

                $bookmarkChk = false;
                for($k = 0; $k < $bookmarkCount; $k++){
                    // 북마크 상태 확인
                    if($bookmark[$k] == $row[recipeNum]){
                        $bookmarkChk = true;
                        break;
                    }
                }

                // 좋아요 상태 확인
                $sql3 = "SELECT * FROM likeDB where recipeNum = '$row[recipeNum]'";
                $likeCountResult = mq($sql3);
                $like = mysqli_fetch_array($likeCountResult);
                $like = json_decode($like['userId'], true);
                $likeChk = false;


                // 좋아요 상태 적용
                if($like[$userId] == "true"){
                    $likeChk = true;
                }
                
                array_push($output,
                    // output에 넣을 데이터 예시
                    array(
                        'recipeName' => $row['recipeName'],
                        'cookingLevel' => $row['level'],
                        'cookingNumber' => $row['number'],
                        'cookingTime' => $row['cookingTime'],
                        'cookingDesc' => $row['introduction'],
                        'ingredient' => $row['ingredient'],
                        'representativeImg' => $row['representativeImg'],
                        'Stage' => urldecode($row['Stage']),
                        'typeCategory' => $row['typeCategory'],
                        'situationCategory' => $row['situationCategory'],
                        'ingredientCategory' => $row['ingredientCategory'],
                        'methodCategory' => $row['methodCategory'],
                        'bookmark' => $bookmarkChk,
                        'replyCount' => $replyCount,
                        'likeCheck' => $likeChk,
                        'likeCount' => $row['likeCount']
                    )
                );
            }
            
        } else{
            echo "레시피 데이터 불러오기 실패";
        }
    } else{
        echo "내가 본 레시피 저장 실패";
    }

   
    echo json_encode($output);

?>