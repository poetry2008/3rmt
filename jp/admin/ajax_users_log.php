<?php
require('includes/application_top.php');
if($_GET['action'] == 'ip_unlock'){
  //解锁ip
  $ip = $_POST['ip'];
  $user = $_POST['user'];
  if(trim($user) != ''){
    $user_login_query = tep_db_query("update login set status='1',is_locked = '0' where account='".$user."' and address='".$ip."'");
  }else{
    $user_login_query = tep_db_query("update login set status='1',is_locked = '0' where address='".$ip."'"); 
  }
  if($user_login_query){
    echo 'success';  
  }
}else if($_GET['action'] == 'ip_lock'){
  $ip = $_POST['ip'];
  $user = $_POST['user'];
  if(trim($user) != ''){
    $user_login_query = tep_db_query("update login set status='0',is_locked = '1' where account='".$user."' and address='".$ip."'");
  }
  if($user_login_query){
    echo 'success';  
  }
}
?>
