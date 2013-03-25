<?php
  require(DIR_WS_LANGUAGES . $language . '/change_preorder.php');
  require('address_preorder/AD_Option.php');
  require('address_preorder/AD_Option_Group.php');
  require(DIR_WS_CLASSES.'payment.php'); 
  $payment_modules = payment::getInstance(SITE_ID); 
  $error = false;  
  if (isset($_SESSION['preorder_information'])) {
    if (isset($_GET['ao_type'])) {
      foreach ($_SESSION['preorder_information'] as $ao_key => $ao_value) {
        $tmp_ao_single_str = substr($ao_key, 0, 3);
        if ($tmp_ao_single_str == 'op_') {
          unset($_SESSION['preorder_information'][$ao_key]); 
        }
      }
    }
  }
  $ad_option = new AD_Option();

  $preorder_raw = tep_db_query('select * from '.TABLE_PREORDERS." where check_preorder_str = '".$_GET['pid']."' and site_id = '".SITE_ID."' and is_active = '1'");
  $preorder_res = tep_db_fetch_array($preorder_raw); 
  if (!$preorder_res) {
    //判断该预约订单是否存在 
    forward404(); 
  }
  
  $customer_info_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$preorder_res['customers_id']."' and site_id = '".SITE_ID."'"); 
  $customer_info_res = tep_db_fetch_array($customer_info_raw);
  
  $is_member_single = 0;
  if ($customer_info_res['customers_guest_chk'] == '0') {
    $is_member_single = 1; 
  }
  
  if (!tep_session_is_registered('customer_id')) {
      //判断是否登录 
      if ($customer_info_res['customers_guest_chk'] == '0') {
        $navigation->set_snapshot();
        tep_redirect(tep_href_link(FILENAME_LOGIN, 'pid='.$_GET['pid'], 'SSL'));
      }
  } else {
    if ($is_member_single) { 
      if ($guestchk == '0') {
        if ($customer_emailaddress != $preorder_res['customers_email_address']) {
          //判断登录的邮箱是否和预约订单中的邮箱是否一致 
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
    //判断该预约订单的确保期限是否过期 
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

    //计算商品的总价格及总重量
  $shi_preorders_query = tep_db_query("select * from ".TABLE_PREORDERS." where check_preorder_str = '".$_GET['pid']."'");
  $shi_preorders_array = tep_db_fetch_array($shi_preorders_query);
  $shi_pid = $shi_preorders_array['orders_id'];
  tep_db_free_result($shi_preorders_query);
  $weight_count = 0; 
  $shi_products_query = tep_db_query("select * from ". TABLE_PREORDERS_PRODUCTS ." where orders_id='". $shi_pid ."'");
  while($shi_products_array = tep_db_fetch_array($shi_products_query)){

    $shi_products_weight_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id='". $shi_products_array['products_id'] ."'");
    $shi_products_weight_array = tep_db_fetch_array($shi_products_weight_query);
    tep_db_free_result($shi_products_weight_query);
    $weight_count += $shi_products_weight_array['products_weight']*$shi_products_array['products_quantity'];
  }
  tep_db_free_result($shi_products_query);
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
        $time_error = TEXT_PREORDER_ERROR_TIME;
      }
    
      if ($preorder_min == '') {
        $error = true;
        $time_error = TEXT_PREORDER_ERROR_TIME;
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
      //判断点数或者优惠券信息是否正确 
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
      //判断优惠券信息是否正确 
      $_POST['camp_preorder_point'] = get_strip_campaign_info($_POST['camp_preorder_point']); 
      $campaign_query = tep_db_query("select * from ".TABLE_CAMPAIGN." where keyword = '".trim($_POST['camp_preorder_point'])."' and (site_id = '".SITE_ID."' or site_id = '0') and status = '1' and is_preorder = '1' and end_date >= '".date('Y-m-d', time())."' and start_date <= '".date('Y-m-d', time())."' and type = '2' order by site_id desc limit 1"); 
      $campaign_res = tep_db_fetch_array($campaign_query);
      if ($campaign_res) {
          $max_campaign_query = tep_db_query("select count(*) as total from ".TABLE_CUSTOMER_TO_CAMPAIGN." where customer_id = '".$customer_id."' and campaign_id = '".$campaign_res['id']."'"); 
          $max_campaign_res = tep_db_fetch_array($max_campaign_query);
          if ((int)$max_campaign_res['total'] < $campaign_res['max_use']) {
            $preorder_subtotal = 0; 
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
      $preorder_information = array(); 
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
      tep_redirect(tep_href_link('change_preorder.php', 'pid='.$_GET['pid'].'&is_check=1'));
      }
    }
  }
