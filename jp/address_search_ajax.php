<?php

/*
 * 邮编号码 Ajax
 */
require('includes/application_top.php');

$code = tep_db_prepare_input($_POST['code']);

$code_query = tep_db_query("select * from zcode where zipcode='". $code ."'");
$code_array = tep_db_fetch_array($code_query);
$num = tep_db_num_rows($code_query);
tep_db_free_result($code_query);
if($num == 1){

  echo $code_array['yc1'] .','. $code_array['yc2'] .','. $code_array['yc3'];
}else{
  echo '';
}
?>
