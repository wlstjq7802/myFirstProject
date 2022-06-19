<?php
    include_once "dbcon.php";
    header('Content-Type: application/json; charset=UTF-8'); // JSON 형태로 데이터 출력


    $userId = $_POST['userId'];         // 사용자 id
    $recipeNum = $_POST['recipeNum'];   // 추가할 레시피 번호
    $folder = $_POST['folder'];         // 폴더 번호
    

    // 레시피 번호 배열
    $scrapResult = array();
    $bookmarkStatus = "bookmark";
    $folderArr = json_decode($folder, true);
    $checkCount = 0;
    $folderCount = 0;


    // 1.사용자 id로 스크랩 데이터를 조회한다
    // 2.조회된 데이터의 전체 폴더에 recipe번호가 있는지 확인한다
    //   없다면 전체 폴더에 recipe번호를 맨 앞에 추가 후 3번 진행
    // 3.db의 recipeInfolder에 post로 받아온 폴더데이터의 이름을 키로 갖는 데이터가 있는지 확인
    //   있다면 recipe번호가 있는지 확인하고 있다면 완료,
    //   없다면 폴더의 이름을 키로하는 데이터를 생성하고 recipe번호를 추가한다.
    

    // 1.사용자 id로 bookmark 데이터를 조회한다
    $sql = "SELECT * FROM bookmark where userId = '$userId'";
    $result = mq($sql);
    $userChk = mysqli_num_rows($result);
    

    // bookmark 테이블에 사용자id를 갖는 데이터가 있는 경우
    if($userChk > 0){

        $scrapResult = mysqli_fetch_array($result);

        // 2.조회된 데이터의 전체 폴더가 존재하는지 확인
        if(isset($scrapResult['bookmarkRecipeNum']) && count(json_decode($scrapResult['bookmarkRecipeNum'])) > 0){

            // 전체 레시피번호가 담긴 배열
            $totalRecipeNumArr = json_decode($scrapResult['bookmarkRecipeNum']);
            
            $arrSize = count($totalRecipeNumArr);
            $folderCount = count($folderArr);

            // 선택한 폴더가 있는지 확인
            for($u=0; $u<$folderCount; $u++){
                if($folderArr[$u]['check']=="uncheck"){
                    $checkCount++;
                }
            }
            
            // 2.전체 폴더에 recipe번호가 있는지 확인
            for($i = 0; $i < $arrSize; $i++){
            
                // 받은 폴더 데이터가 전부 uncheck인 경우 전체 레시피번호 배열에서 삭제
                if($checkCount == $folderCount && $totalRecipeNumArr[$i]==$recipeNum){
                    unset($totalRecipeNumArr[$i]);
                    $totalRecipeNumArr = array_values($totalRecipeNumArr);
                    break;
                }
                // 있다면 다음 진행
                if($totalRecipeNumArr[$i]==$recipeNum){
                    break;
                } else{
                    //   없다면 전체 폴더에 recipe번호를 맨 앞에 추가 후 3번 진행
                    if($i == $arrSize-1){
                        array_unshift($totalRecipeNumArr, $recipeNum);
                        break;
                    }
                }
                
            } 


        // 2.데이터가 있지만 bookmarkRecipeNum에 값이 없는경우
        } else{
            $totalRecipeNumArr = array();
            $totalRecipeNumArr[] = $recipeNum;
        }
        

        $totalRecipeNumArr = json_encode($totalRecipeNumArr);
        $sql3 = "
                    update bookmark SET 
                    bookmarkRecipeNum = '$totalRecipeNumArr'
                    WHERE userId = '$userId'
                ";
        $totalRecipeResult = mq($sql3);

      // bookmark 테이블에 사용자id를 갖는 데이터가 없는 경우
    } else{
        $totalRecipeNumArr = array();
        $totalRecipeNumArr[] = $recipeNum;
        $totalRecipeNumArr = json_encode($totalRecipeNumArr);

        $sql2 = "
            INSERT INTO bookmark SET 
            bookmarkRecipeNum = '$totalRecipeNumArr',
            userId = '$userId'
        ";

        $totalRecipeResult = mq($sql2);
        
    }


    // 3.선택한 폴더가 db의 폴더 배열에 있는지 확인한다
    //   없다면 추가를 하고 4번 진행,
    if($totalRecipeResult){

            // 4.db의 recipeInfolder에 받아온 폴더의 이름을 키로 갖는 값이 있는지 확인한다
            // 4-1.있다면 배열의 값이 CHECK인지 UNCHECK인지 확인한다.
            // 4-1-1. CHECK라면 레시피번호가 있는지 확인 후 있다면 db에 저장,
            //        없다면 맨 앞에 추가한다
            // 4-1-2. UNCHECK라면 레시피번호가 있는지 확인 후 있다면 제거한다
            //        없다면 db에 저장.
            // 4-2.없다면 키:폴더명으로 하고 전달 받은 폴더 배열의 폴더의 값이 
            //     CHECK인지 UNCHECK인지 확인한다
            // 4-2-1. CHECK라면 레시피 번호를 추가한다
            // 4-2-2. UNCHECK라면 db에 저장.
            
            // 폴더배열(폴더들을 저장할 배열)을 생성한다 = $recipeInFolderArr
            // 폴더(레시피 번호를 저장할 배열)를 생성한다 = $folderValue

            if(isset($scrapResult['recipeInfolder']) && count(json_decode($scrapResult['recipeInfolder'], true)) > 0){
                
                // 1.폴더 배열에 선택한 폴더의 이름이 있는지 확인한다
                $scrapSql = "SELECT * FROM bookmark where userId = '$userId'";
                $scrapResult = mq($scrapSql);
                $scrapFolderArr = mysqli_fetch_array($scrapResult);

                $recipeInFolderArr = json_decode($scrapFolderArr['recipeInfolder'], true);
                for($k=0; $k<count($folderArr); $k++){

                    //folder이름을 키로하는 값이 있는지 확인
                    if(isset($recipeInFolderArr[$folderArr[$k]['folderName']])&&count($recipeInFolderArr[$folderArr[$k]['folderName']]) > 0){
                        // folder이름을 키로하는 레시피번호가 담긴 배열
                        $recipeFolder = $recipeInFolderArr[$folderArr[$k]['folderName']];
                        
                        //4-1.folder의 값이 check인지 uncheck인지 확인

                        // 4-1-1. CHECK라면 레시피번호가 있는지 확인 후 있다면 건너뛰고,
                        //        없다면 맨 앞에 추가한다
                        if($folderArr[$k]['check']=="check"){

                           // 4-1-1 레시피 번호가 없는지 확인한다
                           if(!in_array($recipeNum, $recipeFolder)){
                                array_unshift($recipeFolder, $recipeNum);
                           } 
                        } 
                        // 4-1-2. UNCHECK라면 레시피번호가 있는지 확인 후 있다면 제거한다
                        //        없다면 건너뛴다.
                        else{
                            for($m=0; $m<count($recipeFolder); $m++){
                                if($recipeFolder[$m]==$recipeNum){
                                    unset($recipeFolder[$m]);
                                    $recipeFolder = array_values($recipeFolder);
                                    break;
                                }
                            }
                        }
                    }

                    // folder이름을 키로하는 값이 없는 경우
                    else{
                        
                        $recipeFolder = array();
                        if($folderArr[$k]['check']=="check"){
                            array_push($recipeFolder, $recipeNum);
                        }
                    }

                    // 폴더들을 전체 폴더에 저장
                    // $recipeFolder = json_encode($recipeFolder);
                    $recipeInFolderArr[$folderArr[$k]['folderName']] = $recipeFolder;
                }

            } 
            // 폴더 안에 레시피가 전혀 없는 경우
            else{
                // 1.폴더배열(폴더들을 저장할 배열)을 생성한다 = $recipeInFolderArr
                // 2.폴더(레시피 번호를 저장할 배열)를 생성한다 = $folderValue
                // 3.폴더배열에 key:폴더의 이름 value:폴더를 저장한다.
                $recipeInFolderArr = array();
                for($k=0; $k < count($folderArr); $k++){
                    $folderValue = array();
                    if($folderArr[$k]['check']=="check"){
                        array_push($folderValue, $recipeNum);
                    }
                    
                    $recipeInFolderArr[$folderArr[$k]['folderName']] = $folderValue;
                }

            }

            $recipeInFolderArr = json_encode($recipeInFolderArr, JSON_UNESCAPED_UNICODE);
            $recipeFolderUpdateSql = "
                    update bookmark SET 
                    recipeInfolder = '$recipeInFolderArr'
                    WHERE userId = '$userId'
                ";
            $recipeFolderUpdateResult = mq($recipeFolderUpdateSql);
            if($recipeFolderUpdateResult){
                if($checkCount == $folderCount && $checkCount != 0){
                    echo "unscrap";
                } else{
                    echo "scrap";
                }
                
            } else{
                echo "bookmark실패3";
            }


    } else{
        echo "bookmark실패1";
    }

?>