<?php 
    include "dbcon.php"; // db 연결 파일


    $feedId = $_POST["feedId"];
    $comment = $_POST["comment"];
    $imgRequest = $_POST["imgRequest"]; // 첨부된 사진 개수
    $writer = $_POST['writer'];
    $wTime = $_POST['wTime'];
    $groupOrd = $_POST["groupOrd"];
    $groupLayer = 1;

    $resultArr = array();


    // if($imgRequest == "없음") {
    
    //     $imgFile = $_FILES['image']; // img 파일
    //     $tmp_name = $imgFile["tmp_name"];
    //     $oldName = $imgFile["name"]; //ex) example.jpg
    //     $type = $imgFile["type"]; // application/octet-stream
    //     $oldName_array = explode(".", $oldName);
    //     $type = $oldName_array[(count($oldName_array)-1)]; //ex) jpg
    //     $imgName = date("YmdHis").'_0'.'.' .$type; //ex) 날짜.jpg
    //     $path = "/var/www/html/img/". $imgName;
    //     // 임시 경로에 저장된 사진을 $path로 옮김
    //     $uploadResult = move_uploaded_file($tmp_name, $path);


    // } else{
    //     $imgName = $imgRequest;
    // }

    // 그룹순서 확인
    // 그룹 순서가 없으면 처음 댓글 작성
    // 순서가 있으면 답글 작성
    if(isset($groupOrd)){

        // 댓글 삭제되었는지 확인 예외처리
        $commentChkSql = "SELECT replyId AS max_groupLayer FROM reply WHERE groupOrd = '$groupOrd' AND feedId = '$feedId' AND groupLayer = 1";
        $commentChkResult = mq($commentChkSql);
    
        $commentChkArr  = mysqli_fetch_array($commentChkResult);
        if(sizeof($commentChkArr) > 0){
            $groupLayerSql = "SELECT MAX(groupLayer) AS max_groupLayer FROM reply WHERE groupOrd = '$groupOrd' AND feedId = '$feedId'";
            $groupLayerResult = mq($groupLayerSql);
        
            $resultArr  = mysqli_fetch_array($groupLayerResult);
            if(isset($resultArr) > 0){
                $groupLayer = ((int)$resultArr['max_groupLayer'])+1;
            } else{
                $groupLayer = 2;
            }
        } else{
            $groupLayer = -1;
        }
        
        

    } else{
        $groupOrdSql = "SELECT MAX(groupOrd) AS max_groupOrd FROM reply WHERE feedId = '$feedId'";
        $groupOrdResult = mq($groupOrdSql);
    
        $resultArr  = mysqli_fetch_array($groupOrdResult);
        if(isset($resultArr) > 0){
            $groupOrd = ((int)$resultArr['max_groupOrd'])+1;
        } else{
            $groupOrd = 1;
        }
    }


    // 댓글 삭제 후 작성하는 경우 예외처리
    if($groupLayer == -1){
        $resultArr['result'] = "null";
    } else{
        // db에 포스트 저장하기
        $sql = "
        INSERT INTO reply SET
            feedId = '$feedId',
            comment = '$comment',
            img = '$imgName',
            writer = '$writer',
            wTime = '$wTime',
            groupOrd = '$groupOrd',
            groupLayer = '$groupLayer'
        ";

        $sqlResult = mq($sql);

        if($sqlResult) {
            // 포스트 저장 완료
            
            $replyAddSql = "SELECT replyCnt FROM feed WHERE feedId = '$feedId'";
            $replyAddResult = mq($replyAddSql);
            $resultArr  = mysqli_fetch_array($replyAddResult);

            $replyAddResult = ((int) $resultArr['replyCnt'])+1;

            $replyUpdateSql = "
                update feed SET
                replyCnt = '$replyAddResult'
                WHERE feedId = '$feedId'
            ";
            $replyUpdateResult = mq($replyUpdateSql);

            if($replyUpdateResult){

                // 답글 작성시 클라이언트에서 바로 추가하기 위한 답글 데이터 조회
                $replyselectSql = "SELECT replyId FROM reply WHERE writer = '$writer' AND wTime = '$wTime'";
                $replyselectResult = mq($replyselectSql);
                $replyselectResult  = mysqli_fetch_array($replyselectResult);

                $replyCntSql = "SELECT replyId FROM reply WHERE feedId = '$feedId'";
                $replyCntSqlResult = mq($replyCntSql);
                $replyCnt  = mysqli_num_rows($replyCntSqlResult);
                
                $resultArr['replyCnt'] =  $replyCnt;
                $resultArr['result'] = "success";
                $resultArr['replyId'] = $replyselectResult['replyId'];

                $result = "success";
            } else{
                $resultArr['result'] = "fail";
            }


        } else {
            // 리뷰 저장 실패
            $resultArr['result'] = "fail";
        }
    }
    
    

    
    echo json_encode($resultArr, JSON_UNESCAPED_UNICODE);

?>