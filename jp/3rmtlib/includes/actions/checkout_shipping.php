<?php
require('address/AD_Option.php');
require('address/AD_Option_Group.php');

$hm_option = new AD_Option();
$check_before_pos = strpos($_SERVER['HTTP_REFERER'], 'login.php');
if ($check_before_pos !== false || !isset($_SERVER['HTTP_REFERER'])) {
  if ($cart->count_contents() > 0) {
    $c_products_list = $cart->get_products();  
    $check_op_single = false; 
    require('option/HM_Option.php'); 
    require('option/HM_Option_Group.php');
    $op_option = new HM_Option(); 
    foreach ($c_products_list as $ch_key => $ch_value) {
       $op_pro_raw = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".(int)$ch_value['id']."'"); 
       $op_pro_res = tep_db_fetch_array($op_pro_raw);
       if ($op_pro_res) {
         if (!empty($op_pro_res['belong_to_option'])) {
           if ($op_option->check_old_symbol_show($op_pro_res['belong_to_option'], true)) {
             $check_op_single = true;
             break;
           }
         }
       }
    }
    if ($check_op_single) {
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL'));
    } 
  }
}

if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  //ccdd
  $point_query = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
  $point = tep_db_fetch_array($point_query);
}
  
$error = false;
$campaign_error = false;
$campaign_error_str = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  unset($_SESSION['campaign_fee']);
  tep_session_unregister('hc_point');
  tep_session_unregister('hc_camp_point');
}

if (!empty($_POST['camp_point'])) {
  $_POST['camp_point'] = get_strip_campaign_info($_POST['camp_point']); 
  if ($cart->show_total() > 0) {
    $campaign_query = tep_db_query("select * from ".TABLE_CAMPAIGN." where keyword = '".trim($_POST['camp_point'])."' and (site_id = '".SITE_ID."' or site_id = '0') and status = '1' and end_date >= '".date('Y-m-d', time())."' and start_date <= '".date('Y-m-d', time())."' and type='1' order by site_id desc limit 1"); 
  } else if ($cart->show_total() < 0) {
    $campaign_query = tep_db_query("select * from ".TABLE_CAMPAIGN." where keyword = '".trim($_POST['camp_point'])."' and (site_id = '".SITE_ID."' or site_id = '0') and status = '1' and end_date >= '".date('Y-m-d', time())."' and start_date <= '".date('Y-m-d', time())."' and type='2' order by site_id desc limit 1"); 
  }
  if ($campaign_query) { 
  $campaign_res = tep_db_fetch_array($campaign_query); 
  if ($campaign_res) {
    if ($cart->show_total() > 0) {
      if ($cart->show_total() <= $campaign_res['limit_value']) {
        $campaign_error = true;
      } 
    } else {
      if ($cart->show_total() >= $campaign_res['limit_value']) {
        $campaign_error = true;
      } 
    }
    $max_campaign_query = tep_db_query("select count(*) as total from ".TABLE_CUSTOMER_TO_CAMPAIGN." where customer_id = '".$customer_id."' and campaign_id = '".$campaign_res['id']."'"); 
    $max_campaign_res = tep_db_fetch_array($max_campaign_query); 
    if ((int)$max_campaign_res['total'] >= $campaign_res['max_use']) {
        $campaign_error = true;
    }
    if (!$campaign_error) {
      if (isset($_POST['point'])) {
        $_POST['point'] = 0; 
      }
      $hc_camp_point = $_POST['camp_point'];
      tep_session_register('hc_camp_point');
      $percent_pos = strpos($campaign_res['point_value'], '%'); 
      if ($percent_pos !== false) {
        $campaign_fee = $order->info['subtotal']*substr($campaign_res['point_value'], 0, -1)/100; 
        if ($campaign_fee > 0) {
          $campaign_fee = 0 - $campaign_fee; 
        }
      } else {
        $campaign_fee = $campaign_res['point_value']; 
      }
      @eval("\$campaign_fee = (int)$campaign_fee;");
      if (!tep_session_is_registered('campaign_fee')) {
        tep_session_register('campaign_fee'); 
      }
      $camp_id = $campaign_res['id'];
      if (!tep_session_is_registered('camp_id')) {
        tep_session_register('camp_id'); 
      } 
    }
  } else {
    $campaign_error = true;
  }
  }
} else {
if (!empty($_POST['point'])) {
  $_POST['point'] = get_strip_campaign_info($_POST['point']); 
  if (preg_match('/^[0-9a-zA-Z]+$/', $_POST['point'])) {
    if (!preg_match('/^[0-9]+$/', $_POST['point'])) {
      $campaign_query = tep_db_query("select * from ".TABLE_CAMPAIGN." where keyword = '".trim($_POST['point'])."' and (site_id = '".SITE_ID."' or site_id = '0') and status = '1' and end_date >= '".date('Y-m-d', time())."' and start_date <= '".date('Y-m-d', time())."' order by site_id desc limit 1"); 
      
      $campaign_res = tep_db_fetch_array($campaign_query); 
      if ($campaign_res) {
        if ($campaign_res['type'] != '1') {
          $campaign_error = true;
        } else {
          if ($cart->show_total() <= $campaign_res['limit_value']) {
            $campaign_error = true;
          }
        }
         
        $max_campaign_query = tep_db_query("select count(*) as total from ".TABLE_CUSTOMER_TO_CAMPAIGN." where customer_id = '".$customer_id."' and campaign_id = '".$campaign_res['id']."'"); 
        $max_campaign_res = tep_db_fetch_array($max_campaign_query); 
        if ((int)$max_campaign_res['total'] >= $campaign_res['max_use']) {
            $campaign_error = true;
        } 
        
        if (!$campaign_error) {
          $hc_point = $_POST['point']; 
          tep_session_register('hc_point'); 
          $_POST['point'] = 0; 
          $percent_pos = strpos($campaign_res['point_value'], '%'); 
          if ($percent_pos !== false) {
            $campaign_fee = $order->info['subtotal']*substr($campaign_res['point_value'], 0, -1)/100; 
            if ($campaign_fee > 0) {
              $campaign_fee = 0 - $campaign_fee; 
            }
          } else {
            $campaign_fee = $campaign_res['point_value']; 
          }
          @eval("\$campaign_fee = (int)$campaign_fee;");
          if (!tep_session_is_registered('campaign_fee')) {
            tep_session_register('campaign_fee'); 
          } 
          $camp_id = $campaign_res['id'];
          if (!tep_session_is_registered('camp_id')) {
            tep_session_register('camp_id'); 
          } 
        }
      } else {
        $campaign_error = true;
      }
    } else {
      if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
        $cus_point_query = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
        $cus_point = tep_db_fetch_array($cus_point_query);
        if ($cus_point['point'] < $_POST['point']) {
          $campaign_error = true;
        }
      }
    }
  }
}
}

if ($campaign_error) {
  $campaign_error_str = isset($_POST['point'])?$_POST['point']:(isset($_POST['camp_point'])?$_POST['camp_point']:0);
}

if ($campaign_error) {
   $error = true;
}

if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') 
{
  $current_point_raw = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
  $current_point_res = tep_db_fetch_array($current_point_raw);
}
?>
