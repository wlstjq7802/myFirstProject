<?php
    include_once "dbcon.php";
    // $arr = array();
    // array_push($arr, 100);
    // array_push($arr, 200);
    // array_push($arr, 300);

    // $arr2 = array();
    // array_push($arr2, 300);
    // array_push($arr2, 500);
    // array_push($arr2, 600);
    
    // $output = array_merge($arr, $arr2);
    // $output = array_unique($output);
    // $output = array_values($output);

    // $output = json_encode($output);
    // echo $output;




    // function arr_del($list_arr, $del_value) // 배열, 삭제할 값
    // {
    //     $b = array_search($del_value,$list_arr); 
    //     if($b!==FALSE) unset($list_arr[$b]); 
    //     return $list_arr;
    // }


    $arrtmp1 = ['apple', 'orange', 'melon', 'banana', 'pineapple'];
    $arrtmp2 = ['apple', 'orange', 'melon', 'grape'];

    //array_diff함수를 사용 배열을 비교
    $arrtmp_diff = array_diff($arrtmp1, $arrtmp2);

    echo json_encode($arrtmp_diff);

?>
