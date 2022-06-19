<?php

$jwt = new JWT();

// $id = "wlstjq12@naver.com";
// $id = base64_encode($id); // .이 들어가도 JWT가 분리되지 않기 위한 base64 인코딩

// $password = "비밀번호123";
// $password = base64_encode($password); // .이 들어가도 JWT가 분리되지 않기 위한 base64 인코딩


// // 유저 정보를 가진 jwt 만들기
// $token = $jwt->hashing(array(
//     'exp' => time() + (360 * 30), // 만료기간
//     'iat' => time(), // 생성일
//     'id' => $id,
//     'password' => $password
// ));

// var_dump($token);
// echo "<br/><br/>";


$token = "eyJhbGciOiJzaGEyNTYiLCJ0eXAiOiJKV1QifS57ImV4cCI6MTY0NTI3ODAyMSwiaWF0IjoxNjQ1MjY3MjIxLCJpZCI6ImQyeHpkR3B4TVRKQWJtRjJaWEl1WTI5dCIsInBhc3N3b3JkIjoiNjdtRTY3Q0E2N0tJN1ppNE1USXoifS45M2ZkYzI3MjY5OGQzYjcyNzJmNTNiMjZjODVjZTU1OTc4MWM5NGRmNjE2NmM0ZjM4Zjk4MGZiZWIzNmZkZDMz";
// jwt에서 유저 정보 가져오기
$data = $jwt->dehashing($token);

$parted = explode('.', base64_decode($token));

$payload = json_decode($parted[1], true);

var_dump($payload);

echo "<br/><br/>";
echo "id: " . base64_decode($payload['id']);
echo "<br/><br/>";
echo "password: " . base64_decode($payload['password']);




class JWT
{
    protected $alg;
    protected $secret_key;

//    생성자
    function __construct()
    {
        //사용할 알고리즘
        $this->$alg = 'sha256';

        // 비밀 키
        $this->$secret_key = "ddKdd123123K";
    }


//    jwt 발급하기
    function hashing(array $data): string
    {
        // 헤더 - 사용할 알고리즘과 타입 명시
        $header = json_encode(array(
            'alg' => $this->alg,
            'typ' => 'JWT'
        ));

        // 페이로드 - 전달할 데이터
        $payload = json_encode($data);

        // 시그니처
        $signature = hash($this->alg, $header . $payload . $this->secret_key);

        return base64_encode($header . '.' . $payload . '.' . $signature);
    }



//    jwt 해석하기
    function dehashing($token)
    {
        // 구분자 . 로 토큰 나누기
        $parted = explode('.', base64_decode($token));

        $signature = $parted[2];

        // 토큰 만들 때처럼 시그니처 생성 후 비교
        if (hash($this->alg, $parted[0] . $parted[1] . $this->secret_key) != $signature) {
            return "시그니쳐 오류";
        }

        // 만료 검사
        $payload = json_decode($parted[1], true);
        if ($payload['exp'] < time()) { // 유효시간이 현재 시간보다 전이면
            return "만료 오류";
        }

        /*
         *
         * 기타 토큰 확인 작업
         *
         */

        return json_decode($parted[1], true);
    }
}
?>