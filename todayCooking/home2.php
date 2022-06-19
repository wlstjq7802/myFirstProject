<?php
include "/var/www/html/vendor/autoload.php";



// @slopeOne 코드1
  /**
     Example using "rating: liked and disliked"
     like: score = 1;  dislike: score = 0
  **/
  $table = [
    [
     'product_id'=> 'A',
     'score'=> 1, 
     'user_id'=> '김씨'
    ],
    [
     'product_id'=> 'B',
     'score'=> 1, 
     'user_id'=> '김씨'
    ],
    [
      'product_id'=> 'C',
      'score'=> 1, 
      'user_id'=> '김씨'
     ],
    [
     'product_id'=> 'A',
     'score'=> 1, 
     'user_id'=> '이씨'
    ],
    [
     'product_id'=> 'B',
     'score'=> 1, 
     'user_id'=> '이씨'
    ],
    [
      'product_id'=> 'C',
      'score'=> 0, 
      'user_id'=> '이씨'
     ],
    [
     'product_id'=> 'A',
     'score'=> 1,
     'user_id'=> '박씨'
    ],
    [
      'product_id'=> 'B',
      'score'=> 1,
      'user_id'=> '박씨'
     ]
];
use Tigo\Recommendation\Recommend; // import class
$client = new Recommend();
// print_r($client->ranking($table,"Pedro")); // result = ['C' => 2]
// echo "<br>"; 
// print_r($client->ranking($table,"Pedro",1)); // result = []; 
// echo "<br>";


// print_r($client->euclidean($table,"이씨")); // result = ['C' => 1]
// echo "<br>";
// print_r($client->euclidean($table,"이씨", 2)); // result = [] ;  
// echo "<br>";

// print_r($client->slopeOne($table,'이씨')); // result = ['C' => 1]
// echo "<br>";
// print_r($client->slopeOne($table,'이씨', 2)); // result = []
// echo "<br>";

echo "ranking: ";
print_r($client->ranking($table,'박씨')); // result = ['C' => 1]
echo "<br>";

echo "euclidean: ";
print_r($client->euclidean($table,'박씨')); // result = ['C' => 1]
echo "<br>";

echo "slopeOne: ";
print_r($client->slopeOne($table,'박씨')); // result = ['C' => 1]
echo "<br>";



// // 옵션인 세 번째 파라미터는 수락되지 않은 점수를 참조합니다.
// $client->ranking($table,$user);

// //옵션인 세 번째 파라미터는 최소 허용 점수를 나타냅니다.
// $client->euclidean($table,$user); 

// // slopeOne("데이터", "사용자")를 입력하면
// // 평가하지 않은 제품에 대한 사용자의 평가를 예측해서 반환한다.
// //옵션인 세 번째 파라미터는 최소 허용 점수를 나타냅니다.
// $client->slopeOne($table, $user); 









// // @ slopeOne 코드2
// use PHPJuice\Slopeone\Algorithm;

// // Create an instance
// $slopeone = new Algorithm();


// $data =[
//   [
//     "s" => 1,
//     "c" => 0,
//     "o" => 1
//   ],
//   [
//     "s" => 0,
//     "c" => 1,
//     "o" => 0
//   ]
// ];

// $slopeone->update($data);

// $results = $slopeone->predict([
//   "s" => 1,
//   "c" => 0
// ]);

// echo json_encode($results);







// include 'similarity.php';

// // article= 기사 제목
// // tags = 값(백터값)

// $articles = array(
// 	array(
// 		"article" => "사용자1", 
// 		"tags" => array("C", "D", "E", "F", "G") 
// 	),
// 	array(
// 		"article" => "사용자2",  
// 		"tags" => array("A", "D") 
// 	),
// 	array(
// 		"article" => "사용자3", 
// 		"tags" => array("E", "F") 
// 	)
// );

// $dot = Similarity::dot(call_user_func_array("array_merge", array_column($articles, "tags")));
// // A B C D E F G H I J K L
// $target = array('C', 'D');

// echo "compare two one-hot encoding vector<br><br>";

// echo "example articles:<br>";
// foreach($articles as $index => $value) {
//   echo $value['article'].": ";
//   foreach($value['tags'] as $tagsValue){
//     echo $tagsValue. ",";
//   }
//   echo "<br><br>";
// }

// echo "사용자4:<br>";
// foreach($target as $index => $value) {
//   echo $value. ", ";
// }
// echo "<br><br>";

// foreach($articles as $article) {
// 	$score[$article['article']] = Similarity::cosine($target, $article['tags'], $dot);
// }
// arsort($score);

// echo "유사도 결과:";
// echo "<br>";
// foreach($score as $index => $value) {
//   echo $index.": ". $value. "<br>";
// }
// echo "<br><br>";

?>