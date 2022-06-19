<?php 
    include "dbcon.php"; // db 연결 파일
    
    $replyId = $_POST["replyId"];
    $feedId = $_POST["feedId"];

    // db에 포스트 저장하기
    $sql = "
        DELETE FROM reply WHERE replyId = '$replyId'
    ";

    $resultArr = array();

    $sqlResult = mq($sql);
    
    if($sqlResult) {
        
        // 댓글 게수 변경
        $replyCntSql = "SELECT replyCnt FROM feed WHERE feedId = '$feedId'";
        $replyCntSqlResult = mq($replyCntSql);
        $resultArr  = mysqli_fetch_array($replyCntSqlResult);

        $replyCnt = ((int) $resultArr['replyCnt'])-1;

        $replyUpdateSql = "
            update feed SET
            replyCnt = '$replyCnt'
            WHERE feedId = '$feedId'
        ";

        $replyUpdateResult = mq($replyUpdateSql);
        
        if($replyUpdateResult) {
            // 포스트 저장 완료
            $replyCntSql = "SELECT replyId FROM reply WHERE feedId = '$feedId'";
            $replyCntSqlResult = mq($replyCntSql);
            $replyCnt  = mysqli_num_rows($replyCntSqlResult);
            
            $resultArr['replyCnt'] =  $replyCnt;
            $resultArr['result'] = "success";
            
        } else {
            $resultArr['result'] = "fail";
        }

    } else {
        // 리뷰 저장 실패
        $resultArr['result'] = "fail";
    }


    echo json_encode($resultArr);
?>