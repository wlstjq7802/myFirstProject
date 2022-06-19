<?php
    include 'similarity.php';
    include_once "dbcon.php";
    header('Content-Type: application/json; charset=UTF-8');


    $userId = $_POST['userId'];
    $articles = array();

    $bookmarkSql = "SELECT * FROM bookmark";
    $bookmarkResult = mq($bookmarkSql);

    $likeRecipeSql = "SELECT * FROM userInfo";
    $likeRecipeResult = mq($likeRecipeSql);


    // 추천받는 사용자의 데이터는 $target에 다른 사용자는 $articles에 저장
    
    // 1.사용자들의 북마크 데이터(사용자 id, 북마크한 레시피 번호)를 저장한다. 
    // 2.사용자들의 좋아요 데이터(사용자 id, 좋아요한 레시피 번호)를 저장한다.
    // 3.추천받는 사용자와 다른 사용자들의 유사도를 구한다.
    // 4.가장 유사한 사용자의 좋아요/북마크한 레시피 번호 중 추천받는 사용자가 좋아요/북마크하지 않은
    //   레시피 번호를 배열에 저장한다.
    // 5.해당 배열에 있는 레시피 데이터를 클라이언트로 전송한다.


    // 1.북마크한 사용자들의 데이터(사용자 id, 북마크한 레시피 번호)를 저장한다. 
    // 북마크한 사용자들의 데이터를 조회한다.
    // 조회한 데이터에서 userId가 추천받는 사용자인 경우 $target 배열, 아닌 경우 $arr 배열에 입력한다.
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
    
    // 2.좋아요한 사용자들의 데이터(사용자 id, 북마크한 레시피 번호)를 저장한다.
    while ($row = mysqli_fetch_assoc($likeRecipeResult)) {
        if($row['id'] != $userId){
          if(count(json_decode($row['likeRecipeNum']))>0){
            for($i=0; $i < count($articles); $i++){
              
              if($articles[$i]['article'] == $row['id']){
                $bookmarkArr = $articles[$i]['tags'];  // 각 사용자가 북마크한 레시피번호 배멸
                $likeRecipeArr = json_decode($row['likeRecipeNum']); // 각 사용자가 좋아요한 레시피번호 배열
                
                $recipeArr = array_merge($bookmarkArr, $likeRecipeArr); // 배열을 합치는 기능
                $recipeArr = array_unique($recipeArr);  // 배열에서 중복값을 제거하는 기능
                $recipeArr = array_values($recipeArr);  // 연관배열을 인덱스 배열로 변환
                $articles[$i]['tags'] = $recipeArr;     // 다시 $articles의 tags 키값에 저장한다.
                break;
              } else if($i == count($articles) - 1){    // 북마크를 하지않은 사용자 예외처리
                $arr = array(
                  "article" => $row['id'],
                  "tags" => json_decode($row['likeRecipeNum'])
                );
                array_push($articles, $arr);
                break;
              }
            }
          }
        } else if($row['id'] == $userId){       // 추천 받는 사용자의 경우 tartget에 저장한다.
          $target = array_merge($target, json_decode($row['likeRecipeNum']));
          $target = array_unique($target);
          $target = array_values($target);
        }
    }


// 3.추천받는 사용자와 다른 사용자들의 유사도를 구한다.
$dot = Similarity::dot(call_user_func_array("array_merge", array_column($articles, "tags")));

foreach($articles as $article) {
	$score[$article['article']] = Similarity::cosine($target, $article['tags'], $dot);
}
arsort($score);



// 키값만 배열에 입력
$SimilarityUser = array_keys($score);


// 4.유사도가 가장 높은 사용자의 좋아요/북마크한 레시피 번호 중 추천받는 사용자가 좋아요/북마크한 레시피 번호에 없는
//   레시피 번호를 배열에 저장한다.
// 가장 유사한 사용자 id인 $score의 0번째 index값을 받아서 
// $articles의 article key의 값이 가장 유사한 사용자 id인 배열의 tags(좋아요/북마크한 레시피 번호)를 불러온다.
$recomRecipeNum = array();
  for($j=0; $j<count($articles); $j++){
    if($SimilarityUser[0] == $articles[$j]['article']){
      // $recomRecipeNum = array_merge($recomRecipeNum, $articles[$j]['tags']);
      // $recomRecipeNum = array_unique($recomRecipeNum);
      // $recomRecipeNum = array_values($recomRecipeNum);

      // 유사한 사용자의 레시피 목록에서 추천받는 사용자에게 없는 레시피 번호만 추출한다.
      // array_diff(A, B) A 배열의 값중 B 배열에 없는 값을 반환한다.
      $recomRecipeNum = array_diff($articles[$j]['tags'], $target); 
      
      break;
    }
  }

$recomRecipeNum = array_values($recomRecipeNum);



// 5.해당 배열에 있는 레시피 데이터를 클라이언트로 전송한다.
// 레시피 데이터 조회 및 배열에 저장
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

      // 북마크 상태 확인 
      $sql3 = "SELECT * FROM bookmark where userId = '$userId'";
      $likeCountResult = mq($sql3);
      $bookmark = mysqli_fetch_array($likeCountResult);
      $bookmark = json_decode($bookmark['bookmarkRecipeNum']);
      $bookmarkCount = count($bookmark);

      $bookmarkChk = false;
      for($k = 0; $k < $bookmarkCount; $k++){
          // 북마크 상태 확인
          if($bookmark[$k] == $row['recipeNum']){
              $bookmarkChk = true;
              break;
          }
      }

      // 좋아요 상태 확인
      $sql3 = "SELECT * FROM likeDB where recipeNum = '$row[recipeNum]'";
      $likeCountResult = mq($sql3);
      $like = mysqli_fetch_array($likeCountResult);
      $like = json_decode($like['userId'], true);
      $likeChk = false;

      // 좋아요 상태 적용
      if($like[$userId] == "true"){
          $likeChk = true;
      }

      // 요리 완성 이미지 중 첫번째 이미지 불러오기
      $cookingImg = json_decode($row['representativeImg']);
      

      array_push($output,
          // output에 넣을 데이터 예시
          array(
              'recipeNum' => $row['recipeNum'],
              'recipeName' => $row['recipeName'],
              'writer' => $userData['nick'],
              'recipeImg' => $cookingImg[0],
              'cookingTime' => $row['cookingTime'],
              'number' => $row['number'],
              'level' => $row['level'],
              'writerProfile' => $userData['profile_img'],
              'writerId' => $userData['id'],
              'reportDate' => $row['reportDate'],
              'bookmarkCheck' => $bookmarkChk,
              'likeCheck' => $likeChk,
              'likeCount' => $row['likeCount']
          )
      );

  }
}

  echo json_encode($output);

?>