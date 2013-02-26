<?php
require('includes/application_top.php');
if($_GET['action'] == 'ip_unlock'){
  //解锁ip
  $ip = $_POST['ip'];
  $user = $_POST['user'];
  if(trim($user) != ''){
    
    $user_login_query = tep_db_query("update login set status='1' where account='".$user."' and address='".$ip."' and time_format(timediff(now(),logintime),'%H')<24");
  }else{
    $user_login_query = tep_db_query("update login set status='1' where address='".$ip."' and time_format(timediff(now(),logintime),'%H')<24"); 
  }
  if($user_login_query){
    echo 'success';  
  }
}
?>
