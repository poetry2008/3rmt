<?php
//header("Content-Type: text/xml");
require('includes/application_top.php');
$action = $_POST['action'];
switch($action){
  case 'make_pw':
if(isset($_POST['pattern'])&&$_POST['pattern']&&
isset($_POST['pwd_len'])&&$_POST['pwd_len']){
  echo tep_get_new_random($_POST['pattern'],$_POST['pwd_len']);
}else{
  echo '';
}
break;
case 'load';
if(isset($_POST['idpw'])&&$_POST['idpw']&&
isset($_POST['from'])&&$_POST['from']){
  echo tep_get_pwm_info($_POST['idpw'],$_POST['from']);
}else{
  echo '';
}
break;
}
?>
