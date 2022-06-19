<?php

// require_once '/var/www/html/ex5.php'; 
// require_once 'ex2.php';
// //namespace test 
// echo "en : ".language\en\welcome(); 
// //Hello world! 
// echo "<br>"; 
// echo "kr : ".language\kr\welcome(); 
// //안녕 세계! 
// echo "<br>"; 
//use test 
use language\kr as kr;
use language\en as en; 

echo kr\welcome();
echo "<br>";
echo en\welcome(); //안녕 세계!








?>