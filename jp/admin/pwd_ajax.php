<?php
//header("Content-Type: text/xml");
require('includes/application_top.php');

if(isset($_POST['pattern'])&&$_POST['pattern']&&
isset($_POST['pwd_len'])&&$_POST['pwd_len']){
  echo tep_get_new_random($_POST['pattern'],$_POST['pwd_len']);
}else{
  echo '';
}
?>
