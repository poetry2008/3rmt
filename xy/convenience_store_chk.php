<?php
/*
  $Id$
*/

  include('includes/application_top.php');
  
  if($_GET['sid'] != ""){
    #基本情報
  $ip = MODULE_PAYMENT_CONVENIENCE_STORE_IP;
  $sid = $_GET['sid'];
  $oid = substr($_GET['sid'],0,8) . '-' . substr($_GET['sid'],8,8);
  
  #DB取得情報
  //ccdd
  $orders_status_history_query = tep_db_query("select comments from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . $oid . "'");
  $orders_status_history_result = tep_db_fetch_array($orders_status_history_query);
  
  if($orders_status_history_result['comments'] != ""){
    $osh_text = explode("\n",mb_convert_encoding($orders_status_history_result['comments'], 'SJIS', 'EUC-JP'));
    foreach($osh_text as $val){    
    
    if(ereg('郵便番号:',$val)){
      $yubin1 = str_replace('郵便番号:',"",$val);
    }
    
    if(ereg('住所1:',$val)){
      $adr1 = str_replace('住所1:',"",$val);
    }
    
    if(ereg('住所2:',$val)){
      $adr2 = str_replace('住所2:',"",$val);
    }
    
    if(ereg('氏:',$val)){
      $name1 = str_replace('氏:',"",$val);
    }
    
    if(ereg('名:',$val)){
      $name2 = str_replace('名:',"",$val);
    }
    
    if(ereg('電話番号:',$val)){
      $tel = str_replace('電話番号:',"",$val);
    }  
    }
  }
  
  //ccdd
  $order_query = tep_db_query("select customers_email_address from " .  TABLE_ORDERS . " where orders_id = '" . $oid . "' and site_id = '".SITE_ID."'");
  $order_result = tep_db_fetch_array($order_query);
  $mail = $order_result['customers_email_address'];
  
  //ccdd
  $op_count_query = tep_db_query("select count(*) from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $oid . "'");
  $op_count_result = tep_db_fetch_array($op_count_query);
  
  if($op_count_result['count(*)'] < 8){
    $count = 1;
    $n = "";
    $k = "";
    
    //ccdd
    $order_products_query = tep_db_query("select * from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $oid . "'");
    while($order_products_result = tep_db_fetch_array($order_products_query)){
      $n .= '&N' . $count . '=' . mb_substr($order_products_result['products_name'],0,20) . '(' . $order_products_result['products_quantity'] . ')';
      $k .= '&K' . $count . '=' . (int)$order_products_result['final_price'] * $order_products_result['products_quantity'];
    
      $count = $count + 1;
    }
    
    $n .= '&N' . $count . '=' . 'Commission';
    $k .= '&K' . $count . '=200';
  
  }else{
    $n = "";
    $k = "";
    
    //ccdd
    $order_total_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $oid . "' and class = 'ot_subtotal'");
    $order_total_result = tep_db_fetch_array($order_total_query);
    
    $n .= '&N1=' . STORE_NAME . '&N2=' . 'コンビニ決済手数料';
    $k .= '&K1=' . (int)$order_total_result['value'] . '&K2=200';
    
  }
  
  $pr = '?IP=' . $ip . '&SID=' . $sid . '&NAME1=' . $name1 . '&NAME2=' . $name2 . '&TEL=' . $tel . '&YUBIN1=' . $yubin1 . '&ADR1=' . $adr1 . '&ADR2=' . $adr2 . '&MAIL=' . $mail . mb_convert_encoding($n, 'SJIS', 'EUC-JP') . $k;
  
  if(MODULE_PAYMENT_CONVENIENCE_STORE_OK_URL != ''){
    $pr .= '&OKURL=' . MODULE_PAYMENT_CONVENIENCE_STORE_OK_URL;
  }
  
  if(MODULE_PAYMENT_CONVENIENCE_STORE_NG_URL != ''){
    $pr .= '&RT=' . MODULE_PAYMENT_CONVENIENCE_STORE_NG_URL;
  }
  
  
    mb_internal_encoding('SJIS');
    header ("Content-Type: text/html; charset=Shift_JIS");
    header("location:" . MODULE_PAYMENT_CONVENIENCE_STORE_URL . $pr);
  }else{
    echo 'エラーが発生しました。'."\n";
    echo 'コンビニ決済が行われませんでした。'."\n";
  }
