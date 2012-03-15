<?php
  require(DIR_WS_CLASSES. 'payment.php'); 
  
  if (!isset($_POST['pid'])) {
    forward404(); 
  }
  
  $preorder_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$_POST['pid']."' and site_id = '".SITE_ID."'");
  $preorder_res = tep_db_fetch_array($preorder_raw);
  if (!$preorder_res) {
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
  
  $check_preorder_str = $preorder_res['check_preorder_str'];
 
  $preorder_subtotal = 0;
  $preorder_subtotal_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$_POST['pid']."' and class = 'ot_subtotal'");
  $preorder_subtotal_res = tep_db_fetch_array($preorder_subtotal_raw);
  if ($preorder_subtotal_res) {
    $preorder_subtotal = number_format($preorder_subtotal_res['value'], 0, '.', ''); 
  }

  $preorder_info_attr = array();
  foreach ($_POST as $pc_key => $pc_value) {
    if (is_array($pc_value)) {
      foreach ($pc_value as $pcs_key => $pcs_value) {
        $preorder_info_attr[$pcs_key] =$pcs_value; 
      }
    }
  }
  
  if (!tep_session_is_registered('preorder_info_attr')) {
    tep_session_register('preorder_info_attr'); 
  }
  
  
  $preorder_info_tori = $_POST['torihikihouhou'];
  $preorder_info_date = $_POST['date'];
  $preorder_info_hour = $_POST['hour'];
  $preorder_info_min = $_POST['min'];
  $preorder_info_character = $_POST['p_character'];
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
  
  if (!tep_session_is_registered('preorder_info_min')) {
    tep_session_register('preorder_info_min'); 
  }
  
  if (!tep_session_is_registered('preorder_info_character')) {
    tep_session_register('preorder_info_character'); 
  }
  
  if (!tep_session_is_registered('preorder_info_id')) {
    tep_session_register('preorder_info_id'); 
  }
  
  if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
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
