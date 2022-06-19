<?php
    include_once "dbcon.php";
    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력


    $recipeNum = $_POST['recipeNum']; // 레시피 번호
    $userId = $_POST['userId'];       // 사용자 id
    
    // 레시피 번호 배열
    $recipeNumArr = array();
    

    // 1.북마크 table에 사용자의 id를 갖는 데이터가 있는지 조회한다.
    // 2.데이터가 있는 경우 bookmarkRecipeNum에 값이 있는지 확인한다. 
    // 3.bookmarkRecipeNum에 데이터가 있는 경우 값을 받아서 array로 변환한다.
    // 4.변환된 array에 post로 받은 recipeNum가 있는지 확인한다.
    // 5.recipeNum가 있다면 unset으로 삭제하고 배열의 번호를 재정렬한다.
    //               없다면 번호를 index 0번에 입력한다.
    // 6.완료되면 북마크 table에 update한다.
    

    // 1.북마크 table에 사용자의 id를 갖는 데이터가 있는지 조회한다.
    $sql = "SELECT * FROM bookmark where userId = '$userId'";
    $result = mq($sql);
    $userChk = mysqli_num_rows($result);
    

    // bookmark 테이블에 사용자id를 갖는 데이터가 있는 경우
    if($userChk > 0){

        
        $bookmarkData = mysqli_fetch_array($result);

        // 2.데이터가 있는 경우 bookmarkRecipeNum에 값이 있는지 확인한다.
        if(isset($bookmarkData['bookmarkRecipeNum']) && count(json_decode($bookmarkData['bookmarkRecipeNum'])) > 0){

            // 3.bookmarkRecipeNum에 데이터가 있는 경우 값을 받아서 array로 변환한다.
            $recipeNumArr = json_decode($bookmarkData['bookmarkRecipeNum']);
            $arrSize = count($recipeNumArr);

            // 4.변환된 array에 post로 받은 recipeNum가 있는지 확인한다.
            for($i = 0; $i < $arrSize; $i++){
            
                // 5.recipeNum가 있다면 unset으로 삭제하고 배열의 번호를 재정렬한다.
                if($recipeNumArr[$i]==$recipeNum){
                    unset($recipeNumArr[$i]);
                    $recipeNumArr = array_values($recipeNumArr);
                    $bookmarkStatus = "unbookmark";
                    break;
                } else{
                    // 없다면 번호를 index 0번에 입력한다.
                    if($i == $arrSize-1){
                        array_unshift($recipeNumArr, $recipeNum);
                        $bookmarkStatus = "bookmark";
                        break;
                    }
                }
            }

        // 2.데이터가 있지만 bookmarkRecipeNum에 값이 없는경우
        } else{
            $recipeNumArr = array();
            $recipeNumArr[] = $recipeNum;
        }
        
        
        // 5.완료되면 북마크 table에 update한다.
        $recipeNumArr = json_encode($recipeNumArr);

        // recipeInfolder에 데이터가 있느지 확인
        if(isset($bookmarkData['recipeInfolder']) && count(json_decode($bookmarkData['recipeInfolder'])) > 0){
            $recipeInFolder = json_decode($bookmarkData['recipeInfolder'], true);
            $folder = $recipeInFolder['기본폴더'];
            
            // 기본폴더에 데이터가 있는지 확인
            if(isset($folder) && count($folder) > 0){
                $size = count($folder);

                
                for($i = 0; $i < $size; $i++){
                
                    // 5.recipeNum가 있다면 unset으로 삭제하고 배열의 번호를 재정렬한다.
                    if($folder[$i]==$recipeNum){
                        unset($folder[$i]);
                        $folder = array_values($folder);
                        $bookmarkStatus = 'unbookmark';
                        break;
                    } else{
                        // 없다면 번호를 index 0번에 입력한다.
                        if($i == $size-1){
                            array_unshift($folder, $recipeNum);
                            $bookmarkStatus = 'bookmark';
                            break;
                        }
                    }

                }
            } else{
                $folder = array();
                array_push($folder, $recipeNum);
                $bookmarkStatus = 'bookmark';
            }

            $recipeInFolder['기본폴더'] = $folder;

        } else{
            $recipeInFolder = array();
            $folder = array();
            array_push($folder, $recipeNum);
            $recipeInFolder['기본폴더'] = $folder;
            $bookmarkStatus = 'bookmark';
        }

        // unbookmark이면 recipeInfolder의 폴더에 레시피번호를 전부 삭제한다.
        if($bookmarkStatus == "unbookmark"){
            $folderNameArr = json_decode($bookmarkData['folder']);
            for($i = 0; $i < count($folderNameArr); $i++){
                $recipeFolder = $recipeInFolder[$folderNameArr[$i]];
                for($j = 0; $j<count($recipeInFolder[$folderNameArr[$i]]); $j++){
                    if($recipeFolder[$j] == $recipeNum){
                        unset($recipeFolder[$j]);
                        $recipeFolder = array_values($recipeFolder);
                        break;
                    }
                }
                $recipeInFolder[$folderNameArr[$i]] = $recipeFolder;
            }

        }

        $recipeInFolder = json_encode($recipeInFolder, JSON_UNESCAPED_UNICODE); 


        $sql3 = "
                    update bookmark SET 
                    bookmarkRecipeNum = '$recipeNumArr',
                    recipeInfolder = '$recipeInFolder'
                    WHERE userId = '$userId'
                ";
        $result = mq($sql3);

        if($result){
            // echo $resultValue;
            echo $bookmarkStatus;
        } else{
            echo "bookmark실패1";
        }

      // bookmark 테이블에 사용자id를 갖는 데이터가 없는 경우
    } else{

        $recipeNumArr[] = $recipeNum;
        $folder = array();
        $recipeInFolder = array();
    
        $recipeInFolder['기본폴더'] = $recipeNumArr;

        // 폴더명과 레시피번호가 담긴 데이터
        $recipeInFolder = json_encode($recipeInFolder, JSON_UNESCAPED_UNICODE); 
        $recipeNumArr = json_encode($recipeNumArr); //전체 레시피 번호 데이터
        array_push($folder, "기본폴더");             //폴더명 데이터
        $folder = json_encode($folder, JSON_UNESCAPED_UNICODE);

        $sql2 = "
            INSERT INTO bookmark SET 
            bookmarkRecipeNum = '$recipeNumArr',
            folder = '$folder',
            recipeInfolder = '$recipeInFolder',
            userId = '$userId'
        ";

        $userIdAddResult = mq($sql2);

        if($userIdAddResult){
            echo "bookmark";
        } else{
            echo "bookmark실패2";
        }

    }

    

?>