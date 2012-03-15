<?php
  require(DIR_WS_LANGUAGES . $language . '/change_preorder.php');
  require('address_preorder/AD_Option.php');
  require('address_preorder/AD_Option_Group.php');

  $ad_option = new AD_Option();

  $preorder_raw = tep_db_query('select * from '.TABLE_PREORDERS." where check_preorder_str = '".$_GET['pid']."' and site_id = '".SITE_ID."' and is_active = '1'");
  $preorder_res = tep_db_fetch_array($preorder_raw); 
  if (!$preorder_res) {
    forward404(); 
  }
  
  $customer_info_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$preorder_res['customers_id']."' and site_id = '".SITE_ID."'"); 
  $customer_info_res = tep_db_fetch_array($customer_info_raw);
  
  $is_member_single = 0;
  if ($customer_info_res['customers_guest_chk'] == '0') {
    $is_member_single = 1; 
  }
  
  if (!tep_session_is_registered('customer_id')) {
      if ($customer_info_res['customers_guest_chk'] == '0') {
        $navigation->set_snapshot();
        tep_redirect(tep_href_link(FILENAME_LOGIN, 'pid='.$_GET['pid'], 'SSL'));
      }
  } else {
    if ($is_member_single) { 
      if ($guestchk == '0') {
        if ($customer_emailaddress != $preorder_res['customers_email_address']) {
          $navigation->set_snapshot();
          
          tep_session_unregister('customer_id');
          tep_session_unregister('customer_default_address_id');
          tep_session_unregister('customer_first_name');
          tep_session_unregister('customer_last_name'); 
          tep_session_unregister('customer_country_id');
          tep_session_unregister('customer_zone_id');
          tep_session_unregister('comments');
          tep_session_unregister('customer_emailaddress');
          tep_session_unregister('guestchk');

          
          tep_redirect(tep_href_link(FILENAME_LOGIN, 'pid='.$_GET['pid'], 'SSL'));
        }
      }
    }
  } 
 
  require('option/HM_Option.php');
  require('option/HM_Option_Group.php');
  
  $hm_option = new HM_Option();
  $option_info_array = array();

  unset($_SESSION['preorder_campaign_fee']);
  unset($_SESSION['preorder_camp_id']);
  
  $preorder_point = (int)$customer_info_res['point'];  
  
  $preorder_id = $preorder_res['orders_id'];
  
  $ensure_date_info = explode(' ', $preorder_res['ensure_deadline']);
  $year_info = explode('-', $ensure_date_info[0]);
  $ensure_datetime = mktime(23, 59, 59, $year_info[1], $year_info[2], $year_info[0]);
  if (time() > $ensure_datetime) {
    $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$preorder_id."'"); 
    $preorder_product_res = tep_db_fetch_array($preorder_product_raw); 
    tep_redirect(tep_href_link('change_preorder_timeout.php?pname='.urlencode($preorder_product_res['products_name']))); 
  }
  $error = false;  
  if ($_POST['action'] == 'process') {
    $preorder_torihikihouhou = tep_db_prepare_input($_POST['torihikihouhou']);
    $preorder_date = tep_db_prepare_input($_POST['date']);
    $preorder_hour = tep_db_prepare_input($_POST['hour']);
    $preorder_min = tep_db_prepare_input($_POST['min']);

    //住所信息处理 
    $address_option_info_array = array(); 
    if (!$ad_option->check()) {
      foreach ($_POST as $ad_key => $ad_value) {
        $ad_single_str = substr($ad_key, 0, 3);
        if ($ad_single_str == 'op_') {
          $address_option_info_array[$ad_key] = $ad_value; 
        } 
      }
    }else{
      $error_str = true;
    }
    
    if($error_str == true){

      $error = true;
    }
    if ($preorder_torihikihouhou == '') {
      $error = true;
      $torihikihouhou_error = TEXT_PREORDER_ERROR_TORIHIKIHOUHOU;
    }
    
    if ($preorder_date == '') {
      $error = true; 
      $date_error = TEXT_PREORDER_ERROR_DATE; 
    }
    
    if ($preorder_hour == '') {
      $error = true;
      $jikan_error = TEXT_PREORDER_ERROR_JIKAN;
    }
    
    if ($preorder_min == '') {
      $error = true;
      $jikan_error = TEXT_PREORDER_ERROR_JIKAN;
    }
   
    if (isset($_POST['p_character'])) {
      $tmp_character = $_POST['p_character']; 
      $tmp_character = str_replace(' ', '', $tmp_character); 
      $tmp_character = str_replace('　', '', $tmp_character); 
      if ($tmp_character == '') {
        $error = true;
        $character_error = TEXT_PREORDER_ERROR_CHARACTER;
      }
    }
    
    if (isset($_POST['preorder_point'])) {
      $_POST['preorder_point'] = get_strip_campaign_info($_POST['preorder_point']); 
      if (!empty($_POST['preorder_point'])) { 
      if (is_numeric($_POST['preorder_point'])) {
        if ($_POST['preorder_point'] > $preorder_point) {
          if (($_POST['preorder_point'] != '0') && ($preroder_point != '0')) {
            $error = true;
            $point_error = TEXT_PREORDER_ERROR_POINT;
          }
        }
        if (($_POST['preorder_point'] < 0)) {
          $error = true;
          $point_error = TEXT_PREORDER_ERROR_POINT;
        }
      } else {
        $campaign_query = tep_db_query("select * from ".TABLE_CAMPAIGN." where keyword = '".trim($_POST['preorder_point'])."' and (site_id = '".SITE_ID."' or site_id = '0') and status = '1' and is_preorder = '1' and end_date >= '".date('Y-m-d', time())."' and start_date <= '".date('Y-m-d', time())."' and type = '1' order by site_id desc limit 1"); 
        $campaign_res = tep_db_fetch_array($campaign_query);
        if ($campaign_res) {
          $max_campaign_query = tep_db_query("select count(*) as total from ".TABLE_CUSTOMER_TO_CAMPAIGN." where customer_id = '".$customer_id."' and campaign_id = '".$campaign_res['id']."'"); 
          $max_campaign_res = tep_db_fetch_array($max_campaign_query);
          if ((int)$max_campaign_res['total'] < $campaign_res['max_use']) {
            $preorder_subtotal = 0; 
            $preorder_subtotal_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$preorder_res['orders_id']."' and class = 'ot_subtotal'");
            $preorder_subtotal_res = tep_db_fetch_array($preorder_subtotal_raw);
            if ($preorder_subtotal_res) {
              $preorder_subtotal = number_format($preorder_subtotal_res['value'], 0, '.', ''); 
            }
            if ($campaign_res['limit_value'] < $preorder_subtotal) {
              $_POST['preorder_point'] = 0;
              $_POST['preorder_campaign_id'] = $campaign_res['id'];
              $_POST['preorder_campaign_info'] = $campaign_res['keyword'];
            } else {
              $error = true;
              $point_error = TEXT_PREORDER_ERROR_CAMPAIGN;
            }
          } else {
            $error = true;
            $point_error = TEXT_PREORDER_ERROR_CAMPAIGN;
          }
        } else {
          $error = true;
          $point_error = TEXT_PREORDER_ERROR_CAMPAIGN;
        }
      }
      } 
    }
    
    if (!empty($_POST['camp_preorder_point'])) {
      $_POST['camp_preorder_point'] = get_strip_campaign_info($_POST['camp_preorder_point']); 
      $campaign_query = tep_db_query("select * from ".TABLE_CAMPAIGN." where keyword = '".trim($_POST['camp_preorder_point'])."' and (site_id = '".SITE_ID."' or site_id = '0') and status = '1' and is_preorder = '1' and end_date >= '".date('Y-m-d', time())."' and start_date <= '".date('Y-m-d', time())."' and type = '2' order by site_id desc limit 1"); 
      $campaign_res = tep_db_fetch_array($campaign_query);
      if ($campaign_res) {
          $max_campaign_query = tep_db_query("select count(*) as total from ".TABLE_CUSTOMER_TO_CAMPAIGN." where customer_id = '".$customer_id."' and campaign_id = '".$campaign_res['id']."'"); 
          $max_campaign_res = tep_db_fetch_array($max_campaign_query);
          if ((int)$max_campaign_res['total'] < $campaign_res['max_use']) {
            $preorder_subtotal = 0; 
            $preorder_subtotal_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$preorder_res['orders_id']."' and class = 'ot_subtotal'");
            $preorder_subtotal_res = tep_db_fetch_array($preorder_subtotal_raw);
            if ($preorder_subtotal_res) {
              $preorder_subtotal = number_format($preorder_subtotal_res['value'], 0, '.', ''); 
            }
            if ($campaign_res['limit_value'] > $preorder_subtotal) {
              $_POST['preorder_campaign_id'] = $campaign_res['id'];
              $_POST['preorder_campaign_info'] = $campaign_res['keyword'];
            } else {
              $error = true;
              $point_error = TEXT_PREORDER_ERROR_CAMPAIGN;
            }
          } else {
            $error = true;
            $point_error = TEXT_PREORDER_ERROR_CAMPAIGN;
          }
      } else {
        $error = true;
        $point_error = TEXT_PREORDER_ERROR_CAMPAIGN;
      }
    }
   
    if ($hm_option->check()) {
      $error = true; 
    }
  }
