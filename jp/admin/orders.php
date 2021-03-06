<?php
/*
   $Id$
 */
require('includes/application_top.php');
include(DIR_FS_ADMIN . DIR_WS_LANGUAGES .  '/default.php');
require_once(DIR_WS_CLASSES . 'payment.php');
if (isset($_GET['keywords'])) {
  $_GET['keywords'] = tep_db_prepare_input($_GET['keywords']);
}
// action ajax order 
if ($_POST['orders_id'] && ($_POST['orders_comment']||$_POST['orders_comment_flag']=='true') && $_POST['action']=='ajax_orders') {
  // update orders_comment
  tep_db_perform('orders', array('orders_comment' => $_POST['orders_comment']), 'update', "orders_id='".$_POST['orders_id']."'");
  tep_redirect(tep_href_link(FILENAME_ORDERS,'page='.$_POST['page'].'&oID='.$_POST['orders_id'].'&action=edit'));
}
/* -----------------------------------------------------
   功能: 显示订单的详细信息 
   参数: $orders_id(string) 订单id 
   返回值: 弹出的订单信息(string) 
 -----------------------------------------------------*/
function tep_show_orders_products_info($orders_id) {
  $str = '';

  $orders_info_raw = tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".$orders_id."'"); 
  $orders = tep_db_fetch_array($orders_info_raw);

  if (!$orders) {
    return $str; 
  }

  $str .= '<table id="infoBox_01" border="0" cellpadding="0" cellspacing="0">';
  $str .= '<tr><td class="main" colspan="2">&nbsp;</td><tr>';
  if (ORDER_INFO_TRANS_NOTICE == 'true') {
    if ($orders['orders_care_flag']) {
      $str .= '<tr>'; 
      $str .= '<td class="main" colspan="2"><font color="red">';
      $str .= RIGHT_ORDER_INFO_TRANS_NOTICE; 
      $str .= '</font></td>'; 
      $str .= '</tr>'; 
      $str .= '<tr><td colspan="2"><hr></td></tr>'; 
    }
  }

  if (ORDER_INFO_TRANS_WAIT == 'true') {
    if ($orders['orders_wait_flag']) {
      $str .= '<tr>'; 
      $str .= '<td class="main" colspan="2"><font color="red">';
      $str .= '<b>';
      $str .= RIGHT_ORDER_INFO_TRANS_WAIT; 
      $str .= '</b>';
      $str .= '</font></td>'; 
      $str .= '</tr>'; 
      $str .= '<tr><td colspan="2"><hr></td></tr>'; 
    } 
  }

  if (ORDER_INFO_INPUT_FINISH == 'true') {
    if ($orders['orders_inputed_flag']) {
      $str .= '<tr>'; 
      $str .= '<td class="main" colspan="2"><font color="red">';
      $str .= '<b>';
      $str .= RIGHT_ORDER_INFO_INPUT_FINISH; 
      $str .= '</b>';
      $str .= '</font></td>'; 
      $str .= '</tr>'; 
      $str .= '<tr><td colspan="2"><hr></td></tr>'; 
    } 
  }
  if(ORDER_INFO_BASIC_TEXT == 'true'){
    $str .= '<tr>';
    $str .= '<td class="main"><b>';
    $str .= TEXT_FUNCTION_HEADING_CUSTOMERS;
    $str .= '</b></td>';
    $str .= '<td class="main"><b>';
    $str .= tep_output_string_protected($orders['customers_name']); 
    $str .= '</b></td>';
    $str .= '</tr>';
    $str .= '<tr><td colspan="2"><hr></td></tr>'; 
  }
  $show_payment_method =
    payment::changeRomaji($orders['payment_method'],'title');
  $str .= '<tr><td class="main"
    width="70"><b>'.TEXT_FUNCTION_PAYMENT_METHOD.'</b></td><td class="main"
    style="color:darkred;"><b>'.$show_payment_method.'</b></td></tr>';
  if ($orders['confirm_payment_time'] != '0000-00-00 00:00:00') {
    $time_str = date(TEXT_FUNCTION_DATE_STRING, strtotime($orders['confirm_payment_time'])); 
  }else if(tep_check_order_type($orders['orders_id'])!=2){
    $time_str = TEXT_NO_RECEIVABLES; 
  }
  if(isset($time_str)&&$time_str){
    $str .= '<tr><td class="main"><b>'.TEXT_FUNCTION_UN_GIVE_MONY_DAY.'</b></td><td class="main" style="color:red;"><b>'.$time_str.'</b></td></tr>';
  }
  $str .= '<tr><td class="main"><b>'.TEXT_FUNCTION_OPTION.'</b></td><td class="main" style="color:blue;"><b>'.$orders['torihiki_houhou'].'</b></td></tr>';

  $orders_products_query = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS." op,".TABLE_PRODUCTS." p where p.products_id = op.products_id and op.orders_id = '".$orders['orders_id']."'");
  $autocalculate_arr = array();
  $autocalculate_sql = "select oaf.value as arr_str from ".TABLE_OA_FORMVALUE." oaf,".
    TABLE_OA_ITEM." oai 
    where oaf.item_id = oai.id 
    and oai.type = 'autocalculate' 
    and oaf.orders_id = '".$orders['orders_id']."' 
    order by oaf.id asc limit 1 ";
  $autocalculate_query = tep_db_query($autocalculate_sql);
  $autocalculate_row = tep_db_fetch_array($autocalculate_query);
  $arr_checked = explode('_',$autocalculate_row['arr_str']);
  $autocalculate_arr = array();
  foreach($arr_checked as $key=>$value){
    $temp_arr = explode('|',$value);
    if($temp_arr[0] != 0 && $temp_arr[3] != 0){
      $autocalculate_arr[] = array($temp_arr[0],$temp_arr[3]);
    }
  }
  $tmpArr = array();
  if (ORDER_INFO_PRODUCT_LIST == 'true') { 
    while ($p = tep_db_fetch_array($orders_products_query)) {
      if(in_array($p,$tmpArr)){
        continue;
      }
      $tmpArr[] = $p ;
      $products_attributes_query = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id='".$p['orders_products_id']."'");
      if(in_array(array($p['products_id'],$p['orders_products_id']),$autocalculate_arr)&&
          !empty($autocalculate_arr)){
        $str .= '<tr><td class="main"><b>'.TEXT_FUNCTION_CATEGORY.'</b><font
          color="red">'.TEXT_FUNCTION_FINISH.'</font></td><td class="main">'.$p['products_name'].'</td></tr>';
      }else{
        $str .= '<tr><td class="main"><b>'.TEXT_FUNCTION_CATEGORY.'</b><font
          color="red">'.TEXT_FUNCTION_UNFINISH.'</font></td><td class="main">'.$p['products_name'].'</td></tr>';
      }
      $str .= '<tr><td class="main"><b>'.TEXT_FUNCTION_NUMBER.'</b></td><td
        class="main">'.$p['products_quantity'].TEXT_FUNCTION_NUM.tep_get_full_count2($p['products_quantity'], $p['products_id'], $p['products_rate']).'</td></tr>';
      while($pa = tep_db_fetch_array($products_attributes_query)){
        $str .= '<tr><td class="main"><b>'.$pa['products_options'].'：</b></td><td class="main">'.$pa['products_options_values'].'</td></tr>';
      }
      $names = tep_get_buttons_names_by_orders_id($orders['orders_id']);
      if ($names) {
        $str .= '<tr><td class="main"><b>PC：</b></td><td class="main">'.implode('&nbsp;,&nbsp;', $names).'</td></tr>';
      }
      $str .= '<tr><td class="main"></td><td class="main"></td></tr>';
    }
    $str .= '<tr><td colspan="2"><hr></td></tr>'; 
  }


  if (ORDER_INFO_ORDER_INFO == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_FROM.'</b></td>'; 
    $str .= '<td class="main">';
    $str .= tep_get_site_name_by_order_id($orders['orders_id']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_FETCH_TIME.'</b></td>';
    $str .= '<td class="main">';
    $str .= str_replace('/','<br>',$orders['torihiki_date']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_OPTION.'</b></td>';
    $str .= '<td class="main">';
    $str .= $orders['torihiki_houhou'];    
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_ID.'</b></td>';
    $str .= '<td class="main">';
    $str .= $orders['orders_id']; 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.TEXT_FUNCTION_ORDER_ORDER_DATE.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_date_long($orders['date_purchased']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_CUSTOMER_TYPE.'</b></td>';
    $str .= '<td class="main">';
    if(get_guest_chk($orders['customers_id'])==0){
      $str .= TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_MEMBER;
    }else{
      $str .= TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_CUSTOMER;
    }
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_CUSTOMER_NAME.'</b></td>';
    $str .= '<td class="main">';
    $str .= '<a href="">'.$orders['customers_name'].'</a>'; 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    if(defined(constant("SITE_TOPIC_".$orders['site_id']))){
      $t_topicid = constant("SITE_TOPIC_".$orders['site_id']);
    }else{
      $t_topicid = '';
    }
    $ostGetPara = array( "name"=>urlencode($orders['customers_name']),
        "topicid"=>urlencode($t_topicid),
        "source"=>urlencode('Email'), 
        "email"=>urlencode($orders['customers_email_address']));
    $parmStr = '';
    foreach($ostGetPara as $key=>$value){
      $parmStr.= '&'.$key.'='.$value; 
    }
    $remoteurl = (defined('OST_SERVER')?OST_SERVER:'scp')."/tickets.php?a=open2".$parmStr."";

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_EMAIL.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_output_string_protected($orders['customers_email_address']).'&nbsp;&nbsp;<a title="'.RIGHT_TICKIT_ID_TITLE.'" href="'.$remoteurl.'" target="_blank">'.RIGHT_TICKIT_EMAIL.'</a>&nbsp;&nbsp;<a href="telecom_unknow.php?keywords='.tep_output_string_protected($orders['customers_email_address']).'">'.RIGHT_TICKIT_CARD.'</a>'; 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    if ( (($orders['cc_type']) || ($orders['cc_owner']) || ($orders['cc_number'])) ) {  
      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_TYPE.'</b></td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_type']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 

      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_OWNER.'</b></td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_owner']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 

      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_ID.'</b></td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_number']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 

      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_EXPIRE_TIME.'</b></td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_expires']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
    } 
    $str .= '<tr><td colspan="2"><hr></td></tr>'; 
  }
  if (ORDER_INFO_CUSTOMER_INFO == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_IP.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_ip'] ?  $orders['orders_ip'] : 'UNKNOW',IP_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_HOST.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_host_name']?'<font'.($orders['orders_host_name'] == $orders['orders_ip'] ? ' color="red"':'').'>'.$orders['orders_host_name'].'</font>':'UNKNOW',HOST_NAME_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_USER_AGEMT.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_user_agent'] ?  $orders['orders_user_agent'] : 'UNKNOW',USER_AGENT_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    if ($orders['orders_user_agent']) { 
      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_OS.'</b></td>';
      $str .= '<td class="main">';
      $str .= tep_high_light_by_keywords(getOS($orders['orders_user_agent']),OS_LIGHT_KEYWORDS); 
      $str .= '</td>'; 
      $str .= '</tr>'; 

      $browser_info = getBrowserInfo($orders['orders_user_agent']); 
      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_BROWSE_TYPE.'</b></td>';
      $str .= '<td class="main">';
      $str .= tep_high_light_by_keywords($browser_info['longName'] . ' ' .  $browser_info['version'],BROWSER_LIGHT_KEYWORDS); 
      $str .= '</td>'; 
      $str .= '</tr>'; 
    } 
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_BROWSE_LAN.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_http_accept_language'] ?  $orders['orders_http_accept_language'] : 'UNKNOW',HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_COMPUTER_LAN.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_system_language'] ?  $orders['orders_system_language'] : 'UNKNOW',SYSTEM_LANGUAGE_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_USER_LAN.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_user_language'] ?  $orders['orders_user_language'] : 'UNKNOW',USER_LANGUAGE_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_PIXEL.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_screen_resolution'] ?  $orders['orders_screen_resolution'] : 'UNKNOW',SCREEN_RESOLUTION_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_COLOR.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_color_depth'] ?  $orders['orders_color_depth'] : 'UNKNOW',COLOR_DEPTH_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_FLASH.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_flash_enable'] === '1' ?  'YES' : ($orders['orders_flash_enable'] === '0' ? 'NO' : 'UNKNOW'),FLASH_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    if ($orders['orders_flash_enable']) {
      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_FLASH_VERSION.'</b></td>';
      $str .= '<td class="main">';
      $str .= tep_high_light_by_keywords($orders['orders_flash_version'],FLASH_VERSION_LIGHT_KEYWORDS); 
      $str .= '</td>'; 
      $str .= '</tr>'; 
    }
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_DIRECTOR.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_director_enable'] === '1' ? 'YES' : ($orders['orders_director_enable'] === '0' ? 'NO' : 'UNKNOW'),DIRECTOR_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_QUICK_TIME.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_quicktime_enable'] === '1' ? 'YES' : ($orders['orders_quicktime_enable'] === '0' ? 'NO' : 'UNKNOW'),QUICK_TIME_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_REAL_PLAYER.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_realplayer_enable'] === '1' ?  'YES' : ($orders['orders_realplayer_enable'] === '0' ? 'NO' : 'UNKNOW'),REAL_PLAYER_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_WINDOWS_MEDIA.'</b></td>';
    $str .= '<td class="main">'; $str .= tep_high_light_by_keywords($orders['orders_windows_media_enable'] === '1' ? 'YES' : ($orders['orders_windows_media_enable'] === '0' ?  'NO' : 'UNKNOW'),WINDOWS_MEDIA_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_PDF.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_pdf_enable'] === '1' ?  'YES' : ($orders['orders_pdf_enable'] === '0' ? 'NO' : 'UNKNOW'),PDF_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_JAVA.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_java_enable'] === '1' ?  'YES' : ($orders['orders_java_enable'] === '0' ? 'NO' : 'UNKNOW'),JAVA_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    $str .= '<tr><td colspan="2"><hr></td></tr>'; 
  }

  if (ORDER_INFO_REFERER_INFO == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>Referer Info：</b></td>';
    $str .= '<td class="main">';
    $str .= mb_convert_encoding(urldecode($orders['orders_ref']),'utf-8'); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    if ($orders['orders_ref_keywords']) {
      $str .= '<tr>'; 
      $str .= '<td class="main"><b>KEYWORDS：</b></td>';
      $str .= '<td class="main">';
      $str .= $orders['orders_ref_keywords']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
    }
    $str .= '<tr><td colspan="2"><hr></td></tr>'; 
  }

  if (ORDER_INFO_ORDER_HISTORY == 'true') {
    $order_history_list_raw = tep_db_query("select * from ".TABLE_ORDERS." where customers_email_address = '".$orders['customers_email_address']."' order by date_purchased desc limit 5"); 
    if (tep_db_num_rows($order_history_list_raw)) {
      $str .= '<tr>';      
      $str .= '<td class="main" colspan="2">';      
      $str .= '<table width="100%" border="0" cellspacing="0" cellpadding="2">'; 
      $str .= '<tr>'; 
      $str .= '<td colspan="4"><b>Order History：</b></td>'; 
      $str .= '</tr>'; 
      while ($order_history_list = tep_db_fetch_array($order_history_list_raw)) {
        $str .= '<tr>'; 
        $str .= '<td>'; 
        $store_name_raw = tep_db_query("select * from ".TABLE_SITES." where id = '".$order_history_list['site_id']."'"); 
        $store_name_res = tep_db_fetch_array($store_name_raw); 
        $str .= $store_name_res['romaji']; 
        $str .= '</td>'; 
        $str .= '<td>'; 
        $str .= $order_history_list['date_purchased']; 
        $str .= '</td>'; 
        $str .= '<td>'; 
        $str .= strip_tags(tep_get_ot_total_by_orders_id($order_history_list['orders_id'], true)); 
        $str .= '</td>'; 
        $str .= '<td>'; 
        $str .= $order_history_list['orders_status_name']; 
        $str .= '</td>'; 
        $str .= '</tr>'; 
      }
      $str .= '</table>'; 
      $str .= '</td>';      
      $str .= '</tr>';      
      $str .= '<tr><td colspan="2"><hr></td></tr>'; 
    }
  }

  if (ORDER_INFO_REPUTAION_SEARCH == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">';
    $str .= '<b>'.RIGHT_ORDER_INFO_REPUTAION_SEARCH.'</b>'; 
    $str .= '</td>';
    $str .= '<td class="main">';
    $str .= tep_get_customers_fax_by_id($orders['customers_id']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    $str .= '<tr><td colspan="2"><hr></td></tr>'; 
  }

  if (ORDER_INFO_ORDER_COMMENT == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">';
    $str .= '<b>'.RIGHT_ORDER_COMMENT_TITLE.'</b>'; 
    $str .= '</td>';
    $str .= '<td class="main">';
    $str .= nl2br($orders['orders_comment']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    $str .= '<tr><td colspan="2"><hr></td></tr>'; 
  }
  $str .= '<tr><td class="main" colspan="2">&nbsp;</td><tr>';
  $str .= '</table>';
  $str=str_replace("\n","",$str);
  $str=str_replace("\r","",$str);
  return $str;
  return htmlspecialchars($str);
}
require_once('oa/HM_Form.php'); 
require_once('oa/HM_Group.php'); 
require(DIR_WS_FUNCTIONS . 'visites.php');
require(DIR_WS_CLASSES . 'currencies.php');
$currencies          = new currencies(2);
$orders_statuses     = $all_orders_statuses = $orders_status_array = array();
$all_payment_method = payment::getPaymentList(2);
$all_search_status = array(); 
$orders_status_query = tep_db_query("select orders_status_id, orders_status_name, is_reorder from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");

while ($orders_status = tep_db_fetch_array($orders_status_query)) {
  if ( $orders_status['is_reorder'] != 1)
    $orders_statuses[] = array('id' => $orders_status['orders_status_id'],'text' => $orders_status['orders_status_name']);

  $all_orders_statuses[] = array('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
  $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
  $all_search_status[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
}

if (isset($_GET['action'])){ 
switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'sele_act' 给选择的订单更新状态并发送邮件 
   case 'update_order' 更新订单相关信息并发送邮件 
   case 'deleteconfirm' 删除订单 
------------------------------------------------------*/
  case 'sele_act':
    if($_POST['chk'] == ""){
      $_SESSION['error_orders_status'] = WARNING_ORDER_NOT_UPDATED; 
      $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
      tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))));
    }
    $update_user_info = tep_get_user_info($ocertify->auth_user);
    if (empty($_POST['status']) || empty($update_user_info['name'])) {
      $_SESSION['error_orders_status'] = WARNING_LOSING_INFO_TEXT; 
      $messageStack->add_session(WARNING_LOSING_INFO_TEXT, 'warning');
      tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))));
    }
    foreach($_POST['chk'] as $value){
      $oID      = $value;
      $status   = tep_db_prepare_input($_POST['status']);
      $title    = tep_db_prepare_input($_POST['os_title']);
      $comments = tep_db_prepare_input($_POST['comments']);
      $site_id  = tep_get_site_id_by_orders_id($value);

      $order_updated = false;
      $check_status_query = tep_db_query("select customers_name, customers_id, customers_email_address, orders_status, date_purchased, site_id,payment_method, torihiki_date,  torihiki_date_end,code_fee,shipping_fee from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
      $check_status = tep_db_fetch_array($check_status_query);

       
      $cpayment = payment::getInstance($check_status['site_id']);
      //Add Point System
      if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && MODULE_ORDER_TOTAL_POINT_ADD_STATUS != '0') {
        $pcount_query = tep_db_query("select count(*) as cnt from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".MODULE_ORDER_TOTAL_POINT_ADD_STATUS."' and orders_id = '".$oID."'");
        $pcount = tep_db_fetch_array($pcount_query);
        if($pcount['cnt'] == 0 && $status == MODULE_ORDER_TOTAL_POINT_ADD_STATUS) {
          $query1  = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '".$oID."'");
          $result1 = tep_db_fetch_array($query1);
          $query2  = tep_db_query("select value from " . TABLE_ORDERS_TOTAL." where class = 'ot_point' and orders_id = '".tep_db_input($oID)."'");
          $result2 = tep_db_fetch_array($query2);
          $query3  = tep_db_query("select value from " . TABLE_ORDERS_TOTAL." where class = 'ot_subtotal' and orders_id = '".tep_db_input($oID)."'");
          $result3 = tep_db_fetch_array($query3);
          $query4  = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '".$result1['customers_id']."'");
          $result4 = tep_db_fetch_array($query4);

          // 计算各个不同顾客的返点率从这开始============================================================
          if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
            $customer_id = $result1['customers_id'];
            //规定期间内，计算订单合计金额------------
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
            //这次的订单金额除外
            $total_buyed_date = $total_buyed_date - ($result3['value'] - (int)$result2['value']);

            //计算返点率----------------------------------
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
            $point_rate = $back_rate;
          } else {
            $point_rate = MODULE_ORDER_TOTAL_POINT_FEE;
          }
          // 计算各个不同顾客的返点率到此结束============================================================

          if ($result3['value'] >= 0) {
            $get_point = ($result3['value'] - (int)$result2['value']) * $point_rate;
          } else {
            $get_point = $cpayment->admin_get_fetch_point(payment::changeRomaji($check_status['payment_method'],'code'),$result3['value']);
          }
        } else {
          $os_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
          $os_result = tep_db_fetch_array($os_query);
          if($os_result['orders_status_name']==TEXT_PAYMENT_NOTICE){
            $query1 = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '".$oID."'");
            $result1 = tep_db_fetch_array($query1);
            $get_point = $cpayment->admin_get_orders_point(payment::changeRomaji($check_status['payment_method'],'code'),$oID);
            $point_done_query =tep_db_query("select count(orders_status_history_id) cnt from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".$status."' and orders_id = '".tep_db_input($oID)."'");
            $point_done_row  =  tep_db_fetch_array($point_done_query);
          }
        }
      }   

      if ($check_status['orders_status'] != $status || $comments != '') {
        tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");
        orders_updated(tep_db_input($oID));
        orders_wait_flag(tep_db_input($oID));

        $customer_notified = '0';

        if ($_POST['notify'] == 'on') {
          //发送邮件
          $ot_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_total'");
          $ot_result = tep_db_fetch_array($ot_query);
          $otm = (int)$ot_result['value'] . TEXT_YEN;

          $point_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_point'");
          $point_result = tep_db_fetch_array($point_query);
          $point_value = (int)$point_result['value'];

          $os_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
          $os_result = tep_db_fetch_array($os_query);
          $title = str_replace(array(
                '${USER_NAME}',
                '${USER_MAIL}',
                '${ORDER_DATE}',
                '${ORDER_NUMBER}',
                '${PAYMENT}',
                '${ORDER_TOTAL}',
                '${ORDER_STATUS}',
                '${SITE_NAME}',
                '${SITE_URL}',
                '${PAY_DATE}'

                ),array(
                  $check_status['customers_name'],
                  $check_status['customers_email_address'],
                  tep_date_long($check_status['date_purchased']),
                  $oID,
                  $check_status['payment_method'],
                  $otm,
                  $os_result['orders_status_name'],
                  get_configuration_by_site_id('STORE_NAME', $site_id),
                  get_url_by_site_id($site_id),
                  date('Y'.TEXT_ORDER_YEAR.'n'.TEXT_ORDER_MONTH.'j'.TEXT_ORDER_DAY,strtotime(tep_get_pay_day()))
                  ),$title
                );
          $comments = str_replace(array(
                '${USER_NAME}',
                '${USER_MAIL}',
                '${ORDER_DATE}',
                '${ORDER_NUMBER}',
                '${PAYMENT}',
                '${ORDER_TOTAL}',
                '${ORDER_STATUS}',
                '${SITE_NAME}',
                '${SITE_URL}',
                '${SUPPORT_MAIL}',
                '${PAY_DATE}',
                '${COMMISSION}',
                '${SHIPPING_FEE}',
                '${ORDER_COMMENT}',
                '${SHIPPING_METHOD}',
                '${POINT}',
                '${TOTAL}',
                ),array(
                  $check_status['customers_name'],
                  $check_status['customers_email_address'],
                  tep_date_long($check_status['date_purchased']),
                  $oID,
                  $check_status['payment_method'],
                  $otm,
                  $os_result['orders_status_name'],
                  get_configuration_by_site_id('STORE_NAME', $site_id),
                  get_url_by_site_id($site_id),
                  get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $site_id),
                  date('Y'.SENDMAIL_TEXT_DATE_YEAR.'n'.SENDMAIL_TEXT_DATE_MONTH.'j'.SENDMAIL_TEXT_DATE_DAY,strtotime(tep_get_pay_day())),
                  $check_status['code_fee'],
                  $check_status['shipping_fee'],
                  '',
                  '',
                  $point_value, 
                  str_replace(TEXT_YEN,'',$otm),
                  ),$comments
                );
          $products_ordered_mail = '';
          $order_pro_array = array(); 
          $search_products_id_list = array();
          $mode_products_name_list = array();
          $order_pro_list_query = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS.  " where orders_id = '".$oID."'"); 
          while ($order_pro_list_res = tep_db_fetch_array($order_pro_list_query)) {
             $search_products_id_list[] = $order_pro_list_res['products_id'];
             $mode_products_name_list[] = $order_pro_list_res['products_name'];
             $order_pro_attr_list_raw = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$oID."' and orders_products_id = '".$order_pro_list_res['orders_products_id']."'"); 
             $max_c_len = 0;
             $max_len_array = array();
             $attr_list_array = array(); 
             while ($order_pro_attr_list_res = tep_db_fetch_array($order_pro_attr_list_raw)) {
               $attr_info_str = @unserialize(stripslashes($order_pro_attr_list_res['option_info'])); 
               $max_len_array[] = mb_strlen($attr_info_str['title'], 'utf-8'); 
               $attr_list_array[] = $order_pro_attr_list_res; 
             }
             if (!empty($max_len_array)) {
               $max_c_len = max($max_len_array); 
             }
             if ($max_c_len < 4) {
               $max_c_len = 4; 
             }
             
             $products_ordered_mail .= ORDERS_PRODUCTS.str_repeat('　', intval($max_c_len - mb_strlen(ORDERS_PRODUCTS, 'utf-8'))).'：' .  $order_pro_list_res['products_name'] . '（' .  $order_pro_list_res['products_model'] . '）';
             if ($order_pro_list_res['products_price'] != '0') {
               $products_ordered_mail .= '（'.$currencies->display_price($order_pro_list_res['products_price'], $order_pro_list_res['products_tax']).'）'; 
             }
             
             $products_ordered_mail .= "\n"; 
             if (!empty($attr_list_array)) {
               foreach ($attr_list_array as $at_key => $at_value) {
                 $em_attr_info = @unserialize(stripslashes($at_value['option_info'])); 
                 $products_ordered_mail .=  $em_attr_info['title'] . str_repeat('　', intval($max_c_len - mb_strlen($em_attr_info['title'], 'utf-8'))).'：';
                 $products_ordered_mail .= str_replace(array("<br>", "<BR>", "\r", "\n", "\r\n"), "", $em_attr_info['value']);
                 if ($at_value['options_values_price'] != '0') {
                  $products_ordered_mail .= '（'.$currencies->format($at_value['options_values_price']).'）'; 
                 }
                 $products_ordered_mail .= "\n"; 
               }
             }
             $products_ordered_mail .= SENDMAIL_QTY_NUM.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_QTY_NUM, 'utf-8'))).'：' .  $order_pro_list_res['products_quantity']. SENDMAIL_EDIT_ORDERS_NUM_UNIT .  tep_get_full_count2($order_pro_list_res['products_quantity'], $order_pro_list_res['products_id']) . "\n";
             $products_ordered_mail .= SENDMAIL_TABLE_HEADING_PRODUCTS_PRICE.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_TABLE_HEADING_PRODUCTS_PRICE, 'utf-8'))).'：' .  $currencies->display_price($order_pro_list_res['final_price'], $order_pro_list_res['products_tax']) . "\n";
             $products_ordered_mail .= str_replace(':', '',SENDMAIL_ENTRY_SUB_TOTAL).str_repeat('　', intval($max_c_len - mb_strlen(str_replace(':', '', SENDMAIL_ENTRY_SUB_TOTAL), 'utf-8'))).'：' .  $currencies->display_price($order_pro_list_res['final_price'], $order_pro_list_res['products_tax'], $order_pro_list_res['products_quantity']) . "\n";
             $products_ordered_mail .= '------------------------------------------' . "\n";
          }
          
          $total_details_mail = '';
          $totals_query = tep_db_query("select * from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . tep_db_input($oID) . "' order by sort_order");
          $order->totals = array();
          while ($totals = tep_db_fetch_array($totals_query)) {
            if ($totals['class'] == "ot_point" || $totals['class'] == "ot_subtotal") {
              if ($totals['class'] == "ot_point") {
                $camp_exists_query = tep_db_query("select * from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$oID."' and site_id = '".$site_id."'"); 
                if (tep_db_num_rows($camp_exists_query)) {
                  $total_details_mail .= SENDMAIL_TEXT_POINT . $currencies->format(abs($campaign_fee)) . "\n";
                } else {
                  if ((int)$totals['value'] >= 1 && $totals['class'] != "ot_subtotal") {
                    $total_details_mail .= SENDMAIL_TEXT_POINT .  $currencies->format($totals['value']) . "\n";
                  }
                }
              } else {
                if ((int)$totals['value'] >= 1 && $totals['class'] != "ot_subtotal") {
                  $total_details_mail .= SENDMAIL_TEXT_POINT .  $currencies->format($totals['value']) . "\n";
                }
              }
            } elseif ($totals['class'] == "ot_total") {
              if($handle_fee)
                $total_details_mail .= SENDMAIL_TEXT_HANDLE_FEE.$currencies->format($handle_fee)."\n";
              $total_details_mail .= SENDMAIL_TEXT_PAYMENT_AMOUNT . $currencies->format($totals['value']);
            } else {
              $totals['title'] = str_replace(SENDMAIL_TEXT_TRANSACTION_FEE, SENDMAIL_TEXT_REPLACE_HANDLE_FEE, $totals['title']);
              $total_details_mail .= $totals['title'] . str_repeat('　', intval((16 - strlen($totals['title']))/2)) . '：' . $currencies->format($totals['value']) . "\n";
              $totals_email_str .= $totals['title'] . str_repeat('　', intval((16 - strlen($totals['title']))/2)) . '：' . $currencies->format($totals['value']) . "\n"; 
            }
          }
          
          
          $email_content = $products_ordered_mail;
          $email_content .= $total_details_mail;
          $comments = str_replace('${CONTENT}', $email_content, $comments);
          $comments = str_replace('${ORDER_PRODUCTS}', $products_ordered_mail, $comments);
          //自定义费用
          if($totals_email_str != ''){
            $comments = str_replace('${CUSTOMIZED_FEE}'."\r\n",str_replace('▼','',$totals_email_str), $comments);
          }else{
            $comments = str_replace("\r\n".'${CUSTOMIZED_FEE}','', $comments); 
            $comments = str_replace('${CUSTOMIZED_FEE}','', $comments);
          }
          //address
          $option_info_array = array();
          $address_query = tep_db_query("select name,value from ". TABLE_ADDRESS_ORDERS ." where orders_id = '".$oID."' and billing_address='0' order by id");
          while($address_array = tep_db_fetch_array($address_query)){
          
            $option_info_array[$address_array['name']] = $address_array['value']; 
          }
          tep_db_free_result($address_query);
            if(isset($option_info_array) && !empty($option_info_array)){
              $address_len_array = array();
              foreach($option_info_array as $address_value){

                $address_len_array[] = strlen($address_value);
              }
              $maxlen = max($address_len_array);
              $email_address_str = "";
              $email_address_str .= '------------------------------------------'."\n";
              $maxlen = 9;
              foreach($option_info_array as $ad_key=>$ad_value){
                $ad_name_query = tep_db_query("select name from ". TABLE_ADDRESS ." where name_flag='". $ad_key ."'");
                $ad_name_array = tep_db_fetch_array($ad_name_query);
                tep_db_free_result($ad_name_query);
                $ad_len = mb_strlen($ad_name_array['name'],'utf8');
                $temp_str = str_repeat('　',$maxlen-$ad_len);
                $email_address_str .= $ad_name_array['name'].$temp_str.'：'.$ad_value."\n";
              }
              $email_address_str .= '------------------------------------------'."\n";
            }
          //住所
          if($email_address_str != ''){
            $comments = str_replace('${USER_ADDRESS}',str_replace('▼','',$email_address_str), $comments);
          }else{
            $comments = str_replace("\n".'${USER_ADDRESS}','', $comments); 
            $comments = str_replace('${USER_ADDRESS}','', $comments);
          }
          
          $fetch_time_start_array = explode(' ', $check_status['torihiki_date']); 
          $fetch_time_end_array = explode(' ', $check_status['torihiki_date_end']); 
          $tmp_date = date('D', strtotime($check_status['torihiki_date'])); 
          switch(strtolower($tmp_date)) {
            case 'mon':
             $week_str = '（'.SENDMAIL_TEXT_DATE_MONDAY.'）'; 
             break;
            case 'tue':
             $week_str =  '（'.SENDMAIL_TEXT_DATE_TUESDAY.'）'; 
             break;
            case 'wed':
             $week_str =  '（'.SENDMAIL_TEXT_DATE_WEDNESDAY.'）'; 
             break;
           case 'thu':
             $week_str =  '（'.SENDMAIL_TEXT_DATE_THURSDAY.'）'; 
             break;
           case 'fri':
             $week_str =  '（'.SENDMAIL_TEXT_DATE_FRIDAY.'）'; 
             break;
           case 'sat':
             $week_str =  '（'.SENDMAIL_TEXT_DATE_STATURDAY.'）'; 
             break;
           case 'sun':
             $week_str =  '（'.SENDMAIL_TEXT_DATE_SUNDAY.'）'; 
             break;
           default:
             break;
          }
          $fetch_time_str = date('Y'.SENDMAIL_TEXT_DATE_YEAR.'m'.SENDMAIL_TEXT_DATE_MONTH.'d'.SENDMAIL_TEXT_DATE_DAY, strtotime($check_status['torihiki_date'])).$week_str.$fetch_time_start_array[1].' '.SENDMAIL_TEXT_TIME_LINK.' '.$fetch_time_end_array[1]; 
          
          $comments = str_replace('${SHIPPING_TIME}', $fetch_time_str, $comments); 
          $title = str_replace('${SHIPPING_TIME}', $fetch_time_str, $title); 
          $customer_info_raw = tep_db_query("select is_send_mail from ".TABLE_CUSTOMERS." where customers_id = '".$check_status['customers_id']."'"); 
          $customer_info_res = tep_db_fetch_array($customer_info_raw); 
          $search_products_name_list = array();
          foreach($search_products_id_list as $products_name_value){
            $search_products_name_query = tep_db_query("select products_name from ". TABLE_PRODUCTS_DESCRIPTION ." where products_id='".$products_name_value."' and language_id='".$languages_id."' and (site_id='".$site_id."' or site_id='0') order by site_id DESC");
            $search_products_name_array = tep_db_fetch_array($search_products_name_query);
            tep_db_free_result($search_products_name_query);
            $search_products_name_list[] = $search_products_name_array['products_name'];
          }
          $comments = tep_replace_mail_templates($comments,$check_status['customers_email_address'],$check_status['customers_name'],$site_id);
          $comments = html_entity_decode(htmlspecialchars($comments));
          if ($customer_info_res['is_send_mail'] != '1') {
            tep_mail($check_status['customers_name'], $check_status['customers_email_address'], $title, str_replace($mode_products_name_list,$search_products_name_list,$comments), get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $site_id), $site_id);
          } 
          tep_mail(get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('SENTMAIL_ADDRESS', $site_id), $title, $comments, $check_status['customers_name'], $check_status['customers_email_address'], $site_id);
          $customer_notified = '1';
        }


        if($_POST['notify_comments'] == 'on') {
          $customer_notified = '1';
        } else {
          $customer_notified = '0';
        }
      //增加销售处理
      $orders_status_flag = false;
      $orders_status_history_flag = false;
      $orders_oa_flag = false;
      $end_orders_status_flag = false;
      $status_list_array = array();
      $orders_status_finish_query = tep_db_query("select orders_status_id,finished from ". TABLE_ORDERS_STATUS);
      while($orders_status_finish_array = tep_db_fetch_array($orders_status_finish_query)){

        $status_list_array[$orders_status_finish_array['orders_status_id']] = $orders_status_finish_array['finished'];
      }
      tep_db_free_result($orders_status_finish_query);
      $orders_status_flag = $status_list_array[tep_db_input($status)] == 1 ? true : $orders_status_flag;
      $orders_status_history_list_query = tep_db_query("select orders_status_id from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".tep_db_input($oID)."'");
      while($orders_status_history_list_array = tep_db_fetch_array($orders_status_history_list_query)){

        if($status_list_array[$orders_status_history_list_array['orders_status_id']] == 1){

          $orders_status_history_flag = true;
          break;
        }
      }
      tep_db_free_result($orders_status_history_list_query);

      $orders_oa_flag = tep_orders_finishqa(tep_db_input($oID)) == 1 ? true : $orders_oa_flag;

      //获取最后一次订单状态
      $orders_status_id_query = tep_db_query("select orders_status_id from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".tep_db_input($oID)."' order by date_added desc limit 0,1");
      $orders_status_id_array = tep_db_fetch_array($orders_status_id_query);
      tep_db_free_result($orders_status_id_query);
      $end_orders_status_flag = $status_list_array[$orders_status_id_array['orders_status_id']] == 1 ? true : $end_orders_status_flag;

      if($orders_oa_flag == true && $orders_status_flag == true && ($orders_status_history_flag == false || $end_orders_status_flag == false)){

        $orders_products_query = tep_db_query("select products_id,products_quantity from ". TABLE_ORDERS_PRODUCTS ." where orders_id='".tep_db_input($oID)."'");
        while($orders_products_array = tep_db_fetch_array($orders_products_query)){
          tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . sprintf('%d', $orders_products_array['products_quantity']) . " where products_id = '" . (int)$orders_products_array['products_id'] . "'");
        }
        tep_db_free_result($orders_products_query);
      }

      if($orders_oa_flag == true && $orders_status_history_flag == true && $orders_status_flag == false && $end_orders_status_flag == true){

        $orders_products_query = tep_db_query("select products_id,products_quantity from ". TABLE_ORDERS_PRODUCTS ." where orders_id='".tep_db_input($oID)."'");
        while($orders_products_array = tep_db_fetch_array($orders_products_query)){
          tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered - " . sprintf('%d', $orders_products_array['products_quantity']) . " where products_id = '" . (int)$orders_products_array['products_id'] . "'");
        }
        tep_db_free_result($orders_products_query);    
      }

        //获取订单最后一次备注信息
        $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".tep_db_input($oID)."' order by date_added desc limit 0,1");
        $orders_status_history_array = tep_db_fetch_array($orders_status_history_query);
        tep_db_free_result($orders_status_history_query);
        $sql_data_array = array('last_modified' => 'now()','user_update' => $_SESSION['user_name']);
        tep_db_perform(TABLE_ORDERS, $sql_data_array, 'update', "orders_id='".$oID."'");
        $sql_data_array = array('orders_id' => tep_db_input($oID),
                              'orders_status_id' => tep_db_input($status),
                              'date_added' => 'now()',
                              'customer_notified' => $customer_notified,
                              'comments' => $orders_status_history_array['comments'],
                              'user_added' => $update_user_info['name']
          );
        tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
        $order_updated = true;

      }

      if ($order_updated) {
        $messageStack->add_session(TEXT_ORDERS_ID . $oID . TEXT_OF . SUCCESS_ORDER_UPDATED, 'success');
      } else {
        $messageStack->add_session(TEXT_ORDERS_ID . $oID . TEXT_OF . WARNING_ORDER_NOT_UPDATED, 'warning');
      }
      tep_order_status_change($oID,$status);
    }
    tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))));


    break;
  case 'update_order':
    $update_user_info = tep_get_user_info($ocertify->auth_user);
    if (empty($_POST['s_status']) || empty($update_user_info['name'])) {
      $_SESSION['error_orders_status'] = WARNING_LOSING_INFO_TEXT; 
      $messageStack->add_session(WARNING_LOSING_INFO_TEXT, 'warning');
      tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=edit'));
    }
    $oID      = tep_db_prepare_input($_GET['oID']);
    $status   = tep_db_prepare_input($_POST['s_status']);
    $title    = tep_db_prepare_input($_POST['title']);
    $comments = tep_db_prepare_input($_POST['comments']);
    $site_id  = tep_get_site_id_by_orders_id($oID);
    $order_updated = false;
    $check_status_query = tep_db_query("
        select site_id, orders_id, 
        customers_name, 
        customers_id,
        customers_email_address, 
        orders_status, 
        date_purchased, 
        payment_method, 
        torihiki_date,
        torihiki_date_end,
        code_fee,
        shipping_fee
        from " . TABLE_ORDERS . " 
        where orders_id = '" . tep_db_input($oID) . "'");
    $check_status = tep_db_fetch_array($check_status_query);
    $cpayment = payment::getInstance($check_status['site_id']);
    //oa start 如果状态发生改变，找到当前的订单的
    tep_order_status_change($oID,$status);
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



        // 计算各个不同顾客的返点率从这开始============================================================
        if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
          $customer_id = $result1['customers_id'];
          //规定期间内，计算订单合计金额------------
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
          //这次的订单金额除外
          $total_buyed_date = $total_buyed_date - ($result3['value'] - (int)$result2['value']);

          //计算返点率----------------------------------
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
          $point_rate = $back_rate;
        } else {
          $point_rate = MODULE_ORDER_TOTAL_POINT_FEE;
        }
        // 计算各个不同顾客的返点率到此结束============================================================
        if ($result3['value'] >= 0) {
          $get_point = ($result3['value'] - (int)$result2['value']) * $point_rate;
        } else {
          $get_point = $cpayment->admin_get_fetch_point(payment::changeRomaji($check_status['payment_method'],'code'),$result3['value']);
        }

      }else{
        $os_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
        $os_result = tep_db_fetch_array($os_query);
        if($os_result['orders_status_name']==TEXT_PAYMENT_NOTICE){
          $query1 = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '".$oID."'");
          $result1 = tep_db_fetch_array($query1);

          $get_point = $cpayment->admin_get_orders_point(payment::changeRomaji($check_status['payment_method'],'code'),$oID);
          
          $point_done_query =tep_db_query("select count(orders_status_history_id) cnt from
              ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".$status."' and 
              orders_id = '".tep_db_input($oID)."'");
          $point_done_row  =  tep_db_fetch_array($point_done_query);
        }
      }
    }

    if ($check_status['orders_status'] != $status || $comments != '') {
      tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");
      orders_updated(tep_db_input($oID));
      orders_wait_flag(tep_db_input($oID));
      $customer_notified = '0';

      if ($_POST['notify'] == 'on') {
        //发送邮件
        $ot_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_total'");
        $ot_result = tep_db_fetch_array($ot_query);
        $otm = (int)$ot_result['value'] . TEXT_YEN;

        $point_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_point'");
        $point_result = tep_db_fetch_array($point_query);
        $point_value = (int)$point_result['value'];

        $os_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
        $os_result = tep_db_fetch_array($os_query);

        $title = str_replace(array(
              '${USER_NAME}',
              '${USER_MAIL}',
              '${ORDER_DATE}',
              '${ORDER_NUMBER}',
              '${PAYMENT}',
              '${ORDER_TOTAL}',
              '${ORDER_STATUS}',
              '${SITE_NAME}',
              '${SITE_URL}',
              '${SUPPORT_MAIL}',
              '${PAY_DATE}'
              ),array(
                $check_status['customers_name'],
                $check_status['customers_email_address'],
                tep_date_long($check_status['date_purchased']),
                $oID,
                $check_status['payment_method'],
                $otm,
                $os_result['orders_status_name'],
                get_configuration_by_site_id('STORE_NAME', $site_id),
                get_url_by_site_id($site_id),
                get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $site_id),
                date('Y'.SENDMAIL_TEXT_DATE_YEAR.'n'.SENDMAIL_TEXT_DATE_MONTH.'j'.SENDMAIL_TEXT_DATE_DAY,strtotime(tep_get_pay_day()))
                ),$title);

        $comments = str_replace(array(
              '${USER_NAME}',
              '${USER_MAIL}',
              '${ORDER_DATE}',
              '${ORDER_NUMBER}',
              '${PAYMENT}',
              '${ORDER_TOTAL}',
              '${ORDER_STATUS}',
              '${SITE_NAME}',
              '${SITE_URL}',
              '${SUPPORT_MAIL}',
              '${PAY_DATE}',
              '${MAIL_COMMENT}',
              '${COMMISSION}',
              '${SHIPPING_FEE}',
              '${ORDER_COMMENT}',
              '${SHIPPING_METHOD}',
              '${POINT}',
              '${TOTAL}',
              ),array(
                $check_status['customers_name'],
                $check_status['customers_email_address'],
                tep_date_long($check_status['date_purchased']),
                $oID,
                $check_status['payment_method'],
                $otm,
                $os_result['orders_status_name'],
                get_configuration_by_site_id('STORE_NAME', $site_id),
                get_url_by_site_id($site_id),
                get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $site_id),
                date('Y'.SENDMAIL_TEXT_DATE_YEAR.'n'.SENDMAIL_TEXT_DATE_MONTH.'j'.SENDMAIL_TEXT_DATE_DAY,strtotime(tep_get_pay_day())),
                orders_a($oID),
                $check_status['code_fee'],
                $check_status['shipping_fee'],
                '',
                '',
                $point_value,
                str_replace(TEXT_YEN,'',$otm),
                ),$comments);
          
        $products_ordered_mail = '';
        $order_pro_array = array(); 
        $search_products_id_list = array();
        $mode_products_name_list = array();
        $order_pro_list_query = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS.  " where orders_id = '".$oID."'"); 
        while ($order_pro_list_res = tep_db_fetch_array($order_pro_list_query)) {
           $search_products_id_list[] = $order_pro_list_res['products_id'];
           $mode_products_name_list[] = $order_pro_list_res['products_name'];
           $order_pro_attr_list_raw = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$oID."' and orders_products_id = '".$order_pro_list_res['orders_products_id']."'"); 
           $max_c_len = 0;
           $max_len_array = array();
           $attr_list_array = array(); 
           while ($order_pro_attr_list_res = tep_db_fetch_array($order_pro_attr_list_raw)) {
             $attr_info_str = @unserialize(stripslashes($order_pro_attr_list_res['option_info'])); 
             $max_len_array[] = mb_strlen($attr_info_str['title'], 'utf-8'); 
             $attr_list_array[] = $order_pro_attr_list_res; 
           }
           if (!empty($max_len_array)) {
             $max_c_len = max($max_len_array); 
           }
           if ($max_c_len < 4) {
             $max_c_len = 4; 
           }
           
           $products_ordered_mail .= SENDMAIL_ORDERS_PRODUCTS.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_ORDERS_PRODUCTS, 'utf-8'))).'：' .  $order_pro_list_res['products_name'] . '（' .  $order_pro_list_res['products_model'] . '）';
           if ($order_pro_list_res['products_price'] != '0') {
             $products_ordered_mail .= '（'.$currencies->display_price($order_pro_list_res['products_price'], $order_pro_list_res['products_tax']).'）'; 
           }
           
           $products_ordered_mail .= "\n"; 
           if (!empty($attr_list_array)) {
             foreach ($attr_list_array as $at_key => $at_value) {
               $em_attr_info = @unserialize(stripslashes($at_value['option_info'])); 
               $products_ordered_mail .=  $em_attr_info['title'] . str_repeat('　', intval($max_c_len - mb_strlen($em_attr_info['title'], 'utf-8'))).'：';
               $products_ordered_mail .= str_replace(array("<br>", "<BR>", "\r", "\n", "\r\n"), "", $em_attr_info['value']);
               if ($at_value['options_values_price'] != '0') {
                $products_ordered_mail .= '（'.$currencies->format($at_value['options_values_price']).'）'; 
               }
               $products_ordered_mail .= "\n"; 
             }
           }
          
           $products_ordered_mail .= SENDMAIL_QTY_NUM.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_QTY_NUM, 'utf-8'))).'：' .
           $order_pro_list_res['products_quantity']. SENDMAIL_EDIT_ORDERS_NUM_UNIT. tep_get_full_count2($order_pro_list_res['products_quantity'], $order_pro_list_res['products_id']) . "\n";
           $products_ordered_mail .= SENDMAIL_TABLE_HEADING_PRODUCTS_PRICE.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_TABLE_HEADING_PRODUCTS_PRICE, 'utf-8'))).'：' .  $currencies->display_price($order_pro_list_res['final_price'], $order_pro_list_res['products_tax']) . "\n";
           $products_ordered_mail .= str_replace(':', '', SENDMAIL_ENTRY_SUB_TOTAL).str_repeat('　', intval($max_c_len - mb_strlen(str_replace(':', '', SENDMAIL_ENTRY_SUB_TOTAL), 'utf-8'))).'：' .  $currencies->display_price($order_pro_list_res['final_price'], $order_pro_list_res['products_tax'], $order_pro_list_res['products_quantity']) . "\n";
           $products_ordered_mail .= '------------------------------------------' . "\n";
        }
        
        $total_details_mail = '';
        $totals_query = tep_db_query("select * from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . tep_db_input($oID) . "' order by sort_order");
        $order->totals = array();
        while ($totals = tep_db_fetch_array($totals_query)) {
          if ($totals['class'] == "ot_point" || $totals['class'] == "ot_subtotal") {
            if ($totals['class'] == "ot_point") {
              $camp_exists_query = tep_db_query("select * from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$oID."' and site_id = '".$site_id."'"); 
              if (tep_db_num_rows($camp_exists_query)) {
                $total_details_mail .= SENDMAIL_TEXT_POINT . $currencies->format(abs($campaign_fee)) . "\n";
              } else {
                if ((int)$totals['value'] >= 1 && $totals['class'] != "ot_subtotal") {
                  $total_details_mail .= SENDMAIL_TEXT_POINT .  $currencies->format($totals['value']) . "\n";
                }
              }
            } else {
              if ((int)$totals['value'] >= 1 && $totals['class'] != "ot_subtotal") {
                $total_details_mail .= SENDMAIL_TEXT_POINT .  $currencies->format($totals['value']) . "\n";
              }
            }
          } elseif ($totals['class'] == "ot_total") {
            if($handle_fee)
              $total_details_mail .= SENDMAIL_TEXT_HANDLE_FEE.$currencies->format($handle_fee)."\n";
            $total_details_mail .= SENDMAIL_TEXT_PAYMENT_AMOUNT . $currencies->format($totals['value']);
          } else {
            $totals['title'] = str_replace(SENDMAIL_TEXT_TRANSACTION_FEE, SENDMAIL_TEXT_REPLACE_HANDLE_FEE, $totals['title']);
            $total_details_mail .= $totals['title'] . str_repeat('　', intval((16 - strlen($totals['title']))/2)) . '：' . $currencies->format($totals['value']) . "\n";
            $totals_email_str .= $totals['title'] . str_repeat('　', intval((16 - strlen($totals['title']))/2)) . '：' . $currencies->format($totals['value']) . "\n";
          }
        }
        
        
        $email_content  = $products_ordered_mail;
        $email_content .= $total_details_mail;
        $comments = str_replace('${CONTENT}', $email_content, $comments);  
        $comments = str_replace('${ORDER_PRODUCTS}', $products_ordered_mail, $comments);
          //自定义费用
          if($totals_email_str != ''){
            $comments = str_replace('${CUSTOMIZED_FEE}'."\r\n",str_replace('▼','',$totals_email_str), $comments);
          }else{
            $comments = str_replace("\r\n".'${CUSTOMIZED_FEE}','', $comments); 
            $comments = str_replace('${CUSTOMIZED_FEE}','', $comments);
          }
          //address
          $option_info_array = array();
          $address_query = tep_db_query("select name,value from ". TABLE_ADDRESS_ORDERS ." where orders_id = '".$oID."' and billing_address='0' order by id");
          while($address_array = tep_db_fetch_array($address_query)){
          
            $option_info_array[$address_array['name']] = $address_array['value']; 
          }
          tep_db_free_result($address_query);
            if(isset($option_info_array) && !empty($option_info_array)){
              $address_len_array = array();
              foreach($option_info_array as $address_value){

                $address_len_array[] = strlen($address_value);
              }
              $maxlen = max($address_len_array);
              $email_address_str = "";
              $email_address_str .= '------------------------------------------'."\n";
              $maxlen = 9;
              foreach($option_info_array as $ad_key=>$ad_value){
                $ad_name_query = tep_db_query("select name from ". TABLE_ADDRESS ." where name_flag='". $ad_key ."'");
                $ad_name_array = tep_db_fetch_array($ad_name_query);
                tep_db_free_result($ad_name_query);
                $ad_len = mb_strlen($ad_name_array['name'],'utf8');
                $temp_str = str_repeat('　',$maxlen-$ad_len);
                $email_address_str .= $ad_name_array['name'].$temp_str.'：'.$ad_value."\n";
              }
              $email_address_str .= '------------------------------------------'."\n";
            }
          //住所
          if($email_address_str != ''){
            $comments = str_replace('${USER_ADDRESS}',str_replace('▼','',$email_address_str), $comments);
          }else{
            $comments = str_replace("\n".'${USER_ADDRESS}','', $comments); 
            $comments = str_replace('${USER_ADDRESS}','', $comments);
          }
        
        $fetch_time_start_array = explode(' ', $check_status['torihiki_date']); 
        $fetch_time_end_array = explode(' ', $check_status['torihiki_date_end']); 
        $tmp_date = date('D', strtotime($check_status['torihiki_date'])); 
        switch(strtolower($tmp_date)) {
          case 'mon':
           $week_str = '（'.SENDMAIL_TEXT_DATE_MONDAY.'）'; 
           break;
          case 'tue':
           $week_str =  '（'.SENDMAIL_TEXT_DATE_TUESDAY.'）'; 
           break;
          case 'wed':
           $week_str =  '（'.SENDMAIL_TEXT_DATE_WEDNESDAY.'）'; 
           break;
         case 'thu':
           $week_str =  '（'.SENDMAIL_TEXT_DATE_THURSDAY.'）'; 
           break;
         case 'fri':
           $week_str =  '（'.SENDMAIL_TEXT_DATE_FRIDAY.'）'; 
           break;
         case 'sat':
           $week_str =  '（'.SENDMAIL_TEXT_DATE_STATURDAY.'）'; 
           break;
         case 'sun':
           $week_str =  '（'.SENDMAIL_TEXT_DATE_SUNDAY.'）'; 
           break;
         default:
           break;
        }
        $fetch_time_str = date('Y'.SENDMAIL_TEXT_DATE_YEAR.'m'.SENDMAIL_TEXT_DATE_MONTH.'d'.SENDMAIL_TEXT_DATE_DAY, strtotime($check_status['torihiki_date'])).$week_str.$fetch_time_start_array[1].' '.SENDMAIL_TEXT_TIME_LINK.' '.$fetch_time_end_array[1];
        
        $comments = str_replace('${SHIPPING_TIME}', $fetch_time_str, $comments); 
        $title = str_replace('${SHIPPING_TIME}', $fetch_time_str, $title); 
        $customer_info_raw = tep_db_query("select is_send_mail from ".TABLE_CUSTOMERS." where customers_id = '".$check_status['customers_id']."'"); 
        $customer_info_res = tep_db_fetch_array($customer_info_raw); 
        $search_products_name_list = array();
        foreach($search_products_id_list as $products_name_value){
          $search_products_name_query = tep_db_query("select products_name from ". TABLE_PRODUCTS_DESCRIPTION ." where products_id='".$products_name_value."' and language_id='".$languages_id."' and (site_id='".$site_id."' or site_id='0') order by site_id DESC");
          $search_products_name_array = tep_db_fetch_array($search_products_name_query);
          tep_db_free_result($search_products_name_query);
          $search_products_name_list[] = $search_products_name_array['products_name'];
        }
        $comments = tep_replace_mail_templates($comments,$check_status['customers_email_address'],$check_status['customers_name'],$site_id);
        $comments = html_entity_decode(htmlspecialchars($comments));
        if ($customer_info_res['is_send_mail'] != '1') {
          tep_mail($check_status['customers_name'], $check_status['customers_email_address'], $title, str_replace($mode_products_name_list,$search_products_name_list,$comments), get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $site_id), $site_id);
        }
        tep_mail(get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('SENTMAIL_ADDRESS', $site_id), $title, $comments, $check_status['customers_name'], $check_status['customers_email_address'], $site_id);
        $customer_notified = '1';
      }


      if($_POST['notify_comments'] == 'on') {
        $customer_notified = '1';
      } else {
        $customer_notified = '0';
      }
      //增加销售处理
      $orders_status_flag = false;
      $orders_status_history_flag = false;
      $orders_oa_flag = false;
      $end_orders_status_flag = false;
      $status_list_array = array();
      $orders_status_finish_query = tep_db_query("select orders_status_id,finished from ". TABLE_ORDERS_STATUS);
      while($orders_status_finish_array = tep_db_fetch_array($orders_status_finish_query)){

        $status_list_array[$orders_status_finish_array['orders_status_id']] = $orders_status_finish_array['finished'];
      }
      tep_db_free_result($orders_status_finish_query);
      $orders_status_flag = $status_list_array[tep_db_input($status)] == 1 ? true : $orders_status_flag;
      $orders_status_history_list_query = tep_db_query("select orders_status_id from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".tep_db_input($oID)."'");
      while($orders_status_history_list_array = tep_db_fetch_array($orders_status_history_list_query)){

        if($status_list_array[$orders_status_history_list_array['orders_status_id']] == 1){

          $orders_status_history_flag = true;
          break;
        }
      }
      tep_db_free_result($orders_status_history_list_query);

      $orders_oa_flag = tep_orders_finishqa(tep_db_input($oID)) == 1 ? true : $orders_oa_flag;

      //获取最后一次订单状态
      $orders_status_id_query = tep_db_query("select orders_status_id from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".tep_db_input($oID)."' order by date_added desc limit 0,1");
      $orders_status_id_array = tep_db_fetch_array($orders_status_id_query);
      tep_db_free_result($orders_status_id_query);
      $end_orders_status_flag = $status_list_array[$orders_status_id_array['orders_status_id']] == 1 ? true : $end_orders_status_flag;

      if($orders_oa_flag == true && $orders_status_flag == true && ($orders_status_history_flag == false || $end_orders_status_flag == false)){

        $orders_products_query = tep_db_query("select products_id,products_quantity from ". TABLE_ORDERS_PRODUCTS ." where orders_id='".tep_db_input($oID)."'");
        while($orders_products_array = tep_db_fetch_array($orders_products_query)){
          tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . sprintf('%d', $orders_products_array['products_quantity']) . " where products_id = '" . (int)$orders_products_array['products_id'] . "'");
        }
        tep_db_free_result($orders_products_query);
      }

      if($orders_oa_flag == true && $orders_status_history_flag == true && $orders_status_flag == false && $end_orders_status_flag == true){

        $orders_products_query = tep_db_query("select products_id,products_quantity from ". TABLE_ORDERS_PRODUCTS ." where orders_id='".tep_db_input($oID)."'");
        while($orders_products_array = tep_db_fetch_array($orders_products_query)){
          tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered - " . sprintf('%d', $orders_products_array['products_quantity']) . " where products_id = '" . (int)$orders_products_array['products_id'] . "'");
        }
        tep_db_free_result($orders_products_query);    
      }

      //获取订单最后一次备注信息
      $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".tep_db_input($oID)."' order by date_added desc limit 0,1");
      $orders_status_history_array = tep_db_fetch_array($orders_status_history_query);
      tep_db_free_result($orders_status_history_query);
      $sql_data_array = array('last_modified' => 'now()','user_update' => $_SESSION['user_name']);
      tep_db_perform(TABLE_ORDERS, $sql_data_array, 'update', "orders_id='".$oID."'");
      $sql_data_array = array('orders_id' => tep_db_input($oID),
                              'orders_status_id' => tep_db_input($status),
                              'date_added' => 'now()',
                              'customer_notified' => $customer_notified,
                              'comments' => $orders_status_history_array['comments'],
                              'user_added' => $update_user_info['name']
          );
      tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
      // 同步问答
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


    tep_remove_order($oID, $_POST['restock']);

    tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action'))));
    break;
}
}

if ( isset($_GET['action']) && ($_GET['action'] == 'edit') && ($_GET['oID']) ) {
  $oID = tep_db_prepare_input($_GET['oID']); 
  $orders_query = tep_db_query("
      select orders_id 
      from " . TABLE_ORDERS . " 
      where orders_id = '" . tep_db_input($oID) . "'");
  $order_exists = true;
  //判断订单是否存在 
  if (!tep_db_num_rows($orders_query)) {
    $order_exists = false;
    $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
  }
}

include(DIR_WS_CLASSES . 'order.php');

$suu = 0;
$text_suu = 0;  
$__orders_status_query = tep_db_query("
    select orders_status_id 
    from " . TABLE_ORDERS_STATUS . " 
    where language_id = " . $languages_id . " 
    order by orders_status_id");
$__orders_status_ids   = array();
if(isset($_GET['action'])&&$_GET['action']=='edit'){
}else{
  $image_name_list = array();
  $image_alt_list = array();
  $c_image_sql = "select count(id) as con from ".TABLE_CUSTOMERS_PIC_LIST;
  $c_image_query = tep_db_query($c_image_sql);
  if($c_image_raw = tep_db_fetch_array($c_image_query)){
    if($c_image_raw['con'] < MAX_DISPLAY_SEARCH_RESULTS){
      $c_image_list_sql = "select pic_name,pic_alt from ".TABLE_CUSTOMERS_PIC_LIST;
      $c_image_list_query = tep_db_query($c_image_list_sql);
      while($c_image_list_raw = tep_db_fetch_array($c_image_list_query)){
        $image_name_list[] = $c_image_list_raw['pic_name']; 
        $image_alt_list[] = $c_image_list_raw['pic_alt'];
      }
    }
  }
  $_SESSION['c_image_list'] = array();
  $_SESSION['c_image_list']['name'] = $image_name_list;
  $_SESSION['c_image_list']['alt'] = $image_alt_list;
}

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
if ( isset($_GET['action']) && ($_GET['action'] == 'edit') && ($order_exists) ) {
}else{
#{{
  $where_no_error = ' and(o.torihiki_date is not null and 
      o.date_purchased is not null and 
      o.torihiki_date != "" and 
      o.date_purchased != "" and 
      o.torihiki_date != "0000-00-00 00:00:00" and 
      o.date_purchased != "0000-00-00 00:00:00" 
      )';
  $where_type = '';
  $where_payment = '';
  $from_payment = '';
  $sort_table = '';
  $sort_where = '';
  if(isset($_GET['keyword'])||isset($_GET['search_type'])){
    $order_str = ''; 
  }else{
    $order_str = ''; 
  }
  $user_info = tep_get_user_info($ocertify->auth_user);
  $sort_setting_flag = false;
  $is_show_transaction = false; 
  if (PERSONAL_SETTING_TRANSACTION_FINISH != '') {
    $show_transaction_array = @unserialize(PERSONAL_SETTING_TRANSACTION_FINISH);  
    if (isset($show_transaction_array[$ocertify->auth_user])) {
      if ($show_transaction_array[$ocertify->auth_user] == '1') {
        $is_show_transaction = true; 
      }
    }
  }
  if(PERSONAL_SETTING_ORDERS_SORT != ''){
    $sort_list_array = array("0"=>"site_romaji",
                             "1"=>"customers_name",
                             "2"=>"ot_total",
                             "3"=>"torihiki_date",
                             "4"=>"date_purchased",
                             "5"=>"orders_status_name"
                           );
    $sort_type_array = array("0"=>"asc",
                             "1"=>"desc"
                           );
    $sort_array = array();
    $sort_setting_array = unserialize(PERSONAL_SETTING_ORDERS_SORT);
    if(array_key_exists($user_info['name'],$sort_setting_array)){
      $sort_setting_str = $sort_setting_array[$user_info['name']]; 
      $sort_array = explode('|',$sort_setting_str);
      $orders_sort = $sort_list_array[$sort_array[0]];
      $orders_type = $sort_type_array[$sort_array[1]];
    }else{
      $sort_setting_flag = true; 
    } 
  }
  if(!isset($HTTP_GET_VARS['order_sort'])||$HTTP_GET_VARS['order_sort']=='') {
    if(PERSONAL_SETTING_ORDERS_SORT == '' || $sort_setting_flag == true){
      $order_str .= 'o.torihiki_date DESC';
    }else{
      if($orders_sort == 'site_romaji'){
        $sort_table = " ,".TABLE_SITES." s ";
        $sort_where = " o.site_id = s.id and ";
        $order_str .= " s.romaji ".$orders_type;
      }else if($orders_sort == 'customers_name'){
        $order_str .= " o.customers_name ".$orders_type;
      }else if($orders_sort == 'ot_total'){
        $sort_table = " ,". TABLE_ORDERS_TOTAL." ot ";
        $sort_where = " o.orders_id = ot.orders_id and ot.class  ='ot_total' and ";
        $order_str .= " ot.value ".$orders_type;
      }else if($orders_sort == 'torihiki_date'){
        $order_str .= " o.torihiki_date ".$orders_type;
      }else if($orders_sort == 'date_purchased'){
        $order_str .= " o.date_purchased ".$orders_type;
      }else if($orders_sort == 'orders_status_name'){
        $order_str .= " o.orders_status_name ".$orders_type;
      }
    }
  }else{
    if($HTTP_GET_VARS['order_sort'] == 'site_romaji'){
      $sort_table = " ,".TABLE_SITES." s ";
      $sort_where = " o.site_id = s.id and ";
      $order_str .= " s.romaji ".$HTTP_GET_VARS['order_type'];
    }else if($HTTP_GET_VARS['order_sort'] == 'customers_name'){
      $order_str .= " o.customers_name ".$HTTP_GET_VARS['order_type'];
    }else if($HTTP_GET_VARS['order_sort'] == 'ot_total'){
      $sort_table = " ,". TABLE_ORDERS_TOTAL." ot ";
      $sort_where = " o.orders_id = ot.orders_id and ot.class  ='ot_total' and ";
      $order_str .= " ot.value ".$HTTP_GET_VARS['order_type'];
    }else if($HTTP_GET_VARS['order_sort'] == 'torihiki_date'){
      $order_str .= " o.torihiki_date ".$HTTP_GET_VARS['order_type'];
    }else if($HTTP_GET_VARS['order_sort'] == 'date_purchased'){
      $order_str .= " o.date_purchased ".$HTTP_GET_VARS['order_type'];
    }else if($HTTP_GET_VARS['order_sort'] == 'orders_status_name'){
      $order_str .= " o.orders_status_name ".$HTTP_GET_VARS['order_type'];
    }
  }
  if ($HTTP_GET_VARS['order_type'] == 'asc') {
    $type_str = 'desc';
  }else{
    $type_str = 'asc';
  }
  $work_array = array();
  $work_default = '0|1|2|3|4';
  if(PERSONAL_SETTING_ORDERS_WORK != ''){
    $work_setting_array = unserialize(PERSONAL_SETTING_ORDERS_WORK);
    if(array_key_exists($user_info['name'],$work_setting_array)){

      $work_setting_str = $work_setting_array[$user_info['name']];
    }else{
      $work_setting_str = $work_default; 
    }
  }else{
    $work_setting_str = $work_default; 
  } 
  $work_array = explode('|',$work_setting_str); 
  $work_str = implode('-',$work_array);
  $mark_sql_str = ''; 
  if (isset($_GET['mark'])) { 
    $mark_info = explode('-', $_GET['mark']); 
    if (in_array('0', $mark_info)) {
      if (count($mark_info) == 1) {
        $mark_sql_str = "((o.orders_work is null) or (o.orders_work = ''))"; 
      } else {
        $mark_str = ''; 
        foreach ($mark_info as $m_key => $m_value) {
          if ($m_value == '1') {
            $mark_str .= '\'a\','; 
          } else if ($m_value == '2') {
            $mark_str .= '\'b\','; 
          } else if ($m_value == '3') {
            $mark_str .= '\'c\','; 
          } else if ($m_value == '4') {
            $mark_str .= '\'d\','; 
          } 
        }
        $mark_str = substr($mark_str, 0, -1);
        if($mark_str==''){
          $mark_sql_str = "((o.orders_work is null) or (o.orders_work = ''))";
        }else {
          $mark_sql_str = "((o.orders_work is null) or (o.orders_work = '') or (o.orders_work in (".$mark_str.")))"; 
        }
      }
    } else {
      $mark_str = ''; 
      foreach ($mark_info as $m_key => $m_value) {
        if ($m_value == '1') {
          $mark_str .= '\'a\','; 
        } else if ($m_value == '2') {
          $mark_str .= '\'b\','; 
        } else if ($m_value == '3') {
          $mark_str .= '\'c\','; 
        } else if ($m_value == '4') {
          $mark_str .= '\'d\','; 
        }
      }
      $mark_str = substr($mark_str, 0, -1);
      if($mark_str==''){
        $mark_sql_str = '';
      }else {
        $mark_sql_str = "o.orders_work in (".$mark_str.")";
      } 
    }
  }else{
    $mark_info = explode('-', $work_str); 
    if (in_array('0', $mark_info)) {
      if (count($mark_info) == 1) {
         $mark_sql_str = "((o.orders_work is null) or (o.orders_work = ''))";
      } else {
        $mark_str = ''; 
        foreach ($mark_info as $m_key => $m_value) {
          if ($m_value == '1') {
            $mark_str .= '\'a\','; 
          } else if ($m_value == '2') {
            $mark_str .= '\'b\','; 
          } else if ($m_value == '3') {
            $mark_str .= '\'c\','; 
          } else if ($m_value == '4') {
            $mark_str .= '\'d\','; 
          } 
        }
        $mark_str = substr($mark_str, 0, -1);
        if($mark_str==''){
          $mark_sql_str = "((o.orders_work is null) or (o.orders_work = ''))";
        }else {
           $mark_sql_str = "((o.orders_work is null) or (o.orders_work = '') or (o.orders_work in (".$mark_str.")))"; 
        }
        }
    } else {
      $mark_str = ''; 
      foreach ($mark_info as $m_key => $m_value) {
        if ($m_value == '1') {
          $mark_str .= '\'a\','; 
        } else if ($m_value == '2') {
          $mark_str .= '\'b\','; 
        } else if ($m_value == '3') {
          $mark_str .= '\'c\','; 
        } else if ($m_value == '4') {
          $mark_str .= '\'d\','; 
        }
      }
      $mark_str = substr($mark_str, 0, -1);
      if($mark_str==''){
        $mark_sql_str = '';
      }else {
        $mark_sql_str = "o.orders_work in (".$mark_str.")"; 
      }
    } 
  } 
  if(!isset($_GET['site_id'])){ 
    $site_array = array();
    $orders_site_array = array();
    $orders_site_query = tep_db_query("select id from ". TABLE_SITES);
    while($orders_site_rows = tep_db_fetch_array($orders_site_query)){
      $orders_site_array[] = $orders_site_rows['id'];
    }
    tep_db_free_result($orders_site_query);
    $site_default = implode('|',$orders_site_array);
    if(PERSONAL_SETTING_ORDERS_SITE != ''){
      $site_setting_array = unserialize(PERSONAL_SETTING_ORDERS_SITE);
      if(array_key_exists($user_info['name'],$site_setting_array)){

        $site_setting_str = $site_setting_array[$user_info['name']];
      }else{
        $site_setting_str = $site_default; 
      } 
    }else{

      $site_setting_str = $site_default;
    }
    $site_array = explode('|',$site_setting_str);
    $site_list_str = implode(',',$site_array);
  }else{
      $site_array = array();
      $site_array = explode('-',$_GET['site_id']);
      $site_list_str = implode(',',$site_array);
  }
  $site_list_string = implode('-',$site_array);
  if (isset($_GET['cEmail']) && $_GET['cEmail']) {
    //邮件查询 
    $cEmail = tep_db_prepare_input($_GET['cEmail']);
    $orders_query_raw = "
      select distinct o.orders_id, 
             o.torihiki_date, 
             IF( o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
               as torihiki_date_error,
             IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
               as date_purchased_error,
             o.customers_name, 
             o.customers_id, 
             o.payment_method, 
             o.date_purchased, 
             o.last_modified, 
             o.currency, 
             o.currency_value, 
             o.orders_status, 
             o.orders_status_name, 
             o.orders_important_flag,
             o.orders_care_flag,
             o.orders_wait_flag,
             o.orders_inputed_flag,
             o.orders_work,
             o.customers_email_address,
             o.orders_comment,
             o.torihiki_houhou,
             o.confirm_payment_time, 
             o.torihiki_date_end, 
             o.site_id,
             o.is_gray,
             o.read_flag
               from " . TABLE_ORDERS . " o " . $from_payment . $sort_table."
               where ".$sort_where." o.customers_email_address = '" . tep_db_input($cEmail) . "' 
               " . " and o.site_id in (". $site_list_str .")"  . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str:'') . " " . $where_payment . $where_type .$where_no_error . " order by ".$order_str;
    
  } else if (isset($_GET['cID']) && $_GET['cID']) {
    //顾客id查询 
    $cID = tep_db_prepare_input($_GET['cID']);
    $orders_query_raw = "
      select distinct o.orders_id, 
             o.torihiki_date, 
             IF(o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
               as torihiki_date_error,
             IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
               as date_purchased_error,
             o.customers_name, 
             o.customers_id, 
             o.payment_method, 
             o.date_purchased, 
             o.last_modified, 
             o.currency, 
             o.currency_value, 
             o.orders_status, 
             o.orders_status_name, 
             o.orders_important_flag,
             o.orders_care_flag,
             o.orders_wait_flag,
             o.orders_inputed_flag,
             o.orders_work,
             o.customers_email_address,
             o.torihiki_houhou,
             o.orders_comment,
             o.confirm_payment_time, 
             o.torihiki_date_end, 
             o.site_id,
             o.is_gray,
             o.read_flag
               from " . TABLE_ORDERS . " o " . $from_payment . $sort_table."
               where ".$sort_where." o.customers_id = '" . tep_db_input($cID) . "' 
               " . " and o.site_id in (". $site_list_str .")"  . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str:'') . "
               " . $where_payment . $where_type .$where_no_error . " order by ".$order_str;
  } elseif (isset($_GET['status']) && $_GET['status']) {
    //状态查询 
    $status = tep_db_prepare_input($_GET['status']);
    $orders_query_raw = "
      select distinct o.orders_id, 
             o.torihiki_date, 
             IF(o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
               as torihiki_date_error,
             IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
               as date_purchased_error,
             o.customers_id, 
             o.customers_name, 
             o.payment_method, 
             o.date_purchased, 
             o.last_modified, 
             o.currency, 
             o.currency_value, 
             o.orders_status, 
             o.orders_status_name, 
             o.orders_important_flag,
             o.orders_care_flag,
             o.orders_wait_flag,
             o.orders_inputed_flag,
             o.orders_work,
             o.torihiki_houhou,
             o.customers_email_address,
             o.orders_comment,
             o.confirm_payment_time, 
             o.torihiki_date_end, 
             o.site_id,
             o.is_gray,
             o.read_flag
               from " . TABLE_ORDERS . " o " . $from_payment . $sort_table."
               where ".$sort_where."
               o.orders_status = '" . tep_db_input($status) . "' 
               " . " and o.site_id in (". $site_list_str .")" . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str:'') . "
               " . $where_payment . $where_type .$where_no_error . " order by ".$order_str;
  }  elseif (isset($_GET['keywords']) && isset($_GET['search_type']) && $_GET['search_type'] == 'products_name' && !$_GET['type'] && !$payment) {
    //商品名查询 
    $orders_query_raw = " select distinct op.orders_id from " .
      TABLE_ORDERS_PRODUCTS . " op use index(products_name), ".TABLE_ORDERS." o 
      ".$sort_table." where ".$sort_where." op.orders_id = o.orders_id and op.products_name ";
    if(isset($_GET['real_name'])&&$_GET['real_name']){
      $orders_query_raw .=  "= '".$_GET['keywords']."' " ;
    }else{
      $orders_query_raw .=  "like '%".$_GET['keywords']."%' " ;
    }
    $orders_query_raw .= " and o.site_id in (". $site_list_str .")" . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str:'') .$where_no_error . " order by ".$order_str;
  } elseif (isset($_GET['products_id']) && isset($_GET['search_type']) && $_GET['search_type'] == 'products_id' ) {
    //商品id查询 
    $orders_query_raw = " select distinct op.orders_id from " .  TABLE_ORDERS_PRODUCTS . " op, ".TABLE_ORDERS." o ".$sort_table." where ".$sort_where." op.orders_id = o.orders_id and op.products_id='".$_GET['products_id']."'";

    $orders_query_raw .= " and o.site_id in (". $site_list_str .")" . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str:'') .$where_no_error . " order by ". $order_str;
  } elseif (isset($_GET['scategories_id']) && isset($_GET['search_type']) && $_GET['search_type'] == 'categories_id') {
    //分类id查询 
    if (isset($_GET['site_id'])) {
      $tmp_site_array = explode('-', $_GET['site_id']); 
    }
    $relate_category_array = tep_get_child_category_by_cid($_GET['scategories_id']);
    $orders_query_raw = " select distinct op.orders_id from " .  TABLE_ORDERS_PRODUCTS . " op, ".TABLE_ORDERS." o,".TABLE_PRODUCTS_TO_CATEGORIES." p2c " .$sort_table." where ".$sort_where." op.orders_id = o.orders_id and op.products_id = p2c.products_id and p2c.categories_id in (".implode(',', $relate_category_array).")";
    $orders_query_raw .= " and o.site_id in (". $site_list_str .")" . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str:'') .$where_no_error . " order by ".str_replace('torihiki_date_error desc,date_purchased_error desc,', '', $order_str);
  } elseif (isset($_GET['keywords']) && isset($_GET['search_type']) && $_GET['search_type'] == 'sproducts_id' ) {
    //未完成订单查询 
    $query_str = ''; 
    $query_num = '';
    if(!empty($site_id)){

      if(get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',$site_id) != ''){
          $query_num = get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',$site_id);
      }else{

          if(get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',0) != ''){
            $query_num = get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',0); 
          }
      }
    }else{
      if(get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',0) != ''){
          $query_num = get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',0); 
      }
    }

    if(!empty($site_id) && $site_id != 0){
      if($query_num != ''){

        $query_str = " and date_format(o.date_purchased,'%Y-%m-%d %H:%i:%s') >= '".date('Y-m-d H:i:s',strtotime('-'.$query_num.' minutes'))."'";
      }
    }else{

      $site_id_query = tep_db_query("select id from ".TABLE_SITES);
      $query_str = ' and (';
      while($site_id_array = tep_db_fetch_array($site_id_query)){

        $site_temp_id = $site_id_array['id'];
        $query_temp_num = '';
        if(!empty($site_temp_id)){

          if(get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',$site_temp_id) != ''){
            $query_temp_num = get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',$site_temp_id);
          }else{

            if(get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',0) != ''){
              $query_temp_num = get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',0); 
            }
          }
        }else{
            if(get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',0) != ''){
              $query_temp_num = get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',0); 
            }
        } 
        $query_str .= "(o.site_id = ".$site_temp_id;
        if($query_temp_num != ''){
          $query_str .= " and date_format(o.date_purchased,'%Y-%m-%d %H:%i:%s') >= '".date('Y-m-d H:i:s',strtotime('-'.$query_temp_num.' minutes'))."') or ";
        }else{
          $query_str .= ') or ';
        }
      }
      tep_db_free_result($site_id_query);
      $query_str = substr($query_str,0,-4);
      $query_str .= ')';
    } 
    $orders_query_raw = " select distinct op.orders_id from " . TABLE_ORDERS_PRODUCTS . " op
      ,".TABLE_ORDERS." o,".TABLE_ORDERS_STATUS." o_s 
      ".$sort_table." where ".$sort_where." 
      o.orders_id = op.orders_id and o.orders_status = o_s.orders_status_id and op.products_id ";
    $orders_query_raw .=  "= '".$_GET['keywords']."' " ;
    $orders_query_raw .= " and o.finished = '0' and flag_qaf = '0'and o_s.is_cancle = 0".$query_str;
    $orders_query_raw .= " and o.site_id in (". $site_list_str .")" . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str:'') .$where_no_error . " order by ".str_replace('torihiki_date_error desc,date_purchased_error desc,', '', $order_str);
  } elseif (isset($_GET['keywords']) && ((isset($_GET['search_type']) && preg_match('/^os_\d+$/', $_GET['search_type'])))) {
    //订单状态查询 
    if (!empty($_GET['keywords'])) {
      $orders_query_raw = "
        select distinct(o.orders_id), 
               o.torihiki_date, 
               IF(o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
                 as torihiki_date_error,
               IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
                 as date_purchased_error,
               o.customers_id, 
               o.customers_name, 
               o.payment_method, 
               o.date_purchased, 
               o.last_modified, 
               o.currency, 
               o.currency_value, 
               o.orders_status, 
               o.orders_status_name,
               o.orders_important_flag,
               o.orders_care_flag,
               o.orders_wait_flag,
               o.orders_inputed_flag,
               o.orders_work,
               o.customers_email_address,
               o.torihiki_houhou,
               o.orders_comment,
               o.confirm_payment_time, 
               o.torihiki_date_end, 
               o.site_id,
               o.is_gray,
               o.read_flag
                 from " . TABLE_ORDERS . " o " . $from_payment . " ,
               ".TABLE_ORDERS_PRODUCTS." op ".$sort_table." where ".$sort_where .
                 " o.site_id in (". $site_list_str .")" . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str.' and ':' and ') . " o.orders_status =
                  '".substr($_GET['search_type'], 3)."' and o.orders_id = op.orders_id and
                  (o.orders_id like '%".$_GET['keywords']."%' or o.customers_name like
                   '%".$_GET['keywords']."%' or o.customers_email_address like
                   '%".$_GET['keywords']."%' or op.products_name like
                   '%".$_GET['keywords']."%') " . $where_payment . $where_type .$where_no_error ."  order by
                  ".$order_str;
                  } else {
                  $orders_query_raw = "
                  select distinct(o.orders_id), 
                  o.torihiki_date, 
                  IF(        o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
                  as torihiki_date_error,
                  IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
                  as date_purchased_error,
                  o.customers_id, 
                  o.customers_name, 
                  o.payment_method, 
                  o.date_purchased, 
                  o.last_modified, 
                  o.currency, 
                  o.currency_value, 
                  o.orders_status, 
                  o.orders_status_name,
                  o.orders_important_flag,
                  o.orders_care_flag,
                  o.orders_wait_flag,
                  o.orders_inputed_flag,
                  o.orders_work,
                  o.customers_email_address,
                  o.torihiki_houhou,
                  o.orders_comment,
                  o.confirm_payment_time, 
                  o.torihiki_date_end, 
                  o.site_id,
                  o.is_gray,
                  o.read_flag
                    from " . TABLE_ORDERS . " o " . $from_payment . $sort_table." where 
                    ".$sort_where .
                    " o.site_id in (". $site_list_str .")" .  ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str.' and ':' and '). "
                     o.orders_status = '".substr($_GET['search_type'], 3)."'" . $where_payment
                     . $where_type .$where_no_error ."  order by ".$order_str;
                     }
                     } elseif (isset($_GET['keywords']) && ((isset($_GET['search_type']) && $_GET['search_type'] == 'orders_id'))) {
                     //订单id查询 
                     $tmp_order_id = date("Ymd") . '-' . date("His") .  tep_get_order_end_num();
                     if(strlen(trim($_GET['keywords']))==strlen($tmp_order_id)){
                       $orders_id_search = " o.orders_id ='".trim($_GET['keywords'])."' ";
                     }else{
                       $orders_id_search = " o.orders_id like '%".trim($_GET['keywords'])."%' ";
                     }
                     $orders_query_raw = "
                     select o.orders_id, 
                     o.torihiki_date, 
                     IF(o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
                     as torihiki_date_error,
                     IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
                     as date_purchased_error,
                     o.customers_id, 
                     o.customers_name, 
                     o.payment_method, 
                     o.date_purchased, 
                     o.last_modified, 
                     o.currency, 
                     o.currency_value, 
                     o.orders_status, 
                     o.orders_status_name,
                     o.orders_important_flag,
                     o.orders_care_flag,
                     o.orders_wait_flag,
                     o.orders_inputed_flag,
                     o.orders_work,
                     o.customers_email_address,
                     o.torihiki_houhou,
                     o.orders_comment,
                     o.confirm_payment_time, 
                     o.torihiki_date_end, 
                     o.site_id,
                     o.is_gray,
                     o.read_flag
                       from " . TABLE_ORDERS . " o use index(orders_id) " . $from_payment .$sort_table ." where " . $sort_where.  " o.site_id in (". $site_list_str .")" . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str.' and ':' and ') .  $orders_id_search .  $where_payment . $where_type.$where_no_error ."  order by ".$order_str;
                     } elseif ( isset($_GET['keywords']) && ((isset($_GET['search_type']) && $_GET['search_type'] == 'customers_name') or (isset($_GET['search_type']) && $_GET['search_type'] == 'email'))
                         ) {
                       //顾客名/邮件查询 
                       $use_index = '';
                       if($_GET['search_type'] == 'customers_name'){
                         $use_index = ' use index(customers_name_2) ';
                       }else if ($_GET['search_type'] == 'email'){
                         $use_index = ' use index(customers_email_address) ';
                       }
                       $orders_query_raw = "
                         select o.orders_id, 
                                o.torihiki_date, 
                                IF(o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
                                  as torihiki_date_error,
                                IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
                                  as date_purchased_error,
                                o.customers_id, 
                                o.customers_name, 
                                o.payment_method, 
                                o.date_purchased, 
                                o.last_modified, 
                                o.currency, 
                                o.currency_value, 
                                o.orders_status, 
                                o.orders_status_name,
                                o.orders_important_flag,
                                o.orders_care_flag,
                                o.orders_wait_flag,
                                o.orders_inputed_flag,
                                o.orders_work,
                                o.customers_email_address,
                                o.torihiki_houhou,
                                o.orders_comment,
                                o.confirm_payment_time, 
                                o.torihiki_date_end, 
                                o.site_id,
                                o.is_gray,
                                o.read_flag
                                  from " . TABLE_ORDERS . " o  " .$use_index. $from_payment . $sort_table."
                                  where   
                                  " .$sort_where . " 1=1 ".
                                  " and o.site_id in (". $site_list_str .")" . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str:'') ."
                                  " . $where_payment . $where_type ;

                       $keywords = str_replace('　', ' ', $_GET['keywords']);

                       tep_parse_search_string($keywords, $search_keywords);

                       if (isset($search_keywords) && (sizeof($search_keywords) > 0)) {
                         $orders_query_raw .= " and ";
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
                               if (isset($_GET['search_type']) && $_GET['search_type'] == 'customers_name') {
                                 $orders_query_raw .= "(o.customers_name like '%" . tep_db_input($keyword) . "%' or ";
                                 $orders_query_raw .= "o.customers_name_f like '%" . tep_db_input($keyword) . "%')";
                               } else if (isset($_GET['search_type']) && $_GET['search_type'] == 'email') {
                                 $orders_query_raw .= "o.customers_email_address like '%" . tep_db_input($keyword) . "%'";
                               }
                               break;
                           }
                         } 
                       }

                       $orders_query_raw .= $where_no_error ." order by ".$order_str;
                     }
  
  
  
 elseif (isset($_GET['keywords']) && ((isset($_GET['search_type']) && $_GET['search_type'] == 'value'))) {
             //金额查询  
             $keywords = $_GET['keywords'];
             $orders_total_query = tep_db_query("select * from ". TABLE_ORDERS_TOTAL ." where class='ot_total' and value='".$keywords.".0000'" );
             $orders_like_str = '';
             $orders_like_array = array();
             while($orders_total_array = tep_db_fetch_array($orders_total_query)){
                $orders_like_array[] = $orders_total_array['orders_id'];
             }
             $orders_like_str = implode("','",$orders_like_array);
             if(count($orders_like_array) == 1){
               $orders_str = "='".$orders_like_str."'";
             }else{
               $orders_str = " in ('".$orders_like_str."')";
              }
	     $orders_query_raw = "
	     select o.orders_id, 
	     o.torihiki_date, 
	     IF(o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
	     as torihiki_date_error,
	     IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
	     as date_purchased_error,
	     o.customers_id, 
	     o.customers_name, 
	     o.payment_method, 
	     o.date_purchased, 
	     o.last_modified, 
	     o.currency, 
	     o.currency_value, 
	     o.orders_status, 
	     o.orders_status_name,
	     o.orders_important_flag,
	     o.orders_care_flag,
	     o.orders_wait_flag,
	     o.orders_inputed_flag,
	     o.orders_work,
	     o.customers_email_address,
	     o.torihiki_houhou,
	     o.orders_comment,
	     o.confirm_payment_time, 
             o.torihiki_date_end, 
             o.site_id,
             o.is_gray,
             o.read_flag
	       from " . TABLE_ORDERS . " o use index(orders_id) " . $from_payment .$sort_table ."
	       where " . $sort_where.
	       " o.site_id in (". $site_list_str .")" . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str.' and ':' and ') . " o.orders_id" .$orders_str.
	       $where_payment . $where_type.$where_no_error .' order by '.$order_str;
	     }

  
  
                    else if(isset($_GET['keywords']) && ((isset($_GET['search_type']) &&
                             preg_match('/^payment_method/', $_GET['search_type'])))){
                       //支付方法查询 
                       $payment_m = explode('|',$_GET['search_type']);
                       if (!empty($_GET['keywords'])) {
                         $orders_query_raw = "
                           select distinct(o.orders_id), 
                                  o.torihiki_date, 
                                  IF(o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
                                    as torihiki_date_error,
                                  IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
                                    as date_purchased_error,
                                  o.customers_id, 
                                  o.customers_name, 
                                  o.payment_method, 
                                  o.date_purchased, 
                                  o.last_modified, 
                                  o.currency, 
                                  o.currency_value, 
                                  o.orders_status, 
                                  o.orders_status_name,
                                  o.orders_important_flag,
                                  o.orders_care_flag,
                                  o.orders_wait_flag,
                                  o.orders_inputed_flag,
                                  o.orders_work,
                                  o.customers_email_address,
                                  o.torihiki_houhou,
                                  o.orders_comment,
                                  o.confirm_payment_time, 
                                  o.torihiki_date_end, 
                                  o.site_id,
                                  o.is_gray,
                                  o.read_flag
                                    from " . TABLE_ORDERS . " o " . $from_payment . " ,
                                  ".TABLE_ORDERS_PRODUCTS." op ".$sort_table." where ".$sort_where .
                                    " o.site_id in (". $site_list_str .")" . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str.' and ':' and '). " o.payment_method =
                                     '".$payment_m[1]."' and o.orders_id = op.orders_id and
                                     (o.orders_id like '%".$_GET['keywords']."%' or o.customers_name like
                                      '%".$_GET['keywords']."%' or o.customers_email_address like
                                      '%".$_GET['keywords']."%' or op.products_name like
                                      '%".$_GET['keywords']."%') ".$where_no_error ." order by
                                     ".$order_str;
                                     } else {
                                     $orders_query_raw = "
                                     select o.orders_id, 
                                     o.torihiki_date, 
                                     IF(o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
                                     as torihiki_date_error,
                                     IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
                                     as date_purchased_error,
                                     o.customers_id, 
                                     o.customers_name, 
                                     o.payment_method, 
                                     o.date_purchased, 
                                     o.last_modified, 
                                     o.currency, 
                                     o.currency_value, 
                                     o.orders_status, 
                                     o.orders_status_name,
                                     o.orders_important_flag,
                                     o.orders_care_flag,
                                     o.orders_wait_flag,
                                     o.orders_inputed_flag,
                                     o.orders_work,
                                     o.customers_email_address,
                                     o.torihiki_houhou,
                                     o.orders_comment,
                                     o.confirm_payment_time, 
                                     o.torihiki_date_end, 
                                     o.site_id,
                                     o.is_gray,
                                     o.read_flag
                                       from " . TABLE_ORDERS . " o " . $from_payment . $sort_table."
                                       where ".$sort_where." o.site_id in (". $site_list_str .")" . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str.' and ':' and ') . " o.payment_method like '".$payment_m[1]."'";
                                    $orders_query_raw .= $where_no_error ."order by ".$order_str;
                                     }
                     }else if(isset($_GET['keywords']) && ((isset($_GET['search_type']) &&
                             preg_match('/^type/', $_GET['search_type'])))){
                       //订单类型查询 
                       $type_arr = explode('|',$_GET['search_type']);
                      /* -----------------------------------------------------
                         case 'sell' 贩卖
                         case 'buy' 买取
                         case 'mix' 混合
                      -----------------------------------------------------*/
                       switch ($type_arr[1]) { 
                         case 'sell':
                           $w_type = 'orders_type = 1';  
                           break;
                         case 'buy':
                           $w_type = 'orders_type = 2';  
                           break;
                         case 'mix':
                           $w_type = 'orders_type = 3';  
                           break;
                       }
                       if (!empty($_GET['keywords'])) {
                         $orders_query_raw = "
                           select distinct(o.orders_id), 
                                  o.torihiki_date, 
                                  IF(o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
                                    as torihiki_date_error,
                                  IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
                                    as date_purchased_error,
                                  o.customers_id, 
                                  o.customers_name, 
                                  o.payment_method, 
                                  o.date_purchased, 
                                  o.last_modified, 
                                  o.currency, 
                                  o.currency_value, 
                                  o.orders_status, 
                                  o.orders_status_name,
                                  o.orders_important_flag,
                                  o.orders_care_flag,
                                  o.orders_wait_flag,
                                  o.orders_inputed_flag,
                                  o.orders_work,
                                  o.customers_email_address,
                                  o.torihiki_houhou,
                                  o.orders_comment,
                                  o.confirm_payment_time, 
                                  o.torihiki_date_end, 
                                  o.site_id,
                                  o.is_gray,
                                  o.read_flag
                                    from " . TABLE_ORDERS . " o, " .TABLE_ORDERS_PRODUCTS." op ". $f_payment . $sort_table."
                                    where ".$sort_where." o.site_id in (". $site_list_str .")" . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str.' and ':' and ') . " ".$w_type. " and o.orders_id = op.orders_id and (o.orders_id like '%".$_GET['keywords']."%' or o.customers_name like '%".$_GET['keywords']."%' or o.customers_email_address like '%".$_GET['keywords']."%' or op.products_name like '%".$_GET['keywords']."%') ";
                         $orders_query_raw .= $where_no_error ." order by ".$order_str;
                       } else {
                         $orders_query_raw = "
                           select distinct(o.orders_id), 
                                  o.torihiki_date, 
                                  IF(o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
                                    as torihiki_date_error,
                                  IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
                                    as date_purchased_error,
                                  o.customers_id, 
                                  o.customers_name, 
                                  o.payment_method, 
                                  o.date_purchased, 
                                  o.last_modified, 
                                  o.currency, 
                                  o.currency_value, 
                                  o.orders_status, 
                                  o.orders_status_name,
                                  o.orders_important_flag,
                                  o.orders_care_flag,
                                  o.orders_wait_flag,
                                  o.orders_inputed_flag,
                                  o.orders_work,
                                  o.customers_email_address,
                                  o.torihiki_houhou,
                                  o.orders_comment,
                                  o.confirm_payment_time, 
                                  o.torihiki_date_end, 
                                  o.site_id,
                                  o.is_gray,
                                  o.read_flag
                                    from " . TABLE_ORDERS . " o " . $f_payment . $sort_table."
                                    where ".$sort_where." o.site_id in (". $site_list_str .")" . ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str.' and ':' and ') . " ".$w_type;
                         $orders_query_raw .= $where_no_error ." order by ".$order_str;
                       }
                     }elseif (isset($_GET['keywords']) && $_GET['keywords']) {
                       //关键字查询 
                       $orders_query_raw = "
                         select distinct(o.orders_id), 
                                o.torihiki_date, 
                                IF(o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
                                  as torihiki_date_error,
                                IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
                                  as date_purchased_error,
                                o.customers_id, 
                                o.customers_name, 
                                o.payment_method, 
                                o.date_purchased, 
                                o.last_modified, 
                                o.currency, 
                                o.currency_value, 
                                o.orders_status, 
                                o.orders_status_name,
                                o.orders_important_flag,
                                o.orders_care_flag,
                                o.orders_wait_flag,
                                o.orders_inputed_flag,
                                o.orders_work,
                                o.customers_email_address,
                                o.torihiki_houhou,
                                o.orders_comment,
                                o.confirm_payment_time, 
                                o.torihiki_date_end, 
                                o.site_id,
                                o.is_gray,
                                o.read_flag
                                  from " . TABLE_ORDERS . " o " . $from_payment . ", " . TABLE_ORDERS_PRODUCTS . " op 
                                  ".$sort_table."
                                  where ".$sort_where." o.orders_id = op.orders_id
                                  " . " and o.site_id in (". $site_list_str .")" .  ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str:'') ."
                                  " . $where_payment . $where_type ;
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

                       $orders_query_raw .= $where_no_error ."order by ".$order_str;

                     } else {
                       // orders_list 隐藏 「取消」和「取消订单」
                       $orders_query_raw = "
                         select distinct o.orders_status as orders_status_id, 
                                o.orders_id, 
                                o.torihiki_date, 
                                IF(o.torihiki_date = '0000-00-00 00:00:00' or o.torihiki_date ='',1,0) 
                                  as torihiki_date_error,
                                IF(o.date_purchased = '0000-00-00 00:00:00' or o.date_purchased ='',1,0) 
                                  as date_purchased_error,
                                o.customers_id, 
                                o.customers_name, 
                                o.payment_method, 
                                o.date_purchased, 
                                o.last_modified, 
                                o.currency, 
                                o.currency_value, 
                                o.orders_status, 
                                o.orders_status_name, 
                                o.orders_status_image,
                                o.orders_important_flag,
                                o.orders_care_flag,
                                o.orders_wait_flag,
                                o.orders_inputed_flag,
                                o.orders_work,
                                o.customers_email_address,
                                o.orders_comment,
                                o.torihiki_houhou,
                                o.confirm_payment_time, 
                                o.torihiki_date_end, 
                                o.site_id,
                                o.is_gray,
                                o.read_flag
                                  from " . TABLE_ORDERS . " o " . $from_payment . $sort_table."
                                  where 
                                  ".$sort_where."
                                  o.site_id in (".$site_list_str.")". 
                                  ((!$is_show_transaction)?" and o.flag_qaf = 0":'').(($mark_sql_str != '')?' and '.$mark_sql_str:'')." 
                                  " . $where_payment . $where_type .$where_no_error . " order by ".$order_str;
                     }
  // old sort is  order by torihiki_date_error DESC,o.torihiki_date DESC
  // new sort is  order by o.torihiki_date DESC
  $from_pos = strpos($orders_query_raw, 'from orders');
  $order_pos = strpos($orders_query_raw, 'and(o.torihiki_date');
  $op_pos = strpos($orders_query_raw, 'distinct op.orders_id'); 
  $start_pos = strpos($orders_query_raw, 'from orders');
  $end_pos = strpos($orders_query_raw,'and(o.torihiki_date');
  if(($start_pos!==false)&&($end_pos!==false)){
    if ($op_pos !== false) {
      $error_order_sql = "select op.orders_id ".substr($orders_query_raw, $start_pos, $end_pos - $start_pos).
         "and(o.date_purchased is null or o.torihiki_date is null or 
         o.date_purchased ='' or o.torihiki_date = '' or 
         o.date_purchased ='0000-00-00 00:00:0' or o.torihiki_date = '0000-00-00 00:00:00')";
    }else{
      $error_order_sql = "select o.orders_id ".substr($orders_query_raw, $start_pos, $end_pos - $start_pos).
         "and(o.date_purchased is null or o.torihiki_date is null or 
         o.date_purchased ='' or o.torihiki_date = '' or 
         o.date_purchased ='0000-00-00 00:00:0' or o.torihiki_date = '0000-00-00 00:00:00')";
    }
  }
  $e_orders_arr =array();
  $e_orders_id_arr =array();
  $error_order_query = tep_db_query($error_order_sql);
  while ($e_orders = tep_db_fetch_array($error_order_query)){
    $e_orders_arr[] = $e_orders;
    $e_orders_id_arr[] = '"'.$e_orders['orders_id'].'"';
  }
  if (($from_pos !== false) && ($order_pos !== false)) {
    if ($op_pos !== false) {
      $sql_count_query = "select count(op.orders_id) as count ".substr($orders_query_raw, $from_pos, $order_pos - $from_pos);
    } else {
      $sql_count_query = "select count(o.orders_id) as count ".substr($orders_query_raw, $from_pos, $order_pos - $from_pos);
    }
  } 
  if(!empty($e_orders_id_arr)){
    $sql_count_query .= ' and orders_id not in ('.implode(',',$e_orders_id_arr).')';
  }
#}}

}
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
# 永远是改动过的
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
# HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
# HTTP/1.0
header("Pragma: no-cache");
/* -----------------------------------------------------
   功能: 判断配送开始时间是否为空 
   参数: $oid(string) 订单id 
   返回值: 是否为空(boolean) 
-----------------------------------------------------*/
function check_torihiki_date_error($oid){
  $query = tep_db_query("select * from " . TABLE_ORDERS . " where orders_id='" . $oid . "'");
  $order = tep_db_fetch_array($query);
  if ($order['torihiki_date'] == '0000-00-00 00:00:00') {
    return true;
  }
  return false;
}

if(isset($_SESSION['error_orders_status'])&&$_SESSION['error_orders_status']){
  $messageStack->add($_SESSION['error_orders_status'], 'error');
  unset($_SESSION['error_orders_status']);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<?php 
// 订单详细页，TITLE显示交易商品名
if ($_GET['action']=='edit' && $_GET['oID']) {?>
	<title>
<?php 
echo tep_get_orders_edit_title_from_oID($_GET['oID'])." ". HEADING_TITLE; 

?>
</title>
<?php } else if($_GET['action']=='show_manual_info' && $_GET['oID'] && $_GET['pID']){
    ?><title><?php echo tep_get_orders_manual_title_from_pID($_GET['pID']); ?></title>
<?php 
}else if($_GET['action']=='search_manual_info' && $_GET['keyword'] ){
	?>
<title><?php echo $_GET['keyword'].MANUAL_SEARCH_HEAD; ?></title> 
<?php
}else if($_GET['action']=='show_search_manual'  && $_GET['pID']){
    ?><title><?php echo tep_get_orders_manual_title_from_pID($_GET['pID']); ?></title>
<?php 
}else if($_GET['action']=='show_search_manual'  && $_GET['cID']){
    ?><title><?php echo tep_get_orders_manual_title_from_cID($_GET['cID']); ?></title>
<?php 
}else if($_GET['action']=='show_search_manual'  && $_GET['cPath']){
    ?><title><?php echo tep_get_orders_manual_title_from_cPath($_GET['cPath']); ?></title>
<?php 

}




else { ?>
<title><?php echo HEADING_TITLE; ?></title>
<?php }?>
        <link rel="stylesheet" type="text/css" href="includes/stylesheet.css?v=<?php echo $back_rand_info?>">
        <script language="javascript" src="includes/jquery_tool.php?v=<?php echo $back_rand_info?>"></script>
        <script language="javascript">
        var tmp_other_str = '<?php echo $_SERVER['PHP_SELF'];?>'; 
        var notice_relogin_str = '<?php echo TEXT_TIMEOUT_RELOGIN;?>'; 
        </script> 
        <script language="javascript">
<?php // 用作跳转?>
        var base_url = '<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('questions_type')));?>';
        <?php // 非完成状态的订单不显示最终确认?>
        var show_q_8_1_able  = <?php echo tep_orders_finished($_GET['oID']) && !check_torihiki_date_error($_GET['oID']) ?'true':'false';?>;
        var cfg_last_customer_action = '<?php echo LAST_CUSTOMER_ACTION;?>';
<?php
if(isset($_GET['keywords'])&&$_GET['keywords']){
  ?>
        var js_orders_status_keywords = true;
        var js_orders_keywords = '<?php echo $_GET['keywords'];?>';
  <?php
}else{
?>
        var js_orders_status_keywords = false;
<?php
}
if(isset($_GET['search_type'])&&$_GET['search_type']){
  ?>
        var js_orders_search_type = true;
        var js_orders_option_value = '<?php echo urldecode($_GET['search_type']);?>';
  <?php
}else{
?>
        var js_orders_search_type = false;
<?php
}

?>
<?php
if (!isset($_GET['action'])) {
  ?>
        var js_orders_action_isset = true;
  <?php
}else{
?>
        var js_orders_action_isset = false;
<?php
}
?>
var popup_num = 1;
</script>
<script language="javascript" src="includes/orders_tool.php?v=<?php echo $back_rand_info?>"></script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=[^&]+/',$belong,$belong_temp_array);
if($belong_temp_array[0][0] != ''){
  if($belong_temp_array[0][0] == 'action=show_manual_info'){
    preg_match_all('/pID=[^&]+/',$belong,$belong_array);
    if($belong_array[0][0] != ''){

      $belong = $href_url.'?action=show_manual_info|||'.$belong_array[0][0];
    }else{

      $belong = $href_url;
    }
  }else if($belong_temp_array[0][0] == 'action=search_manual_info'){
    $belong = $href_url.'?action=search_manual_info';
  }else if($belong_temp_array[0][0] == 'action=show_search_manual'){
    preg_match_all('/cPath=[^&]+/',$belong,$belong_array);
    if($belong_array[0][0] != ''){
      $belong = $href_url.'?action=show_search_manual|||'.$belong_array[0][0];
    }else{
      preg_match_all('/pID=[^&]+/',$belong,$belong_array); 
      if($belong_array[0][0] != ''){
        $belong = $href_url.'?action=show_manual_info|||'.$belong_array[0][0]; 
      }
    }
  }else{
    preg_match_all('/oID=[^&]+/',$belong,$belong_array);
    if($belong_array[0][0] != ''){

      $belong = $href_url.'?'.$belong_array[0][0];
    }else{

      $belong = $href_url;
    }
  }
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
</head>
<body>
<?php //oa数据请求失败时的弹出层?>
<div id="popup_info" style="display:none;">
<div class="popup_img"><img onclick="close_error_message()" src="images/close_error_message.gif" alt="close" /></div>
<span><?php echo TEXT_EOF_ERROR_MSG;?></span>
</div>
<div id="popup_box" style="display:none;"></div>
<?php
if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>', '<?php echo JS_TEXT_INPUT_ONETIME_PWD?>', '<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>');
  </script>
    <?php }?>
    <!-- header -->
    <?php
    require(DIR_WS_INCLUDES . 'header.php');
    ?>
    <!-- header_eof -->
    <!-- body -->
    <table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
    <tr>
    <?php
      echo '<td width="' . BOX_WIDTH . '" valign="top">';
      echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
      echo '<!-- left_navigation -->';
      require(DIR_WS_INCLUDES . 'column_left.php');
      echo '<!-- left_navigation_eof -->';
      echo '</table>';
      echo '</td>';
    ?>
<script>

$(document).ready(function(){ 
<?php
  //当请求为手册显示时执行
  if($_GET['action'] == 'show_manual_info' || $_GET['action'] == 'show_search_manual'){
?>
    <?php //手册内容隐藏，显示?>
    var show_height = 200;
    var manual_top = $("#manual_top").height();
    manual_top = parseInt(manual_top);
    if(manual_top > show_height){

      $("#manual_top_show").css({"height":"200px","overflow":"hidden"});
      $("#manual_top_show").html($("#manual_top").html());
      $("#manual_top_all").show();
      $("#manual_top").html('');
    }else{
      $("#manual_top_show").html($("#manual_top").html()); 
      $("#manual_top").html('');
    }

    var manual_top_categories = $("#manual_top_categories").height();
    manual_top_categories = parseInt(manual_top_categories);
    if(manual_top_categories > show_height){

      $("#manual_top_categories_show").css({"height":"200px","overflow":"hidden"});
      $("#manual_top_categories_show").html($("#manual_top_categories").html());
      $("#manual_top_categories_all").show();
      $("#manual_top_categories").html('');
    }else{
      $("#manual_top_categories_show").html($("#manual_top_categories").html()); 
      $("#manual_top_categories").html('');
    }

    var manual_categories = $("#manual_categories").height();
    manual_categories = parseInt(manual_categories);
    if(manual_categories > show_height){

      $("#manual_categories_show").css({"height":"200px","overflow":"hidden"});
      $("#manual_categories_show").html($("#manual_categories").html());
      $("#manual_categories_all").show();
      $("#manual_categories").html('');
    }else{
      $("#manual_categories_show").html($("#manual_categories").html()); 
      $("#manual_categories").html('');
    }

    var manual_categories_children = $("#manual_categories_children").height();
    manual_categories_children = parseInt(manual_categories_children);
    if(manual_categories_children > show_height){

      $("#manual_categories_children_show").css({"height":"200px","overflow":"hidden"});
      $("#manual_categories_children_show").html($("#manual_categories_children").html());
      $("#manual_categories_children_all").show();
      $("#manual_categories_children").html('');
    }else{
      $("#manual_categories_children_show").html($("#manual_categories_children").html()); 
      $("#manual_categories_children").html('');
    }

    var manual_products = $("#manual_products").height();
    manual_products = parseInt(manual_products);
    if(manual_products > show_height){

      $("#manual_products_show").css({"height":"200px","overflow":"hidden"});
      $("#manual_products_show").html($("#manual_products").html());
      $("#manual_products_all").show();
      $("#manual_products").html('');
    }else{
      $("#manual_products_show").html($("#manual_products").html()); 
      $("#manual_products").html('');
    }
<?php
  }
?>
  if($(".dataTableContent").find('input|[type=checkbox][checked]').length!=0){
    if(document.sele_act.elements["chk[]"]){
      document.getElementsByName("all_chk")[0].checked = false;
      for(i = 0; i < document.sele_act.elements["chk[]"].length; i++){
        document.sele_act.elements["chk[]"][i].checked = false;
        var tr_id = 'tr_' + document.sele_act.elements["chk[]"][i].value;
        if(document.getElementById(tr_id).className != 'dataTableRowSelected'){
          document.getElementById(tr_id).style.backgroundColor = "";
        }
      }
    }
  }
  if($(".note_head").val()== ""&&$("#orders_list_table").width()< 714){
    $(".box_warp").css('height','100%');
  }
    });
</script>
<!-- body_text -->
<td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
if ( isset($_GET['action']) && ($_GET['action'] == 'edit') && ($order_exists) ) {
  // edit start
  $order = new order($oID);
  ?>
    <tr>
    <td width="100%">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <td width="100%">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
    <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
    <td class="pageHeading" align="right">
    <?php if ($ocertify->npermission) { ?>
      <?php 
        if(isset($order->info['flag_qaf'])&&$order->info['flag_qaf']&&($ocertify->npermission != 31)){
          echo tep_html_element_button(IMAGE_EDIT, 'onclick="once_pwd_redircet_new_url(\''.  tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action','status','questions_type')) .'&action=edit') .'\', \''.$ocertify->npermission.'\', \''.JS_TEXT_INPUT_ONETIME_PWD.'\', \''.JS_TEXT_ONETIME_PWD_ERROR.'\')"');
        }else{
          echo '<a href="' . tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action','status','questions_type')) . '&action=edit') . '">';
          echo tep_html_element_button(IMAGE_EDIT);
          echo '</a>'; 
        }
      ?>
        <?php } ?>
        <?php echo '<a id="back_link" href="' .  tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action','status','questions_type'))) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?>
        </td>
        </tr>
        </table>
        </div>
        </td>
        </tr>
        <?php // 三种状态 + A,B,C,D ?>
        <tr>
        <td width="100%">
        <div id="orders_flag">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td width="50%" align="left">
        <div class="td_title_text">
          <div class='<?php echo $order->info['orders_care_flag'] ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_flag(this, 'care', '<?php echo $order->info['orders_id'];?>')"><?php echo TEXT_STATUS_HANDLING_WARNING;?></div>
          <div class='<?php echo $order->info['orders_wait_flag'] ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_flag(this, 'wait', '<?php echo $order->info['orders_id'];?>')"><?php echo TEXT_STATUS_WAIT_TRADE;?></div>
          <div class='<?php echo $order->info['orders_inputed_flag'] ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_flag(this, 'inputed', '<?php echo $order->info['orders_id'];?>')"><?php echo TEXT_STATUS_READY_ENTER;?></div>
        </div>
        </td>
        <td width="50%" align="right">
        <div class="td_title_alphabet">
        <div id="work_a" class='<?php echo $order->info['orders_work'] == 'a' ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_work(this, 'a', '<?php echo $order->info['orders_id'];?>')">A</div>
        <div id="work_b" class='<?php echo $order->info['orders_work'] == 'b' ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_work(this, 'b', '<?php echo $order->info['orders_id'];?>')">B</div>
        <div id="work_c" class='<?php echo $order->info['orders_work'] == 'c' ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_work(this, 'c', '<?php echo $order->info['orders_id'];?>')">C</div>
        <div id="work_d" class='<?php echo $order->info['orders_work'] == 'd' ? 'orders_flag_checked' : 'orders_flag_unchecked'; ?>' onclick="orders_work(this, 'd', '<?php echo $order->info['orders_id'];?>')">D</div>
        </div>
        </td>
        </tr>
        </table>
        </div>
        </td>
        </tr> 
        <?php 
          $buttons = tep_get_buttons();
          $o2c       = tep_get_buttons_by_orders_id($order->info['orders_id']);
          if ($buttons) {
        ?>
          <tr><td>
          <?php foreach ($buttons as $button) {?>
          <div id="orders_alert_<?php echo $button['buttons_id'];?>" onclick="orders_buttons(this, <?php echo $button['buttons_id'];?>, '<?php echo $order->info['orders_id'];?>');" class="<?php echo in_array($button['buttons_id'], $o2c) ? 'orders_buttons_checked' : 'orders_buttons_unchecked' ;?>"><?php echo $button['buttons_name'];?></div>
        <?php 
          } 
        ?>
          </td></tr> 
        <?php 
          } 
        ?> 
        <tr>
        <td>
        <!-- left -->
        <div class="pageHeading_box">
        <div id="orders_info">
        <h3>Order Info</h3>
        <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
        <td class="main" valign="top" width="30%"><?php echo TEXT_SITE_ORDER_FORM;?></td>
        <td class="main"><font color="#FF0000"><?php echo tep_get_site_name_by_order_id($oID);?></font></td>
        </tr>
        <tr>
        <td class="main" valign="top" width="30%"><?php echo TEXT_TRADE_DATE;?></td>
        <td class="main"><font color="#0000FF"><?php echo str_replace('_', TEXT_TIME_LINK, $order->tori['date']);?></font></td>
        </tr>
        <tr>
        <td class="main" valign="top"><?php echo TEXT_ORDERS_OID;?></td>
        <td class="main"><?php echo $_GET['oID'] ?></td>
        </tr>
        <tr>
        <td class="main" valign="top"><?php echo TEXT_ORDERS_DATE;?></td>
        <td class="main"><?php echo tep_date_long($order->customer['date']); ?></td>
        </tr>
        <tr>
        <td class="main" valign="top"><?php echo TEXT_CUSTOMER_CLASS;?></td>
        <td class="main"><?php echo get_guest_chk($order->customer['id'])?TEXT_GUEST:TEXT_MEMBER;?></td>
        </tr>
        <tr>
        <td class="main" valign="top"><?php echo ENTRY_CUSTOMER; ?></td>
        <td class="main" style="text-decoration: underline; ">
        <?php
        if ($order->info['is_gray'] == '1') {
        ?>
        <div class="highlight_color"> 
        <?php
        } 
        $customers_name_arr = explode('|',CUSTOMER_NAME_KEYWORDS); 
        $customers_name_str = str_replace("\n","<br>",tep_replace_to_red($customers_name_arr,htmlspecialchars($order->customer['name'])));
        ?>
        <a href="<?php echo tep_href_link(FILENAME_CUSTOMERS, 'type=cid&search='.$order->customer['id']);?>"><?php echo $customers_name_str; ?></a>
        <?php
        if ($order->info['is_gray'] == '1') {
        ?>
        </div> 
        <?php
        } 
        ?>
        </td>
        </tr>
        <tr>
        <td class="main"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
        <td class="main">
        <?php 
        //osticket
        $ostGetPara = array(
            "name"=>$order->customer['name'],
            "topicid"=>constant("SITE_TOPIC_".$order->info['site_id']),
            "source"=>'Email',
            "email"=>$order->customer['email_address']);
      function makeValueUrlencode(&$value,$key){
        $value = urlencode($value);
      }
      array_walk($ostGetPara,'makeValueUrlencode');
      $parmStr = '';
      foreach($ostGetPara as $key=>$value){
        $parmStr.= '&'.$key.'='.$value;
      }
      $remoteurl = (defined('OST_SERVER')?OST_SERVER:'scp')."/tickets.php?a=open2".$parmStr."";
      $customers_email_arr = explode('|',CUSTOMER_EMAIL_KEYWORDS); 
      $customers_email_str = str_replace("\n","<br>",tep_replace_to_red($customers_email_arr,tep_output_string_protected($order->customer['email_address'])));
      ?>
        <?php echo '<a class="order_link" href="javascript:void(0);" onclick="copyToClipboard(\'' .  tep_output_string_protected($order->customer['email_address']) . '\', \''.JS_TEXT_ALL_ORDER_BROWER_REJECTED.'\', \''.JS_TEXT_ALL_ORDER_COPY_TO_CLIPBOARD.'\')">' . $customers_email_str . '</a>&nbsp;&nbsp;&nbsp;&nbsp;<a title="'.TEXT_CREATE_NEW_NUMBER_SEARCH.'" href="'.$remoteurl.'" target="_blank">'.TEXT_EMAIL_ADDRESS.'</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="telecom_unknow.php?keywords='.tep_output_string_protected($order->customer['email_address']).'">'.TEXT_TEL_UNKNOW.'</a>'; 
      ?></td>
        </tr>
        <tr>
        <td class="main" valign="top" width="30%"><?php echo ENTRY_PAYMENT_METHOD; ?></td>
        <td class="main"><?php echo $order->info['payment_method']; ?></td>
        </tr>
        <?php
        if ( (($order->info['cc_type']) || ($order->info['cc_owner']) || ($order->info['cc_number'])) ) {
          ?>
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
        //判断配送地址与帐单邮寄地址是否一样
        $address_diff_arr = array();
        $address_diff_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' and billing_address='0'"); 
        while($address_diff_array = tep_db_fetch_array($address_diff_query)){

          $address_diff_arr[$address_diff_array['address_id']] = $address_diff_array['value'];
        }
        $billing_diff_arr = array();
        $billing_diff_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' and billing_address='1'");
        while($billing_diff_array = tep_db_fetch_array($billing_diff_query)){

          $billing_diff_arr[$billing_diff_array['address_id']] = $billing_diff_array['value'];
        }
        $billing_address_flag = false;
        $address_i = 0;
        foreach($address_diff_arr as $key=>$value){

          if(trim($value) == trim($billing_diff_arr[$key])){

            $address_i++;
          }
        }
        if(count($address_diff_arr) == $address_i){

          $billing_address_flag = true;
        }
            $address_temp_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' and billing_address='0'");
            $count_num = tep_db_num_rows($address_temp_query);
            if($count_num > 0){
            ?>
            <tr>
              <td colspan="2">
              <hr width="100%" style="border-width: medium medium 1px; border-style: none none dashed; height: 2px; margin: 5px 0px; border-color: -moz-use-text-color -moz-use-text-color rgb(204, 204, 204);">
              </td> 
            </tr>
            <tr>
            <td class="main" colspan="2"><?php echo TEXT_ADDRESS_INFO.($billing_address_flag == true ? '（'.TEXT_BILLING_ADDRESS.'）' : '');?></td>
            </tr>
            <?php
        $address_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' and billing_address='0' order by id");
        while($address_array = tep_db_fetch_array($address_query)){

          $address_title_query = tep_db_query("select * from ". TABLE_ADDRESS ." where id=".$address_array['address_id']); 
          $address_title_array = tep_db_fetch_array($address_title_query);
          if(trim($address_title_array['name']) != '' && trim($address_array['value']) != ''){
            echo '<tr>';
            echo '<td class="main" valign="top">'. $address_title_array['name'] .':</td>';
            echo '<td class="main">'. htmlspecialchars($address_array['value']) .'</td>';
            echo '</tr>';
          }
          tep_db_free_result($address_title_query);
        }
        tep_db_free_result($address_query);
            } 
        $address_temp_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' and billing_address='1'");
            $count_num = tep_db_num_rows($address_temp_query);
            if($count_num > 0 && $billing_address_flag == false){
            ?>
            <tr>
              <td colspan="2">
              <hr width="100%" style="border-width: medium medium 1px; border-style: none none dashed; height: 2px; margin: 5px 0px; border-color: -moz-use-text-color -moz-use-text-color rgb(204, 204, 204);">
              </td> 
            </tr>
            <tr>
            <td class="main" colspan="2"><?php echo TEXT_BILLING_ADDRESS;?></td>
            </tr>
            <?php
        $address_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' and billing_address='1' order by id");
        while($address_array = tep_db_fetch_array($address_query)){

          $address_title_query = tep_db_query("select * from ". TABLE_ADDRESS ." where id=".$address_array['address_id']); 
          $address_title_array = tep_db_fetch_array($address_title_query);
          if(trim($address_title_array['name']) != '' && trim($address_array['value']) != ''){
            echo '<tr>';
            echo '<td class="main" valign="top">'. $address_title_array['name'] .':</td>';
            echo '<td class="main">'. htmlspecialchars($address_array['value']) .'</td>';
            echo '</tr>';
          }
          tep_db_free_result($address_title_query);
        }
        tep_db_free_result($address_query);
            }
            ?>
        </table>
        </div>
        <?php // 订单备注 ?>
        <div style="float:left; width:100%;">
        <div id="orders_client">
        <h3>Customer Info</h3>
        <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
        <td class="main" valign="top" width="30%" nowrap><?php echo TEXT_IP_ADDRESS;?></td>
        <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_ip'] ? $order->info['orders_ip'] : 'UNKNOW',IP_LIGHT_KEYWORDS);?></td>
        </tr>
        <tr>
        <td class="main" valign="top" width="30%"><?php echo TEXT_HOST_NAME;?></td>
        <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_host_name']?'<font'.($order->info['orders_host_name'] == $order->info['orders_ip'] ? ' color="red"':'').'>'.$order->info['orders_host_name'].'</font>':'UNKNOW',HOST_NAME_LIGHT_KEYWORDS);?></td>
        </tr>
        <tr>
        <td class="main" valign="top" width="30%"><?php echo TEXT_USER_AGENT;?></td>
        <td class="main" style="word-break:break-all;word-wrap:break-word;overflow:hidden;display:block;"><?php echo tep_high_light_by_keywords($order->info['orders_user_agent'] ? $order->info['orders_user_agent'] : 'UNKNOW',USER_AGENT_LIGHT_KEYWORDS);?></td>
        </tr>
        <?php if ($order->info['orders_user_agent']) {?>
          <tr>
            <td class="main" valign="top" width="30%">OS:</td>
            <td class="main"><?php echo tep_high_light_by_keywords(getOS($order->info['orders_user_agent']),OS_LIGHT_KEYWORDS);?></td>
            </tr>
            <tr>
            <td class="main" valign="top" width="30%"><?php echo TEXT_BROWSER_TYPE;?></td>
            <td class="main">
            <?php $browser_info = getBrowserInfo($order->info['orders_user_agent']);?>
            <?php echo tep_high_light_by_keywords($browser_info['longName'] . ' ' . $browser_info['version'],BROWSER_LIGHT_KEYWORDS); ?>
            </td>
            </tr>
            <?php }?>
            <tr>
            <td class="main" valign="top" width="30%"><?php echo TEXT_BROWSER_LANGUAGE;?></td>
            <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_http_accept_language'] ? $order->info['orders_http_accept_language'] : 'UNKNOW',HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS);?></td>
            </tr>
            <tr>
            <td class="main" valign="top" width="30%"><?php echo TEXT_PC_LANGUAGE;?></td>
            <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_system_language'] ? $order->info['orders_system_language'] : 'UNKNOW',SYSTEM_LANGUAGE_LIGHT_KEYWORDS);?></td>
            </tr>
            <tr>
            <td class="main" valign="top" width="30%"><?php echo TEXT_USERS_LANGUAGE;?></td>
            <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_user_language'] ? $order->info['orders_user_language'] : 'UNKNOW',USER_LANGUAGE_LIGHT_KEYWORDS);?></td>
            </tr>
            <tr>
            <td class="main" valign="top" width="30%"><?php echo TEXT_SCREEN_RESOLUTION;?></td>
            <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_screen_resolution'] ? $order->info['orders_screen_resolution'] : 'UNKNOW',SCREEN_RESOLUTION_LIGHT_KEYWORDS);?></td>
            </tr>
            <tr>
            <td class="main" valign="top" width="30%"><?php echo TEXT_SCREEN_COLOR;?></td>
            <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_color_depth'] ? $order->info['orders_color_depth'] : 'UNKNOW',COLOR_DEPTH_LIGHT_KEYWORDS);?></td>
            </tr>
            <tr>
            <td class="main" valign="top" width="30%">Flash:</td>
            <td class="main">
            <?php echo tep_high_light_by_keywords($order->info['orders_flash_enable'] === '1' ? 'YES' : ($order->info['orders_flash_enable'] === '0' ? 'NO' : 'UNKNOW'),FLASH_LIGHT_KEYWORDS);?>
            </td>
            </tr>
            <?php 
            if ($order->info['orders_flash_enable']) {
              ?>
                <tr>
                <td class="main" valign="top" width="30%"><?php echo TEXT_FLASH_VERSION;?></td>
                <td class="main"><?php echo tep_high_light_by_keywords($order->info['orders_flash_version'],FLASH_VERSION_LIGHT_KEYWORDS);?></td>
                </tr>
                <?php } ?>
                <tr>
                <td class="main" valign="top" width="30%">Director:</td>
                <td class="main">
                <?php echo tep_high_light_by_keywords($order->info['orders_director_enable'] === '1' ? 'YES' : ($order->info['orders_director_enable'] === '0' ? 'NO' : 'UNKNOW'),DIRECTOR_LIGHT_KEYWORDS);?>
                </td>
                </tr>
                <tr>
                <td class="main" valign="top" width="30%">Quick time:</td>
                <td class="main">
                <?php echo tep_high_light_by_keywords($order->info['orders_quicktime_enable'] === '1' ? 'YES' : ($order->info['orders_quicktime_enable'] === '0' ? 'NO' : 'UNKNOW'),QUICK_TIME_LIGHT_KEYWORDS);?>
                </td>
                </tr>
                <tr>
                <td class="main" valign="top" width="30%">Real player:</td>
                <td class="main">
                <?php echo tep_high_light_by_keywords($order->info['orders_realplayer_enable'] === '1' ? 'YES' : ($order->info['orders_realplayer_enable'] === '0' ? 'NO' : 'UNKNOW'),REAL_PLAYER_LIGHT_KEYWORDS);?>
                </td>
                </tr>
                <tr>
                <td class="main" valign="top" width="30%">Windows media:</td>
                <td class="main">
                <?php echo tep_high_light_by_keywords($order->info['orders_windows_media_enable'] === '1' ? 'YES' : ($order->info['orders_windows_media_enable'] === '0' ? 'NO' : 'UNKNOW'),WINDOWS_MEDIA_LIGHT_KEYWORDS);?>
                </td>
                </tr>
                <tr>
                <td class="main" valign="top" width="30%">Pdf:</td>
                <td class="main">
                <?php echo tep_high_light_by_keywords($order->info['orders_pdf_enable'] === '1' ? 'YES' : ($order->info['orders_pdf_enable'] === '0' ? 'NO' : 'UNKNOW'),PDF_LIGHT_KEYWORDS);?>
                </td>
                </tr>
                <tr>
                <td class="main" valign="top" width="30%">Java:</td>
                <td class="main">
                <?php echo tep_high_light_by_keywords($order->info['orders_java_enable'] === '1' ? 'YES' : ($order->info['orders_java_enable'] === '0' ? 'NO' : 'UNKNOW'),JAVA_LIGHT_KEYWORDS);?>
                </td>
                </tr>
                </table>
                </div>
                <?php //访问解析 ?>
                <div id="orders_referer">
                <h3>Referer Info</h3>
                <table width="100%" border="0" cellspacing="0" cellpadding="2">
                <tr>
                <td class="main" valign="top" width="30%" nowrap="nowrap">Referer:</td>
                <td class="main"><p
                style="word-break:break-all;width:250px;word-wrap:break-word;overflow:hidden;display:block;"><?php echo mb_convert_encoding(urldecode($order->info['orders_ref']),"utf-8");?></p></td>
                </tr>
                <?php if ($order->info['orders_ref_keywords']) { ?>
                  <tr>
                    <td class="main" valign="top" width="30%">Keywords:</td>
                    <td class="main"><?php echo $order->info['orders_ref_keywords'];?></td>
                    </tr>
                    <?php } ?>
                    </table>
                    </div>

                    <?php 
                       //检测此订单的支付方法，当前是否存在
                       $payment_array = payment::getPaymentList(); 
                       $payment_exists_flag = true;
                       if(!in_array($order->info['payment_method'],$payment_array[1])){

                         $payment_exists_flag = false;
                       }
                       $payment_show_flag = true;
                       $payment_paypal_show_flag = true; 
                       $payment_show = payment::getInstance($order->info['site_id']);
                       if($payment_exists_flag == true){
                         if($payment_show->admin_get_payment_symbol(payment::changeRomaji($order->info['payment_method'],'code')) == 1){
                           $payment_show_flag = true; 
                         }else{
                           $payment_show_flag = false; 
                         }
                       }else{

                         if($order->info['telecom_name']!='' || $order->info['telecom_tel']!= '' || $order->info['telecom_email']!='' || $order->info['telecom_money']!= ''){
                           if($order->info['paypal_business']!='' || $order->info['paypal_countrycode']!='' || $order->info['paypal_payerstatus']!='' || $order->info['paypal_paymenttype']!= ''){

                             $payment_show_flag = false; 
                             $payment_paypal_show_flag = true;
                           }
                         }else{

                           $payment_show_flag = false;
                           $payment_paypal_show_flag = false;
                         }
                       }
                       if($payment_exists_flag == true){
                         if($payment_show->admin_get_payment_symbol(payment::changeRomaji($order->info['payment_method'],'code')) == 2){
                           $payment_paypal_show_flag = true; 
                         }else{
                           $payment_paypal_show_flag = false; 
                         }
                       } 
                       if ($payment_show_flag) { ?>
                       <?php // 信用卡信息 ?>

                        <div id="orders_telecom">
                        <h3><?php echo TEXT_CART_INFO;?></h3>
                        <table width="100%" border="0" cellspacing="0" cellpadding="2" class="order02_link">
                        <tr>
                        <td class="main" valign="top" width="20%"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_name']);?>&search_type=username"><?php echo TEXT_CART_HOLDER;?></a></td>
                        <td class="main" width="30%"><?php echo $order->info['telecom_name'];?></td>
                        <td class="main" valign="top"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_tel']);?>&search_type=telno"><?php echo TEXT_TEL_NUMBER;?></a></td>
                        <td class="main"><?php echo tep_high_light_by_keywords($order->info['telecom_tel'],TELNO_KEYWORDS);?></a></td>
                        </tr>
                        <tr>
                        <td class="main" valign="top"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_email']);?>&search_type=email"><?php echo TEXT_EMAIL_ADDRESS_INFO;?></a></td>
                        <td class="main"><?php echo $order->info['telecom_email'];?></a></td>
                        <td class="main" valign="top"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_money']);?>&search_type=money"><?php echo TEXT_PRICE;?></a></td>
                        <td class="main"><?php echo $order->info['telecom_money'];?></a></td>
                        </tr>
                        </table>
                        </div>

                        <?php }else if ($payment_paypal_show_flag) {?>
                        <?php // PAYPAL信息 ?>

                            <div id="orders_paypal">
                            <h3><?php echo TEXT_CART_INFO;?></h3>
                            <table width="100%" border="0" cellspacing="0" cellpadding="2" class="order02_link">
                            <tr>
                            <td class="main" valign="top" width="20%"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_name']);?>"><?php echo TEXT_CART_HOLDER;?></a></td>
                            <td class="main" colspan="3"><?php echo $order->info['telecom_name'].(isset($order->info['paypal_business']) && $order->info['paypal_business'] != '' ? ' / '.$order->info['paypal_business'] : '');?></td> 
                            </tr>
                            <tr>
                            <td class="main" valign="top"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_email']);?>"><?php echo TEXT_EMAIL_ADDRESS_INFO;?></a></td>
                            <td class="main" colspan="3"><?php echo $order->info['telecom_email'];?></a></td> 
			    </tr>
			    <tr>
                            <td class="main" valign="top"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_tel']);?>"><?php echo TEXT_TEL_NUMBER;?></a></td>
			    <td class="main"><?php echo tep_high_light_by_keywords($order->info['telecom_tel'],TELNO_KEYWORDS);?></a></td> 
                            <td class="main" valign="top"><a href="telecom_unknow.php?keywords=<?php echo tep_output_string_protected($order->info['telecom_money']);?>"><?php echo TEXT_PRICE;?></a></td>
                            <td class="main"><?php echo $order->info['telecom_money'];?></a></td>
                            </tr>
                            <tr>
                            <td class="main" valign="top" width="20%"><?php echo TEXT_COUNTRY_CODE?></td>
                            <td class="main" width="30%"><?php echo $order->info['paypal_countrycode'];?></td>
                            <td class="main" valign="top"><?php echo TEXT_PAYER_STATUS;?></td>
                            <td class="main"><?php echo $order->info['paypal_payerstatus'];?></a></td>
                            </tr>
                            <tr>
                            <td class="main" valign="top"><?php echo TEXT_PAYMENT_STATUS;?></td>
                            <td class="main"><?php echo $order->info['paypal_paymentstatus'];?></a></td>
                            <td class="main" valign="top"><?php echo TEXT_PAYMENT_TYPE;?></td>
                            <td class="main"><?php echo $order->info['paypal_paymenttype'];?></a></td>
                            </tr>
                            </table>
                            </div>
                            <?php } ?>
                            <?php // 注文履历 ?>
                            <?php // 订单历史5条 ?>
                            <div id="orders_history">
                            <h3><a href="<?php echo tep_href_link('customers_products.php', 'cID='.$order->customer['id'].'&cpage=1')?>">Order History</a></h3>
                            <?php 
                            $customer_email_raw = tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".$order->info['orders_id']."'"); 
      $customer_email_res = tep_db_fetch_array($customer_email_raw); 
      $history_list_array = array(); 
      //判断此顾客邮箱是否有预约订单
      $preorder_num_query = tep_db_query("select orders_id,date_purchased from ".TABLE_PREORDERS." where customers_email_address = '".$customer_email_res['customers_email_address']."'"); 
      $preorder_num = tep_db_num_rows($preorder_num_query);
      if($preorder_num > 0){
        //判断此顾客邮箱预约订单是否大于等于5个
        if($preorder_num >= 5){
          $preorder_history_query = tep_db_query("
            select orders_id, date_purchased 
            from ".TABLE_PREORDERS." 
            where   customers_email_address = '".$customer_email_res['customers_email_address']."'
            order by date_purchased desc
            limit 5
          ");
        }else{
          $preorder_history_query = $preorder_num_query; 
        }
        while ($preorder_history_res = tep_db_fetch_array($preorder_history_query)) {
          $history_list_array['p_'.$preorder_history_res['orders_id']] = strtotime($preorder_history_res['date_purchased']); 
        }
      }

      //判断此顾客邮箱订单是否大于等于5个
      $order_num_query = tep_db_query("select orders_id,date_purchased from ".TABLE_ORDERS." where customers_email_address = '".$customer_email_res['customers_email_address']."'"); 
      $order_num = tep_db_num_rows($order_num_query); 
      if($order_num >= 5){
        $order_history_query = tep_db_query("
          select orders_id, date_purchased 
          from ".TABLE_ORDERS." 
          where   customers_email_address = '".$customer_email_res['customers_email_address']."'
          order by date_purchased desc
          limit 5
          ");
      }else{
        $order_history_query = $order_num_query; 
      }

      while ($order_history_res = tep_db_fetch_array($order_history_query)) {
        $history_list_array['o_'.$order_history_res['orders_id']] = strtotime($order_history_res['date_purchased']); 
      }   
      if (!empty($history_list_array)) {
        arsort($history_list_array); 
        ?>
          <table width="100%" border="0" cellspacing="0" cellpadding="2">
          <?php
          $history_list_num = 0; 
        foreach ($history_list_array as $h_key => $h_value) { 
          if ($history_list_num > 4) {
            break; 
          }
          $from_site_single = 0;
          $history_table = TABLE_ORDERS; 
          $from_site_char = substr($h_key, 0, 1);
          $h_order_id = substr($h_key, 2);
          if ($from_site_char == 'p') {
            $from_site_single = 1;
            $history_table = TABLE_PREORDERS; 
          }
          $order_history_info_raw = tep_db_query("select orders_id, date_purchased, orders_status_name, site_id from ".$history_table." where orders_id = '".$h_order_id."'"); 
          $order_history_info = tep_db_fetch_array($order_history_info_raw); 
          ?>
            <tr>
            <td class="main">
            <?php
            $store_name_raw = tep_db_query("select * from ".TABLE_SITES." where id = '".$order_history_info['site_id']."'");  
          $store_name_res = tep_db_fetch_array($store_name_raw); 
          echo $store_name_res['romaji']; 
          ?>
            </td> 
            <td class="main">
            <?php
            if (!$from_site_single) {
              echo TEXT_ORDER_HISTORY_FROM_ORDER; 
            } else {
              echo TEXT_ORDER_HISTORY_FROM_PREORDER; 
            }
          ?>
            </td>
            <td class="main"><?php echo $order_history_info['date_purchased'];?></td>
            <td class="main">
            <?php 
            if (!$from_site_single) {
              echo strip_tags(tep_get_ot_total_by_orders_id($order_history_info['orders_id'],true));
            } else {
              echo strip_tags(tep_get_pre_ot_total_by_orders_id($order_history_info['orders_id'],true));
            }
          ?>
            </td>
            <td class="main"><?php echo $order_history_info['orders_status_name'];?></td>
            </tr>
            <?php
            $history_list_num++; 
        }
        ?>
          </table>
          <?php
      } else {
        echo "no orders";
      }
      ?>
        </div>
        </div>
        </div> 
        <div class="pageHeading_box_right">
        <div id="orders_comment">
        <h3>Order Comment</h3>
        <form action="ajax_orders.php" onsubmit="return validate_comment()" id='form_orders_comment' method="post">

        <textarea name="orders_comment" cols="100" rows="10" 
        style = "overflow-y:auto"class="pageHeading_box_textarea"><?php echo stripslashes($order->info['orders_comment']);?></textarea><br>
        <input type="hidden" name="orders_id" value="<?php echo $order->info['orders_id'];?>">
        <input type="hidden" name="orders_comment_flag" value="">
        <input type="hidden" name="page" value="<?php echo $_GET['page'];?>">
        <div align="right" style="clear:both;"><input type="Submit" value="<?php echo TEXT_SAVE;?>"></div>
        </form>
        </div>
        <div id="orders_answer">
        <?php
        // 取得问答的答案
        $total_order_query = tep_db_query("select * from ".TABLE_ORDERS_TOTAL." where orders_id = '".$order->info['orders_id']."' and class = 'ot_total'"); 
      $total_order_res = tep_db_fetch_array($total_order_query); 
      $total_order_sum = 0; 
      if ($total_order_res) {
        $total_order_sum = $total_order_res['value']; 
      }
      // 自动或者手动判断问答种类
      // 0=>贩卖, 1=>买取, 2=>信用卡, 3=>返点/来店 , 4=>不需要支付
      ?>
        <h3><?php echo TEXT_ORDER_ANSWER;?></h3>
        <!--new order answer{{-->
          <?php
          $order_id = $order->info['orders_id'];
          $formtype = tep_check_order_type($order_id);
          $payment_romaji = tep_get_payment_code_by_order_id($order_id); 
          $oa_form_sql = "select * from ".TABLE_OA_FORM." where formtype = '".$formtype."' and payment_romaji = '".$payment_romaji."'";
          $form = tep_db_fetch_object(tep_db_query($oa_form_sql), "HM_Form");
          if(!$form){

            $oa_form_temp_sql = "select * from ".TABLE_OA_FORM." limit 0,1";  
            $form = tep_db_fetch_object(tep_db_query($oa_form_temp_sql), "HM_Form");
            $form->id = '';
            $form->groups = array();
            $form->payment_romaji = $payment_romaji;
            $form->formtype = $formtype;
            $form->option = '';
          }
          if($form){
            $form->loadOrderValue($order_id);
            $form->setAction('oa_answer_process.php?oID='.$order_id);
            $form->render();
          }
          ?>
            </td>
            </tr>
            </table>
            </div>
            </div>
            <!-- right -->
            </td>
            </tr>
            <?php // 信用调查 ?>
            <tr>
            <td>
            <div id="orders_credit">
            <h3><?php echo TEXT_CREDIT_FIND;?></h3>
            <table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr>
            <form action="ajax_orders.php?orders_id=<?php echo $order->info['orders_id'];?>" id='form_orders_credit' method="post">
            <td class="main">
            <div >
            <div id="customer_fax_textarea" style="display:none">
            <textarea name="orders_credit" style="width:98%;height:42px;*height:40px;"><?php echo tep_get_customers_fax_by_id($order->customer['id']);?></textarea>
            <input type="hidden" name="orders_id" value="<?php echo $order->info['orders_id'];?>">
            <input type="hidden" name="page" value="<?php echo $_GET['page'];?>">
            </div>
            <div id="customer_fax_text" style="width:98%;height:42px;*height:40px;overflow-y:auto">
            <?php 
            $fax_arr = explode('|',CUSTOMER_FAX_KEYWORDS); 
            echo str_replace("\n","<br>",tep_replace_to_red($fax_arr,tep_get_customers_fax_by_id($order->customer['id'])));
            ?>
            </div>
            </td>
            <td class="main" width="30">
            <div id="customer_fax_textarea_input" style="display:none">
            <input type="submit" value="<?php echo TEXT_ORDER_SAVE;?>">
            </div>
            <div id="customer_fax_text_input">
            <input type="button" onclick="show_edit_fax()" value="<?php echo IMAGE_EDIT;?>">
            </div>
            </td>
            </form>
            </tr>
            </table>
          </div>
        </td>
      </tr>
      <?php //订单商品 ?>
      <tr>
        <td>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
        <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></td>
      </tr>
  <?php
      for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
        echo '    <tr class="dataTableRow">' . "\n" . 
       '      <td class="dataTableContent" valign="top" align="right" nowrap>' . $order->products[$i]['qty']. tep_get_full_count2($order->products[$i]['qty'], $order->products[$i]['id'], $order->products[$i]['rate']) . '&nbsp;x</td>' . "\n" .
       '      <td class="dataTableContent" valign="top">' . $order->products[$i]['name'];
        if ($order->products[$i]['price'] != '0') {
          if ($order->products[$i]['price'] < 0) {
            echo ' (<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '',$currencies->format($order->products[$i]['price'], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL.')';
          } else {
            echo ' ('.$currencies->format($order->products[$i]['price'], true, $order->info['currency'], $order->info['currency_value']).')';
          }
        } else if($order->products[$i]['final_price'] != '0'){
          if ($order->products[$i]['final_price'] < 0) {
            echo ' (<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '',$currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL.')';
          } else {
            echo ' ('.$currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']).')';
          }
        }
if (isset($order->products[$i]['attributes']) && $order->products[$i]['attributes'] && ($k = sizeof($order->products[$i]['attributes'])) > 0) {
  $all_show_option_id = array();
  $all_show_option = array();
  $option_item_order_sql = "select it.id from ".TABLE_PRODUCTS."
  p,".TABLE_OPTION_ITEM." it 
  where p.products_id = '".(int)$order->products[$i]['id']."' 
  and p.belong_to_option = it.group_id 
  and it.status = 1
  order by it.sort_num,it.title";
  $option_item_order_query = tep_db_query($option_item_order_sql);
  while($show_option_row_item = tep_db_fetch_array($option_item_order_query)){
    $all_show_option_id[] = $show_option_row_item['id'];
  }
  $op_include_array = array(); 
  for ($j = 0; $j < $k; $j++) {
    $all_show_option[$order->products[$i]['attributes'][$j]['option_item_id']] = $order->products[$i]['attributes'][$j];
  }
  foreach($all_show_option_id as $t_item_id){
    $op_include_array[] = $all_show_option[$t_item_id]['id']; 
    if (is_array($all_show_option[$t_item_id]['option_info'])) {
      echo '<table><tr><td valign="top">-&nbsp; </td><td>' .  $all_show_option[$t_item_id]['option_info']['title'] . ': ' .  htmlspecialchars(str_replace(array("<br>", "<BR>"), "",$all_show_option[$t_item_id]['option_info']['value']));
      if ($all_show_option[$t_item_id]['price'] != '0'){
        if ($all_show_option[$t_item_id]['price'] < 0) {
          echo ' (<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '',$currencies->format($all_show_option[$t_item_id]['price'], true, $order->info['currency'], $order->info['currency_value'])) . '</font>'.TEXT_MONEY_SYMBOL.')';
        } else {
          echo ' (' .$currencies->format($all_show_option[$t_item_id]['price'], true, $order->info['currency'], $order->info['currency_value']) . ')';
        }
      }
      echo '</td></tr>';
    }  
    if (is_array($all_show_option[$t_item_id]['option_info'])) {
      echo '</table>';
    }
  }
  foreach ($order->products[$i]['attributes'] as $ex_key => $ex_value) {
    if (!in_array($ex_value['id'], $op_include_array)) {
      echo '<table><tr><td valign="top">-&nbsp; </td><td>' .  $ex_value['option_info']['title'] . ': ' .  htmlspecialchars(str_replace(array("<br>", "<BR>"), "",$ex_value['option_info']['value']));
      if ($ex_value['price'] != '0') {
        if ($ex_value['price'] < 0) {
          echo ' (<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '',$currencies->format($ex_value['price'], true, $order->info['currency'], $order->info['currency_value'])) . '</font>'.TEXT_MONEY_SYMBOL.')';
        } else {
          echo ' (' .$currencies->format($ex_value['price'], true, $order->info['currency'], $order->info['currency_value']) . ')';
        }
      }
      echo '</td></tr></table>';
    }
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
                '      <td class="dataTableContent" valign="top" nowrap>' . $order->products[$i]['model'] . '</td>' . "\n" .
                '      <td class="dataTableContent" align="right" valign="top">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n" .
                '      <td class="dataTableContent" align="right" valign="top" nowrap>';
              if ($price_with_tax != '---') {
                if ($order->products[$i]['final_price'] < 0) {
                  echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $price_with_tax).'</font>'.TEXT_MONEY_SYMBOL; 
                } else {
                  echo $price_with_tax;
                }
              } else {
                echo $price_with_tax;
              }

              echo '</td>' . "\n" .
                '      <td class="dataTableContent" align="right" valign="top" nowrap>';
              if ($tprice_with_tax != '---') {
                if ($order->products[$i]['final_price'] < 0) {
                  echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $tprice_with_tax).'</font>'.TEXT_MONEY_SYMBOL; 
                } else {
                  echo $tprice_with_tax;
                }
              } else {
                echo $tprice_with_tax;
              }
              echo '</td>' . "\n";
              echo '    </tr>' . "\n";
            }
          ?>
            <tr>
            <td align="right" colspan="9">
            <table border="0" cellspacing="0" cellpadding="2">
            <?php
            $total_point_key = 0;
            $total_total_key = 0;
            $total_point_value = 0;
            $total_total_value = 0;
            foreach($order->totals as $total_key=>$total_value){

              if($total_value['class'] == 'ot_point'){

                $total_point_value = $total_value;
                $total_point_key = $total_key;
                unset($order->totals[$total_key]);
              }    
              if($total_value['class'] == 'ot_total'){

                $total_total_value = $total_value;
                $total_total_key = $total_key;
                unset($order->totals[$total_key]);
              }
            }
            $order->totals[$total_point_key] = $total_point_value;
            $order->totals[$total_total_key] = $total_total_value;
            $total_point_flag = false;
            foreach ($order->totals as $i=>$total_value) {
              if ($order->totals[$i]['class'] == 'ot_point') {
                $total_point_flag = true;
                $campaign_query = tep_db_query("select * from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$_GET['oID']."' and site_id = '".$order->info['site_id']."'"); 
                $campaign_res = tep_db_fetch_array($campaign_query);
                if ($campaign_res) {
                  if ((int)$campaign_res['campaign_fee'] == 0) {
                    if($total_point_flag == true && $total_fee_flag == false){
                      $total_fee_flag = true;
                     //配送费用,手续费用
                     echo 
                        '    <tr>' . "\n" .
                        '      <td align="right" class="smallText">' . TEXT_SHIPPING_FEE . '</td>' . "\n" .
                        '      <td align="right" class="smallText">' . $currencies->format($order->info['shipping_fee']) . '</td>' . "\n" .
                        '    </tr>' . "\n";
                     echo 
                       '    <tr>' . "\n" .
                       '      <td align="right" class="smallText">' . TEXT_CODE_HANDLE_FEE . '</td>' . "\n" .
                       '      <td align="right" class="smallText">' . $currencies->format($order->info['code_fee']) . '</td>' . "\n" .
                       '    </tr>' . "\n"; 
                    }
                    continue; 
                  }
                } else {
                  if ($order->totals[$i]['value'] == 0) {
                    if($total_point_flag == true && $total_fee_flag == false){
                      $total_fee_flag = true;
                      //配送费用,手续费用
                      echo 
                          '    <tr>' . "\n" .
                          '      <td align="right" class="smallText">' . TEXT_SHIPPING_FEE . '</td>' . "\n" .
                          '      <td align="right" class="smallText">' . $currencies->format($order->info['shipping_fee']) . '</td>' . "\n" .
                          '    </tr>' . "\n";
                      echo 
                          '    <tr>' . "\n" .
                          '      <td align="right" class="smallText">' . TEXT_CODE_HANDLE_FEE . '</td>' . "\n" .
                          '      <td align="right" class="smallText">' . $currencies->format($order->info['code_fee']) . '</td>' . "\n" .
                          '    </tr>' . "\n"; 
                    }
                    continue; 
                  }
                }
              }
              $totals_str = $order->totals[$i]['class'] == 'ot_custom' ? ':' : '';
              echo 
                '    <tr>' . "\n" .
                '      <td align="right" class="smallText">' . $order->totals[$i]['title'] . $totals_str .'</td>' . "\n" .
                '      <td align="right" class="smallText">';
              // add font color for '-' value

              if ($order->totals[$i]['class'] == 'ot_point') {
                $campaign_query = tep_db_query("select * from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$_GET['oID']."' and site_id = '".$order->info['site_id']."'"); 
                $campaign_res = tep_db_fetch_array($campaign_query);
                if ($campaign_res) {
                  echo '<font color="red">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($campaign_res['campaign_fee'])).'</font>'.TEXT_MONEY_SYMBOL;
                } else {
                  if ($order->totals[$i]['value']>=0) {
                    echo '<font color="red">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->totals[$i]['value'])).'</font>'.TEXT_MONEY_SYMBOL;
                  } else {
                    echo "<font color='red'>";
                    echo str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->totals[$i]['value']));
                    echo "</font>".TEXT_MONEY_SYMBOL;
                  }
                }
              } else {
                if($order->totals[$i]['value']>=0){
                  echo $currencies->format($order->totals[$i]['value']);
                }else{
                  if($order->totals[$i]['class'] == 'ot_total'){
                    echo "<font color='red'>";
                    echo str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->totals[$i]['value']));
                    echo "</font>".TEXT_MONEY_SYMBOL;
                  }else{
                    echo "<font color='red'>"; echo str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->totals[$i]['value']));
                    echo "</font>".TEXT_MONEY_SYMBOL;
                  }
                }
              }
              echo '</td>' . "\n" .
                '    </tr>' . "\n";
              if($total_point_flag == true && $total_fee_flag == false){
                $total_fee_flag = true;
                //配送费用,手续费用
                echo 
               '    <tr>' . "\n" .
               '      <td align="right" class="smallText">' . TEXT_SHIPPING_FEE . '</td>' . "\n" .
               '      <td align="right" class="smallText">' . $currencies->format($order->info['shipping_fee']) . '</td>' . "\n" .
               '    </tr>' . "\n";
                echo 
                  '    <tr>' . "\n" .
                  '      <td align="right" class="smallText">' . TEXT_CODE_HANDLE_FEE . '</td>' . "\n" .
                  '      <td align="right" class="smallText">' . $currencies->format($order->info['code_fee']) . '</td>' . "\n" .
                  '    </tr>' . "\n";
              }
            }
          ?>
            <tr>
            <td align="right" class="smallText"><?php echo TEXT_ORDER_TEST_TEXT;?></td>
            <?php
            $warning_sell = '';
          $warning_sell =
            tep_get_ot_total_num_by_text(abs($order->totals[sizeof($order->totals)-1]['value']));
          ?>
            <td align="right" class="smallText"><?php echo $warning_sell; ?></td>
            </tr>
            </table>
            </td>
            </tr>
            </table>
            </td>
            </tr>
	    <?php // 订单状态历史记录 ?>
            <!-- orders status history -->
            <tr>
            <td class="main" align="left">
            <table border="0" cellspacing="2" cellpadding="5" bgcolor="#cccccc">
            <tr bgcolor="#FFFFFF">
            <td class="smallText" align="center"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
            <td class="smallText" align="center" nowrap="true"><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></td>
            <td class="smallText" align="center" nowrap="true"><?php echo TABLE_HEADING_STATUS; ?></td>
            <td class="smallText" align="center"><?php echo TABLE_HEADING_COMMENTS; ?></td>
            <td class="smallText" align="center"><?php echo TEXT_OPERATE_USER; ?></td>
            <?php if ($ocertify->npermission >= 15) { ?>
            <td class="smallText" align="center"></td>
            <?php }?> 
            </tr>
            <?php
            $comment_warning_arr = explode('|',COMMENT_SHOW_KEYWORDS);
            $cpayment = payment::getInstance($orders['site_id']);
            $orders_history_query = tep_db_query("select orders_status_history_id, orders_status_id, date_added, customer_notified, comments, user_added from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($oID) . "' order by date_added");
          $orders_status_history_str = '';
          if (tep_db_num_rows($orders_history_query)) {
            while ($orders_history = tep_db_fetch_array($orders_history_query)) {
              $select_select = $orders_history['orders_status_id'];
              echo 
                '    <tr bgcolor="#FFFFFF">' . "\n" .
                '      <td class="smallText" align="center">' . tep_datetime_short_torihiki($orders_history['date_added']) . '</td>' . "\n" .
                '      <td class="smallText" align="center">';
              if ($orders_history['customer_notified'] == '1') {
                echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK) . "</td>\n";
              } else {
                echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS) . "</td>\n";
              }
              echo '      <td class="smallText">' .  $orders_status_array[$orders_history['orders_status_id']];
              echo '</td>' . "\n";
              //不显示重复备注信息
              $orders_explode_array = array();
              $orders_explode_all_array = explode("\n",$orders_history['comments']);
              $orders_explode_array = explode(':',$orders_explode_all_array[0]);
              if(count($orders_explode_all_array) > 1){

                if(strlen(trim($orders_explode_array[1])) == 0){ 
                  if(count($orders_explode_array) > 1){
                    unset($orders_explode_all_array[0]);
                  }
                  $orders_history_comment = implode("\n",$orders_explode_all_array); 
                }else{ 
                  $orders_temp_str = end($orders_explode_all_array);
                  array_pop($orders_explode_all_array);
                  $orders_comments_old_str = implode("\n",$orders_explode_all_array);
                  if(trim($orders_comments_old_str) == trim($orders_status_history_str) && $orders_status_history_str != ''){

                    $orders_history_comment = $orders_temp_str;
                  }else{
                    $orders_history_comment = $orders_history['comments']; 
                  }
               }
           }else{
               $orders_history_comment = $orders_history['comments'];
           }
           if($orders_history['comments'] != $orders_status_history_str && $payment_exists_flag == true){
             echo '      <td class="smallText"><p style="word-break:break-all;word-wrap:break-word;overflow:hidden;display:block;width:170px;">' .  tep_replace_to_red($comment_warning_arr,nl2br(tep_db_output($cpayment->admin_get_comment(payment::changeRomaji($order->info['payment_method'],PAYMENT_RETURN_TYPE_CODE),stripslashes($orders_history_comment))))) . '&nbsp;</p></td>' . "\n";
           }else{

             echo '      <td class="smallText"><p style="word-break:break-all;word-wrap:break-word;overflow:hidden;display:block;width:170px;">&nbsp;</p></td>' . "\n";
           }
              echo '<td class="smallText">'.htmlspecialchars($orders_history['user_added']).'</td>'; 
              $orders_status_history_str = $orders_history['comments'];
              if ($ocertify->npermission >= 15) {
                echo '<td>';
                $order_confirm_payment_raw = tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".tep_db_input($oID)."'"); 
                $order_confirm_payment_res = tep_db_fetch_array($order_confirm_payment_raw); 
                echo '<input type="button" class="element_button" onclick="del_confirm_payment_time(\''.$oID.'\', \''.$orders_history['orders_status_history_id'].'\', \''.NOTICE_DEL_CONFIRM_PAYEMENT_TIME.'\', \''.NOTICE_DEL_CONFIRM_PAYMENT_TIME_SUCCESS.'\', \''.TEXT_INPUT_ONE_TIME_PASSWORD.'\', \''.TEXT_INPUT_PASSWORD_ERROR.'\', \''.$ocertify->npermission.'\');" value="'.DEL_CONFIRM_PAYMENT_TIME.'">'; 
                echo '</td>';
              }
              echo '</tr>' . "\n";
            }
          } else {
            echo
              '    <tr bgcolor="#FFFFFF">' . "\n" .
              '      <td class="smallText" colspan="6">' . TEXT_NO_ORDER_HISTORY . '</td>' . "\n" .
              '    </tr>' . "\n";
          }
          ?>
            </table>
            </td>
            </tr>
            </table>
            <!-- orders status history -->
            <!-- mail -->

            <table border="0" width="100%">
            <tr>
            <td width="50%">
            <?php echo tep_draw_form('sele_act', FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=update_order', 'post'); ?>
            <table width="100%" border="0">
            <tr>
            <td class="main"><?php echo ENTRY_STATUS; ?>
            <?php echo tep_draw_pull_down_menu('s_status', $orders_statuses, $select_select, ' onChange="new_mail_text(this, \'s_status\',\'comments\',\'title\', \''.JS_TEXT_ALL_ORDER_NOT_CHOOSE.'\', \''.JS_TEXT_ALL_ORDER_NO_OPTION_ORDER.'\')" id="mail_title_status"'); ?>
            <input type="hidden" name="tmp_orders_id" id="tmp_orders_id" value="<?php echo $order->info['orders_id'];?>">
            <div style="display:none" id='edit_order_send_mail'></div>
            </td>
            </tr>
            <?php

            $ma_se = "select * from ".TABLE_MAIL_TEMPLATES." where ";
          if(!isset($_GET['status']) || $_GET['status'] == ""){
            $ma_se .= " flag = 'ORDERS_STATUS_MAIL_TEMPLATES_".$order->info['orders_status']."' ";

            // 用来判断是否选中 送信&通知，如果nomail==1则不选中
            $ma_s = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$order->info['orders_status']."'"));
          }else{
            $ma_se .= " flag = 'PREORDERS_STATUS_MAIL_TEMPLATES_".$_GET['status']."' ";

            // 用来判断是否选中 送信&通知，如果nomail==1则不选中
            $ma_s = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$_GET['status']."'"));
          }
          $ma_se .= "and site_id='0'";
          $mail_sele = tep_db_query($ma_se);
          $mail_sql  = tep_db_fetch_array($mail_sele);
          $sta       = isset($_GET['status'])?$_GET['status']:'';
          ?>
            <tr>
            <td class="main"><?php echo ENTRY_EMAIL_TITLE; ?><?php echo tep_draw_input_field('title', $mail_sql['title'],'style="width:315px;" id="mail_title"'); ?></td>
            </tr>
            <tr>
            <td class="main">
            <?php echo TABLE_HEADING_COMMENTS; ?>:
            <?php echo TEXT_MAIL_CONTENT_INFO;?>
            <table><tr class="smalltext"><td><font color="red">※</font>&nbsp;
          <?php echo TEXT_ORDER_COPY;?></td><td>
            <?php echo TEXT_ORDER_LOGIN;?>
            </td></tr></table>
            </td>
            </tr>
            <tr>
            <td class="main">
            <textarea id="c_comments" style="font-family:monospace;font-size:12px; width:400px;" name="comments" wrap="hard" rows="30" cols="74"><?php echo str_replace('${MAIL_COMMENT}',orders_a($order->info['orders_id']),$mail_sql['contents']); ?></textarea>
            </td>
            </tr>
            <tr>
            <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr>
            <td class="main"><?php echo tep_draw_checkbox_field('notify', '', true && $ma_s['nomail'] != '1', '', 'id="notify"'); ?>
            <?php echo TEXT_ORDER_SEND_MAIL;?></td>
            <td class="main"><?php echo tep_draw_checkbox_field('notify_comments', '', true && $ma_s['nomail'] != '1', '', 'id="notify_comments"'); ?>
            <?php echo TEXT_ORDER_STATUS;?></td>
            </tr>
            <tr>
            <td class="main" colspan="2">
            <?php echo tep_draw_hidden_field('qu_type', $orders_questions_type);?> 
            <br><font color="#FF0000;">
            <?php echo TEXT_ORDER_HAS_ERROR;?></font><br><br><a href="javascript:void(0);"><?php echo tep_html_element_button(IMAGE_UPDATE, 'onclick="confrim_mail_title(\''.$_GET['oID'].'\', \''.TEXT_STATUS_MAIL_TITLE_CHANGED.'\');"'); ?></a></td>
            </tr>
            </table>
            </td>
            </tr>
            </form>
            </table>
            <!-- mail -->
            </td>
            <td width="50%" align="left" valign="top">
            <table width="100%">
            <tr><td width="30%">&nbsp; 
                  </td>
                  </tr>
                  </table>
                  </td>
                  </tr>
                  </table>

                  <tr>
                  <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action','status','questions_type'))) . '">' .  tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td>
                  </tr>
                  </table>
                  </td>
                  </tr>


                  <?php
                  // edit over
        } else if ($_GET['action']=='show_manual_info' && $_GET['oID'] && $_GET['pID']) {
//显示订单的手册信息
$oID=$_GET['oID'];
$pID=$_GET['pID'];
$page=$_GET['page'];
$site_id=0;
$categories_info_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pID."'");
$categories_info_array=tep_db_fetch_array($categories_info_query);
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_info_array['categories_id']."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$cp_manual_query=tep_db_query("select categories_id,categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid_array['parent_id']."' and site_id='".$site_id."'");
$cp_manual_array=tep_db_fetch_array($cp_manual_query);

//判断是否存在上级分类
$categories_parent_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_pid_array['parent_id']."'");
$categories_parent_num = tep_db_num_rows($categories_parent_query);

if($categories_parent_num > 0){
 
  $categories_parent_array = tep_db_fetch_array($categories_parent_query);
  $parent_manual_query = tep_db_query("select categories_id,categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_parent_array['parent_id']."' and site_id='".$site_id."'");
  $parent_manual_array = tep_db_fetch_array($parent_manual_query);
  tep_db_free_result($parent_manual_query);
}
tep_db_free_result($categories_parent_query);

$c_manual_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_info_array['categories_id']."' and site_id='".$site_id."'");
$c_manual_array=tep_db_fetch_array($c_manual_query);

$pro_manual_query=tep_db_query("select products_name,p_manual from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$pID."' and site_id='".$site_id."'");
$pro_manual_array=tep_db_fetch_array($pro_manual_query);
$params="oID=".$oID."&page=".$page;
?>
<td width="100%" valign="top" id ='categories_right_td'>
<table border="0" width="100%" cellspacing="0" cellpadding="0">

<tr>
            <td width="100%" style="padding-top:10px;" valign="top">

            <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
            <td class="pageHeading"><?php echo ($parent_manual_array['categories_name'] != '' ? $parent_manual_array['categories_name'].'/' : '').($cp_manual_array['categories_name'] != '' ? $cp_manual_array['categories_name'].'/' : '').($c_manual_array['categories_name'] != '' ? $c_manual_array['categories_name'].'/' : '').$pro_manual_array['products_name'].SHOW_MANUAL_TITLE; ?></td>
            <td align="right" class="smallText" valign="top">
            <table width="275px"  border="0" cellspacing="1" cellpadding="0">
            <tr>
            <td class="smallText" valign='top' align="right">
	    

	    <form name="manual" action="orders.php" method="get">
<input name="keyword" style="width:200px;" type="text" id="keyword" size="40" value="" onclick="CtoH(this.id);">
<input type="submit"  onclick="return check_manual_search_form();" value="<?php echo SHOW_MANUAL_SEARCH;?>">
<input type="hidden" name="action" value="search_manual_info">
</form>
	    </td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="10">
<tr>
<td>
<h2>
<?php echo ORDER_TOP_MANUAL_TEXT.SHOW_MANUAL_TITLE;?>
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=edit_top_categories_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_top_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_top_all"><a href="javascript:void(0);" onclick="manual_show('top', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_top" style="display:none;">
<?php 
$tmp_top_manual = get_configuration_by_site_id('TOP_MANUAL_CONTENT');
echo (!empty($tmp_top_manual)?stripslashes($tmp_top_manual):'<font color="red">'.SHOW_MANUAL_NONE.'</font>');?>
</div>
<hr>
<?php 
$check_categories_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$cp_manual_array['categories_id']."'");
$check_categories_array = tep_db_fetch_array($check_categories_query);
if($check_categories_array['parent_id']!=0){
$get_categories_info = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$check_categories_array['parent_id']."' and site_id='".$site_id."'");
$get_categories_array = tep_db_fetch_array($get_categories_info);
?>
<h2>
<?php echo $get_categories_array['categories_name'].SHOW_MANUAL_TITLE?> 
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=p_categories_manual&parent=1')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_top_categories_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_top_categories_all"><a href="javascript:void(0);" onclick="manual_show('top_categories', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_top_categories" style="display:none;">
<?php echo $get_categories_array['c_manual']!='' ? stripslashes($get_categories_array['c_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>';?>
</div>
<hr>

<?php
}
?>
<h2>
<?php echo $cp_manual_array['categories_name'].SHOW_MANUAL_TITLE?> 
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=p_categories_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_categories_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_categories_all"><a href="javascript:void(0);" onclick="manual_show('categories', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_categories" style="display:none;">
<?php echo $cp_manual_array['c_manual']!='' ? stripslashes($cp_manual_array['c_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>';?>
</div>
<hr>
<h2>
<?php echo $c_manual_array['categories_name'].SHOW_MANUAL_TITLE?>
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=show_categories_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_categories_children_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_categories_children_all"><a href="javascript:void(0);" onclick="manual_show('categories_children', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_categories_children" style="display:none;">
<?php echo $c_manual_array['c_manual']!='' ? stripslashes($c_manual_array['c_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>';?>
</div>
<hr>
<h2>
<?php echo $pro_manual_array['products_name'].SHOW_MANUAL_TITLE?>
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=show_products_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_products_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_products_all"><a href="javascript:void(0);" onclick="manual_show('products', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_products" style="display:none;">
<?php echo $pro_manual_array['p_manual']!='' ? stripslashes($pro_manual_array['p_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>'?>
</div>
<hr>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>


<?php 
}else if($_GET['action']=='search_manual_info'){
//搜索手册信息
$search_res_arr=array();



if(isset($_GET['cid']) && $_GET['cid']!=""){
$cid     = $_GET['cid'];
$site_id = 0;
$s_pID   = $_GET['pID'];
$return_params = tep_get_all_get_params(array('cid','p_pid'));

?>
<td width="100%" valign="top" id ='categories_right_td'>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
$site_id        = 0;
$products_query = tep_db_query("select products_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where categories_id='".$cid."'");

?>
<tr>
<td width="100%" valign="top" style="padding-top:10px;" valign="top">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
	    <td class="pageHeading">
<?php
if (isset($_GET['keyword'])) {
  if ($_GET['keyword'] != '') {
    echo $_GET['keyword'].MANUAL_SEARCH_HEAD; 
  }
}
?>
</td>
            <td align="right" class="smallText" valign="top">
            <table width="275px"  border="0" cellspacing="1" cellpadding="0">
            <tr>
            <td class="smallText" valign='top' align="right">
<form name="manual" action="orders.php" method="get">
<input name="keyword" style="width:200px;" type="text" id="keyword" onclick="CtoH(this.id);" size="40" value="<?php echo$p_keyword; ?>">
<input type="submit"  onclick="return check_manual_search_form();" value="<?php echo SHOW_MANUAL_SEARCH;?>">
<input type="hidden" name="action" value="search_manual_info">
<input type="hidden" name="site_id" value="<?php echo $site_id?>">
</form>
	    	    </td>
</tr>
</table>
</td>
</tr>
</table>
</td>

</tr>


<tr>
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="2" >
<tr>
<td>
</td>
</tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="2" >

<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent"><?php echo SEARCH_CAT_PRO_TITLE;?> </td>
<td class="dataTableHeadingContent"><?php echo SEARCH_MANUAL_CONTENT;?> </td>
<td class="dataTableHeadingContent" align="right" nowrap><?php echo SEARCH_MANUAL_LOOK;?> </td>
</tr>

<?php
$odd            = "dataTableSecondRow";
$even="dataTableRow";
$products_query = tep_db_query("select products_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where categories_id='".$cid."'");
$products_info_sql = "select products_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where categories_id='".$cid."'";
$products_split    = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_info_sql, $products_numrows);
$products_query    = tep_db_query($products_info_sql);
if(isset($_GET['p_pid']) && $_GET['p_pid']!=""){
$p_pid             = $_GET['p_pid'];
}
while($products_array=tep_db_fetch_array($products_query)){
$products_info_query=tep_db_query("select products_name,p_manual from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$products_array['products_id']."' and site_id='".$site_id."'");
$products_info_array=tep_db_fetch_array($products_info_query);
if(empty($products_info_array)){
continue;
}
if(isset($now_class) && $now_class==$odd){
$now_class=$even;	
	}else{
$now_class=$odd	;
	}
if(((!isset($_GET['p_pid']) || !$_GET['p_pid'])&& (!isset($p_pid) || !$p_pid)) || ($products_array['products_id']==$_GET['p_pid'])){
$p_pid=$products_array['products_id'];
}

echo '<tr class='.$now_class.'>';
?>


<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('s_pID')).'p_pid='.$products_array['products_id']);?>'">
<?php echo $products_info_array['products_name']; ?>
</td>
	<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('s_pID')).'p_pid='.$products_array['products_id']);?>'">
<?php $manual_content=$products_info_array['products_manual']=='' ? "<font color='red'>".SHOW_MANUAL_NONE."</font>" : $products_info_array['products_manual'];
 echo $products_info_array['products_manual']=='' ? "<font color='red'>".SHOW_MANUAL_NONE."</font>" :mb_substr(preg_replace('/&.+;/','',strip_tags($manual_content)),0,77,'utf-8');
?>
</td>

        <td class="dataTableContent" align="right"><a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params().'&pID='.$products_array['products_id'].'&action=show_search_manual');?>"><?php echo tep_image(DIR_WS_IMAGES.'icon_info.gif', ICON_INFO);?></a>&nbsp;</td>
</tr>

<?php
}
?>
<tr>
             <td colspan="10"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $products_split->display_count($products_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_MANUAL); ?></td>
                    <td class="smallText" align="right"><?php echo $products_split->display_links($products_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'pID'))); ?></td>
                  </tr>

</table>
</td>
</tr>

</table>
</td>
<tr>
<td align="right">
<a href="<?php echo tep_href_link(FILENAME_ORDERS,$return_params)?>"><button><?php echo SHOW_MANUAL_RETURN;?></button></a>
</td>
</tr>

<?php
}


if((isset($_GET['cid2']) && $_GET['cid2']) && (!isset($_GET['cid']) && !$_GET['cid']) ){
$cid2 = $_GET['cid2'];
$site_id = 0;
$categories_query = tep_db_query("select categories_id from ".TABLE_CATEGORIES." where parent_id='".$cid2."'");
$return_params = tep_get_all_get_params(array('cid2','s_cid2'))
?>
<td>


<tr>
<td width="100%" style="padding-top:10px;" valign="top">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
	    <td class="pageHeading">
<?php
if (isset($_GET['keyword'])) {
  if ($_GET['keyword'] != '') {
    echo $_GET['keyword'].MANUAL_SEARCH_HEAD; 
  }
}
?>
</td>
            <td align="right" class="smallText" valign="top">
            <table width="275px"  border="0" cellspacing="1" cellpadding="0">
            <tr>
            <td class="smallText"  align="right">
<form name="manual" action="orders.php" method="get">
<input name="keyword" style="width:200px;" type="text" id="keyword" onclick="CtoH(this.id);" size="40" value="<?php echo $_GET['keyword'];?>">
<input type="submit"  onclick="return check_manual_search_form();" value="<?php echo SHOW_MANUAL_SEARCH;?>">
<input type="hidden" name="action" value="search_manual_info">
<input type="hidden" name="site_id" value="<?php echo $site_id?>">

</form>

	    	    </td>
</tr>
</table>
</td>
</tr>
</table>
</td>

</tr>


<tr>
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="2" >
<tr>
<td>
</td>
</tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="2" >

<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent"><?php echo SEARCH_CAT_PRO_TITLE;?> </td>
<td class="dataTableHeadingContent"><?php echo SEARCH_MANUAL_CONTENT;?> </td>
<td class="dataTableHeadingContent" align="right" nowrap><?php echo SEARCH_MANUAL_LOOK;?> </td>
</tr>

<?php
$odd    = "dataTableSecondRow";
$even   = "dataTableRow";
if(isset($_GET['s_cid2']) && $_GET['s_cid2']){
$s_cid2  = $_GET['s_cid2'];
}
while($categories_array=tep_db_fetch_array($categories_query)){
$categories_manual_query = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_array['categories_id']."' and site_id='".$site_id."'");
$categories_manual_array = tep_db_fetch_array($categories_manual_query);
if(empty($categories_manual_array)){
continue;
}
if(isset($now_class) && $now_class==$odd){
$now_class=$even;	
	}else{
$now_class=$odd	;
	}

if(((!isset($_GET['s_cid2']) || !$_GET['s_cid2']) &&(!isset($s_cid2) && !$s_cid2))|| ($categories_array['categories_id']==$_GET['s_cid2'])){
$s_cid2=$categories_array['categories_id'];
}
echo '<tr class='.$now_class.'>';
?>
<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array("s_cid2")).'s_cid2='.$categories_array['categories_id']);?>'">
	<?php
$check_categories_query = tep_db_query("select categories_id from ".TABLE_CATEGORIES." where parent_id='".$categories_array['categories_id']."'");
$check_categories_array = tep_db_fetch_array($check_categories_query);
if(empty($check_categories_array)){
?>
<a href="<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params().'cid='.$categories_array['categories_id']);?>">
<?php echo tep_image(DIR_WS_ICONS.'folder.gif', ICON_FOLDER);?></a> 
<?php
}else{
?>
<a href="<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params().'cid3='.$categories_array['categories_id']);?>">
<?php echo tep_image(DIR_WS_ICONS.'folder.gif', ICON_FOLDER);?></a> 
<?php
}
?>
<?php echo $categories_manual_array['categories_name']; ?>
</td>
	<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params("s_cid2").'s_cid2='.$categories_array['categories_id']);?>'"><?php 
$manual_content= $categories_manual_array['c_manual']=='' ? "<font color='red'>".SHOW_MANUAL_NONE."</font>" : $categories_manual_array['c_manual'];
echo $categories_manual_array['c_manual']=='' ? "<font color='red'>".SHOW_MANUAL_NONE."</font>" : mb_substr(preg_replace('/&.+;/','',strip_tags($manual_content)),0,77,'utf-8');
?></td>
        <td class="dataTableContent" align="right"><a href="<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('action')).'cID='.$categories_array['categories_id'].'&action=show_search_manual');?>"><?php echo tep_image(DIR_WS_IMAGES.'icon_info.gif', ICON_INFO);?></a>&nbsp;</td>
</tr>

<?php
}
?>
<tr>
<td></td>
<td></td>
<td align="right">
<a href="<?php echo tep_href_link(FILENAME_ORDERS,$return_params)?>"><button><?php echo SHOW_MANUAL_RETURN;?></button></a>
</td>
</tr>

<?php

}






if((isset($_GET['cPath']) && $_GET['cPath']!="") && !isset($_GET['cid'])  && !isset($_GET['cid2'])){
$cpath   = $_GET['cPath'];
$site_id = 0;
$s_cid   = $_GET['s_cid'];
$p_keyword = $_GET['p_keyword'];
$page      = $_GET['page'];
$params    = "action=search_manual_info&site_id=".$site_id."&s_cid=".$s_cid."&page=".$page."&keyword=".$p_keyword;
$categories_s_query = tep_db_query("select categories_id from ".TABLE_CATEGORIES." where parent_id='".$cpath."'");

?>
<td>


<tr>
<td width="100%" style="padding-top:10px;" valign="top">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
	    <td class="pageHeading">
<?php
if (isset($_GET['keyword'])) {
  if ($_GET['keyword'] != '') {
    echo $_GET['keyword'].MANUAL_SEARCH_HEAD; 
  }
}
?>
</td>
            <td align="right" class="smallText" valign="top">
            <table width="275px"  border="0" cellspacing="1" cellpadding="0">
            <tr>
            <td class="smallText"  align="right">
<form name="manual" action="orders.php" method="get">
<input name="keyword" style="width:200px;" type="text" id="keyword" size="40" onclick="CtoH(this.id);" value="<?php echo$_GET['keyword'];?>">
<input type="submit"  onclick="return check_manual_search_form();" value="<?php echo SHOW_MANUAL_SEARCH;?>">
<input type="hidden" name="action" value="search_manual_info">
<input type="hidden" name="site_id" value="<?php echo $site_id?>">

</form>

	    	    </td>
</tr>
</table>
</td>
</tr>
</table>
</td>

</tr>


<tr>
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="2" >
<tr>
<td>
</td>
</tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="2" >

<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent"><?php echo SEARCH_CAT_PRO_TITLE;?> </td>
<td class="dataTableHeadingContent"><?php echo SEARCH_MANUAL_CONTENT;?> </td>
<td class="dataTableHeadingContent" align="right" nowrap><?php echo SEARCH_MANUAL_LOOK;?> </td>
</tr>

<?php
$odd    = "dataTableSecondRow";
$even   = "dataTableRow";
if(isset($_GET['s_cpath']) && $_GET['s_cpath']){
$s_cpath  = $_GET['s_cpath'];
}
while($categories_s_array=tep_db_fetch_array($categories_s_query)){
$categories_manual_query = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_s_array['categories_id']."' and site_id='".$site_id."'");
$categories_manual_array = tep_db_fetch_array($categories_manual_query);
if(empty($categories_manual_array)){
continue;
}
if(isset($now_class) && $now_class==$odd){
$now_class=$even;	
	}else{
$now_class=$odd	;
	}

if(((!isset($_GET['s_cpath']) || !$_GET['s_cpath']) &&(!isset($s_cpath) && !$s_cpath))|| ($categories_s_array['categories_id']==$_GET['s_cpath'])){
$s_cpath=$categories_s_array['categories_id'];

}
echo '<tr class='.$now_class.'>';
?>
	<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params().'&s_cpath='.$categories_s_array['categories_id']);?>'">
	<?php
$check_categories_query = tep_db_query("select categories_id from ".TABLE_CATEGORIES." where parent_id='".$categories_s_array['categories_id']."'");
$check_categories_array = tep_db_fetch_array($check_categories_query);
if(empty($check_categories_array)){
?>
<a href="<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params().'&cid='.$categories_s_array['categories_id']);?>">
<?php echo tep_image(DIR_WS_ICONS.'folder.gif', ICON_FOLDER);?></a> 
<?php
}else{
?>
<a href="<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params().'&cid2='.$categories_s_array['categories_id']);?>">
<?php echo tep_image(DIR_WS_ICONS.'folder.gif', ICON_FOLDER);?></a> 
<?php
}
?>
<?php echo $categories_manual_array['categories_name']; ?>
</td>
	<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params().'&s_cpath='.$categories_s_array['categories_id']);?>'"><?php 
$manual_content= $categories_manual_array['c_manual']=='' ? "<font color='red'>".SHOW_MANUAL_NONE."</font>" : $categories_manual_array['c_manual'];
echo $categories_manual_array['c_manual']=='' ? "<font color='red'>".SHOW_MANUAL_NONE."</font>" : mb_substr(preg_replace('/&.+;/','',strip_tags($manual_content)),0,77,'utf-8');
?></td>
        <td class="dataTableContent" align="right"><a href="<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('action')).'&cID='.$categories_s_array['categories_id'].'&action=show_search_manual');?>"><?php echo tep_image(DIR_WS_IMAGES.'icon_info.gif', ICON_INFO);?></a>&nbsp;</td>
</tr>

<?php
}
?>
<tr>
<td></td>
<td></td>
<td align="right">
<a href="<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('cPath')))?>"><button><?php echo SHOW_MANUAL_RETURN;?></button></a>
</td>
</tr>
<?php
}





if((isset($_GET['keyword']) && $_GET['keyword']!='') && !isset($_GET['cPath']) && !isset($_GET['cid']) && !isset($_GET['cid2'])){
$keyword=$_GET['keyword'];
$site_id=0;
$categories_info_query=tep_db_query("select categories_id,categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where (categories_name like '%".$keyword."%' or c_manual like '%".$keyword."%') and site_id='".$site_id."'");
$num = mysql_num_rows($categories_info_query);
while($categories_info_array=tep_db_fetch_array($categories_info_query)){
$check_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_info_array['categories_id']."'");
$check_array=tep_db_fetch_array($check_query);
$search_res_arr[]=array('c_id'=>$categories_info_array['categories_id'],'c_name'=>$categories_info_array['categories_name'],'c_manual'=>$categories_info_array['c_manual'],'parent_id'=>$check_array['parent_id']);
}
$products_info_sql   = "select products_id,products_name,p_manual from ".TABLE_PRODUCTS_DESCRIPTION." where (products_name like '%".$keyword."%' or p_manual like '%".$keyword."%') and site_id='".$site_id."'";
$products_info_query = tep_db_query($products_info_sql);
 $num  = $num+mysql_num_rows($products_info_query);
 
?>
<tr>
<td width="100%" style="padding-top:10px;" valign="top">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
	    <td class="pageHeading">
<?php

if (isset($_GET['keyword'])) {
  if ($_GET['keyword'] != '') {
    echo $_GET['keyword'].MANUAL_SEARCH_HEAD; 
  }
}

?>
</td>
            <td align="right" class="smallText" valign="top">
            <table width="275px"  border="0" cellspacing="1" cellpadding="0">
            <tr>
            <td class="smallText" valign='top' align="right">
<form name="manual" action="orders.php" method="get">
<input name="keyword" style="width:200px;" type="text" id="keyword" onclick="CtoH(this.id);" size="40" value="<?php echo $_GET['keyword'];?>">
<input type="submit"  onclick="return check_manual_search_form();" value="<?php echo SHOW_MANUAL_SEARCH;?>">
<input type="hidden" name="action" value="search_manual_info">
</form>

	    	    </td>
</tr>
</table>
</td>
</tr>
</table>
</td>

</tr>


<tr>
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="2" >
<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent"><?php echo SEARCH_CAT_PRO_TITLE;?> </td>
<td class="dataTableHeadingContent"><?php echo SEARCH_MANUAL_CONTENT;?> </td>
<td class="dataTableHeadingContent" align="right" nowrap><?php echo SEARCH_MANUAL_LOOK;?> </td>
</tr>
<?php 
$odd="dataTableSecondRow";
$even="dataTableRow";
if(isset($_GET['s_cid']) && $_GET['s_cid']){
$s_pid=$_GET['s_cid'];
}
foreach($search_res_arr as $key=>$val){
	if(isset($now_class) && $now_class==$odd){
$now_class=$even;	
	}else{
$now_class=$odd	;
	}
if(((!isset($_GET['s_cid']) || !$_GET['s_cid']) && (!isset($s_cid) || !$s_cid)) || ($val['c_id']==$_GET['s_cid'])){
$s_cid=$val['c_id'];
}
echo '<tr class='.$now_class.'>';
?>
	<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('s_cid')).'s_cid='.$val['c_id']);?>'">
<?php 
if($val['parent_id']!=0){
$check_parent_query = tep_db_query("select categories_id from ".TABLE_CATEGORIES." where parent_id='".$val['c_id']."'");
$check_parent_array = tep_db_fetch_array($check_parent_query);
if(empty($check_parent_array)){
	?>
<a href="<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params().'cid='.$val['c_id']);?>"><?php echo tep_image(DIR_WS_ICONS.'folder.gif', ICON_FOLDER);?></a> 

<?php
}else{
	?>
<a href="<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params().'cPath='.$val['c_id']);?>"><?php echo tep_image(DIR_WS_ICONS.'folder.gif', ICON_FOLDER);?></a> 
<?
}
}else{
?>
<a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params().'cPath='.$val['c_id']);?>"><?php echo tep_image(DIR_WS_ICONS.'folder.gif', ICON_FOLDER);?></a> 
<?php }?>

<?php echo $val['c_name']; ?>

</td>
	<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('s_cid')).'s_cid='.$val['c_id']);?>'"> <?php 
	if(isset($val['c_manual']) && $val['c_manual']){
$manual_content= $val['c_manual'];
echo   mb_substr(preg_replace('/&.+;/','',strip_tags($manual_content)),0,77,'utf-8');

	}else{
echo "<font color='red'>".SHOW_MANUAL_NONE."</font>";	
	}
?>
</td>
<td class="dataTableContent" align="right"><a href="<?php echo tep_href_link(FILENAME_ORDERS, 'cPath='.$val['c_id']).'&action=show_search_manual&site_id='.$site_id.'&page='.$page.'&s_pid='.$s_pid.'&s_cid='.$val['c_id'].'&keyword='.$keyword;?>"><?php echo tep_image(DIR_WS_IMAGES.'icon_info.gif', ICON_INFO);?>&nbsp;</a> 
</td>
</tr>
<?php
}
?>

<?php
$manual_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $products_info_sql, $products_query_numrows);
    $products_query = tep_db_query($products_info_sql);
if(isset($_GET['s_pid']) && $_GET['s_pid']){
$s_pid=$_GET['s_pid'];
}
while($products_info_array=tep_db_fetch_array($products_query)){
if(isset($now_class) && $now_class==$odd){
$now_class=$even;	
	}else{
$now_class=$odd	;
	}
if(((!isset($_GET['s_pid']) || !$_GET['s_pid'] ) && (!isset($s_pid) || !$s_pid))|| ($products_info_array['products_id']==$_GET['s_pid'])  ){
$s_pid=$products_info_array['products_id'];

}
echo '<tr class='.$now_class.'>';


?>
	<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('s_pid')).'s_pid='.$products_info_array['products_id']);?>'"><?php echo $products_info_array['products_name']; ?></td>
	<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('s_pid')).'s_pid='.$products_info_array['products_id']);?>'">
<?php 
$manual_content= $products_info_array['p_manual']=='' ? "<font color='red'>".SHOW_MANUAL_NONE."</font>" : $products_info_array['p_manual'];
echo  $products_info_array['p_manual']=='' ? $manual_content : mb_substr(preg_replace('/&.+;/','',strip_tags($manual_content)),0,77,'utf-8');

?>
</td>
        <td class="dataTableContent" align="right"><a href="<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('action')).'action=show_search_manual&pID='.$products_info_array['products_id']);?>"><?php echo tep_image(DIR_WS_IMAGES.'icon_info.gif', ICON_INFO);?>&nbsp;</a></td>
</tr>
<?php

}
?>
<tr>
             <td colspan="10"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
		  <td  valign="top"><?php 
if($num==0){
echo '<b><font color="red">'.MANUAL_SEARCH_NORES.'</font></b>';
} ?></td>
                    <td class="smallText" align="right"></td>
                  </tr>

</table>
</td>
</tr>
<?php

}
?>
</table>
</td>
<?php
}else if($_GET['action']=='show_search_manual'){
//显示搜索手册
$site_id = 0;


	if(isset($_GET['pID']) && $_GET['pID']!=''){

$pid = $_GET['pID'];

$categories_info_query = tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
$categories_info_array = tep_db_fetch_array($categories_info_query);
$categories_pid_query  = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_info_array['categories_id']."'");
$categories_pid_array  = tep_db_fetch_array($categories_pid_query);
$cp_manual_query       = tep_db_query("select categories_id,categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid_array['parent_id']."' and site_id='".$site_id."'");
$cp_manual_array       = tep_db_fetch_array($cp_manual_query);

$c_manual_query        = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_info_array['categories_id']."' and site_id='".$site_id."'");
$c_manual_array        = tep_db_fetch_array($c_manual_query);

$pro_manual_query      = tep_db_query("select products_name,p_manual from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$pid."' and site_id='".$site_id."'");
$pro_manual_array      = tep_db_fetch_array($pro_manual_query);

$check_categories_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$cp_manual_array['categories_id']."'");
$check_categories_array = tep_db_fetch_array($check_categories_query);
if($check_categories_array['parent_id']!=0){
$get_categories = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$check_categories_array['parent_id']."' and site_id='".$site_id."'");
$get_categories_title = tep_db_fetch_array($get_categories);
$title_cp = $get_categories_title['categories_name'].'/';
}
?>
<td width="100%" valign="top" id ='categories_right_td'>
<table border="0" width="100%" cellspacing="0" cellpadding="0">

<tr>
            <td width="100%" style="padding-top:10px;" valign="top">

            <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
	    <td class="pageHeading">
<?php echo $title_cp.$cp_manual_array['categories_name'].'/'.$c_manual_array['categories_name'].'/'.$pro_manual_array['products_name'].SHOW_MANUAL_TITLE; ?>
</td>
            <td align="right" class="smallText" valign="top">
            <table width="275px"  border="0" cellspacing="1" cellpadding="0">
            <tr>
            <td class="smallText" align="right">
<form name="manual" action="orders.php" method="get">
<input name="keyword" style="width:200px;" type="text" id="keyword" onclick="CtoH(this.id);" size="40" value="<?php echo $keyword ?>">
<input type="submit"  onclick="return check_manual_search_form();" value="<?php echo SHOW_MANUAL_SEARCH;?>">
<input type="hidden" name="action" value="search_manual_info">
</form>


	    	    </td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="10" >
<tr>
<td>
<h2>
<?php echo ORDER_TOP_MANUAL_TEXT.SHOW_MANUAL_TITLE;?>
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=edit_top_categories_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_top_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_top_all"><a href="javascript:void(0);" onclick="manual_show('top', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_top" style="display:none;">
<?php 
$tmp_top_manual = get_configuration_by_site_id('TOP_MANUAL_CONTENT');
echo (!empty($tmp_top_manual)?stripslashes($tmp_top_manual):'<font color="red">'.SHOW_MANUAL_NONE.'</font>');?>
</div>
<hr>
<?php 
if($check_categories_array['parent_id']!=0){
$get_categories_info = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$check_categories_array['parent_id']."' and site_id='".$site_id."'");
$get_categories_array = tep_db_fetch_array($get_categories_info);
?>
<h2>
<?php echo $get_categories_array['categories_name'].SHOW_MANUAL_TITLE?> 
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=p_categories_manual')."&cID=".$check_categories_array['parent_id']?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_top_categories_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_top_categories_all"><a href="javascript:void(0);" onclick="manual_show('top_categories', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_top_categories" style="display:none;">
<?php echo $get_categories_array['c_manual']!='' ? stripslashes($get_categories_array['c_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>';?>
</div>
<hr>

<?php
}
?>

<h2>
<?php echo $cp_manual_array['categories_name'].SHOW_MANUAL_TITLE?> 
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=p_categories_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_categories_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_categories_all"><a href="javascript:void(0);" onclick="manual_show('categories', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_categories" style="display:none;">
<?php echo $cp_manual_array['c_manual']!='' ? stripslashes($cp_manual_array['c_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>';?>
</div>
<hr>
<h2>
<?php echo $c_manual_array['categories_name'].SHOW_MANUAL_TITLE?>
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=show_categories_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_categories_children_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_categories_children_all"><a href="javascript:void(0);" onclick="manual_show('categories_children', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_categories_children" style="display:none;">
<?php echo $c_manual_array['c_manual']!='' ? stripslashes($c_manual_array['c_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>';?>
</div>
<hr>
<h2>
<?php echo $pro_manual_array['products_name'].SHOW_MANUAL_TITLE?> <a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=show_products_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>


</h2>
<div id="manual_products_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_products_all"><a href="javascript:void(0);" onclick="manual_show('products', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_products" style="display:none;">
<?php echo $pro_manual_array['p_manual']!='' ? stripslashes($pro_manual_array['p_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>'?>
</div>
<hr>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td align="right">
<a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action','pID')).'action=search_manual_info')?>"><button><?php echo SHOW_MANUAL_RETURN;?></button></a>

</td>
</tr>
</table>
</td>

<?php
	}else if(!(isset($_GET['pID']) && $_GET['pID']!='') && (isset($_GET['cID']) && $_GET['cID']!='')){

$cid       = $_GET['cID'];
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$cid."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$cp_manual_query=tep_db_query("select categories_id,categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid_array['parent_id']."' and site_id='".$site_id."'");
$cp_manual_array=tep_db_fetch_array($cp_manual_query);

$c_manual_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cid."' and site_id='".$site_id."'");
$c_manual_array=tep_db_fetch_array($c_manual_query);
$check_categories_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$cp_manual_array['categories_id']."'");
$check_categories_array = tep_db_fetch_array($check_categories_query);
if($check_categories_array['parent_id']!=0){
$get_categories = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$check_categories_array['parent_id']."' and site_id='".$site_id."'");
$get_categories_title = tep_db_fetch_array($get_categories);
$title_cp = $get_categories_title['categories_name'].'/';
}
?>
<td width="100%" valign="top" id ='categories_right_td'>
<table border="0" width="100%" cellspacing="0" cellpadding="0">

<tr>
            <td width="100%" style="padding-top:10px;" valign="top">

            <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
            <td class="pageHeading"><?php echo $title_cp.$cp_manual_array['categories_name'].'/'.$c_manual_array['categories_name'].SHOW_MANUAL_TITLE; ?></td>
            <td align="right" class="smallText" valign="top">
            <table width="275px"  border="0" cellspacing="1" cellpadding="0">
            <tr>
            <td class="smallText" valign='top'>
<form name="manual" action="orders.php" method="get" align="right">
<input name="keyword" style="width:200px;" type="text" id="keyword" size="40" onclick="CtoH(this.id);" value="<?php echo $p_keyword?>">
<input type="submit"  onclick="return check_manual_search_form();" value="<?php echo SHOW_MANUAL_SEARCH;?>">
<input type="hidden" name="action" value="search_manual_info">
</form>

	    	    </td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="10" >
<tr>
<td>
<h2>
<?php echo ORDER_TOP_MANUAL_TEXT.SHOW_MANUAL_TITLE;?>
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=edit_top_categories_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_top_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_top_all"><a href="javascript:void(0);" onclick="manual_show('top', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_top" style="display:none;">
<?php 
$tmp_top_manual = get_configuration_by_site_id('TOP_MANUAL_CONTENT');
echo (!empty($tmp_top_manual)?stripslashes($tmp_top_manual):'<font color="red">'.SHOW_MANUAL_NONE.'</font>');?>
</div>
<hr>
<?php 
$check_categories_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$cp_manual_array['categories_id']."'");
$check_categories_array = tep_db_fetch_array($check_categories_query);
if($check_categories_array['parent_id']!=0){
$get_categories_info = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$check_categories_array['parent_id']."' and site_id='".$site_id."'");
$get_categories_array = tep_db_fetch_array($get_categories_info);
?>
<h2>
<?php echo $get_categories_array['categories_name'].SHOW_MANUAL_TITLE?> 
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=p_categories_manual')."&cID1=".$check_categories_array['parent_id']?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_top_categories_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_top_categories_all"><a href="javascript:void(0);" onclick="manual_show('top_categories', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_top_categories" style="display:none;">
<?php echo $get_categories_array['c_manual']!='' ? stripslashes($get_categories_array['c_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>';?>
</div>
<hr>

<?php
}
?>
<h2>
<?php echo $cp_manual_array['categories_name'].SHOW_MANUAL_TITLE?>
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=p_categories_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
 </h2>
<div id="manual_categories_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_categories_all"><a href="javascript:void(0);" onclick="manual_show('categories', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_categories" style="display:none;">
<?php echo $cp_manual_array['c_manual']!='' ? stripslashes($cp_manual_array['c_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>';?>
</div>
<hr>
<h2>
<?php echo $c_manual_array['categories_name'].SHOW_MANUAL_TITLE?>
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=show_categories_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_categories_children_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_categories_children_all"><a href="javascript:void(0);" onclick="manual_show('categories_children', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_categories_children" style="display:none;">
<?php echo $c_manual_array['c_manual']!='' ? stripslashes($c_manual_array['c_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>';?>
</div>
<hr>
</td>
</tr>
</table>
</td>
</tr>

<tr>
<td align="right">
<a href="<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('action','cID')).'action=search_manual_info')?>"><button><?php echo SHOW_MANUAL_RETURN;?></button></a>

</td>
</tr>
</table>
</td>
<?php
}else if(!(isset($_GET['pID']) && $_GET['pID']!='') && !(isset($_GET['cID']) && $_GET['cID']!='') && (isset($_GET['cPath']) && $_GET['cPath']!='')){
$cpath = $_GET['cPath'];
$c_parent_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$cpath."'");
$c_parent_array = tep_db_fetch_array($c_parent_query);
if($c_parent_array['parent_id']==0){
$c_manual_query   = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cpath."' and site_id='0'");
$c_manual_array   = tep_db_fetch_array($c_manual_query);
$c_title          = $c_manual_array['categories_name'].SHOW_MANUAL_TITLE;
}else{
$c_manual_query   = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cpath."' and site_id='0'");
$c_manual_array   = tep_db_fetch_array($c_manual_query);
$cp_manual_query  = tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$c_parent_array['parent_id']."' and site_id='0'");
$cp_manual_array  = tep_db_fetch_array($cp_manual_query);
$c_title          = $cp_manual_array['categories_name'].'/'.$c_manual_array['categories_name'].SHOW_MANUAL_TITLE;

}
?>
<td width="100%" valign="top" id ='categories_right_td'>
<table border="0" width="100%" cellspacing="0" cellpadding="0">

<tr>
            <td width="100%" style="padding-top:10px;" valign="top">

            <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
            <td class="pageHeading"><?php echo $c_title; ?></td>
            <td align="right" class="smallText" valign="top">
            <table width="275px"  border="0" cellspacing="1" cellpadding="0">
            <tr>
            <td class="smallText" valign='top' align="right">
<form name="manual" action="orders.php" method="get">
<input name="keyword" style="width:200px;" type="text" id="keyword" size="40" onclick="CtoH(this.id);" value="<?php echo $keyword?>">
<input type="submit"  onclick="return check_manual_search_form();" value="<?php echo SHOW_MANUAL_SEARCH;?>">
<input type="hidden" name="action" value="search_manual_info">
</form>

	    	    </td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="10" >
<tr>
<td>
<h2>
<?php echo ORDER_TOP_MANUAL_TEXT.SHOW_MANUAL_TITLE;?>
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=edit_top_categories_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_top_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_top_all"><a href="javascript:void(0);" onclick="manual_show('top', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_top" style="display:none;">
<?php 
$tmp_top_manual = get_configuration_by_site_id('TOP_MANUAL_CONTENT');
echo (!empty($tmp_top_manual)?stripslashes($tmp_top_manual):'<font color="red">'.SHOW_MANUAL_NONE.'</font>');?>
</div>
<hr>
<?php 
if($c_parent_array['parent_id'] == 0){
?>
<h2>
<?php echo $c_manual_array['categories_name'].SHOW_MANUAL_TITLE?>
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=p_categories_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>

 </h2>
<div id="manual_products_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_products_all"><a href="javascript:void(0);" onclick="manual_show('products', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_products" style="display:none;">
<?php echo $c_manual_content = $c_manual_array['c_manual']!='' ? stripslashes($c_manual_array['c_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>';
?>
</div>
<hr>
<?php
}else{
?>
<h2>
<?php echo $cp_manual_array['categories_name'].SHOW_MANUAL_TITLE?>
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=p_categories_manual&p_cpath='.$_GET['cPath'])?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
 </h2>
<div id="manual_products_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_products_all"><a href="javascript:void(0);" onclick="manual_show('products', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_products" style="display:none;">
<?php echo $cp_manual_array['c_manual']!='' ? stripslashes($cp_manual_array['c_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>';?>
</div>
<hr>
<h2>
<?php echo $c_manual_array['categories_name'].SHOW_MANUAL_TITLE?>
<a href="<?php echo tep_href_link(FILENAME_PRODUCTS_MANUAL, tep_get_all_get_params(array('action')).'action=show_categories_manual')?>"><?php echo tep_html_element_button(MANUAL_SEARCH_EDIT);?></a>
</h2>
<div id="manual_categories_show">
</div>
<br>
<div align="left" style="display:none;" id="manual_categories_all"><a href="javascript:void(0);" onclick="manual_show('categories', '<?php echo ORDER_MANUAL_ALL_HIDE;?>', '<?php echo ORDER_MANUAL_ALL_SHOW;?>');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a></div>
<div id="manual_categories" style="display:none;">
<?php echo $c_manual_array['c_manual']!='' ? stripslashes($c_manual_array['c_manual']) : '<font color="red">'.SHOW_MANUAL_NONE.'</font>';?>
</div>
<hr>

<?php
}
?>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td align="right">
<a href="<?php echo tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('action','cPath')).'action=search_manual_info')?>"><button><?php echo SHOW_MANUAL_RETURN;?></button></a>

</td>
</tr>
</table>
</td>
<?php	}
}else {
          //订单列表 
          ?>
            <tr>
            <td width="100%" height="40">

            <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
            <td class="pageHeading" nowrap><?php echo HEADING_TITLE; ?></td>
            <td align="right" class="smallText">
            <table width=""  border="0" cellspacing="1" cellpadding="0">
            <tr>
            <td class="smallText" valign='top' align="right">
			<div class="right_space">
            <?php echo tep_draw_form('orders1', FILENAME_ORDERS, '',
                'get','id="orders1" onsubmit="return false"'); ?><?php echo
            TEXT_ORDER_FIND;?> 
            <input name="keywords" style="width:310px;" type="text" id="keywords" size="40" value="<?php if(isset($_GET['keywords'])) echo stripslashes($_GET['keywords']); ?>">
            <select name="search_type" onChange='search_type_changed(this)' style="text-align:left;">
            <option value="none"><?php echo TEXT_ORDER_FIND_SELECT;?></option>
            <option value="orders_id"><?php echo TEXT_ORDER_FIND_OID;?></option>
            <option value="customers_name"><?php echo TEXT_ORDER_FIND_NAME;?></option>
            <option value="email"><?php echo TEXT_ORDER_FIND_MAIL_ADD;?></option>
            <option value="products_name"><?php echo TEXT_ORDER_FIND_PRODUCT_NAME ;?></option>
            <option value="value"><?php echo TEXT_ORDER_AMOUNT_SEARCH;?></option>
            <?php
            foreach ($all_search_status as $as_key => $as_value) {
              ?>
                <option value="<?php echo 'os_'.$as_key?>"><?php echo ORDERS_STATUS_SELECT_PRE.$as_value.ORDERS_STATUS_SELECT_LAST;?></option> 
                <?php
            }
          foreach ($all_payment_method as $p_method){
            ?>
              <option value="<?php echo "payment_method|".$p_method;?>"><?php echo
              ORDERS_PAYMENT_METHOD_PRE.$p_method.ORDERS_PAYMENT_METHOD_LAST;?></option> 
              <?php
          }
          ?>
            <option value="type|sell"><?php echo 
            TEXT_ORDER_TYPE_PRE.TEXT_ORDER_TYPE_SELL.TEXT_ORDER_TYPE_LAST;?></option>
            <option value="type|buy"><?php echo 
            TEXT_ORDER_TYPE_PRE.TEXT_ORDER_TYPE_BUY.TEXT_ORDER_TYPE_LAST;?></option>
            <option value="type|mix"><?php echo 
            TEXT_ORDER_TYPE_PRE.TEXT_ORDER_TYPE_MIX.TEXT_ORDER_TYPE_LAST;?></option>
            </select>
            <?php
                echo tep_draw_hidden_field('site_id', $site_list_string); 
                if (isset($_GET['mark'])) {
                  echo tep_draw_hidden_field('mark', $_GET['mark']); 
                }
                if(isset($_GET['order_sort'])){
                  echo tep_draw_hidden_field('order_sort', $_GET['order_sort']); 
                }else{
                  echo tep_draw_hidden_field('order_sort', $orders_sort); 
                }
                if(isset($_GET['order_type'])){
                  echo tep_draw_hidden_field('order_type', $_GET['order_type']); 
                }else{
                  echo tep_draw_hidden_field('order_type', $orders_type); 
                }
            ?>
            </form>
			</div>
            </td>
            </tr>
            </table>
            </td>
                </tr>
                </table>

                </td>
                </tr>
          <tr>
          <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
          <td valign="top">
          <?php // 订单信息预览，配合javascript，永远浮动在屏幕右下角 ?>
          <div id="orders_info_box" style="display:none; position:absolute; background:#FFFF00; width:70%;z-index:2; /*bottom:0;margin-top:40px;right:0;width:200px;*/">&nbsp;</div>
          <?php
          if ($ocertify->npermission >= 15) {
            if(!tep_session_is_registered('reload')) $reload = 'yes';
          }
        ?>
          <?php echo tep_draw_form('sele_act', FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) .  'action=sele_act','post'); ?>
          <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
          <td valign="bottom" width="60%">
          <?php tep_site_filter(FILENAME_ORDERS);?>
          </td>
          <td align="right">
          <?php
          if(isset($_GET['mark']) && $_GET['mark'] != ''){
            
            $get_mark_info = explode('-', $_GET['mark']);
          }else{
 
            $work_array = array();
            $work_default = '0|1|2|3|4';
            if(PERSONAL_SETTING_ORDERS_WORK != ''){
              $work_setting_array = unserialize(PERSONAL_SETTING_ORDERS_WORK);
              if(array_key_exists($user_info['name'],$work_setting_array)){

                $work_setting_str = $work_setting_array[$user_info['name']];
              }else{
                $work_setting_str = $work_default; 
              }
            }else{
              $work_setting_str = $work_default; 
            }
            $work_array = explode('|',$work_setting_str); 
            $work_str = implode('-',$work_array);
          }
        if(!is_array($get_mark_info)&&$get_mark_info==null){
          $get_mark_info = array();
        }
         ?> 
          <table border="0" width="100%" cellpadding="0" cellspacing="1" class="table_wrapper">
            <tr> 
<?php
if(PERSONAL_SETTING_TRANSACTION_FINISH == ''){
  $is_finish  = '0';
}else {
  $personal_transaction_array = unserialize(PERSONAL_SETTING_TRANSACTION_FINISH);
  if (array_key_exists($ocertify->auth_user,$personal_transaction_array)) {
    $is_finish  = $personal_transaction_array[$ocertify->auth_user];
  } else {
    $is_finish  = '0';
  }
}            
$transaction_class = ($is_finish == '1')?'mark_flag_checked':'mark_flag_unchecked';
?>
              <td id="mark_t" class="<?php echo  $transaction_class; ?>" style="white-space:nowrap;"  align="center" onclick="transaction_finish(<?php echo $is_finish;?>,'<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'site_id')));?>')"><?php echo TEXT_TRANSACTION_FINISH;?>&nbsp;</td> 
              <td id="mark_o" width="15%"  class="<?php echo (in_array('0', $get_mark_info) || (!isset($_GET['mark']) && in_array('0', $work_array)))?'mark_flag_checked':'mark_flag_unchecked';?>" align="center" onclick="mark_work(this,'0','<?php echo isset($_GET['mark']) ? $_GET['mark'] : $work_str;?>', '<?php echo $_GET['site_id'];?>', '<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'mark', 'site_id')));?>')">&nbsp;</td> 
              <td id="mark_a" width="15%"  class="<?php echo (in_array('1', $get_mark_info) || (!isset($_GET['mark']) && in_array('1', $work_array)))?'mark_flag_checked':'mark_flag_unchecked';?>" align="center" onclick="mark_work(this,'1','<?php echo isset($_GET['mark']) ? $_GET['mark'] : $work_str;?>', '<?php echo $_GET['site_id'];?>', '<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'mark', 'site_id')));?>')">A</td> 
              <td id="mark_b" width="15%"  class="<?php echo (in_array('2', $get_mark_info) || (!isset($_GET['mark']) && in_array('2',$work_array)))?'mark_flag_checked':'mark_flag_unchecked';?>" align="center" onclick="mark_work(this,'2','<?php echo isset($_GET['mark']) ? $_GET['mark'] : $work_str;?>', '<?php echo $_GET['site_id'];?>', '<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'mark', 'site_id')));?>')">B</td> 
              <td id="mark_c" width="15%"  class="<?php echo (in_array('3', $get_mark_info) || (!isset($_GET['mark']) && in_array('3',$work_array)))?'mark_flag_checked':'mark_flag_unchecked';?>" align="center" onclick="mark_work(this,'3','<?php echo isset($_GET['mark']) ? $_GET['mark'] : $work_str;?>', '<?php echo $_GET['site_id'];?>', '<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'mark', 'site_id')));?>')">C</td> 
              <td id="mark_d" width="15%" class="<?php echo (in_array('4', $get_mark_info) || (!isset($_GET['mark']) && in_array('4',$work_array)))?'mark_flag_checked':'mark_flag_unchecked';?>" align="center" onclick="mark_work(this,'4','<?php echo isset($_GET['mark']) ? $_GET['mark'] : $work_str;?>', '<?php echo $_GET['site_id'];?>', '<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'mark', 'site_id')));?>')">D</td> 
            </tr>
          </table>
          </td>
          </tr>
          </table>

          <table border="0" width="100%" cellspacing="0" cellpadding="2" id='orders_list_table'>
          <tr class="dataTableHeadingRow">
          <?php 
          if ($ocertify->npermission) {
            ?>
              <td class="dataTableHeadingContent"><input type="checkbox" name="all_chk" onClick="all_check()"></td>
              <?php 
          }
        ?>
          <td class="dataTableHeadingContent_order"><?php 
          if ($HTTP_GET_VARS['order_sort'] == 'site_romaji'){
            echo "<a class='head_sort_order_select' onclick='select_sort(0,\"".$type_str."\")'  href='".tep_href_link(FILENAME_ORDERS,
                tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).'order_sort=site_romaji&order_type='.$type_str)."'>";
            echo TABLE_HEADING_SITE;
            if($type_str == 'asc'){
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }else{
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }
          }else{
            if($orders_sort == 'site_romaji' && !isset($_GET['order_sort'])){
               $orders_type_str = $orders_type == 'asc' ? 'desc' : 'asc'; 
               echo "<a class='head_sort_order_select' onclick='select_sort(0,\"".$orders_type_str."\")' href='".tep_href_link(FILENAME_ORDERS,
                tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).'order_sort=site_romaji&order_type='.$orders_type_str)."'>";
               echo TABLE_HEADING_SITE;
            if($orders_type == 'desc'){
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }else{
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }
            }else{
              echo "<a class='head_sort_order' onclick='select_sort(0,\"desc\")' href='".tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).
                'order_sort=site_romaji&order_type=desc')."'>";
              echo TABLE_HEADING_SITE;
            }
          }
        echo "</a>";
        ?></td>
          <td class="dataTableHeadingContent_order" width="22%"><?php 
          if ($HTTP_GET_VARS['order_sort'] == 'customers_name'){
            echo "<a class='head_sort_order_select' onclick='select_sort(1,\"".$type_str."\")' href='".tep_href_link(FILENAME_ORDERS,
                tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).'order_sort=customers_name&order_type='.$type_str)."'>";
            echo TABLE_HEADING_CUSTOMERS; 
            if($type_str == 'asc'){
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }else{
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }
          }else{
            if($orders_sort == 'customers_name' && !isset($_GET['order_sort'])){
              $orders_type_str = $orders_type == 'asc' ? 'desc' : 'asc';
              echo "<a class='head_sort_order_select' onclick='select_sort(1,\"".$orders_type_str."\")' href='".tep_href_link(FILENAME_ORDERS,
                tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).'order_sort=customers_name&order_type='.$orders_type_str)."'>";
              echo TABLE_HEADING_CUSTOMERS; 
            if($orders_type == 'desc'){
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }else{
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }
            }else{
              echo "<a class='head_sort_order' onclick='select_sort(1,\"desc\")' href='".tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).
                'order_sort=customers_name&order_type=desc')."'>";
              echo TABLE_HEADING_CUSTOMERS; 
            }
          }
        echo "</a>";
        ?></td>
          <td class="dataTableHeadingContent_order" align="right"><?php 
          if ($HTTP_GET_VARS['order_sort'] == 'ot_total'){
            echo "<a class='head_sort_order_select' onclick='select_sort(2,\"".$type_str."\")' href='".tep_href_link(FILENAME_ORDERS,
                tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).'order_sort=ot_total&order_type='.$type_str)."'>";
            echo TABLE_HEADING_ORDER_TOTAL;
            if($type_str == 'asc'){
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }else{
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }
          }else{
            if($orders_sort == 'ot_total' && !isset($_GET['order_sort'])){
              $orders_type_str = $orders_type == 'asc' ? 'desc' : 'asc';
              echo "<a class='head_sort_order_select' onclick='select_sort(2,\"".$orders_type_str."\")' href='".tep_href_link(FILENAME_ORDERS,
                tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).'order_sort=ot_total&order_type='. $orders_type_str)."'>";
              echo TABLE_HEADING_ORDER_TOTAL;
            if($orders_type == 'desc'){
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }else{
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }
            }else{
              echo "<a class='head_sort_order' onclick='select_sort(2,\"desc\")' href='".tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).
                'order_sort=ot_total&order_type=desc')."'>";
              echo TABLE_HEADING_ORDER_TOTAL;
            }
          }
        echo "</a>";
        ?></td>
	  <td class="dataTableHeadingContent_order" align="center"></td>
          <td class="dataTableHeadingContent_order" align="center"><?php 
          if ($HTTP_GET_VARS['order_sort'] == 'torihiki_date'){
            echo "<a class='head_sort_order_select' onclick='select_sort(3,\"".$type_str."\")' href='".tep_href_link(FILENAME_ORDERS,
                tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).'order_sort=torihiki_date&order_type='.$type_str)."'>";
            echo TEXT_ORDER_ORDER_DATE;
            if($type_str == 'asc'){
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }else{
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }
          }else{
            if($orders_sort == 'torihiki_date' && !isset($_GET['order_sort'])){
              $orders_type_str = $orders_type == 'asc' ? 'desc' : 'asc';
              echo "<a class='head_sort_order_select' onclick='select_sort(3,\"".$orders_type_str."\")' href='".tep_href_link(FILENAME_ORDERS,
                tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).'order_sort=torihiki_date&order_type='.$orders_type_str)."'>";
              echo TEXT_ORDER_ORDER_DATE;
            if($orders_type == 'desc'){
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }else{
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }
            }else{
              echo "<a class='head_sort_order' onclick='select_sort(3,\"desc\")' href='".tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).
                'order_sort=torihiki_date&order_type=desc')."'>";
              echo TEXT_ORDER_ORDER_DATE;
            }
          }
        echo "</a>";
        ?></td>
          <td class="dataTableHeadingContent">&nbsp;</td>
          <td class="dataTableHeadingContent">&nbsp;</td>
          <td class="dataTableHeadingContent">&nbsp;</td>
          <td class="dataTableHeadingContent_order" align="center"><?php 
          if ($HTTP_GET_VARS['order_sort'] == 'date_purchased'){
            echo "<a class='head_sort_order_select' onclick='select_sort(4,\"".$type_str."\")' href='".tep_href_link(FILENAME_ORDERS,
                tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).'order_sort=date_purchased&order_type='.$type_str)."'>";
            echo TABLE_HEADING_DATE_PURCHASED; 
            if($type_str == 'asc'){
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }else{
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }
          }else{
            if($orders_sort == 'date_purchased' && !isset($_GET['order_sort'])){
              $orders_type_str = $orders_type == 'asc' ? 'desc' : 'asc';
              echo "<a class='head_sort_order_select' onclick='select_sort(4,\"".$orders_type_str."\")' href='".tep_href_link(FILENAME_ORDERS,
                tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).'order_sort=date_purchased&order_type='.$orders_type_str)."'>";
              echo TABLE_HEADING_DATE_PURCHASED; 
            if($orders_type == 'desc'){
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }else{
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }
            }else{
              echo "<a class='head_sort_order' onclick='select_sort(4,\"desc\")' href='".tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).
                'order_sort=date_purchased&order_type=desc')."'>";
              echo TABLE_HEADING_DATE_PURCHASED; 
            }
          }
        echo "</a>";
        ?></td>
          <td class="dataTableHeadingContent" align="right"></td>
          <td class="dataTableHeadingContent_order" align="right"><?php 
          if ($HTTP_GET_VARS['order_sort'] == 'orders_status_name'){
            echo "<a class='head_sort_order_select' onclick='select_sort(5,\"".$type_str."\")' href='".tep_href_link(FILENAME_ORDERS,
                tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).'order_sort=orders_status_name&order_type='.$type_str)."'>";
            echo TABLE_HEADING_STATUS; 
            if($type_str == 'asc'){
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }else{
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }
          }else{
            if($orders_sort == 'orders_status_name' && !isset($_GET['order_sort'])){
              $orders_type_str = $orders_type == 'asc' ? 'desc' : 'asc';
              echo "<a class='head_sort_order_select' onclick='select_sort(5,\"".$orders_type_str."\")' href='".tep_href_link(FILENAME_ORDERS,
                tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).'order_sort=orders_status_name&order_type='.$orders_type_str)."'>";
              echo TABLE_HEADING_STATUS; 
            if($orders_type == 'desc'){
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }else{
              echo "<font color='#facb9c'>";
              echo TEXT_SORT_ASC;
              echo "</font>";
              echo "<font color='#c0c0c0'>";
              echo TEXT_SORT_DESC;
              echo "</font>";
            }
            }else{
              echo "<a class='head_sort_order' onclick='select_sort(5,\"desc\")' href='".tep_href_link(FILENAME_ORDERS,tep_get_all_get_params(array('x', 'y', 'order_type',
                    'order_sort')).
                'order_sort=orders_status_name&order_type=desc')."'>";
              echo TABLE_HEADING_STATUS; 
            }
          }
        echo "</a>";
        ?></td>
          <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
          </tr>
          <?php
 $orders_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ORDERS_RESULTS, $orders_query_raw, $orders_query_numrows, $sql_count_query);
        $orders_query = tep_db_query($orders_query_raw);
        $orders_num = tep_db_num_rows($orders_query);

        $allorders    = $allorders_ids = array();
        $orders_i = 0;
        //获取订单状态标记的过期警告数组
        $orders_expired_array = array();
        $orders_expired_array = check_orders_transaction_expired();
        $show_all_site = array(); 
        $show_all_site[0] = 'all'; 
        $show_all_site_raw = tep_db_query("select * from ".TABLE_SITES); 
        while ($show_all_site_res = tep_db_fetch_array($show_all_site_raw)) {
          $show_all_site[$show_all_site_res['id']] = $show_all_site_res['romaji']; 
        }
        $customer_image = array();
        $tmp_order_id_list = array();
        $orders_info_arr = array();
        $orders_id_list = array();
        foreach($e_orders_arr as $e_orders){
          $orders_info_arr[] = $e_orders;
          $orders_id_list[] = $e_orders['orders_id'];
        }
        while ($orders = tep_db_fetch_array($orders_query)) {
          $orders_info_arr[] = $orders;
          $orders_id_list[] = $orders['orders_id'];
        }
        if(isset($_GET['oID'])){
          $oid_is_inpage = in_array($_GET['oID'],$orders_id_list);
        }else{
          $oid_is_inpage = false;
        }
        foreach($orders_info_arr as $orders){
          $orders_i++;
          if (!isset($orders['site_id'])) {
            $orders = tep_db_fetch_array(tep_db_query("
                  select *
                  from ".TABLE_ORDERS." o
                  where orders_id='".$orders['orders_id']."'
                  "));
          }
          $tmp_order_id_list[] = array(
              'orders_id'=>$orders['orders_id'],
              'site_id'=>$orders['site_id']);
          $allorders[] = $orders;
          if (((!isset($_GET['oID']) || !$_GET['oID']) || 
                (isset($_GET['oID'])&&$_GET['oID']&&
                 ($_GET['oID'] == $orders['orders_id']
                  ||! $oid_is_inpage))) 
              && (!isset($oInfo) || !$oInfo)) {
            $oInfo = new objectInfo($orders);
          }

          //过期订单的警告提示
          $expired_orders = '';
          $orders_transaction_time = date('YmdHi',strtotime($orders['torihiki_date_end'])); 
          $orders_today_time = date('YmdHi');
          if($orders_today_time > $orders_transaction_time && $orders_expired_array[$orders['orders_status']] == 1){

            $expired_orders = tep_image(DIR_WS_ICONS . 'blink_exclamation.gif', TEXT_TRANSACTION_EXPIRED);
          }

          //如果是红色显示
          $trade_array = getdate(strtotime(tep_datetime_short_torihiki($orders['torihiki_date_end'])));
          $today_array = getdate();
          if ($trade_array["year"] == $today_array["year"] && $trade_array["mon"] == $today_array["mon"] && $trade_array["mday"] == $today_array["mday"]) {
            $today_color = 'red';
            $trade_minutes = $trade_array["minutes"] < 10 ? '0'.$trade_array["minutes"] : $trade_array["minutes"];
            $today_minutes = $today_array["minutes"] < 10 ? '0'.$today_array["minutes"] : $today_array["minutes"];
            if ($trade_array["hours"].$trade_minutes >= $today_array["hours"].$today_minutes) {
              $next_mark = tep_image(DIR_WS_ICONS . 'arrow_blinking.gif',
                  TEXT_ORDER_NEXT_ORDER); //标记下个订单
            } else {
              $next_mark = '';
            }
          } else {
  $today_color = 'black';
  $next_mark = '';
          }
          $even = 'dataTableSecondRow';
          $odd  = 'dataTableRow';
          if (isset($nowColor) && $nowColor == $odd) {
            $nowColor = $even; 
          } else {
            $nowColor = $odd; 
          }
          $is_gray_style = ''; 
          if ($orders['is_gray'] == '1') {
            $is_gray_style = 'style="background-color:#AAAAAA;"'; 
          }
          if ( (isset($oInfo) && is_object($oInfo)) && ($orders['orders_id'] == $oInfo->orders_id) ) {
            if($orders_i == 1 && !isset($_GET['oID'])){ 
              echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" '.$is_gray_style.'>' . "\n";
            }else{
              echo '    <tr id="tr_' . $orders['orders_id'] . '" class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'">' . "\n"; 
            }
          } else {
            echo '    <tr id="tr_' . $orders['orders_id'] . '" class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\';" '. $is_gray_style.'>' . "\n";
          }
          ?>
            <?php 
            if ($ocertify->npermission) {
              ?>
                <td style="border-bottom:1px solid #000000;" class="dataTableContent">
                <input type="checkbox" name="chk[]" value="<?php echo $orders['orders_id']; ?>" onClick="chg_tr_color(this);show_questions(this, '<?php echo JS_TEXT_ALL_ORDER_COMPLETION_TRANSACTION;?>', '<?php echo JS_TEXT_ALL_ORDER_SAVE;?>');">
                </td>
                <?php 
            }
          ?>
            <td style="border-bottom:1px solid #000000;" class="dataTableContent" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';"><?php echo isset($show_all_site[$orders['site_id']])?$show_all_site[$orders['site_id']]:'';?></td>
            <td style="border-bottom:1px solid #000000;" class="dataTableContent" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';">
            <div class="float_left"><a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit');?>"><?php echo tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW);?></a>&nbsp;
          <a href="<?php echo tep_href_link('orders.php', 'cEmail=' .
            tep_output_string_protected(urlencode($orders['customers_email_address'])));?>"><?php
            echo tep_image(DIR_WS_ICONS . 'search.gif', TEXT_ORDER_HISTORY_ORDER);?></a></div>
            <div>
            <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
              <font color="#999">
                <?php } else { ?>
                  <font color="#000">
                    <?php } ?>
                    <a style="text-decoration:underline;" href="<?php echo tep_href_link('customers.php', 'type=cid&search=' .  tep_output_string_protected($orders['customers_id']));?>"><?php echo tep_output_string_protected($orders['customers_name']);?></a>
                    <input type="hidden" id="cid_<?php echo $orders['orders_id'];?>" name="cid[]" value="<?php echo $orders['customers_id'];?>" />
                    </font>
                    <?php 
                    if(isset($customer_image[$orders['customers_id']])&&!empty($customer_image[$orders['customers_id']])){
                      $customer_image_array = explode(',',$customer_image[$orders['customers_id']]['src']);
                      foreach($customer_image_array as $key => $value){
                      if (file_exists(DIR_FS_DOCUMENT_ROOT.DIR_WS_IMAGES.'icon_list/'.$value)) {
                        echo tep_image(DIR_WS_IMAGES.'icon_list/'.$value, $customer_image[$orders['customers_id']]['alt'][array_search($value,$_SESSION['c_image_list']['name'])]);
                      }
                     }
                    }else{
                    if(isset($_SESSION['c_image_list'])&&!empty($_SESSION['c_image_list']['name'])&&!empty($_SESSION['c_image_list']['alt'])){
                      $customers_info_raw = tep_db_query("select c.pic_icon from ".TABLE_CUSTOMERS." c where c.customers_id = '".$orders['customers_id']."'"); 
                      $customers_info_res = tep_db_fetch_array($customers_info_raw);
                      if ($customers_info_res) {
                        $customers_pic = explode(',',$customers_info_res['pic_icon']);
                        if (!empty($customers_info_res['pic_icon']) &&array_intersect($customers_pic,$_SESSION['c_image_list']['name'])) {
                          foreach($customers_pic as $key => $pic_value){
                          if (file_exists(DIR_FS_DOCUMENT_ROOT.DIR_WS_IMAGES.'icon_list/'.$pic_value)) {
                            $pic_icon_title_str = $_SESSION['c_image_list']['alt'][array_search($pic_value,$_SESSION['c_image_list']['name'])]; 
                            $customer_image[$orders['customers_id']]=array();
                            $customer_image[$orders['customers_id']]['src'] = $customers_info_res['pic_icon'];
                            $customer_image[$orders['customers_id']]['alt'] = $_SESSION['c_image_list']['alt'];
                              echo tep_image(DIR_WS_IMAGES.'icon_list/'.$pic_value, $pic_icon_title_str); 
                            }
                          }
                        }
                      }
                    }else{
                    $customers_info_raw = tep_db_query("select c.pic_icon, pl.pic_alt from ".TABLE_CUSTOMERS." c, ".TABLE_CUSTOMERS_PIC_LIST." pl where c.customers_id = '".$orders['customers_id']."' and c.pic_icon = pl.pic_name limit 1"); 
                    $customers_info_res = tep_db_fetch_array($customers_info_raw);
                    $customers_pic = explode(',',$customers_info_res['pic_icon']);
                    if ($customers_info_res) {
                      if (!empty($customers_info_res['pic_icon'])) {
                        foreach($customers_pic as $key => $pic_value){
                        if (file_exists(DIR_FS_DOCUMENT_ROOT.DIR_WS_IMAGES.'icon_list/'.$pic_value)) {
                          $pic_icon_title_str = ''; 
                          $pic_icon_title_raw = tep_db_query("select pic_alt from ".TABLE_CUSTOMERS_PIC_LIST." where pic_name = '".$pic_value."'"); 
                          $pic_icon_title_res = tep_db_fetch_array($pic_icon_title_raw); 
                          if ($pic_icon_title_res) {
                            $pic_icon_title_str = $pic_icon_title_res['pic_alt']; 
                          }
                          $customer_image[$orders['customers_id']]=array();
                          $customer_image[$orders['customers_id']]['src'] = $pic_value;
                          $customer_image[$orders['customers_id']]['alt'] = $pic_icon_title_str;
                          echo tep_image(DIR_WS_IMAGES.'icon_list/'.$pic_value, $pic_icon_title_str); 
                        }
                       }
                      }
                    }
                    }
                    }
                    ?>
                        <?php if ($orders['orders_care_flag']) { ?>
                          <?php echo tep_image(DIR_WS_ICONS . 'care.gif', TEXT_ORDER_CARE);?>
                            <?php }?>
							</div>

                            </td>
                            <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';">
                            <?php if (!$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)) {?>
                              <font color="#999"><?php echo
                                strip_tags(tep_get_ot_total_by_orders_id_no_abs($orders['orders_id'],true));?></font>
                                <?php } else { ?>
                                  <?php echo str_replace(array('<b>', '</b>'), '', tep_get_ot_total_by_orders_id_no_abs($orders['orders_id'], true));?>
                                    <?php }?>
                                    </td>
				    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="center"><?php echo $expired_orders.$next_mark; ?></td>
                                    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="center" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';"><font color="<?php echo !$ocertify->npermission && (time() - strtotime($orders['date_purchased']) > 86400*7)?'#999':$today_color; ?>" id="tori_<?php echo $orders['orders_id']; ?>">
                                    <?php 
                                    echo tep_datetime_short_torihiki($orders['torihiki_date']); 
                                    $tmp_date_end = explode(' ', $orders['torihiki_date_end']); 
                                    echo TEXT_TIME_LINK.$tmp_date_end[1]; 
                                    ?>
                                    </font>
</td>
<td style="border-bottom:1px solid #000000;" class="dataTableContent" align="left">
<?php
  $read_flag_str_array = explode('|||',$orders['read_flag']);
  if($orders['read_flag'] == ''){
    echo '<a onclick="change_read(\''.$orders['orders_id'].'\',\''.$ocertify->auth_user.'\',\''.TEXT_FLAG_CHECKED.'\',\''.TEXT_FLAG_UNCHECK.'\');" href="javascript:void(0);"><img id="oid_'.$orders['orders_id'].'" border="0" title=" '.TEXT_FLAG_UNCHECK.' " alt="'.TEXT_FLAG_UNCHECK.'" src="images/icons/gray_right.gif"></a>'; 
  }else{

    if(in_array($ocertify->auth_user,$read_flag_str_array)){

      echo '<a onclick="change_read(\''.$orders['orders_id'].'\',\''.$ocertify->auth_user.'\',\''.TEXT_FLAG_CHECKED.'\',\''.TEXT_FLAG_UNCHECK.'\');" href="javascript:void(0);"><img id="oid_'.$orders['orders_id'].'" border="0" title=" '.TEXT_FLAG_CHECKED.' " alt="'.TEXT_FLAG_CHECKED.'" src="images/icons/green_right.gif"></a>';
    }else{

      echo '<a onclick="change_read(\''.$orders['orders_id'].'\',\''.$ocertify->auth_user.'\',\''.TEXT_FLAG_CHECKED.'\',\''.TEXT_FLAG_UNCHECK.'\');" href="javascript:void(0);"><img id="oid_'.$orders['orders_id'].'" border="0" title=" '.TEXT_FLAG_UNCHECK.' " alt="'.TEXT_FLAG_UNCHECK.'" src="images/icons/gray_right.gif"></a>';
    }
  }
?>
</td>
                                    <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="left" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';"><?php
                                    if ($orders['orders_wait_flag']) { echo tep_image(DIR_WS_IMAGES .
                                        'icon_hand.gif', TEXT_ORDER_WAIT); } else { echo '&nbsp;'; } ?></td>
                                      <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="left" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';"><?php echo $orders['orders_work']?strtoupper($orders['orders_work']):'&nbsp;';?></td>
                                        <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="center" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';"><span style="color:#999999;"><?php echo tep_datetime_short_torihiki($orders['date_purchased']); ?></span></td>
                                        <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="center" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';">
                                        <?php 
                                        // 订单历史记录的图标
                                        $___orders_status_query = tep_db_query("select orders_status_id from `".TABLE_ORDERS_STATUS_HISTORY."` WHERE `orders_id`='".$orders['orders_id']."' order by `date_added` asc");
          $___orders_status_ids   = array();
          while($___orders_status = tep_db_fetch_array($___orders_status_query)){
            $___orders_status_ids[] = $___orders_status['orders_status_id'];
          }
          if ($___orders_status_ids) {
            $_orders_status_history_query_raw = "select orders_status_id,orders_status_image from `".TABLE_ORDERS_STATUS."` WHERE `orders_status_id` IN (".join(',',$___orders_status_ids).")";
            $_orders_status_history_query     = tep_db_query($_orders_status_history_query_raw);     $_osh = array();
            $_osi = false;
            while ($_orders_status_history = tep_db_fetch_array($_orders_status_history_query)){
              if(!in_array($_orders_status_history['orders_status_id'], $_osh)
                  && !is_dir(tep_get_upload_dir().'orders_status/'.$_orders_status_history['orders_status_image']) 
                  && file_exists(tep_get_upload_dir().'orders_status/'.$_orders_status_history['orders_status_image'])){
                echo tep_image(tep_get_web_upload_dir(). 'orders_status/' .
                    $_orders_status_history['orders_status_image'],
                    $_orders_status_history['orders_status_image'],0 ,0 , ($_orders_status_history['orders_status_id'] == @$orders['orders_status_id'])?'style="vertical-align: middle;"':'style="vertical-align: middle;"');
                $_osi = $_osi or true;
              }
              $_osh[] = $_orders_status_history['orders_status_id'];
            }
          }
          if(!$_osi){
            echo '　';
          }
          ?>
            </td>
            <td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right" onClick="chg_td_color(<?php echo $orders['orders_id']; ?>); window.location.href='<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID='.$orders['orders_id']);?>';">
            <font color="<?php echo $today_color; ?>">
            <?php 
            echo $orders['orders_status_name'];
          ?>
            <input type="hidden" name="os[]" id="orders_status_<?php echo $orders['orders_id']; ?>" value="<?php echo $orders['orders_status']; ?>"></font></td>
            <?php 
              echo '<td style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"  onmouseout="if(popup_num == 1) hideOrdersInfo(0);">';
              ?>

                <?php echo '<a href="javascript:void(0);" onclick="showOrdersInfo(\''.$orders['orders_id'].'\', this, 1, \''.urlencode(tep_get_all_get_params(array('oID', 'action'))).'\');">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
             ?>&nbsp;</td>
          </tr>
            <?php }
       if(isset($_GET['action'])&&$_GET['action']=='edit'){
       }else{
         $_SESSION['order_id_list'] = $tmp_order_id_list;
       }
        ?>
            </table>
            <script language="javascript">
            window.orderStr = new Array();
          <?php // 订单所属网站?>
          window.orderSite = new Array();
          <?php // 0 空 1 卖 2 买 3 混?>
          var orderType = new Array();
          var questionShow = new Array();
              function submit_confirm()
              {
                var idx = document.sele_act.elements['status'].selectedIndex;
                var CI  = document.sele_act.elements['status'].options[idx].value;
                chk = getCheckboxValue('chk[]')
                  if((chk.length > 1 || chk.length < 1) && document.getElementById('status_text_'+CI+'_0').value.indexOf('${MAIL_COMMENT}') != -1){
                    if(chk.length > 1){
                      alert('<?php echo TEXT_SELECT_MORE;?>');
                    } else {
                      alert('<?php echo TEXT_ORDER_SELECT;?>');
                    }
                    return false;
                  }
                return true;
              }
            </script>
              <table width="100%"><tr><td width="70%" id="send_mail_td">
              <?php  //移动代码开始 
if(isset($_GET['action'])&&$_GET['action']=='edit'){
               ?>
              <table width="100%" id="select_send" style="display:none">
              <tr>
              <td class="main" width="100" nowrap="nowrap"><?php echo ENTRY_STATUS; ?></td>
              <td class="main"><?php echo tep_draw_pull_down_menu('status', $orders_statuses, $select_select, 'onChange="mail_text(\'status\',\'comments\',\'os_title\',\''.JS_TEXT_ALL_ORDER_NOT_CHOOSE.'\', \''.JS_TEXT_ALL_ORDER_NO_OPTION_ORDER.'\')" id="mail_title_status"'); ?> <?php
              if($ocertify->npermission > 7 ) { ?>&nbsp;<a href="<?php echo
                tep_href_link(FILENAME_ORDERS_STATUS,'',SSL);?>"><?php echo
                  TEXT_EDIT_MAIL_TEXT;?></a><?php } ?></td>
                  </tr>
                  <tr>
                  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                  </tr>
                  <tr>
                  <td class="main" nowrap="nowrap"><?php echo ENTRY_EMAIL_TITLE; ?></td>
                  <td class="main"><?php echo tep_draw_input_field('os_title', $select_title,'style=" width:400px;" id="mail_title"'); ?></td>
                  </tr>
                  <tr>
                  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                  </tr>
                  <tr>
                  <td class="main" valign="top" nowrap="nowrap"><?php echo TABLE_HEADING_COMMENTS . ':'; ?></td>
                  <td class="main">
                  <?php echo TEXT_MAIL_CONTENT_INFO;?>
                  <table><tr class="smalltext"><td><font
                  color="red">※</font>&nbsp;<?php echo TEXT_ORDER_COPY;?></td><td>
                  <?php echo TEXT_ORDER_LOGIN;?></td></tr></table>
                  <br>
                  <?php echo tep_draw_textarea_field('comments', 'hard', '74', '30', $select_text, 'style="font-family:monospace;font-size:12px; width:400px;" id="c_comments"'); ?>
                  </td>
                  </tr>
                  <tr>
                  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                  </tr>
                  <tr>
                  <td>&nbsp;</td>
                  <td>
                  <table width="100%" border="0" cellspacing="0" cellpadding="2">
                  <tr>
                  <td class="main"><?php echo tep_draw_checkbox_field('notify', '',
                      !$select_nomail, '', 'id="notify"'); ?><?php echo
                  TEXT_ORDER_SEND_MAIL;?></td>
                  <td class="main" align="right"><?php echo
                  tep_draw_checkbox_field('notify_comments', '', !$select_nomail, '',
                      'id="notify_comments"'); ?><?php echo TEXT_ORDER_STATUS;?></td>
                  </tr>
                  <tr>
                  <td class="main" colspan="2"><br><font color="#FF0000;"><?php
                  foreach($orders_statuses as $o_status){
                    echo '<input type="hidden" id="confrim_mail_title_'.$o_status['id'].
                      '" value="'.$mo[$o_status['id']][0].'">';
                  }
                  echo TEXT_ORDER_HAS_ERROR;?></font><br><br><a href="javascript:void(0);"><?php echo tep_html_element_button(IMAGE_UPDATE, 'onclick="check_list_order_submit(\''.TEXT_STATUS_MAIL_TITLE_CHANGED.'\')"'); ?></a></td>
                  </tr>
                  </table>
                  </td>
                  </tr>
                  </table>
                  <?php  
}
        if($orders_num == 0){

          echo '<tr><td colspan="12"><font color="red"><b>'.TEXT_DATA_IS_EMPTY.'</b></font></td></tr>';
        }
                  //移动代码结束 ?>
                  </td><td valign="top" align="right" width="30%">
                  <div id='select_question' style="display:none" >
                  <table width="100%">
                  <tr>
                  <td align='right'>
                  <select id='oa_dynamic_groups'  ></select>
                  </td>
                  </tr>
                  </table>
                  <table id='oa_dynamic_group_item'  width="100%">

                  </table>
                  <table width="100%">
                  <tr><td align='right'><button id="oa_dynamic_submit" ><?php echo IMAGE_SAVE;?></button></td></tr>
                  </table>
                  </div>
                  </td></tr></table>
                  </form>
                  <!-- display add end-->
                  <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                  <td class="smallText" valign="top"><?php echo $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_ORDERS_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
                  <td class="smallText" align="right">
				  <div class="td_box"><?php echo $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_ORDERS_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'oID', 'action'))); ?></div></td>
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
                $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('restock', 'on', true) . ' ' . TEXT_INFO_RESTOCK_PRODUCT_QUANTITY);
                $contents[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_submit(IMAGE_DELETE) . ' <a href="' .  tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
                break;
              default:
                if (isset($oInfo) && is_object($oInfo)) {
                  $heading[] = array('text' => '<b>[' . $oInfo->orders_id . ']<br>' . tep_datetime_short_torihiki($oInfo->date_purchased) . '</b>');

                  if ($ocertify->npermission == 15) {
                    $contents[] = array('align' => 'center', 'text' => '<a href="' .  tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '">' . tep_html_element_button(IMAGE_DETAILS) . '</a> <a href="' .  tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=delete') .  '">' . tep_html_element_button(IMAGE_DELETE) . '</a>');
                  } else {
                    $contents[] = array('align' => 'center', 'text' => '<a href="' .  tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '">' . tep_html_element_button(IMAGE_DETAILS) . '</a>');
                  }
                  $contents[] = array('text' => '<br>' . TEXT_DATE_ORDER_CREATED . ' ' . tep_date_short($oInfo->date_purchased));
                  if (tep_not_null($oInfo->last_modified)) $contents[] = array('text' => TEXT_DATE_ORDER_LAST_MODIFIED . ' ' . tep_date_short($oInfo->last_modified));
                  $contents[] = array('text' => tep_show_orders_products_info($oInfo->orders_id)); 
                }
                break;
            }

            ?>
              </tr>
              </table>
              </td>
              </tr>
              <?php } ?>

              </table></div></td>
              <!-- body_text_eof -->
              </tr>
              </table>
              </td>
           
              <!-- body_eof -->

              <!-- footer -->
              <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
              <!-- footer_eof -->
              <br>
              <div id="wait" style="position:fixed; left:45%; top:45%; display:none;"><img src="images/load.gif" alt="img"></div>
              </body>
              </html>
              <?php require(DIR_WS_INCLUDES . 'application_bottom.php'); 
            ?>
