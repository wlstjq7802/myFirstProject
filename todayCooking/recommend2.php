<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // 1. 사용자의 id를 받아 bookmarkDB의 bookmarkNum에 데이터가 있는지 확인한다.
    // 2. 사용자의 bookmarkNum의 카테고리들의 점유율 순위를 정한다.
    // 3. 점유율이 높은 카테고리의 인기레시피부터  5개씩 순서대로 불러온다.


    // $userId = "wlstjq15";
    $userId = $_POST['userId'];
    $output = array();
    $categoryData = array();
    $recipeNumArr = array();

    $bookmarkSql = "SELECT * FROM bookmark WHERE userId = '$userId'";
    $bookmarkResult = mq($bookmarkSql);
    $bookmarkResult = mysqli_fetch_array($bookmarkResult);



    if(isset($bookmarkResult['bookmarkRecipeNum'])&&count(json_decode($bookmarkResult['bookmarkRecipeNum']))>0){
        $bookmarkRecipeNum = json_decode($bookmarkResult['bookmarkRecipeNum']);

        for($i=0; $i < count($bookmarkRecipeNum); $i++){
            // 1.북마크에 저장된 레시피 번호로 레시피 데이터를 불러온다.
            // 2.해당 레시피의 4종류의 카테고리를 불러온다.
            // 3.불러온 4종류의 카테고리 값을 array의 key로 정하고 값은 1로 저장한다.
            // 3-1. 만약 저장하려값을 키로하는 데이터가 있다면 해당 키의 데이터를 불러와 +1을
            //      한다.
            // 4.저장한 array의 값을 값이 높은 순서대로 정렬한다.( asort() )
            // 5.정렬한 array의 값이 높은 순서부터 레시피 5개의 데이터를 $output에 저장한다.
            // 6.결과를 response한다.

            // 1.북마크에 저장된 레시피 번호로 레시피 데이터를 불러온다.
            $categorySql = "SELECT * FROM recipe WHERE recipeNum = '$bookmarkRecipeNum[$i]'";
            $categoryResult = mq($categorySql);
            $categoryResult = mysqli_fetch_array($categoryResult);

            
            // 2.해당 레시피의 4종류의 카테고리를 불러온다.
            // 3.불러온 4종류의 카테고리 값을 array의 key로 정하고 값은 1로 저장한다.
            // 3-1. 만약 저장하려값을 키로하는 데이터가 있다면 해당 키의 데이터를 불러와 +1을
            //      한다.

            // 종류별 카테고리 저장
            if(isset($categoryData[$categoryResult['typeCategory']])){
                $categoryData[$categoryResult['typeCategory']] = $categoryData[$categoryResult['typeCategory']] + 1;
            } else{
                $categoryData[$categoryResult['typeCategory']] = 1;
            }

            // 상황별 카테고리 저장
            if(isset($categoryData[$categoryResult['situationCategory']])){
                $categoryData[$categoryResult['situationCategory']] = $categoryData[$categoryResult['situationCategory']] + 1;
            } else{
                $categoryData[$categoryResult['situationCategory']] = 1;
            }

            // 재료별 카테고리 저장
            if(isset($categoryData[$categoryResult['ingredientCategory']])){
                $categoryData[$categoryResult['ingredientCategory']] = $categoryData[$categoryResult['ingredientCategory']] + 1;
            } else{
                $categoryData[$categoryResult['ingredientCategory']] = 1;
            }

            // 방법별 카테고리 저장
            if(isset($categoryData[$categoryResult['methodCategory']])){
                $categoryData[$categoryResult['methodCategory']] = $categoryData[$categoryResult['methodCategory']] + 1;
            } else{
                $categoryData[$categoryResult['methodCategory']] = 1;
            }
            
        }

        // 4.저장한 array의 값을 값이 높은 순서대로 정렬한다.( asort() )
        arsort($categoryData);

        // 키값을 배열로 변환
        $categoryDataKey = array_keys($categoryData);

        // 5.정렬한 array의 값이 높은 순서부터 레시피 5개의 데이터를 $output에 저장한다.
        for($i=0; $i<count($categoryDataKey); $i++){

            $recipeNumSql = "SELECT * FROM recipe WHERE typeCategory = '$categoryDataKey[$i]' 
            OR situationCategory='$categoryDataKey[$i]' OR ingredientCategory='$categoryDataKey[$i]' 
            OR methodCategory='$categoryDataKey[$i]' ORDER BY likeCount desc Limit 5";
            $recipeNumResult = mq($recipeNumSql);
            

            while ($row = mysqli_fetch_assoc($recipeNumResult)) {

                if(!in_array($row['recipeNum'], $recipeNumArr) && !in_array($row['recipeNum'], $bookmarkRecipeNum)){
                    array_push($recipeNumArr, $row['recipeNum']);
                    // 작성자의 닉네임과 프로필 사진 받아오기
                    $sql2 = "SELECT * FROM userInfo where id = '$row[writer]'";
                    $userDataResult = mq($sql2);
                    $userData = mysqli_fetch_array($userDataResult);

                    // 북마크 상태 확인 
                    $sql3 = "SELECT * FROM bookmark where userId = '$userId'";
                    $likeCountResult = mq($sql3);
                    $bookmark = mysqli_fetch_array($likeCountResult);
                    $bookmark = json_decode($bookmark['bookmarkRecipeNum']);
                    $bookmarkCount = count($bookmark);

                    $bookmarkChk = false;
                    for($k = 0; $k < $bookmarkCount; $k++){
                        // 북마크 상태 확인
                        if($bookmark[$k] == $row['recipeNum']){
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


                    // 요리 완성 이미지 중 첫번째 이미지 불러오기
                    $cookingImg = json_decode($row['representativeImg']);
                    

                    array_push($output,
                        // output에 넣을 데이터 예시
                        array(
                            'recipeNum' => $row['recipeNum'],
                            'recipeName' => $row['recipeName'],
                            'writer' => $userData['nick'],
                            'recipeImg' => $cookingImg[0],
                            'cookingTime' => $row['cookingTime'],
                            'number' => $row['number'],
                            'level' => $row['level'],
                            'writerProfile' => $userData['profile_img'],
                            'writerId' => $userData['id'],
                            'reportDate' => $row['reportDate'],
                            'bookmarkCheck' => $bookmarkChk,
                            'likeCheck' => $likeChk,
                            'likeCount' => $row['likeCount']
                        )
                    );
                } 
                
            }

        }

        echo json_encode($output);
    } else{
        $surveySql = "SELECT * FROM userInfo WHERE id = '$userId'";
        $surveyResult = mq($surveySql);
        $surveyResult = mysqli_fetch_array($surveyResult);
        
        if(isset($surveyResult['survey']) && $surveyResult['survey']!=""){
            
            $surveyData = json_decode($surveyResult['survey'], true);

            $typeArr = $surveyData['type'];
            $ingredientArr = $surveyData['ingredient'];
            $situationArr = $surveyData['situation'];
            $methodArr = $surveyData['method'];

            $sql = "SELECT * FROM recipe WHERE level = '$surveyData[level]' AND cookingTime='$surveyData[cookingTime]' AND (";
            
            // 종류별 데이터
            for($i=0; $i<count($typeArr); $i++){
                if($i==0){
                    $sql = $sql. "typeCategory='". $typeArr[$i]."'";
                } else{
                    $sql = $sql. "OR typeCategory='". $typeArr[$i]. "'";
                }
            }

            // 재료별 데이터
            for($i=0; $i<count($ingredientArr); $i++){
                $sql = $sql. "OR ingredientCategory='". $ingredientArr[$i]. "'";
            }

            // 상황별 데이터
            for($i=0; $i<count($situationArr); $i++){
                $sql = $sql. "OR situationCategory='". $situationArr[$i]. "'";
            }

            // 방법별 데이터
            for($i=0; $i<count($methodArr); $i++){
                 if($i == count($methodArr)-1){
                    $sql = $sql. "OR methodCategory='". $methodArr[$i]. "') ORDER BY likeCount desc, recipeNum desc";
                } else{
                    $sql = $sql. "OR methodCategory='". $methodArr[$i]. "'";
                }
            }

            $result = mq($sql);

            while ($row = mysqli_fetch_assoc($result)) {

                if(!in_array($row['recipeNum'], $recipeNumArr) && $row['writer'] != $userId){
                    array_push($recipeNumArr, $row['recipeNum']);
                    // 작성자의 닉네임과 프로필 사진 받아오기
                    $sql2 = "SELECT * FROM userInfo where id = '$row[writer]'";
                    $userDataResult = mq($sql2);
                    $userData = mysqli_fetch_array($userDataResult);

                    // 북마크 상태 확인 
                    $sql3 = "SELECT * FROM bookmark where userId = '$userId'";
                    $likeCountResult = mq($sql3);
                    $bookmark = mysqli_fetch_array($likeCountResult);
                    $bookmark = json_decode($bookmark['bookmarkRecipeNum']);
                    $bookmarkCount = count($bookmark);

                    $bookmarkChk = false;
                    for($k = 0; $k < $bookmarkCount; $k++){
                        // 북마크 상태 확인
                        if($bookmark[$k] == $row['recipeNum']){
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


                    // 요리 완성 이미지 중 첫번째 이미지 불러오기
                    $cookingImg = json_decode($row['representativeImg']);
                    

                    array_push($output,
                        // output에 넣을 데이터 예시
                        array(
                            'recipeNum' => $row['recipeNum'],
                            'recipeName' => $row['recipeName'],
                            'writer' => $userData['nick'],
                            'recipeImg' => $cookingImg[0],
                            'cookingTime' => $row['cookingTime'],
                            'number' => $row['number'],
                            'level' => $row['level'],
                            'writerProfile' => $userData['profile_img'],
                            'writerId' => $userData['id'],
                            'reportDate' => $row['reportDate'],
                            'bookmarkCheck' => $bookmarkChk,
                            'likeCheck' => $likeChk,
                            'likeCount' => $row['likeCount']
                        )
                    );
                } 
                
            }

            echo json_encode($output);

        } else{
            echo "fail";
        }

        
    }
    
    
?>