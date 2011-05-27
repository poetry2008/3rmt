<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require_once('oa/HM_Form.php'); 
  require_once('oa/HM_Group.php'); 
  

  $order_id = $_GET['oID'];
//  $order_id = '20110523-11420425';
   ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html dir="ltr" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script type="text/javascript" src="includes/javascript/jquery.js"></script>
</head>
<body>
<?php
  $formtype = tep_check_order_type($order_id);
  $formtype = '2';
  $payment_romaji = tep_get_payment_code_by_order_id($order_id); 
  $payment_romaji = 'buying'; 
  $oa_form_sql = "select * from ".TABLE_OA_FORM." where formtype = '".$formtype."' and payment_romaji = '".$payment_romaji."'";
  $form = tep_db_fetch_object(tep_db_query($oa_form_sql), "HM_Form");
  
  $form->loadOrderValue($order_id);
  $form->setAction('oa_answer_process.php?oID='.$order_id);
  $form->render();
?>
</body>
</html>