<?php
    include_once "dbcon.php";
    $con = mysqli_connect("localhost", "jin", "jin1234", "todayCooking");

    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력
    
    // 레시피 메뉴에 레시피 데이터 보내기

    $userId = $_POST['userId']; // 객체 데이터
    $type = $_POST['typeValue'];
    $situation = $_POST['situationValue'];
    $ingredient = $_POST['ingredientValue'];
    $method = $_POST['methodValue'];

    $recipeSql;

    // 전체가 하나라도 있는 경우
    if($type=="전체" || $situation=="전체" || $ingredient=="전체" || $method=="전체"){
            
            

        // 종류별만 전체일 경우
        if($type=="전체" && $situation!="전체" && $ingredient!="전체" && $method!="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE situationCategory = '$situation' AND 
            ingredientCategory = '$ingredient' AND methodCategory = '$method' ORDER BY recipeNum desc";
        } 
        // 상황별만 전체일 경우
        else if($type!="전체" && $situation=="전체" && $ingredient!="전체" && $method!="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE typeCategory = '$type' AND 
            ingredientCategory = '$ingredient' AND methodCategory = '$method' ORDER BY recipeNum desc";
        }
        // 재료별만 전체일 경우
        else if($type!="전체" && $situation!="전체" && $ingredient=="전체" && $method!="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE typeCategory = '$type' AND 
            situationCategory = '$situation' AND methodCategory = '$method' ORDER BY recipeNum desc";
        }
        // 방법별만 전체일 경우
        else if($type!="전체" && $situation!="전체" && $ingredient!="전체" && $method=="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE typeCategory = '$type' AND 
            situationCategory = '$situation' AND ingredientCategory = '$ingredient' ORDER BY recipeNum desc";
        }
        // 종류별, 상황별 전체일 경우
        else if($type=="전체" && $situation=="전체" && $ingredient!="전체" && $method!="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE ingredientCategory = '$ingredient' AND 
            methodCategory = '$method' ORDER BY recipeNum desc";
        }
        // 종류별, 재료별 전체일 경우
        else if($type=="전체" && $situation!="전체" && $ingredient=="전체" && $method!="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE situationCategory = '$situation' AND
            methodCategory = '$method' ORDER BY recipeNum desc";
        }
        // 종류별, 방법별 전체일 경우
        else if($type=="전체" && $situation!="전체" && $ingredient!="전체" && $method=="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE situationCategory = '$situation' AND 
            ingredientCategory = '$ingredient' ORDER BY recipeNum desc";
        }
        // 상황별, 재료별 전체일 경우
        else if($type!="전체" && $situation=="전체" && $ingredient=="전체" && $method!="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE typeCategory = '$type' AND  
            methodCategory = '$method' ORDER BY recipeNum desc";
        }
        // 상황별, 방법별 전체일 경우
        else if($type!="전체" && $situation=="전체" && $ingredient!="전체" && $method=="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE typeCategory = '$type' AND 
            ingredientCategory = '$ingredient' ORDER BY recipeNum desc";
        }
        // 재료별, 방법별 전체일 경우
        else if($type!="전체" && $situation!="전체" && $ingredient=="전체" && $method=="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE typeCategory = '$type' AND 
            situationCategory = '$situation' ORDER BY recipeNum desc";
        }
         // 종류별, 상황별, 재료별이 전체일 경우
         else if($type=="전체" && $situation=="전체" && $ingredient=="전체" && $method!="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE methodCategory = '$method' ORDER BY recipeNum desc";
        } 
        // 종류별, 상황별, 방법별이 전체일 경우
        else if($type=="전체" && $situation=="전체" && $ingredient!="전체" && $method=="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE ingredientCategory = '$ingredient' ORDER BY recipeNum desc";
        } 
        // 종류별, 재료별, 방법별이 전체일 경우
        else if($type=="전체" && $situation!="전체" && $ingredient=="전체" && $method=="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE situationCategory = '$situation' ORDER BY recipeNum desc";
        } 
        // 상황별, 재료별, 방법별이 전체일 경우
        else if($type!="전체" && $situation=="전체" && $ingredient=="전체" && $method=="전체"){
            $recipeSql = "SELECT * FROM recipe WHERE typeCategory = '$type' ORDER BY recipeNum desc";
        } 
        // 전부 전체일 경우
        else if($type=="전체" && $situation=="전체" && $ingredient=="전체" && $method=="전체"){
            $recipeSql = "SELECT * FROM recipe ORDER BY recipeNum desc";
        } 

    } 
    // 전부 전체가 아닌 경우
    else if($type!="전체" && $situation!="전체" && $ingredient!="전체" && $method!="전체"){
        $recipeSql = "SELECT * FROM recipe WHERE typeCategory = '$type' AND 
            situationCategory = '$situation' AND ingredientCategory = '$ingredient' AND 
            methodCategory = '$method' ORDER BY recipeNum desc";
    }


    $result = mq($recipeSql);

    if($result){
        
            $output = array();

            // 행별로 유저의 정보 output에 넣어주기
            while ($row = mysqli_fetch_assoc($result)) {

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

            echo json_encode($output);

        
    } else{
        echo "레시피 불러오기 실패";
        echo("Errormessage:". $con -> error);
    }
    

    
    

?>