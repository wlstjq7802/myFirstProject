<?php
    include_once "dbcon.php";
    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력


    $recipeNum = $_POST['recipeNum']; // 레시피 번호
    $userId = $_POST['userId'];       // 사용자 id
    
    // 레시피 번호 배열
    $recipeNumArr = array();

    $sql = "SELECT * FROM bookmark where bookmarkRecipeNum = '$recipeNum'";
    $result = mq($sql);

    $userChk = mysqli_num_rows($result);
    


    // 레시피의 북마크가 한번이라도 눌린경우
    if($userChk > 0){
        $bookmarkCheck = true;
        
        $bookMarkArr = mysqli_fetch_array($result);
        $bookMarkArr = json_decode($bookMarkArr['userId'], true);
        


        // userId라는 키가 있는 경우
        if(isset($bookMarkArr[$userId])){

            // 값이 true이면 좋아요를 해제 
            if($bookMarkArr[$userId] == "true"){
                
                $bookMarkArr[$userId] = "false";
                
                $recipeSql = "SELECT * FROM recipe where recipeNum = '$recipeNum'";
                $recipeResult = mq($recipeSql);
                $recipeResult = mysqli_fetch_array($recipeResult);
                $likeCount = $recipeResult['likeCount'] - 1;

                $bookMarkArr = json_encode($bookMarkArr);

                $sql3 = "
                    update bookmark SET 
                    userId = '$bookMarkArr'
                    WHERE bookmarkRecipeNum = '$recipeNum'
                ";
                

                $result = mq($sql3);
                if($result){

                    // 사용자 정보에 북마크 정보가 있는지 확인 및 북마크에 레시피 번호 추가
                    $recipeSql = "SELECT * FROM userInfo where id = '$userId'";
                    $recipeResult = mq($recipeSql);
                    if($recipeResult){
                        $recipeResult = mysqli_fetch_array($recipeResult);
            
                        // 북마크 정보가 있는 경우
                        if(isset($recipeResult['bookmark'])){
                            $bookMarkArr = json_decode($recipeResult['bookmark']);
                            for($i = 0; $i < count($bookMarkArr); $i++){
                                if($bookMarkArr[$i] == "$recipeNum"){
                                    unset($bookMarkArr[$i]);
                                    $bookMarkArr = array_values($bookMarkArr);
                                    break;
                                }
                            }
                            $bookMarkArr = json_encode($bookMarkArr);
                            
                            $bookmarkSql = "
                                update userInfo SET 
                                bookmark = '$bookMarkArr'
                                WHERE id = '$userId'
                            ";
            
                            $bookmarkResult = mq($bookmarkSql);
                            if($bookmarkResult){
                                echo "unbookmark";
                            } else{
                                echo "fail10";
                            }
            
                        }
                    }
                    


                } else {
                    echo "fail2";
                }

            } 
            
            // false이면 좋아요 체크
            else if($bookMarkArr[$userId] == "false"){
                
                $bookMarkArr[$userId] = "true";
                $recipeSql = "SELECT * FROM userInfo where id = '$userId'";
                $recipeResult = mq($recipeSql);
                $recipeResult = mysqli_fetch_array($recipeResult);
                $likeCount = $recipeResult['bookmark'];
                $bookMarkArr = json_encode($bookMarkArr);

                $sql3 = "
                    update bookmark SET 
                    bookmarkRecipeNum = '$recipeNum',
                    userId = '$bookMarkArr'
                    WHERE bookmarkRecipeNum = '$recipeNum'
                ";

                $result = mq($sql3);
                if($result){

                    bookmarkChk();

                } else {
                    echo "fail4";
                }

            }
        }

        // userId라는 키가 없는 경우
        else{
            $bookMarkArr[$userId] = "true";
            $bookMarkArr = json_encode($bookMarkArr);
            

            $sql2 = "
                update bookmark SET
                bookmarkRecipeNum = '$recipeNum',
                userId = '$bookMarkArr'
                WHERE bookmarkRecipeNum = '$recipeNum'
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
                if($recipeLikeResult){

                    bookmarkChk();
                } else{
                    echo "fail5";
                }

            } else{
                echo "fail6";
            }
        }

       
      // 레시피가 북마크된적이 없는 경우
    } else{

        $userArr[$userId] = "true";
        $userArr = json_encode($userArr);

        $sql2 = "
            INSERT INTO bookmark SET 
            bookmarkRecipeNum = '$recipeNum',
            userId = '$userArr'
        ";

        $userIdAddResult = mq($sql2);

        if($userIdAddResult){

            bookmarkChk();
            

        } else{
            echo "fail8";
        }


    }

    
    function bookmarkChk(){
        // 사용자 정보에 북마크 정보가 있는지 확인 및 북마크에 레시피 번호 추가
        global $userId;
        global $recipeNum;


        $recipeSql = "SELECT * FROM userInfo where id = '$userId'";
        $recipeResult = mq($recipeSql);
        if($recipeResult){
            $recipeResult = mysqli_fetch_array($recipeResult);

            // 북마크 정보가 있는 경우
            if(isset($recipeResult['bookmark'])){
                $bookMarkArr = json_decode($recipeResult['bookmark']);
                $bookMarkArr[] = $recipeNum;
                $bookMarkArr = json_encode($bookMarkArr);
                
                $bookmarkSql = "
                    update userInfo SET 
                    bookmark = '$bookMarkArr'
                    WHERE id = '$userId'
                ";

                $bookmarkResult = mq($bookmarkSql);
                if($bookmarkResult){
                    echo "bookmark";
                } else{
                    echo "fail7";
                }

            } else{ // 북마크 정보가 없는 경우 

                $bookMarkRecipeNum = array();
                $bookMarkRecipeNum[] = $recipeNum;
                $bookMarkRecipeNum = json_encode($bookMarkRecipeNum);

                $bookmarkSql = "
                    update userInfo SET 
                    bookmark = '$bookMarkRecipeNum'
                    WHERE id = '$userId'
                ";

                $bookmarkResult = mq($bookmarkSql);
                if($bookmarkResult){
                    echo "bookmark";
                } else{
                    echo "fail8";
                }

            }
        }
    }
    

?>