<?php
//header("Content-Type: text/xml");
require('includes/application_top.php');
$action = $_POST['action'];
switch($action){
/* -----------------------------------------------------
   case 'make_pw' 生成随机密码    
   case 'load' 取得idpw的指定字段的值   
------------------------------------------------------*/
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
