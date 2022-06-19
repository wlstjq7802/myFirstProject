<?php
include_once "dbcon.php";

$msgCntSql = "SELECT @@global.time_zone, @@session.time_zone";
$msgCntResult = mq($msgCntSql);
$array = mysqli_fetch_array($msgCntResult);

echo $array[0];
echo $array[1];
echo $array[2];
echo $array[3];
echo $array[4];
echo $array[5];
echo $array[6];
echo $array[7];

// echo "ff";

?>
