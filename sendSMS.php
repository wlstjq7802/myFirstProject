<?php

    // 네이버 클라우드 플랫폼의 sens를 이용한 sms 문자 발송
    include_once "dbcon.php";

    $to = $_POST['userPhone'];

    $responseArr = array();// 결과 배열

    // 핸드폰 번호 중복확인
    $sql = "SELECT * FROM userInfo WHERE phone='$to'";

    $result = mq($sql);
    $count = mysqli_num_rows($result);

    if($count == 0){
        // 중복 ID 없음(사용 가능)
        // sms 보내기 추가 
        $sID = "ncp:sms:kr:280046428138:smokin_cessation_helper"; // 서비스 ID
        $smsURL = "https://sens.apigw.ntruss.com/sms/v2/services/".$sID."/messages";
        $smsUri = "/sms/v2/services/".$sID."/messages";
        $sKey = "087eee26200e45b8bfed5231bb392116"; // 서비스 secret Key

        $accKeyId = "t5AbhiBrarQfkkn6116Z";  // API 인증키
        $accSecKey = "gcoC1QzCwnSAbL2hzJKsgeaPM1jLmDhshgB2wjPR"; //API 인증 secret key

        $sTime = floor(microtime(true) * 1000);

        // 랜덤 숫자
        $authNum = rand(100000, 999999);

        // The data to send to the API
        $postData = array(
            'type' => 'SMS',
            'countryCode' => '82',
            'from' => '01034209973', // 발신번호 (등록되어있어야함)
            'contentType' => 'COMM',
            'content' => "메세지 내용",
            'messages' => array(array('content' => "[금연도우미]\n인증번호 [". $authNum. "]를 입력해주세요.", 'to' => $to))
        );

        $postFields = json_encode($postData) ;

        $hashString = "POST {$smsUri}\n{$sTime}\n{$accKeyId}";
        $dHash = base64_encode( hash_hmac('sha256', $hashString, $accSecKey, true) );

        $header = array(
                // "accept: application/json",
                'Content-Type: application/json; charset=utf-8',
                'x-ncp-apigw-timestamp: '.$sTime,
                "x-ncp-iam-access-key: ".$accKeyId,
                "x-ncp-apigw-signature-v2: ".$dHash
            );

        // Setup cURL
        $ch = curl_init($smsURL);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_POSTFIELDS => $postFields
        ));

        $response = curl_exec($ch);
        $response = json_decode($response, true);


        if($response['statusName'] == "success"){
            $responseArr['result'] = "success";
            $responseArr['authNum'] = $authNum;
        } else{
            $responseArr['result'] = "fail";
            
        }
        
        echo json_encode($responseArr, JSON_UNESCAPED_UNICODE);
    }
    else{
        // 중복 ID있음(사용 불가)
        
        $responseArr['result'] = "phoneDuplicate";

        echo json_encode($responseArr, JSON_UNESCAPED_UNICODE);

    }


    


?>