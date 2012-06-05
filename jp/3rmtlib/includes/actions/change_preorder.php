<?php
  require(DIR_WS_LANGUAGES . $language . '/change_preorder.php');
  require('address_preorder/AD_Option.php');
  require('address_preorder/AD_Option_Group.php');
  $error = false;  

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
  $n_option_info_array = array();

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
  
  if ($_POST['action'] == 'process' || isset($_GET['is_check'])) {
    if (tep_session_is_registered('preorder_information') && isset($_GET['is_check'])) {
      $_POST = $preorder_information; 
    }
     
    $preorder_date = tep_db_prepare_input($_POST['date']);
    $preorder_hour = tep_db_prepare_input($_POST['hour']);
    $preorder_min = tep_db_prepare_input($_POST['min']);
    $preorder_start_hour = tep_db_prepare_input($_POST['start_hour']);
    $preorder_start_min = tep_db_prepare_input($_POST['start_min']);
    $preorder_end_hour = tep_db_prepare_input($_POST['end_hour']);
    $preorder_end_min = tep_db_prepare_input($_POST['end_min']);

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
     
    if ($preorder_date == '') {
      $error = true; 
      $date_error = TEXT_PREORDER_ERROR_DATE; 
    }else{
    
      if ($preorder_hour == '') {
        $error = true;
        $jikan_error = TEXT_PREORDER_ERROR_JIKAN;
      }
    
      if ($preorder_min == '') {
        $error = true;
        $jikan_error = TEXT_PREORDER_ERROR_JIKAN;
      }
    }
   
    foreach ($_POST as $po_key => $po_value) {
        $po_single_str = substr($po_key, 0, 3);
        if ($po_single_str == 'op_') {
          $po_tmp_value = str_replace(' ', '', $po_value);
          $po_tmp_value = str_replace('　', '', $po_value);
          
          if ($po_tmp_value != '') {
            $n_option_info_array[$po_key] = $po_value; 
          }
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
            //$preorder_subtotal_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$preorder_res['orders_id']."' and class = 'ot_subtotal'");
            //$preorder_subtotal_res = tep_db_fetch_array($preorder_subtotal_raw);
            //if ($preorder_subtotal_res) {
              //$preorder_subtotal = number_format($preorder_subtotal_res['value'], 0, '.', ''); 
            //}
            $preorder_total_info_array = get_preorder_total_info(payment::changeRomaji($preorder_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $preorder_res['orders_id'], $n_option_info_array);  
            $preorder_subtotal = $preorder_total_info_array['subtotal'];            
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
            //$preorder_subtotal_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$preorder_res['orders_id']."' and class = 'ot_subtotal'");
            //$preorder_subtotal_res = tep_db_fetch_array($preorder_subtotal_raw);
            //if ($preorder_subtotal_res) {
              //$preorder_subtotal = number_format($preorder_subtotal_res['value'], 0, '.', ''); 
            //}
            $preorder_total_info_array = get_preorder_total_info(payment::changeRomaji($preorder_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $preorder_res['orders_id'], $n_option_info_array);  
            $preorder_subtotal = $preorder_total_info_array['subtotal'];            
            
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
    
    if ($error == true) {
      if (!isset($_GET['is_check'])) {
      foreach ($_POST as $post_e_key => $post_e_value) {
        if (is_array($post_e_value)) {
          foreach ($post_e_value as $ps_e_key => $ps_e_value) {
            $preorder_information[$post_e_key][$ps_e_key] = $ps_e_value; 
          }
        } else {
          $preorder_information[$post_e_key] = stripslashes($post_e_value); 
        }
      }
      $preorder_information['pid'] = $preorder_id; 
      if (!tep_session_is_registered('preorder_information')) {
        tep_session_register('preorder_information'); 
      }
      //tep_redirect(tep_href_link('change_preorder_handle.php', 'pid='.$_GET['pid'], 'SSL')); 
      tep_redirect(tep_href_link('change_preorder.php', 'pid='.$_GET['pid'].'&is_check=1', 'SSL'));
      }
    }
  }
