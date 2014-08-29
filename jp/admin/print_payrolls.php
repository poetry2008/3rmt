<?php 
/*
 * print payrolls
 */
require('includes/application_top.php');
include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_PAYROLLS);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; 
charset=<?php echo CHARSET; ?>">
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link media="print" rel="stylesheet" type="text/css" href="includes/print_assets.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript">
  window.print();
</script>
<title></title>
</head>
<body>
<?php
if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']&&false){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php 
}
$group_id = $_GET['group_id'];
$payroll_date = $_GET['save_date'];
$date = tep_start_end_date($group_id,$payroll_date);
?>
<div style="text-align:center;"><?php echo $date['start_date'].'～'.$date['end_date'].'&nbsp;'.TEXT_HEAD_TITLE;?></div>
<br>
<table width="100%" cellpadding="0" cellspacing="0" border="1">
<tr>
<td align="center" bgcolor="#808080"><?php echo TEXT_PAYROLLS_NAME;?></td>
<?php
  $payroll_title = $_GET['payroll_title'];
  $payroll_title_array = explode('|',$payroll_title);
  $payroll_title_array = array_unique($payroll_title_array);
  $payroll_title_array = array_filter($payroll_title_array);
  $payroll_title_lenght = count($payroll_title_array);
  foreach($payroll_title_array as $title_value){
?>
  <td align="center" bgcolor="#808080"><?php echo $title_value;?></td>    
<?php
  }
  $user_id = $_GET['user_id'];
  $user_id_array = explode('|',$user_id);
  $user_id_array = array_unique($user_id_array);
  $user_id_array = array_filter($user_id_array);

  $user_payroll = $_GET['user_payroll'];
  $user_payroll_array = explode('|',$user_payroll);
  $user_payroll_lenght = count($user_payroll_array);

  $user_num = $_GET['user_num'];
  $user_num_array = explode('|',$user_num);
  $user_num_array = array_unique($user_num_array);
  $user_num_array = array_filter($user_num_array);
  $total_array = array();
  foreach($user_id_array as $user_key=>$user_value){
    $user_info = tep_get_user_info($user_value);
?>
</tr>
          <tr>
          <td align="right"><?php echo $user_info['name'];?></td>
<?php
    for($j=$user_num_array[$user_key]*$payroll_title_lenght;$j<=$user_num_array[$user_key]*$payroll_title_lenght+$payroll_title_lenght-1;$j++){

      echo '<td align="right">'.$user_payroll_array[$j].'</td>';
      $total_array[$j%$payroll_title_lenght] += $user_payroll_array[$j]; 
    }
?>
          </tr>
<?php
  }
?>
  <tr><td align="right"><?php echo TEXT_PAYROLLS_TOTAL.'('.$_GET['currency_type'].')';?></td>
<?php
  foreach($total_array as $total_value){

    echo '<td align="right">'.$total_value.'</td>';
  }
?>
</tr>
</table>
</body>
</html>
