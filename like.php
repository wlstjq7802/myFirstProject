<?php 
    include "dbcon.php"; // db 연결 파일

    $con = mysqli_connect("localhost", "jin", "jin1234", "SmokingCessationHelper");
    /*
        @ 좋아요 클릭
        1. 게시글에 좋아요 클릭
        2. 사용자 id, 게시글 번호를 담아서 서버에 좋아요 요청
        3. 서버에서 like table에 사용자 id와 게시글 번호가 일치하는 index 확인
        
        4. 있다면 index 삭제 후 feed table의 해당 게시글 번호 index의 likeCnt
        -1 하고 클라이언트로 unLike 응답
        없다면 사용자 id와 게시글 번호 추가 후 feed table의 해당 게시글 번호
        index의 likCnt +1하고 클라이언트로 like 응답
        
        5. 서버로 부터 받은 응답을 확인하여
        unlike인 경우 좋아요 버튼 img unlike로 변경
        like인 경우 좋아요 버튼 img like로 변경
    */


    $userId = $_POST['userId'];
    $feedNum = $_POST['feedId'];
    $result = "fail";



    $likeChkSql = "
        SELECT * FROM likeTable WHERE feedNum = '$feedNum' AND userId = '$userId'
    ";

    $likeChkResult = mq($likeChkSql);
    $likeChkResult = mysqli_num_rows($likeChkResult);

    // 좋아요 취소(index가 있다면)
    if($likeChkResult >= 1){
        
        $likeSql = "
            DELETE FROM likeTable WHERE feedNum = '$feedNum' AND userId = '$userId'
        ";

        $likeResult = mq($likeSql);

        if($likeResult){
            $likeCntSql = "
            update feed SET
                likeCnt = likeCnt -1
                WHERE feedId = '$feedNum'
            ";
        } else{
            $result = "쿼리 오류 ".mysqli_error($con);
        }

        $likeCntResult = mq($likeCntSql);

        if($likeCntResult){
            $result = "unlike";
        } else{
            $result = "쿼리 오류 ".mysqli_error($con);
        }

    } 
    // 좋아요 (index가 없는 경우)
    else{
        
        $likeSql = "
            INSERT INTO likeTable SET
            userId = '$userId',
            feedNum = '$feedNum'
        ";
        $likeResult = mq($likeSql);

        if($likeResult){
            $likeCntSql = "
            update feed SET
                likeCnt = likeCnt +1
                WHERE feedId = '$feedNum'
            ";
            
        } else{
            $result = "쿼리 오류 ".mysqli_error($con);
        }

        $likeCntResult = mq($likeCntSql);

        if($likeCntResult){
            $result = "like";
        } else{
            $result = "쿼리 오류 ".mysqli_error($con);
        }

    }
    
    echo $result;

?>