<?php

$sum = 9000;

$array = array(20,40,60,80,100);

$num_array = array();

$i = 1;
foreach($array as $key=>$value){

  if(($sum+$value-array_sum($array))%$value == 0){
    $num_array[] = ($sum+$value-array_sum($array))/$value;
  }else{
    $num_array[] = ($sum+$value-array_sum($array)-(($sum+$value-array_sum($array))%$value))/$value; 
  }
}
/*
$rand_num = rand(1,88);
$rand_x = rand(1,5);

$array_y = array_rand($array,$rand_x);

if(((88-$rand_num)*100)%200 == 0){

  $array_z = ((88-$rand_num)*100)/200;
}

foreach($array_y as $key=>$value){

  switch($value){
  case 0:
    
  }
}
 */

$array_temp = array();

sort($num_array);

foreach($num_array as $key=>$value){

  $temp = $value-rand(1,$value);  
}

print_r($num_array);
?>
