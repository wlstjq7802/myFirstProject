<?php
include 'similarity.php';
include_once "dbcon.php";
// article= 기사 제목
// tags = 값(백터값)

    header('Content-Type: application/json; charset=UTF-8');

    $userId = "wlstjq15";
    $articles = array();


    $bookmarkSql = "SELECT * FROM bookmark";
    $bookmarkResult = mq($bookmarkSql);

    $likeRecipeSql = "SELECT * FROM userInfo";
    $likeRecipeResult = mq($likeRecipeSql);
    // $likeRecipeResult = mysqli_fetch_array($likeRecipeResult);

    while ($row = mysqli_fetch_assoc($bookmarkResult)) {
        if($row['userId'] != $userId){
          if(count(json_decode($row['bookmarkRecipeNum']))>0){
            $arr = array(
              "article" => $row['userId'],
              "tags" => json_decode($row['bookmarkRecipeNum'])
            );
            array_push($articles, $arr);
          }
        } else{
          $target = json_decode($row['bookmarkRecipeNum']);
        }
    }
    
    while ($row = mysqli_fetch_assoc($likeRecipeResult)) {
        if($row['id'] != $userId){
          if(count(json_decode($row['likeRecipeNum']))>0){
            for($i=0; $i < count($articles); $i++){
              
              if($articles[$i]['article'] == $row['id']){
                $bookmarkArr = $articles[$i]['tags'];
                $likeRecipeArr = json_decode($row['likeRecipeNum']);
                
                $recipeArr = array_merge($bookmarkArr, $likeRecipeArr);
                $recipeArr = array_unique($recipeArr);
                $recipeArr = array_values($recipeArr);
                $articles[$i]['tags'] = $recipeArr;
                break;
              } else if($i == count($articles) - 1){
                $arr = array(
                  "article" => $row['id'],
                  "tags" => json_decode($row['likeRecipeNum'])
                );
                array_push($articles, $arr);
                break;
              }
            }
          }
        } else if($row['id'] == $userId){
          $target = array_merge($target, json_decode($row['likeRecipeNum']));
          $target = array_unique($target);
          $target = array_values($target);
        }
    }


$dot = Similarity::dot(call_user_func_array("array_merge", array_column($articles, "tags")));




echo "사용자별 좋아요, 스크랩 목록:\n";
foreach($articles as $index => $value) {
  echo $value['article'].": ";
  foreach($value['tags'] as $tagsValue){
    echo $tagsValue. ",";
  }
  echo "\n\n\n";
}

echo "추천 받는 사용자(wlstjq15):\n";
foreach($target as $index => $value) {
  echo $value. ", ";
}
echo "\n\n";

foreach($articles as $article) {
	$score[$article['article']] = Similarity::cosine($target, $article['tags'], $dot);
}
arsort($score);

echo "유사도 결과:";
echo "\n";
foreach($score as $index => $value) {
  echo $index.": ". $value. "\n";
}
echo "\n\n";

// 키값만 배열에 입력
$SimilarityUser = array_keys($score);

// 가장 유사한 사용자 id인 $score의 0번째 index값을 받아서 
// $articles에 article이  가장 유사한 사용자 id인 배열의 tags를 불러온다.
// $SimilarityUser = $SimilarityUser[0];
$recomRecipeNum = array();
// for($i=0; $i<count($SimilarityUser); $i++){
  for($j=0; $j<count($articles); $j++){
    if($SimilarityUser[0] == $articles[$j]['article']){
      // $recomRecipeNum = $recomRecipeNum;
      $recomRecipeNum = array_merge($recomRecipeNum, $articles[$j]['tags']);
      $recomRecipeNum = array_unique($recomRecipeNum);
      $recomRecipeNum = array_values($recomRecipeNum);
      $recomRecipeNum = array_diff($recomRecipeNum, $target);
      break;
    }
  }
//   if(count($recomRecipeNum) >= 10){
//     break;
//   }
  
// }

// 유사한 사용자의 레시피 목록에서 추천받는 사용자에게 없는 레시피 번호만 추출한다.
echo "추천 레시피: \n";

$recomRecipeNum = array_values($recomRecipeNum);
echo json_encode($recomRecipeNum);
echo "\n\n\n\n";

$output = array();
for($i = 0; $i < count($recomRecipeNum); $i++){
            
  // 내가본 레시피 목록에 있는 레시피 조회
  $sql = "SELECT * FROM recipe WHERE recipeNum = '$recomRecipeNum[$i]'";
  $result = mq($sql);

  if($result){
      $row = mysqli_fetch_array($result);

      // 작성자의 닉네임과 프로필 사진 받아오기
      $sql2 = "SELECT * FROM userInfo where id = '$row[writer]'";
      $userDataResult = mq($sql2);
      $userData = mysqli_fetch_array($userDataResult);

      // 요리 완성 이미지 중 첫번째 이미지 불러오기
      $cookingImg = json_decode($row['representativeImg']);

      array_push($output,
          // output에 넣을 데이터 예시
          array(
              'recipeNum' => $row['recipeNum'],
              'recipeName' => $row['recipeName'],
              'writerNick' => $userData['nick'],
              'recipeImg' => $cookingImg[0],
              'writerId' => $userData['id'],
              'reportDate' => $row['reportDate'],
              'writerProfile' => $userData['profile_img']
          )
      );

  }
}

  echo json_encode($output);

?>