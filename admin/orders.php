<?php
/*
   $Id$
*/
  require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');

  $currencies          = new currencies();
  $orders_statuses     = array();
  $orders_status_array = array();
  $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");
  while ($orders_status = tep_db_fetch_array($orders_status_query)) {
    $orders_statuses[] = array('id' => $orders_status['orders_status_id'],
       'text' => $orders_status['orders_status_name']);
    $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
  }

  if (isset($_GET['action'])) 
  switch ($_GET['action']) {
    //一括変更----------------------------------
  case 'sele_act':
    if($_POST['chk'] == ""){
      $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
    tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))));
    }else{
      foreach($_POST['chk'] as $value){
      $oID      = $value;
      $status   = tep_db_prepare_input($_POST['status']);
      $title    = tep_db_prepare_input($_POST['os_title']);
      $comments = tep_db_prepare_input($_POST['comments']);
    
      $order_updated = false;
    $check_status_query = tep_db_query("select customers_name, customers_email_address, orders_status, date_purchased, payment_method, torihiki_date from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
    $check_status = tep_db_fetch_array($check_status_query);
      
      //Add Point System
        if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && MODULE_ORDER_TOTAL_POINT_ADD_STATUS != '0') {
      $pcount_query = tep_db_query("select count(*) as cnt from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".MODULE_ORDER_TOTAL_POINT_ADD_STATUS."' and orders_id = '".$oID."'");
        $pcount = tep_db_fetch_array($pcount_query);
        if($pcount['cnt'] == 0 && $status == MODULE_ORDER_TOTAL_POINT_ADD_STATUS) {
      $query1 = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '".$oID."'");
          $result1 = tep_db_fetch_array($query1);
          $query2 = tep_db_query("select value from " . TABLE_ORDERS_TOTAL." where class = 'ot_point' and orders_id = '".tep_db_input($oID)."'");
          $result2 = tep_db_fetch_array($query2);
          $query3 = tep_db_query("select value from " . TABLE_ORDERS_TOTAL." where class = 'ot_subtotal' and orders_id = '".tep_db_input($oID)."'");
          $result3 = tep_db_fetch_array($query3);
          $query4 = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '".$result1['customers_id']."'");
          $result4 = tep_db_fetch_array($query4);
      
        // ここからカスタマーレベルに応じたポイント還元率算出============================================================
        if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
          $customer_id = $result1['customers_id'];
          //設定した期間内の注文合計金額を算出------------
          $ptoday = date("Y-m-d H:i:s", time());
          $pstday_array = getdate();
          $pstday = date("Y-m-d H:i:s", mktime($pstday_array[hours],$pstday_array[mimutes],$pstday_array[second],$pstday_array[mon],($pstday_array[mday] - MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN),$pstday_array[year]));
      
          $total_buyed_date = 0;
          $customer_level_total_query = tep_db_query("select * from orders where customers_id = '".$customer_id."' and date_purchased >= '".$pstday."'");
          if(tep_db_num_rows($customer_level_total_query)) {
          while($customer_level_total = tep_db_fetch_array($customer_level_total_query)) {
            $cltotal_subtotal_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_subtotal'");
            $cltotal_subtotal = tep_db_fetch_array($cltotal_subtotal_query);
      
            $cltotal_point_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_point'");
            $cltotal_point = tep_db_fetch_array($cltotal_subtotal_query);
       
            $total_buyed_date += ($cltotal_subtotal['value'] - $cltotal_point['value']);
          }
          }
          //----------------------------------------------
          //今回の注文額は除外
          $total_buyed_date = $total_buyed_date - ($result3['value'] - (int)$result2['value']);
      
          //還元率を計算----------------------------------
          if(mb_ereg("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK)) {
          $back_rate_array = explode("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
          $back_rate = MODULE_ORDER_TOTAL_POINT_FEE;
          for($j=0; $j<sizeof($back_rate_array); $j++) {
            $back_rate_array2 = explode(",", $back_rate_array[$j]);
            if($back_rate_array2[2] <= $total_buyed_date) {
            $back_rate = $back_rate_array2[1];
            $back_rate_name = $back_rate_array2[0];
            }
          }
          } else {
          $back_rate_array = explode(",", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
          if($back_rate_array[2] <= $total_buyed_date) {
            $back_rate = $back_rate_array[1];
            $back_rate_name = $back_rate_array[0];
          }
          }
          //----------------------------------------------
          $point_rate = $back_rate;
        } else {
          $point_rate = MODULE_ORDER_TOTAL_POINT_FEE;
        }
        // ここまでカスタマーレベルに応じたポイント還元率算出============================================================
      
          $get_point = ($result3['value'] - (int)$result2['value']) * $point_rate;
      
          $plus = $result4['point'] + $get_point;
      
          tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " . $get_point . " where customers_id = " . $result1['customers_id'] );
        }
        }   
    
    if ($check_status['orders_status'] != $status || $comments != '') {
      tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");

      $customer_notified = '0';
      
      if ($_POST['notify'] == 'on') {
  
        $ot_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_total'");
        $ot_result = tep_db_fetch_array($ot_query);
        $otm = (int)$ot_result['value'] . '円';
        
        $os_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
        $os_result = tep_db_fetch_array($os_query);
  
        $comments = str_replace(array('${NAME}','${MAIL}','${ORDER_D}','${ORDER_N}','${PAY}','${ORDER_M}','${TRADING}','${ORDER_S}'),array($check_status['customers_name'],$check_status['customers_email_address'],tep_date_long($check_status['date_purchased']),$oID,$check_status['payment_method'],$otm,tep_torihiki($check_status['torihiki_date']),$os_result['orders_status_name']),$comments);
        tep_mail($check_status['customers_name'], $check_status['customers_email_address'], $title, $comments, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        tep_mail(STORE_OWNER, SENTMAIL_ADDRESS, '送信済：'.$title, $comments, $check_status['customers_name'], $check_status['customers_email_address']);
        $customer_notified = '1';
      }
      
        
      if($_POST['notify_comments'] == 'on') {
        $customer_notified = '1';
      } else {
        $customer_notified = '0';
      }
      tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), '" . $customer_notified . "', '')");

      $order_updated = true;
    }

    if ($order_updated) {
      $messageStack->add_session('注文ID' . $oID . 'の' . SUCCESS_ORDER_UPDATED, 'success');
    } else {
      $messageStack->add_session('注文ID' . $oID . 'の' . WARNING_ORDER_NOT_UPDATED, 'warning');
    }
    }

      tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))));
    }
    
    break;
    //------------------------------------------
  case 'update_order':
      $oID      = tep_db_prepare_input($_GET['oID']);
      $status   = tep_db_prepare_input($_POST['status']);
      $title    = tep_db_prepare_input($_POST['title']);
      $comments = tep_db_prepare_input($_POST['comments']);

      $order_updated = false;
      $check_status_query = tep_db_query("
          select orders_id, 
                 customers_name, 
                 customers_email_address, 
                 orders_status, 
                 date_purchased, 
                 payment_method, 
                 torihiki_date 
          from " . TABLE_ORDERS . " 
          where orders_id = '" . tep_db_input($oID) . "'");
      $check_status = tep_db_fetch_array($check_status_query);
    
    //Add Point System
    if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && MODULE_ORDER_TOTAL_POINT_ADD_STATUS != '0') {
      $pcount_query = tep_db_query("select count(*) as cnt from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".MODULE_ORDER_TOTAL_POINT_ADD_STATUS."' and orders_id = '".$oID."'");
    $pcount = tep_db_fetch_array($pcount_query);
    if($pcount['cnt'] == 0 && $status == MODULE_ORDER_TOTAL_POINT_ADD_STATUS) {
        $query1 = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '".$oID."'");
      $result1 = tep_db_fetch_array($query1);
      $query2 = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_point' and orders_id = '".tep_db_input($oID)."'");
      $result2 = tep_db_fetch_array($query2);
      $query3 = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_subtotal' and orders_id = '".tep_db_input($oID)."'");
      $result3 = tep_db_fetch_array($query3);
      $query4 = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '".$result1['customers_id']."'");
      $result4 = tep_db_fetch_array($query4);
      
    // ここからカスタマーレベルに応じたポイント還元率算出============================================================
    if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
      $customer_id = $result1['customers_id'];
      //設定した期間内の注文合計金額を算出------------
      $ptoday = date("Y-m-d H:i:s", time());
      $pstday_array = getdate();
      $pstday = date("Y-m-d H:i:s", mktime($pstday_array[hours],$pstday_array[mimutes],$pstday_array[second],$pstday_array[mon],($pstday_array[mday] - MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN),$pstday_array[year]));
      
      $total_buyed_date = 0;
      $customer_level_total_query = tep_db_query("select * from orders where customers_id = '".$customer_id."' and date_purchased >= '".$pstday."'");
      if(tep_db_num_rows($customer_level_total_query)) {
      while($customer_level_total = tep_db_fetch_array($customer_level_total_query)) {
        $cltotal_subtotal_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_subtotal'");
        $cltotal_subtotal = tep_db_fetch_array($cltotal_subtotal_query);
      
        $cltotal_point_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_point'");
        $cltotal_point = tep_db_fetch_array($cltotal_subtotal_query);
       
        $total_buyed_date += ($cltotal_subtotal['value'] - $cltotal_point['value']);
      }
      }
      //----------------------------------------------
      //今回の注文額は除外
      $total_buyed_date = $total_buyed_date - ($result3['value'] - (int)$result2['value']);
      
      //還元率を計算----------------------------------
      if(mb_ereg("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK)) {
      $back_rate_array = explode("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
      $back_rate = MODULE_ORDER_TOTAL_POINT_FEE;
      for($j=0; $j<sizeof($back_rate_array); $j++) {
        $back_rate_array2 = explode(",", $back_rate_array[$j]);
        if($back_rate_array2[2] <= $total_buyed_date) {
        $back_rate = $back_rate_array2[1];
        $back_rate_name = $back_rate_array2[0];
        }
      }
      } else {
      $back_rate_array = explode(",", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
      if($back_rate_array[2] <= $total_buyed_date) {
        $back_rate = $back_rate_array[1];
        $back_rate_name = $back_rate_array[0];
      }
      }
      //----------------------------------------------
      $point_rate = $back_rate;
    } else {
      $point_rate = MODULE_ORDER_TOTAL_POINT_FEE;
    }
    // ここまでカスタマーレベルに応じたポイント還元率算出============================================================
      
      $get_point = ($result3['value'] - (int)$result2['value']) * $point_rate;
      
      $plus = $result4['point'] + $get_point;
      
      tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " . $get_point . " where customers_id = " . $result1['customers_id'] );
    }
    }   
    
      if ($check_status['orders_status'] != $status || $comments != '') {
    tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");

    $customer_notified = '0';
    
    if ($_POST['notify'] == 'on') {

      $ot_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_total'");
      $ot_result = tep_db_fetch_array($ot_query);
      $otm = (int)$ot_result['value'] . '円';
      
      $os_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
      $os_result = tep_db_fetch_array($os_query);

      $comments = str_replace(array('${NAME}','${MAIL}','${ORDER_D}','${ORDER_N}','${PAY}','${ORDER_M}','${TRADING}','${ORDER_S}'),array($check_status['customers_name'],$check_status['customers_email_address'],tep_date_long($check_status['date_purchased']),$oID,$check_status['payment_method'],$otm,tep_torihiki($check_status['torihiki_date']),$os_result['orders_status_name']),$comments);
      tep_mail($check_status['customers_name'], $check_status['customers_email_address'], $title, $comments, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      tep_mail(STORE_OWNER, SENTMAIL_ADDRESS, '送信済：'.$title, $comments, $check_status['customers_name'], $check_status['customers_email_address']);
    $customer_notified = '1';
    }
    
      
    if($_POST['notify_comments'] == 'on') {
      $customer_notified = '1';
    } else {
      $customer_notified = '0';
    }
    tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), '" . $customer_notified . "', '')");

    $order_updated = true;
      }

      if ($order_updated) {
       $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
      } else {
    $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
      }

      tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=edit'));
      break;
    case 'deleteconfirm':
      $oID = tep_db_prepare_input($_GET['oID']);

      tep_remove_attributes($oID, $_POST['restock']);
    
    tep_remove_order($oID, $_POST['restock']);

      tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action'))));
      break;
  }

  if ( isset($_GET['action']) && ($_GET['action'] == 'edit') && ($_GET['oID']) ) {
    $oID = tep_db_prepare_input($_GET['oID']);

    $orders_query = tep_db_query("
        select orders_id 
        from " . TABLE_ORDERS . " 
        where orders_id = '" . tep_db_input($oID) . "'");
    $order_exists = true;
    if (!tep_db_num_rows($orders_query)) {
      $order_exists = false;
      $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
    }
  }

  include(DIR_WS_CLASSES . 'order.php');
  
  //------------------------------------------------
  $suu = 0;
  $text_suu = 0;  
  $__orders_status_query = tep_db_query("
      select orders_status_id 
      from " . TABLE_ORDERS_STATUS . " 
      where language_id = " . $languages_id . " 
      order by orders_status_id");
  $__orders_status_ids   = array();
  while($__orders_status = tep_db_fetch_array($__orders_status_query)){
    $__orders_status_ids[] = $__orders_status['orders_status_id'];
  }
  $select_query = tep_db_query("
      select distinct orders_status_mail,
                      orders_status_title,
                      orders_status_id 
      from ".TABLE_ORDERS_MAIL." 
      where language_id = " . $languages_id . " 
        and orders_status_id IN (".join(',', $__orders_status_ids).")");

  while($select_result = tep_db_fetch_array($select_query)){
    
  if($suu == 0){
    $select_select = $select_result['orders_status_id'];
    $suu = 1;
  }
  
  $osid = $select_result['orders_status_id'];
  
  if($text_suu == 0){
    $select_text = $select_result['orders_status_mail'];
    $select_title = $select_result['orders_status_title'];
    $text_suu = 1;
  }
  
  $mt[$osid] = $select_result['orders_status_mail'];
  $mo[$osid] = $select_result['orders_status_title'];
  }

  //------------------------------------------------
  if(isset($_GET['reload'])) {
    switch($_GET['reload']) {
    case 'Yes':
      $reload = 'yes';
    tep_session_register('reload');
    break;
    case 'No':
      $reload = 'no';
    tep_session_register('reload');
    break;
  } 
  } else {
    if(tep_session_is_registered('reload')) {
    //Read Session
  } else {
    $reload = 'yes';
  }
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/all_order.js"></script>
<script language="javascript">
  window.status_text = new Array();
  window.last_status = 0;

<?php 
  foreach ($mt as $key => $value){
  echo 'window.status_text['.$key.'] = "' . str_replace(array("\r\n","\r","\n"), array('\n', '\n', '\n'),$value) . '";' . "\n";
  }
?>

<?php
if($reload == 'yes') {
  if((int)DS_ADMIN_ORDER_RELOAD < 1) {
    $reloadcnt = '60';
  } else {
    $reloadcnt = DS_ADMIN_ORDER_RELOAD;
  }
?>
function auto_reload(){
  window.location.reload();
}

timerID = setInterval("auto_reload()",<?php echo (int)$reloadcnt * 1000; ?>); //1秒：1000
<?php }?>
function getCheckboxValue(ccName)
{
  var aa     =   document.getElementsByName(ccName);
  var values = new Array();
  for   (var   i=0;   i<aa.length;   i++){
    if(aa[i].checked){
      values[values.length] = aa[i].value;
    }
  }
  return values;
}
function mail_text(st,tt,ot){
  var idx = document.sele_act.elements[st].selectedIndex;
  var CI  = document.sele_act.elements[st].options[idx].value;
  chk = getCheckboxValue('chk[]');
  if((chk.length > 1 || chk.length < 1) && window.status_text[CI].indexOf('${ORDER_A}') != -1){
    if(chk.length > 1){
      alert('複数の選択はできません。');
    } else {
      alert('注文書はまだ選択していません。');
    }
    document.sele_act.elements[st].options[window.last_status].selected = true;
    return false;
  }
  window.last_status = idx;
  document.sele_act.elements[tt].value = window.status_text[CI].replace('${ORDER_A}', window.orderStr[chk[0]]);

  switch(CI){
    <?php
    foreach ($mo as $key => $value){
    echo "case '" . $key . "':" . "\n";
    echo 'document.sele_act.elements[ot].value = "' . str_replace(array("\r\n","\r","\n"), array('\n', '\n', '\n'),$value) . '";' . "\n";
    echo 'break;' . "\n";
    }
  ?>
  } 
}

</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<?php
  if ($ocertify->npermission == 15) {
    echo '<td width="' . BOX_WIDTH . '" valign="top">';
    echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
    echo '<!-- left_navigation //-->';
    require(DIR_WS_INCLUDES . 'column_left.php');
    echo '<!-- left_navigation_eof //-->';
    echo '</table>';
    echo '</td>';
  } else {
    echo '<td>&nbsp;</td>';
  }
?>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ( isset($_GET['action']) && ($_GET['action'] == 'edit') && ($order_exists) ) {
    $order = new order($oID);
?>
      <tr>
    <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
      <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
      <td class="pageHeading" align="right">
      <!---->
      <?php
  if ($ocertify->npermission) {
    ?>
      <?php echo '<a href="' . tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action','status')) . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>'; ?>
      <?php 
  }
?>
      <?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action','status'))) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
      <!---->
    </tr>
    </table></td>
      </tr>
      <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td colspan="3"><?php echo tep_draw_separator(); ?></td>
    </tr>   
    <tr>
      <td class="main" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
    <td class="main" valign="top" width="30%"><b>注文書サイト<?php //echo ENTRY_SITE;?></b></td> <td class="main" width="70%"><b style=" color:#FF0000"><?php echo tep_get_site_romaji_by_order_id($oID);?></b></td>
    </tr>
    <tr>
    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
    </tr>   
    <tr>
    <td class="main" valign="top" width="30%"><b>取引日時</b></td>
    <td class="main" width="70%"><b style=" color:#0000FF"><?php echo $order->tori['date'];?></b></td>
    </tr>
    <tr>
    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
    </tr>
    <tr>
    <td class="main" valign="top"><b>オプション</b></td>
    <td class="main"><b style=" color:#0000FF"><?php echo $order->tori['houhou'];?></b></td>
    </tr>
    <tr>
    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
    </tr>    <tr>
    <td class="main" valign="top"><b>ご注文番号</b></td>
    <td class="main"><?php echo $_GET['oID'] ?></td>
    </tr>
    <tr>
    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
    </tr>    <tr>
    <td class="main" valign="top"><b>注文日</b></td>
    <td class="main"><?php echo tep_date_long($order->customer['date']); ?></td>
    </tr>
    <tr>
    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
    </tr>    
        <tr>
        <td class="main" valign="top"><b><?php echo ENTRY_CUSTOMER; ?></b></td>
    <td class="main"><?php echo $order->customer['name']; ?></td>
    </tr>
    <tr>
    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
    </tr>
    <tr>
    <td class="main"><b><?php echo ENTRY_EMAIL_ADDRESS; ?></b></td>
    <td class="main"><?php echo '<a href="mailto:' . tep_output_string_protected($order->customer['email_address']) . '"><u>' . tep_output_string_protected($order->customer['email_address']) . '</u></a>'; ?></td>
    </tr>
      </table></td>
       </table></td>
      </tr>
      <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td class="main" valign="top" width="30%"><b><?php echo ENTRY_PAYMENT_METHOD; ?></b></td>
      <td class="main" width="70%"><?php echo $order->info['payment_method']; ?></td>
    </tr>
<?php
    if ( (($order->info['cc_type']) || ($order->info['cc_owner']) || ($order->info['cc_number'])) ) {
?>
    <tr>
      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
      <td class="main"><?php echo ENTRY_CREDIT_CARD_TYPE; ?></td>
      <td class="main"><?php echo $order->info['cc_type']; ?></td>
    </tr>
    <tr>
      <td class="main"><?php echo ENTRY_CREDIT_CARD_OWNER; ?></td>
      <td class="main"><?php echo $order->info['cc_owner']; ?></td>
    </tr>
    <tr>
      <td class="main"><?php echo ENTRY_CREDIT_CARD_NUMBER; ?></td>
      <td class="main"><?php echo $order->info['cc_number']; ?></td>
    </tr>
    <tr>
      <td class="main"><?php echo ENTRY_CREDIT_CARD_EXPIRES; ?></td>
      <td class="main"><?php echo $order->info['cc_expires']; ?></td>
    </tr>
<?php
    }
?>
    </table></td>
      </tr>
      <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr class="dataTableHeadingRow">
      <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CHARACTER; ?></td>
      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TAX; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></td>
    </tr>
<?php
    for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
      echo '    <tr class="dataTableRow">' . "\n" .
     '      <td class="dataTableContent" valign="top" align="right">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
     '      <td class="dataTableContent" valign="top">' . $order->products[$i]['name'];

      if (isset($order->products[$i]['attributes']) && $order->products[$i]['attributes'] && ($k = sizeof($order->products[$i]['attributes'])) > 0) {
    for ($j = 0; $j < $k; $j++) {
    echo '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
    if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')<br>';
      echo '</i></small></nobr>';
    }
      }

      if ( DISPLAY_PRICE_WITH_TAX == 'true' ) {
    $price_with_tax = $currencies->format(
    tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']),
    true,
    $order->info['currency'], $order->info['currency_value']);
    $tprice_with_tax = $currencies->format(
    tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'],
    true,
    $order->info['currency'],
    $order->info['currency_value']);
      } else {
    $price_with_tax = $tprice_with_tax = '---';
      }

      echo '      </td>' . "\n" .
     '      <td class="dataTableContent" valign="top">' . $order->products[$i]['character'] . '</td>' . "\n" .
       '      <td class="dataTableContent" valign="top">' . $order->products[$i]['model'] . '</td>' . "\n" .
     '      <td class="dataTableContent" align="right" valign="top">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n" .
     '      <td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" .
     '      <td class="dataTableContent" align="right" valign="top"><b>' . $price_with_tax . '</b></td>' . "\n" .
     '      <td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'],true,$order->info['currency'],$order->info['currency_value']) . '</b></td>' . "\n" .
     '      <td class="dataTableContent" align="right" valign="top"><b>' . $tprice_with_tax . '</b></td>' . "\n";
      echo '    </tr>' . "\n";
    }
?>
    <tr>
      <td align="right" colspan="9"><table border="0" cellspacing="0" cellpadding="2">
<?php
    for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {
      echo '    <tr>' . "\n" .
     '    <td align="right" class="smallText">' . $order->totals[$i]['title'] . '</td>' . "\n" .
     '    <td align="right" class="smallText">' . $order->totals[$i]['text'] . '</td>' . "\n" .
     '    </tr>' . "\n";
     if ($i == 0) {
        echo '    <tr>' . "\n" .
       '    <td align="right" class="smallText">' . TEXT_CODE_HANDLE_FEE . '</td>' . "\n" .
       '    <td align="right" class="smallText">' .  $currencies->format($order->info['code_fee']) . '</td>' . "\n" .
       '    </tr>' . "\n";
     }
    }
?>
        <tr>
          <td align="right" class="smallText">試験運用中<font color="red">（上記の数値と一致しているか確認するように）</font>買取コピペ用:</td>
<?php
  $warning_sell = '';
  $warning_sell = str_replace(array("," , "<b>" , "</b>" , "円") , array("" , "" , "" , "") , $order->totals[2]['text']);
?>
          <td align="right" class="smallText"><?php echo $warning_sell; ?></td>
        </tr>
<?php
  if ( $warning_sell < 5000 ) {
    echo '<tr><td align="right" colspan="2" class="smallText"><font color="blue">この注文は5,000円未満です。買取なら手数料168円引く</font></td></tr>';
  }
?>
      </table></td>
    </tr>
    </table></td>
      </tr>
      <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
    <td class="main"><table border="1" cellspacing="0" cellpadding="5">
    <tr>
      <td class="smallText" align="center"><b><?php echo TABLE_HEADING_DATE_ADDED; ?></b></td>
      <td class="smallText" align="center"><b><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></b></td>
      <td class="smallText" align="center"><b><?php echo TABLE_HEADING_STATUS; ?></b></td>
      <td class="smallText" align="center"><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>
    </tr>
<?php
    $orders_history_query = tep_db_query("select orders_status_id, date_added, customer_notified, comments from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($oID) . "' order by date_added");
    if (tep_db_num_rows($orders_history_query)) {
      while ($orders_history = tep_db_fetch_array($orders_history_query)) {
    echo '    <tr>' . "\n" .
       '      <td class="smallText" align="center">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .
       '      <td class="smallText" align="center">';
    if ($orders_history['customer_notified'] == '1') {
    echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK) . "</td>\n";
    } else {
    echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS) . "</td>\n";
    }
    echo '      <td class="smallText">' . $orders_status_array[$orders_history['orders_status_id']] . '</td>' . "\n" .
       '      <td class="smallText">' . nl2br(tep_db_output($orders_history['comments'])) . '&nbsp;</td>' . "\n" .
       '    </tr>' . "\n";
      }
    } else {
    echo '    <tr>' . "\n" .
       '      <td class="smallText" colspan="5">' . TEXT_NO_ORDER_HISTORY . '</td>' . "\n" .
       '    </tr>' . "\n";
    }
?>
    </table></td>
      </tr>
    <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
      </tr> 


      <?php 
  if ($ocertify->npermission == 15) {
  ?>
    <?php echo tep_draw_form('status', FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=update_order'); ?>
    <tr>
      <td class="main"><b><?php echo ENTRY_STATUS; ?></b>
    <!---->
    <select onChange="if(options[selectedIndex].value) window.location.href=(options[selectedIndex].value)">
    <?php
      $status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " order by orders_status_id ");
      while ($pull_status = tep_db_fetch_array($status_query)) {
      echo '<option value="' . tep_href_link('orders.php',tep_get_all_get_params(array('status')).'status='.$pull_status['orders_status_id']) . '"';
          
      if($_GET['status'] == ''){
        if($order->info['orders_status'] == $pull_status['orders_status_id']){
          echo 'selected' ;
        }
      }else{
        if($_GET['status'] == $pull_status['orders_status_id']) {
          echo 'selected' ;
        }
      }
      
      echo '>' . $pull_status['orders_status_name'] . '</option>'."\n";
      }
    ?>
    </select>
    <!---->
    </td>
      </tr>
    <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
      </tr>
    <?php
      $ma_se = "select * from ".TABLE_ORDERS_MAIL." where ";
      if(!isset($_GET['status']) || $_GET['status'] == ""){
        $ma_se .= " orders_status_id = '".$order->info['orders_status']."' ";
      echo '<input type="hidden" name="status" value="' .$order->info['orders_status'].'">';
      }else{
        $ma_se .= " orders_status_id = '".$_GET['status']."' ";
      echo '<input type="hidden" name="status" value="' .$_GET['status'].'">';
      }
      
      $mail_sele = tep_db_query($ma_se);
      $mail_sql  = tep_db_fetch_array($mail_sele);
      $sta       = isset($_GET['status'])?$_GET['status']:'';
    ?>
    <tr>
      <td class="main"><b><?php echo ENTRY_EMAIL_TITLE; ?></b><?php echo tep_draw_input_field('title', $mail_sql['orders_status_title']); ?></td>
    </tr>
    <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
      </tr>
      <tr>
    <td class="main">
      <b><?php echo TABLE_HEADING_COMMENTS; ?>:</b>自動的に改行して表示し、送信されるメールにも改行が入ります。
      <table><tr class="smalltext"><td><font color="red">※</font>&nbsp;コピペ用:</td><td>ただ今よりログインいたします。</td></tr></table>
    </td>
      </tr>
      <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
      </tr>
      <tr>
    <td class="main">
    <textarea style="font-family:monospace;font-size:x-small" name="comments" wrap="hard" rows="30" cols="74"><?php echo str_replace('${ORDER_A}',orders_a($order->info['orders_id']),$mail_sql['orders_status_mail']); ?></textarea>
    </td>
      </tr>
      <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
            <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr>
            <td class="main"><?php echo tep_draw_checkbox_field('notify', '', true); ?><b>メール送信</b></td>
            <td class="main"><?php echo tep_draw_checkbox_field('notify_comments', '', true); ?><b>ステータス通知</b></td>
            </tr>
            <tr>
            <td class="main" colspan="2"><br><b style="color:#FF0000;">間違い探しはしましたか？</b><br><br><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></td>
            </tr>
          </table></td>
          <td valign="top">&nbsp;</td>
          </tr>
    </form>
      <tr>
    <?php }?>
    <td colspan="2" align="right">
    <?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action','status'))) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
    </td>
      </tr>
<?php
  } else {
?>
      <tr>
    <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
      <td align="right" class="smallText"><?php echo tep_draw_form('orders', FILENAME_ORDERS, '', 'get'); ?>
      <table width=""  border="0" cellspacing="1" cellpadding="0">
    <tr>
    
        <td class="smallText">検索 : 
        <input name="keywords" type="text" id="keywords" size="40" value="<?php if(isset($_GET['keywords'])) echo stripslashes($_GET['keywords']); ?>"></td>
    <td><?php echo tep_image_submit('button_search.gif', '検索する'); ?></td>
    </tr>
        <tr>
          <td colspan="2" class="smallText">※検索対象：「顧客名（姓/名/）」「ふりがな（姓/名）」「メールアドレス」「購入商品名」「電話番号」</td>
        </tr>
      </table></form></td>
      <td align="right"><table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr><?php echo tep_draw_form('orders', FILENAME_ORDERS, '', 'get'); ?>
    <td class="smallText" align="right"><?php echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('oID', '', 'size="12"') . tep_draw_hidden_field('action', 'edit'); ?></td>
    </form></tr>
    <tr><?php echo tep_draw_form('status', FILENAME_ORDERS, '', 'get'); ?>
    <td class="smallText" align="right"><?php echo HEADING_TITLE_STATUS . ' ' . tep_draw_pull_down_menu('status', tep_array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $orders_statuses), '', 'onChange="this.form.submit();"'); ?></td>
    </form></tr>      
      </table></td>
    </tr>
    </table>
  </td>
  </tr>
<?php
  if ($ocertify->npermission == 15) {
?>
  <tr>
  <td>
    <!--ORDER EXPORT SCRIPT //-->
    <form action="<?php echo tep_href_link('orders_csv_exe.php','csv_exe=true', 'SSL') ; ?>" method="post">
    <fieldset><legend class="smallText"><b>注文データダウンロード</b></legend>
    <span class="smallText">ダウンロード中はサーバに対して高負荷となります。アクセスの少ない時間に実行してください。</span>
    <table  border="0" align="center" cellpadding="0" cellspacing="2">
    <tr>
      <td class="smallText" width='150'>
      注文書サイト<?php //echo ENTRY_SITE;?>:
      <?php echo tep_site_pull_down_menu_with_all(isset($_GET['site_id']) ? $_GET['site_id'] :'', false);?>
      </td>
      <td class="smallText">
      開始日:
      <select name="s_y">
      <?php
      for($i=2002; $i<2011; $i++) {
        if($i == date('Y')){
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ;
        }else{
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        } 
      }
      ?>    
      </select>
      年
      <select name="s_m">
      <?php
      for($i=1; $i<13; $i++) {
        if($i == date('m')-1){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      月
      <select name="s_d">
      <?php
      for($i=1; $i<32; $i++) {
        if($i == date('d')){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      日 </td>
      <td width="80" align="center">〜</td>
      <td class="smallText">終了日
      <select name="e_y">
      <?php
      for($i=2002; $i<2011; $i++) {
        if($i == date('Y')){
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ;
        }else{
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        } 
      }
      ?>    
      </select>
      年
      <select name="e_m">
      <?php
      for($i=1; $i<13; $i++) {
        if($i == date('m')){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      月
      <select name="e_d">
      <?php
      for($i=1; $i<32; $i++) {
        if($i == date('d')){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      日 </td>
       <td class="smallText"><?php echo HEADING_TITLE_STATUS . ' ' . tep_draw_pull_down_menu('status', tep_array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $orders_statuses), '', ''); ?></td>
      <td>&nbsp;</td>
    <td><input type="image" src="includes/languages/japanese/images/buttons/button_csv_exe.gif" alt="CSVエクスポート" width="105" height="22" border="0"></td>
      </tr>
    </table></fieldset>
    </form>
    <!--ORDER EXPORT SCRIPT EOF //-->
    </td>
      </tr>
<?php
  }
?>
      <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td valign="top">
<?php
  if ($ocertify->npermission == 15) {
    if(!tep_session_is_registered('reload')) $reload = 'yes';
    if($reload == 'yes') {
?>
      <table border="0" width="100%" cellspacing="1" cellpadding="2" style="background: #FF8E90;" height="30"> 
    <tr style="background: #FFE6E6; font-size: 10px; "> 
    <td><strong><font color="#FF0000"> 【注意】 </font></strong>現在自動リロード機能が有効になっています　→ 【<a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'reload')) . 'reload=No'); ?>"><b>無効にする</b></a>】&nbsp;&nbsp;|&nbsp;&nbsp;一覧に表示する<a href="orders_status.php"><b>注文ステータス設定</b></a></td>
        </tr>
      </table>
<?php
  } else {
?>
      <table border="0" width="100%" cellspacing="1" cellpadding="2" style="background: #FF8E90;" height="30"> 
    <tr style="background: #FFE6E6; font-size: 10px; "> 
    <td><strong><font color="#FF0000"> 【注意】 </font></strong>現在自動リロード機能が無効になっています　→ 【<a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'reload')) . 'reload=Yes'); ?>"><b>有効にする</b></a>】&nbsp;&nbsp;|&nbsp;&nbsp;一覧に表示する<a href="orders_status.php"><b>注文ステータス設定</b></a></td>
        </tr>
      </table>
<?php
    }
  }
?>
    <?php echo tep_draw_form('sele_act', FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'action=sele_act'); ?>
    <?php tep_site_filter(FILENAME_ORDERS);?>
  
          <div id="order_icons">
          <span<?php if (isset($_GET['type']) && $_GET['type'] == 'sell') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'type')) . 'type=sell', 'SSL');?>" title="売"><img src="images/icons/mai4.gif" alt="売" title="売"> </a>
          </span>
          <span<?php if (isset($_GET['type']) && $_GET['type'] == 'buy') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS,  tep_get_all_get_params(array('oID', 'action', 'type')) . 'type=buy','SSL');?>" title="買"><img src="images/icons/mai3.gif" alt="買" title="買"> </a>
          </span>
          <span<?php if (isset($_GET['type']) && $_GET['type'] == 'mix') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS,  tep_get_all_get_params(array('oID', 'action', 'type')) . 'type=mix','SSL');?>" title="混"><img src="images/icons/kon.gif" alt="混" title="混"> </a>
          </span>
          <span<?php if (isset($_GET['payment']) && $_GET['payment'] == 'moneyorder') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS,  tep_get_all_get_params(array('oID', 'action', 'payment')) . 'payment=moneyorder','SSL');?>" title="銀行振込"><img src="images/icons/gi.gif" alt="銀行振込" title="銀行振込"> </a>
          </span>
          <span<?php if (isset($_GET['payment']) && $_GET['payment'] == 'postalmoneyorder') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'payment')) . 'payment=postalmoneyorder','SSL');?>" title="ゆうちょ銀行（郵便局）"><img src="images/icons/yu.gif" alt="ゆうちょ銀行（郵便局）" title="ゆうちょ銀行（郵便局）"> </a>
          </span>
          <span<?php if (isset($_GET['payment']) && $_GET['payment'] == 'telecom') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'payment')) . 'payment=telecom','SSL');?>" title="クレジットカード決済"><img src="images/icons/ku.gif" alt="クレジットカード決済" title="クレジットカード決済"> </a>
          </span>
          <span<?php if (isset($_GET['payment']) && $_GET['payment'] == 'convenience_store') {?> class="order_icons_selected"<?php }?>>
            <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action', 'payment')) . 'payment=convenience_store','SSL');?>" title="コンビニ決済"><img src="images/icons/ko.gif" alt="コンビニ決済" title="コンビニ決済"> </a>
          </span>
          </div>
  
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr class="dataTableHeadingRow">
<?php 
  if ($ocertify->npermission) {
?>
      <td class="dataTableHeadingContent"><input type="checkbox" name="all_chk" onClick="all_check()"></td>
<?php 
  }
?>
      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SITE; ?></td>
      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ORDER_TOTAL; ?></td>
      <td class="dataTableHeadingContent" align="center">取引日</td>
      <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></td>
      <td class="dataTableHeadingContent" align="right"></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
    </tr>
<?php
    
  $where_type = '';
  if(isset($_GET['type'])){
  switch ($_GET['type']) { 
    case 'sell':
      $where_type = " and (!(o.payment_method = '銀行振込(買い取り)' or o.payment_method = '銀行振込（買い取り）') and h.orders_id not in (select orders_id from ".TABLE_ORDERS_STATUS_HISTORY." where comments like '金融機関名%支店名%'))"; 
      break;
    case 'buy':
      $where_type = " and (o.payment_method = '銀行振込(買い取り)' or o.payment_method = '銀行振込（買い取り）')"; 
      break;
    case 'mix':
      $where_type = " and (!(o.payment_method = '銀行振込(買い取り)' or o.payment_method = '銀行振込（買い取り）') and h.comments like '金融機関名%支店名%')"; 
      break;
  }
  }
    
  $where_payment = '';
  if(isset($_GET['payment'])){
  switch ($_GET['payment']) { 
    case 'convenience_store':
      $where_payment = " and o.payment_method = 'コンビニ決済'";
      break;
    case 'telecom':
      $where_payment = " and o.payment_method = 'クレジットカード決済'";
      break;
    case 'postalmoneyorder':
      $where_payment = " and o.payment_method = 'ゆうちょ銀行（郵便局）'";
      break;
    case 'moneyorder':
    case 'buying':
      $where_payment .= " and (o.payment_method = '銀行振込' or o.payment_method = '銀行振込(買い取り)' or o.payment_method = '銀行振込（買い取り）')"; 
      break;
  }
  }

  if (isset($_GET['cID']) && $_GET['cID']) {
      $cID = tep_db_prepare_input($_GET['cID']);
      $orders_query_raw = "
        select o.orders_id, 
               o.torihiki_date, 
               o.customers_name, 
               o.customers_id, 
               o.payment_method, 
               o.date_purchased, 
               o.last_modified, 
               o.currency, 
               o.currency_value, 
               s.orders_status_name, 
               ot.text as order_total,
               si.romaji
        from " . TABLE_ORDERS . " o 
          left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id)  left join " . TABLE_ORDERS_STATUS_HISTORY . " h on (o.orders_id = h.orders_id), " . TABLE_ORDERS_STATUS . " s, ".TABLE_SITES." si
        where o.customers_id = '" . tep_db_input($cID) . "' 
          " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and si.id = '" . intval($_GET['site_id']) . "' " : '') . "
          and si.id = o.site_id
          and o.orders_status = s.orders_status_id 
          and s.language_id = '" . $languages_id . "' 
          and ot.class = 'ot_total' 
          " . $where_payment . $where_type . "
        group by o.orders_id
        order by o.torihiki_date DESC";
    } elseif (isset($_GET['status']) && $_GET['status']) {
      $status = tep_db_prepare_input($_GET['status']);
      $orders_query_raw = "
        select o.orders_id, 
               o.torihiki_date, 
               o.customers_id, 
               o.customers_name, 
               o.payment_method, 
               o.date_purchased, 
               o.last_modified, 
               o.currency, 
               o.currency_value, 
               s.orders_status_name, 
               ot.text as order_total,
               si.romaji
        from " . TABLE_ORDERS . " o 
          left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id)  left join " . TABLE_ORDERS_STATUS_HISTORY . " h on (o.orders_id = h.orders_id), " . TABLE_ORDERS_STATUS . " s, ".TABLE_SITES." si
        where o.orders_status = s.orders_status_id and s.language_id = '" . $languages_id . "' 
          " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and si.id = '" . intval($_GET['site_id']) . "' " : '') . "
          and s.orders_status_id = '" . tep_db_input($status) . "' 
          and ot.class = 'ot_total' 
          and si.id = o.site_id
          " . $where_payment . $where_type . "
        group by o.orders_id
        order by o.torihiki_date DESC";
    } elseif (isset($_GET['keywords']) && $_GET['keywords']) {
      
      $orders_query_raw = "
        select distinct(o.orders_id), 
               o.torihiki_date, 
               o.customers_id, 
               o.customers_name, 
               o.payment_method, 
               o.date_purchased, 
               o.last_modified, 
               o.currency, 
               o.currency_value, 
               s.orders_status_name, 
               ot.text as order_total,
               si.romaji
        from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_STATUS_HISTORY . " h on (o.orders_id = h.orders_id), " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS_STATUS . " s, " . TABLE_ORDERS_PRODUCTS . " op , ".TABLE_SITES." si 
        where o.orders_id = ot.orders_id 
          " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and si.id = '" . intval($_GET['site_id']) . "' " : '') . "
          and si.id = o.site_id
          and o.orders_status = s.orders_status_id 
          and s.language_id = '" . $languages_id . "' 
          and ot.class = 'ot_total' 
          " . $where_payment . $where_type . "
          and o.orders_id = op.orders_id";
    $keywords = str_replace('　', ' ', $_GET['keywords']);
    tep_parse_search_string($keywords, $search_keywords);
    if (isset($search_keywords) && (sizeof($search_keywords) > 0)) {
      $orders_query_raw .= " and (";
      for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
      switch ($search_keywords[$i]) {
      case '(':
      case ')':
      case 'and':
      case 'or':
          $orders_query_raw .= " " . tep_db_prepare_input($search_keywords[$i]) . " ";
    break;
      default:
          $keyword = tep_db_prepare_input($search_keywords[$i]);
    $orders_query_raw .= "(";
        $orders_query_raw .= "o.customers_name like '%" . tep_db_input($keyword) . "%' or ";
        $orders_query_raw .= "o.customers_name_f like '%" . tep_db_input($keyword) . "%' or ";
        $orders_query_raw .= "o.customers_email_address like '%" . tep_db_input($keyword) . "%' or ";
        $orders_query_raw .= "o.customers_telephone like '%" . tep_db_input($keyword) . "%' or ";
        $orders_query_raw .= "op.products_name like '%" . tep_db_input($keyword) . "%'";
        $orders_query_raw .= ")";
    break;
    }
      } 
    $orders_query_raw .= ")";  
    }
    
    $orders_query_raw .= "group by o.orders_id order by o.torihiki_date DESC";
  } else {
      $orders_query_raw = "
        select s.orders_status_id, 
               o.orders_id, 
               o.torihiki_date, 
               o.customers_id, 
               o.customers_name, 
               o.payment_method, 
               o.date_purchased, 
               o.last_modified, 
               o.currency, 
               o.currency_value, 
               s.orders_status_name, 
               s.orders_status_image, 
               ot.text as order_total,
               si.romaji
         from " . TABLE_ORDERS . " o 
           left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id) left join " . TABLE_ORDERS_STATUS_HISTORY . " h on (o.orders_id = h.orders_id), " . TABLE_ORDERS_STATUS . " s , ".TABLE_SITES." si
         where o.orders_status = s.orders_status_id 
          " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and si.id = '" . intval($_GET['site_id']) . "' " : '') . "
           and si.id=o.site_id
           and s.language_id = '" . $languages_id . "' 
           and ot.class = 'ot_total' 
           and s.finished = '0'
           " . $where_payment . $where_type . "
         group by o.orders_id
         order by o.torihiki_date DESC
      ";
    }

    $orders_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ORDERS_RESULTS, $orders_query_raw, $orders_query_numrows);
    $orders_query = tep_db_query($orders_query_raw);
    $allorders    = $allorders_ids = array();
    while ($orders = tep_db_fetch_array($orders_query)) {
      $allorders[] = $orders;
      if (((!isset($_GET['oID']) || !$_GET['oID']) || ($_GET['oID'] == $orders['orders_id'])) && (!isset($oInfo) || !$oInfo)) {
        $oInfo = new objectInfo($orders);
      }

  //今日の取引なら赤色
  $trade_array = getdate(strtotime(tep_datetime_short($orders['torihiki_date'])));
  $today_array = getdate();
  if ($trade_array["year"] == $today_array["year"] && $trade_array["mon"] == $today_array["mon"] && $trade_array["mday"] == $today_array["mday"]) {
    $today_color = 'red';
    if ($trade_array["hours"] >= $today_array["hours"]) {
      $next_mark = tep_image(DIR_WS_ICONS . 'arrow_blinking.gif', '次の注文'); //次の注文に目印をつける
    } else {
      $next_mark = '';
    }
  } else {
    #if ($ocertify->npermission) {
      $today_color = 'black';
    #} else {
      #$today_color = '#999';
    #}
    $next_mark = '';
  }
  
  if ( (isset($oInfo) && is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id) ) {
    echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ondblclick="window.location.href=\''.tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']).'\'">' . "\n";
  } else {
    echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" ondblclick="window.location.href=\''.tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']).'\'">' . "\n";
  }
?>
  <?php 
  if ($ocertify->npermission) {
    ?>
        <td style="border-bottom:1px solid #000000;" class="dataTableContent">
          <input type="checkbox" name="chk[]" value="<?php echo $orders['orders_id']; ?>" onClick="chg_tr_color(this)">
        </td>
<?php 
  }
?>
        <td style="border-bottom:1px solid #000000;" class="dataTableContent" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><?php echo $orders['romaji'];?></td>
        <td style="border-bottom:1px solid #000000;" class="dataTableContent" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)">
          <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit');?>"><?php echo tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW);?></a>&nbsp;
          <a href="<?php echo tep_href_link('orders.php', 'cID=' . tep_output_string_protected($orders['customers_id']));?>"><?php echo tep_image(DIR_WS_ICONS . 'search.gif', '過去の注文');?></a>
<?php if ($ocertify->npermission) {?>
          &nbsp;<a href="<?php echo tep_href_link('customers.php', 'page=1&cID=' . tep_output_string_protected($orders['customers_id']) . '&action=edit');?>"><?php echo tep_image(DIR_WS_ICONS . 'arrow_r_red.gif', '顧客情報');?></a>&nbsp;&nbsp;
<?php }?>
  <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
  <font color="#999">
  <?php }?>
          <b><?php echo tep_output_string_protected($orders['customers_name']);?></b>
  <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
  </font>
  <?php }?>
        </td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)">
  <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
  <font color="#999">
  <?php }?>
          <?php echo strip_tags($orders['order_total']); ?>
  <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
  </font>
  <?php }?>
    </td>
          
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><?php echo $next_mark; ?>
    <font color="<?php !$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)?'#999':$today_color; ?>"><?php echo tep_datetime_short($orders['torihiki_date']); ?></font>
    </td>

    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="center" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><span style="color:#999999;"><?php echo tep_datetime_short($orders['date_purchased']); ?></span></td>

    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="center" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)">
    <?php 
    // ===============================================================
        $___orders_status_query = tep_db_query("select orders_status_id from `".TABLE_ORDERS_STATUS_HISTORY."` WHERE `orders_id`='".$orders['orders_id']."' order by `date_added` asc");
        $___orders_status_ids   = array();
        while($___orders_status = tep_db_fetch_array($___orders_status_query)){
          $___orders_status_ids[] = $___orders_status['orders_status_id'];
        }
        $_orders_status_history_query_raw = "select * from `".TABLE_ORDERS_STATUS."` WHERE `orders_status_id` IN (".join(',',$___orders_status_ids).")";
        $_orders_status_history_query    = tep_db_query($_orders_status_history_query_raw);     $_osh = array();
      $_osi = false;
      while ($_orders_status_history = tep_db_fetch_array($_orders_status_history_query)){
        if(!in_array($_orders_status_history['orders_status_id'], $_osh)
        && !is_dir(tep_get_upload_dir().'orders_status/'.$_orders_status_history['orders_status_image']) 
        && file_exists(tep_get_upload_dir().'orders_status/'.$_orders_status_history['orders_status_image'])){
          echo tep_image(tep_get_web_upload_dir(). 'orders_status/' . $_orders_status_history['orders_status_image'], $_orders_status_history['orders_status_image'], 15, 15, ($_orders_status_history['orders_status_id'] == @$orders['orders_status_id'])?'style="vertical-align: middle;"':'style="vertical-align: middle;"');
          $_osi = $_osi or true;
        }
          $_osh[] = $_orders_status_history['orders_status_id'];
      }
      if(!$_osi){
        echo '　';
      }
    // ===============================================================
    ?>
    </td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>)"><font color="<?php echo $today_color; ?>"><?php echo $orders['orders_status_name']; ?></font></td>
    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"><?php 
    if ( isset($oInfo) && (is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id) ) { 
      echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); 
    } else { 
      echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID')) . 'oID=' . $orders['orders_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
    } ?>&nbsp;</td>
    </tr>
<?php }?>
      </table>
<script language="javascript">
  window.orderStr = new Array();
<?php foreach($allorders as $key=>$orders){?>
  window.orderStr['<?php echo $orders['orders_id'];?>'] = "<?php echo str_replace(array("\r\n","\r","\n"), array('\n', '\n', '\n'), orders_a($orders['orders_id'], $allorders));?>";
<?php }?>
  
function submit_confirm()
{
  var idx = document.sele_act.elements['status'].selectedIndex;
  var CI  = document.sele_act.elements['status'].options[idx].value;
  chk = getCheckboxValue('chk[]')
  if((chk.length > 1 || chk.length < 1) && window.status_text[CI].indexOf('${ORDER_A}') != -1){
    if(chk.length > 1){
      alert('複数の選択はできません。');
    } else {
      alert('注文書はまだ選択していません。');
    }
    return false;
  }
  return true;
}
</script>

      <table width="100%" id="select_send" style="display:none">
        <tr>
          <td class="main"><b><?php echo ENTRY_STATUS; ?></b></td>
        <td class="main"><?php echo tep_draw_pull_down_menu('status', $orders_statuses, $select_select,  'onChange="mail_text(\'status\',\'comments\',\'os_title\')"'); ?> &nbsp;<a href="<?php echo tep_href_link(FILENAME_ORDERS_STATUS,'',SSL);?>">メール本文編集</a></td>
        </tr>
        <tr>
          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="main"><b><?php echo ENTRY_EMAIL_TITLE; ?></b></td>
        <td class="main"><?php echo tep_draw_input_field('os_title', $select_title); ?></td>
        </tr>
        <tr>
          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="main" valign="top"><b><?php echo TABLE_HEADING_COMMENTS . ':'; ?></b></td>
        <td class="main">
          自動的に改行して表示し、送信されるメールにも改行が入ります。
          <table><tr class="smalltext"><td><font color="red">※</font>&nbsp;コピペ用:</td><td>ただ今よりログインいたします。</td></tr></table>
          <br>
          <?php echo tep_draw_textarea_field('comments', 'hard', '74', '30', $select_text, 'style="font-family:monospace;font-size:x-small"'); ?>
        </td>
        </tr>
        <tr>
          <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php echo tep_draw_checkbox_field('notify', '', true); ?><b>メール送信</b></td>
            <td class="main" align="right"><?php echo tep_draw_checkbox_field('notify_comments', '', true); ?><b>ステータス通知</b></td>
          </tr>
          <tr>
            <td class="main" colspan="2"><br><b style="color:#FF0000;">間違い探しはしましたか？</b><br><br><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE, 'onclick="return submit_confirm();"'); ?></td>
          </tr>
        </table></td>
        </tr>
      </table>
      </form>
      <!-- display add end-->
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
    <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
    <td class="smallText" valign="top"><?php echo $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_ORDERS_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
    <td class="smallText" align="right"><?php echo $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_ORDERS_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
      </tr>
    </table></td>
    </tr>
      </table>
      
      
      </td>
<?php
  $heading = array();
  $contents = array();
  switch (isset($_GET['action'])?$_GET['action']:null) {
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ORDER . '</b>');

      $contents = array('form' => tep_draw_form('orders', FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO . '<br><br><b>' . tep_get_fullname(isset($cInfo->customers_firstname)?$cInfo->customers_firstname:'', isset($cInfo->customers_lastname)?$cInfo->customers_lastname:'') . '</b>');
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('restock', '', true) . ' ' . TEXT_INFO_RESTOCK_PRODUCT_QUANTITY);
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (isset($oInfo) && is_object($oInfo)) {
    $heading[] = array('text' => '<b>[' . $oInfo->orders_id . ']<br>' . tep_datetime_short($oInfo->date_purchased) . '</b>');

    if ($ocertify->npermission == 15) {
      $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '">' . tep_image_button('button_details.gif', IMAGE_DETAILS) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
    } else {
      $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '">' . tep_image_button('button_details.gif', IMAGE_DETAILS) . '</a>');
    }
    $contents[] = array('text' => '<br>' . TEXT_DATE_ORDER_CREATED . ' ' . tep_date_short($oInfo->date_purchased));
    if (tep_not_null($oInfo->last_modified)) $contents[] = array('text' => TEXT_DATE_ORDER_LAST_MODIFIED . ' ' . tep_date_short($oInfo->last_modified));
    $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENT_METHOD . ' '  . $oInfo->payment_method);
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '      <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '      </td>' . "\n";
  }
?>
    </tr>
    </table></td>
      </tr>
<?php
  }
?>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php
    require(DIR_WS_INCLUDES . 'footer.php');
?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
