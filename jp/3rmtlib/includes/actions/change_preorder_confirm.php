<?php
  require(DIR_WS_CLASSES. 'payment.php'); 
  
  $_POST = $preorder_information;

  if (!isset($_POST['pid'])) {
    forward404(); 
  }
  
  $preorder_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$_POST['pid']."' and site_id = '".SITE_ID."'");
  $preorder_res = tep_db_fetch_array($preorder_raw);
  if (!$preorder_res) {
    //判断该预约订单是否存在 
    forward404(); 
  } 
  $payment_modules = payment::getInstance(SITE_ID); 
  $con_payment_code = payment::changeRomaji($preorder_res['payment_method'],PAYMENT_RETURN_TYPE_CODE);
 
  $is_guest_single = 0;
  $link_customer_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$preorder_res['customers_id']."'");
  $link_customer_res = tep_db_fetch_array($link_customer_raw);
  if ($link_customer_res) {
    if ($link_customer_res['customers_guest_chk'] == '1') {
      $is_guest_single = 1; 
    }
  }
  
  $option_info_array = array();

  foreach ($_POST as $p_key => $p_value) {
    $op_single_str = substr($p_key, 0, 3);
    if ($op_single_str == 'op_') {
      $p_tmp_value = str_replace(' ', '', $p_value);
      $p_tmp_value = str_replace('　', '', $p_value);
      if ($p_tmp_value != '') {
        $option_info_array[$p_key] = stripslashes($p_value); 
      } else {
        $option_info_array[$p_key] = MSG_TEXT_NULL; 
      }
    }
  }
  
  $preorder_option_info = $option_info_array;
  
  if (!tep_session_is_registered('preorder_option_info')) {
    //把option信息放入session 
    tep_session_register('preorder_option_info'); 
  }
 
  
  $check_preorder_str = $preorder_res['check_preorder_str'];
 
  $preorder_subtotal = 0;
  $preorder_subtotal_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$_POST['pid']."' and class = 'ot_subtotal'");
  $preorder_subtotal_res = tep_db_fetch_array($preorder_subtotal_raw);
  if ($preorder_subtotal_res) {
    $preorder_subtotal = number_format($preorder_subtotal_res['value'], 0, '.', ''); 
  }
  
  $preorder_total_info_array = get_preorder_total_info($con_payment_code, $_POST['pid'], $option_info_array);  
  
  if (isset($preorder_total_info_array['subtotal'])) {
    $preorder_subtotal = number_format($preorder_total_info_array['subtotal'], 0, '.', ''); 
  }
  
  $preorder_info_tori = $_POST['torihikihouhou'];
  $preorder_info_date = $_POST['date'];
  $preorder_info_hour = $_POST['hour'];
  $preorder_info_start_hour = $_POST['start_hour'];
  $preorder_info_start_min = $_POST['start_min'];
  $preorder_info_end_hour = $_POST['end_hour'];
  $preorder_info_end_min = $_POST['end_min'];
  $preorder_info_min = $_POST['min'];
  $preorder_info_id = $_POST['pid'];
  
  if (!tep_session_is_registered('preorder_info_tori')) {
    tep_session_register('preorder_info_tori'); 
  }
  
  if (!tep_session_is_registered('preorder_info_date')) {
    tep_session_register('preorder_info_date'); 
  }
  
  if (!tep_session_is_registered('preorder_info_hour')) {
    tep_session_register('preorder_info_hour'); 
  }
  
  if (!tep_session_is_registered('preorder_info_start_hour')) {
    tep_session_register('preorder_info_start_hour'); 
  }

  if (!tep_session_is_registered('preorder_info_start_min')) {
    tep_session_register('preorder_info_start_min'); 
  } 
  
  if (!tep_session_is_registered('preorder_info_end_hour')) {
    tep_session_register('preorder_info_end_hour'); 
  }
 
  if (!tep_session_is_registered('preorder_info_end_min')) {
    tep_session_register('preorder_info_end_min'); 
  }
  
  if (!tep_session_is_registered('preorder_info_min')) {
    tep_session_register('preorder_info_min'); 
  }
  
  
  if (!tep_session_is_registered('preorder_info_id')) {
    tep_session_register('preorder_info_id'); 
  }
  
  if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
    //把点数放入session 
    if (@$_POST['preorder_point'] < $preorder_subtotal) {
      $preorder_point = isset($_POST['preorder_point'])?$_POST['preorder_point']:0; 
    } else {
      $preorder_point = $preorder_subtotal; 
    }
    $preorder_real_point = $preorder_point;
    
    if (!tep_session_is_registered('preorder_point')) {
      tep_session_register('preorder_point'); 
    } 
    if (!tep_session_is_registered('preorder_real_point')) {
      tep_session_register('preorder_real_point'); 
    } 
    //把优惠券信息放入session 
    $campaign_query = tep_db_query("select * from ".TABLE_CAMPAIGN." where id = '".$_POST['preorder_campaign_id']."' and (site_id = '".SITE_ID."' or site_id = '0') and status = '1' and is_preorder = '1' order by site_id desc limit 1");
    $campaign_res = tep_db_fetch_array($campaign_query); 
    if ($campaign_res) {
      $percent_pos = strpos($campaign_res['point_value'], '%'); 
      if ($percent_pos !== false) {
        $preorder_campaign_fee = $preorder_subtotal*substr($campaign_res['point_value'], 0, -1)/100; 
        if ($preorder_campaign_fee > 0) {
          $preorder_campaign_fee = 0 - $preorder_campaign_fee; 
        }
      } else {
        $preorder_campaign_fee = $campaign_res['point_value']; 
      }
      @eval("\$preorder_campaign_fee = (int)$preorder_campaign_fee;"); 
      tep_session_register('preorder_campaign_fee');
      $preorder_camp_id = $campaign_res['id'];
      tep_session_register('preorder_camp_id');
    }
  }
  
  require(DIR_WS_LANGUAGES . $language . '/change_preorder_confirm.php');
  

$form_action_url = tep_href_link('change_preorder_process.php'); 
if (isset($payment_modules->modules[strtoupper($con_payment_code)]->form_action_url) && $payment_modules->modules[strtoupper($con_payment_code)]->form_action_url) {
  $form_action_url = $payment_modules->modules[strtoupper($con_payment_code)]->form_action_url; 
}
$_SESSION['preorder_option'] = date('Ymd-His'). ds_makeRandStr(2);

