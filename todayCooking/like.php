<?php
    include_once "dbcon.php";
    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력


    $recipeNum = $_POST['recipeNum']; // 레시피 번호
    $userId = $_POST['userId'];       // 사용자 id
    
    // 레시피 번호에 회원이 있는지 확인
    // 레시피 번호 배열
    $userArr = array();

    // 레시피번호가 같은 레코드를 불러온다.
    $sql = "SELECT * FROM likeDB where recipeNum = '$recipeNum'";
    $result = mq($sql);

    $recipeNumSql = "SELECT * FROM userInfo where id = '$userId'";
    $recipeNumResult = mq($recipeNumSql);
    $recipeNumResult = mysqli_fetch_array($recipeNumResult);

    $recipeChk = mysqli_num_rows($result);
    
    
    // 레시피의 좋아요가 한번이라도 눌린경우
    if($recipeChk > 0){

        $likUserArr = mysqli_fetch_array($result);
        $likUserArr = json_decode($likUserArr['userId'], true);

        // userId라는 키가 있는 경우
        if(isset($likUserArr[$userId])){

            // 값이 true이면 좋아요를 해제 
            if($likUserArr[$userId] == "true"){
                
                // 좋아요 해제
                $likUserArr[$userId] = "false";
                
                $recipeSql = "SELECT * FROM recipe where recipeNum = '$recipeNum'";
                $recipeResult = mq($recipeSql);
                $recipeResult = mysqli_fetch_array($recipeResult);
                $likeCount = $recipeResult['likeCount'] - 1;

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

                    // 좋아요한 레시피 번호 목록
                    if(isset($recipeNumResult['likeRecipeNum'])&&count(json_decode($recipeNumResult['likeRecipeNum']))){
                        $recipeNumData = json_decode($recipeNumResult['likeRecipeNum']);
                        if(in_array($recipeNum, $recipeNumData)){
                            $recipeNumData = arr_del($recipeNumData, $recipeNum);
                            $recipeNumData = array_values($recipeNumData);
                        }
                        $recipeNumData = json_encode($recipeNumData);

                        $recipeNumSql = "
                            update userInfo SET 
                            likeRecipeNum = '$recipeNumData'
                            WHERE id = '$userId'
                        ";
                        $recipeLikeResult = mq($recipeNumSql);
                    }


                    if($recipeLikeResult){
                        echo "unlike";
                    } else{
                        echo "좋아요 취소 저장에 실패하였습니다.";
                    }

                } else {
                    echo "좋아요 취소에 실패하였습니다.";
                }

            } 
            // false이면 좋아요 체크
            else if($likUserArr[$userId] == "false"){
                
                $likUserArr[$userId] = "true";
                $recipeSql = "SELECT * FROM recipe where recipeNum = '$recipeNum'";
                $recipeResult = mq($recipeSql);
                $recipeResult = mysqli_fetch_array($recipeResult);
                $likeCount = $recipeResult['likeCount'] + 1;
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

                    // 좋아요한 레시피 번호 목록
                    if(isset($recipeNumResult['likeRecipeNum'])&&count(json_decode($recipeNumResult['likeRecipeNum']))){
                        $recipeNumData = json_decode($recipeNumResult['likeRecipeNum']);
                        if(!in_array($recipeNum, $recipeNumData)){
                            array_push($recipeNumData, $recipeNum);
                            $recipeNumData = json_encode($recipeNumData);

                            $recipeNumSql = "
                                update userInfo SET 
                                likeRecipeNum = '$recipeNumData'
                                WHERE id = '$userId'
                            ";
                            $recipeLikeResult = mq($recipeNumSql);
                        }
                        
                    } else{
                        $recipeNumData = array();
                        array_push($recipeNumData, $recipeNum);
                        $recipeNumData = json_encode($recipeNumData);
        
                        $recipeNumSql = "
                            update userInfo SET 
                            likeRecipeNum = '$recipeNumData'
                            WHERE id = '$userId'
                        ";
                        $recipeLikeResult = mq($recipeNumSql);
                    }

                    
                    if($recipeLikeResult){
                        echo "like";
                    } else{
                        echo "좋아요 추가 저장에 실패하였습니다.";
                    }

                } else {
                    echo "좋아요 추가에 실패하였습니다.";
                }

            }
        } 
        // userId라는 키가 없는 경우
        else{
            $likUserArr[$userId] = "true";
            $likUserArr = json_encode($likUserArr);
            

            $sql2 = "
                update likeDB SET
                recipeNum = '$recipeNum',
                userId = '$likUserArr'
                WHERE recipeNum = '$recipeNum'
            ";

            $userIdAddResult = mq($sql2);

            if($userIdAddResult){

                $recipeSql = "SELECT * FROM recipe where recipeNum = '$recipeNum'";
                $recipeResult = mq($recipeSql);
                $recipeResult = mysqli_fetch_array($recipeResult);
                $likeCount = $recipeResult['likeCount'] + 1;

                $recipeLikeSql = "
                    update recipe SET 
                    likeCount = '$likeCount'
                    WHERE recipeNum = '$recipeNum'
                ";

                $recipeLikeResult = mq($recipeLikeSql);

                // 좋아요한 레시피 번호 목록
                if(isset($recipeNumResult['likeRecipeNum'])&&count(json_decode($recipeNumResult['likeRecipeNum']))){
                    $recipeNumData = json_decode($recipeNumResult['likeRecipeNum']);
                    if(!in_array($recipeNum, $recipeNumData)){
                        array_push($recipeNumData, $recipeNum);
                        $recipeNumData = json_encode($recipeNumData);

                        $recipeNumSql = "
                            update userInfo SET 
                            likeRecipeNum = '$recipeNumData'
                            WHERE id = '$userId'
                        ";
                        $recipeLikeResult = mq($recipeNumSql);
                    }
                    
                } else{
                    $recipeNumData = array();
                    array_push($recipeNumData, $recipeNum);
                    $recipeNumData = json_encode($recipeNumData);
    
                    $recipeNumSql = "
                        update userInfo SET 
                        likeRecipeNum = '$recipeNumData'
                        WHERE id = '$userId'
                    ";
                    $recipeLikeResult = mq($recipeNumSql);
                }
                
                if($recipeLikeResult){

                    echo "like";
                } else{
                    echo "좋아요 추가 저장에 실패하였습니다.";
                }

            } else{
                echo "좋아요 추가에 실패하였습니다.2";
            }
        }

    // 레시피 정보가 없는 경우
    } else{

        $userArr[$userId] = "true";
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

            // 좋아요한 레시피 번호 목록
            if(isset($recipeNumResult['likeRecipeNum'])&&count(json_decode($recipeNumResult['likeRecipeNum']))){
                $recipeNumData = json_decode($recipeNumResult['likeRecipeNum']);
                if(!in_array($recipeNum, $recipeNumData)){
                    array_push($recipeNumData, $recipeNum);
                    $recipeNumData = json_encode($recipeNumData);

                    $recipeNumSql = "
                        update userInfo SET 
                        likeRecipeNum = '$recipeNumData'
                        WHERE id = '$userId'
                    ";
                    $recipeLikeResult = mq($recipeNumSql);
                }
                
            } else{
                $recipeNumData = array();
                array_push($recipeNumData, $recipeNum);
                $recipeNumData = json_encode($recipeNumData);

                $recipeNumSql = "
                    update userInfo SET 
                    likeRecipeNum = '$recipeNumData'
                    WHERE id = '$userId'
                ";
                $recipeLikeResult = mq($recipeNumSql);
            }

            if($recipeLikeResult){
                echo "like";
            } else{
                echo "좋아요 추가 저장에 실패하였습니다.";
            }

        } else{
            echo "좋아요 추가에 실패하였습니다.2";
        }
    }
    

    function arr_del($list_arr, $del_value) // 배열, 삭제할 값
    {
        $b = array_search($del_value,$list_arr); 
        if($b!==FALSE) unset($list_arr[$b]); 
        return $list_arr;
    }
?>