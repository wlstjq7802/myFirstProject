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
    $existingFolderArr = array();


    // 1.사용자 id로 bookmark 데이터를 조회한다
    // 2.조회된 데이터의 전체 폴더에 recipe번호가 있는지 확인 후
    //   있다면 기존 값을 삭제 후 맨 앞에 추가 후 3번 진행,
    //   없다면 전체 폴더에 recipe번호를 맨 앞에 추가 후 3번 진행
    // 3.선택한 폴더가 db의 폴더 배열에 있는지 확인한다
    //   없다면 추가를 하고 4번 진행,
    //   있다면 기존 값을 삭제 후 맨 앞에 추가를 하고 4번 진행
    // 4.db의 recipe데이터에 선택한 폴더의 이름을 키로 갖는 데이터가 있는지 확인
    //   있다면 recipe번호가 있는지 확인하고 있다면 기존 값은 삭제 후 맨 앞에 추가,
    //   없다면 폴더의 이름을 키로하는 데이터를 생성하고 recipe번호를 추가한다.
    

    // 1.사용자 id로 bookmark 데이터를 조회한다
    $sql = "SELECT * FROM bookmark where userId = '$userId'";
    $result = mq($sql);
    $userChk = mysqli_num_rows($result);
    

    // bookmark 테이블에 사용자id를 갖는 데이터가 있는 경우
    if($userChk > 0){

        $scrapResult = mysqli_fetch_array($result);

        // 2.조회된 데이터의 전체 폴더가 존재하는지 확인
        if(isset($scrapResult['bookmarkRecipeNum']) && count(json_decode($scrapResult['bookmarkRecipeNum'])) >= 1){

            $totalRecipeNumArr = json_decode($scrapResult['bookmarkRecipeNum']);
            $arrSize = count($totalRecipeNumArr);
            $folderCount = count($folderArr);
            $checkCount = 0;

            // 선택한 폴더가 있는지 확인
            for($u=0; $u<$folderCount; $u++){
                if($folderArr[$u]['check']=="uncheck"){
                    $checkCount++;
                }
            }
            
            // 2.조회된 데이터의 전체 폴더에 recipe번호가 있는지 확인
            for($i = 0; $i < $arrSize; $i++){
            
                // 받은 폴더 데이터가 전부 uncheck인 경우
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

        // 폴더 데이터가 있는경우
        if(isset($scrapResult['folder']) && count(json_decode($scrapResult['folder'])) >= 1){
            $existingFolderArr = json_decode($scrapResult['folder']);

            for($i=0; $i < count($folderArr); $i++){

                // 없다면 추가한다.
                if(!in_array($folderArr[$i]['folderName'], $existingFolderArr)){
                    array_push($existingFolderArr, $folderArr[$i]['folderName']);
                }                 
            }

            $existingFolderArr = json_encode($existingFolderArr);
            echo "에러1";
        }

        // 폴더 데이터 자체가 없는 경우 
        // 선택한 폴더를 폴더 배열에 저장 
        else{

            for($i=0; $i<count($folderArr); $i++){
                array_push($existingFolderArr, $folderArr[$i]['folderName']);
            }
            $existingFolderArr = json_encode($existingFolderArr);
            echo "에러2";
        }
       
    } else{
        echo "bookmark실패1";
    }

    
        


    

?>