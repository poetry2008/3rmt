<?php

function calc($out,$in,$ship = 40){
  echo $out.'/'.$in."=";
  //echo (($out * (1200 + 1688)) - (1200*$in) - (1800 + 500) - (($in - $out) * 200));
  echo 1200*$in;
  echo '/';
  echo (($out * (1200 + 1648)) - (1200*$in) - (1800 + 500) + (($in - $out) * (1200 - 40)));
  echo "<br>\n";
}
calc(0,3);
calc(1,3);
calc(2,3);
calc(3,3);

calc(0,5);
calc(1,5);
calc(2,5);
calc(3,5);
calc(4,5);
calc(5,5);

calc(0,8);
calc(1,8);
calc(2,8);
calc(3,8);
calc(4,8);
calc(5,8);
calc(6,8);
calc(7,8);
calc(8,8);

calc(0,10);
calc(1,10);
calc(2,10);
calc(3,10);
calc(4,10);
calc(5,10);
calc(6,10);
calc(7,10);
calc(8,10);
calc(9,10);
calc(10,10);