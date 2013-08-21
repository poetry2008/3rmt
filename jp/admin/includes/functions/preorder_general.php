<?php
/* -----------------------------------------------------
 功能: 获取预约状态名称 
 参数: $orders_status_id(string) 订单状态ID值
 参数: $language_id(string) 语言ID值
 返回值: 预约名称(string)  
 -----------------------------------------------------*/
function tep_get_preorders_status_name($orders_status_id, $language_id = '') {
  global $languages_id;

  if (!$language_id) $language_id = $languages_id;
  $orders_status_query = tep_db_query("select orders_status_name from " . TABLE_PREORDERS_STATUS . " where orders_status_id = '" . $orders_status_id . "' and language_id = '" . $language_id . "'");
  $orders_status = tep_db_fetch_array($orders_status_query);

  return $orders_status['orders_status_name'];
}
/*-----------------------------------------------------
 功能: 通过预约订单ID得到计算机名
 参数: $orders_id(string) 订单ID值
 返回值: 计算机名(string)                  
 ----------------------------------------------------*/
function tep_get_buttons_names_by_preorders_id($orders_id)
{
  $names = array();
  $o2c_query = tep_db_query("select * from ".TABLE_PREORDERS_TO_BUTTONS." o2b, ".TABLE_BUTTONS." b where b.buttons_id=o2b.buttons_id and o2b.orders_id = '".$orders_id."' order by sort_order asc");
  while($o = tep_db_fetch_array($o2c_query)) {
    $names[] = $o['buttons_name'];
  }
  return $names;
}
/*----------------------------------------------------
 功能: 该预约订单关联的电脑id 
 参数: $oid(string) 订单编号值
 返回值: 电脑id(array)                      
 ---------------------------------------------------*/
function tep_get_buttons_by_preorders_id($oid)
{
  $c = array();
  $o2c_query = tep_db_query("select * from ".TABLE_PREORDERS_TO_BUTTONS." where orders_id = '".$oid."'");
  while ($o2c = tep_db_fetch_array($o2c_query)) {
    $c[] = $o2c['buttons_id'];
  }
  return $c;
}
/*---------------------------------------------------
 功能: 该预约订单的详细信息 
 参数: $orders_id(string) 订单编号值
 返回值: 详细信息(string)                   
---------------------------------------------------*/
function tep_show_preorders_products_info($orders_id) {
  $str = '';

  $orders_info_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$orders_id."'"); 
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
      $str .= '<b>';
      $str .= RIGHT_ORDER_INFO_TRANS_NOTICE; 
      $str .= '</b>';
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

  $str .= '<tr><td class="main" width="70"><b>'.TEXT_FUNCTION_PAYMENT_METHOD.'</b></td><td class="main" style="color:darkred;"><b>'.$orders['payment_method'].'</b></td></tr>';
    if ($orders['confirm_payment_time'] != '0000-00-00 00:00:00') {
      $time_str = date(TEXT_FUNCTION_DATE_STRING, strtotime($orders['confirm_payment_time'])); 
    }else if(tep_check_pre_order_type($orders['orders_id'])!=2){
      $time_str = TEXT_PREORDER_NOT_COST; 
    }
    if($time_str){
    $str .= '<tr><td class="main"><b>'.TEXT_FUNCTION_UN_GIVE_MONY_DAY.'</b></td><td class="main" style="color:red;"><b>'.$time_str.'</b></td></tr>';
    }
    if(trim($orders['torihiki_houhou']) != ''){
      $str .= '<tr><td class="main"><b>'.TEXT_FUNCTION_OPTION.'</b></td><td class="main" style="color:blue;"><b>'.$orders['torihiki_houhou'].'</b></td></tr>';
    }

  $orders_products_query = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." op,".TABLE_PRODUCTS." p where p.products_id = op.products_id and op.orders_id = '".$orders['orders_id']."'");
  $autocalculate_arr = array();
  $autocalculate_sql = "select oaf.value as arr_str from ".TABLE_PREORDERS_OA_FORMVALUE." oaf,".
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
    $products_attributes_query = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id='".$p['orders_products_id']."'");
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
      $input_op_array = @unserialize(stripslashes($pa['option_info'])); 
      $str .= '<tr><td class="main"><b>'.$input_op_array['title'].'：</b></td><td class="main">'.$input_op_array['values'].'</td></tr>';
    }
    $names = tep_get_buttons_names_by_preorders_id($orders['orders_id']);
    if ($names) {
      $str .= '<tr><td class="main"><b>PC：</b></td><td class="main">'.implode('&nbsp;,&nbsp;', $names).'</td></tr>';
    }
    $str .= '<tr><td class="main"></td><td class="main"></td></tr>';
    $i++;
  }
  $str .= '<tr><td colspan="2"><hr></td></tr>'; 
  }
  
  
  if (ORDER_INFO_ORDER_INFO == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.TEXT_FUNCTION_ORDER_FROM_INFO.'</b></td>'; 
    $str .= '<td class="main">';
    $str .= tep_get_pre_site_name_by_order_id($orders['orders_id']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_FETCH_TIME.'</b></td>';
    $str .= '<td class="main">';
    $str .= $orders['torihiki_date']; 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    if(trim($orders['torihiki_houhou']) != ''){ 
      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_OPTION.'</b></td>';
      $str .= '<td class="main">';
      $str .= $orders['torihiki_houhou'];    
      $str .= '</td>'; 
      $str .= '</tr>'; 
    }
  
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.TEXT_PREORDER_ID_TEXT.'</b></td>';
    $str .= '<td class="main">';
    $str .= $orders['orders_id']; 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.TEXT_DATE_ORDER_CREATED.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_date_long($orders['date_purchased']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.TEXT_FUNCTION_PREDATE.'</b></td>';
    $str .= '<td class="main">';
    $str .= $orders['predate']; 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.TEXT_FUNCTION_ENSURE_DATE.'</b></td>';
    $str .= '<td class="main">';
    $str .= $orders['ensure_deadline']; 
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
    $str .= urldecode($orders['orders_ref']); 
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
    $order_history_list_raw = tep_db_query("select * from ".TABLE_PREORDERS." where customers_email_address = '".$orders['customers_email_address']."' order by date_purchased desc limit 5"); 
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
        $str .= strip_tags(tep_get_pre_ot_total_by_orders_id($order_history_list['orders_id'], true)); 
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
}
/*---------------------------------------------------------
 功能: 预约产品删除属性 
 参数: $order_id(string) 订单编号值
 参数: $restock(boolean) 库存
 返回值: 无
 --------------------------------------------------------*/
function tep_preorder_remove_attributes($order_id, $restock = false) {
  if ($restock == 'on') {
    $orderproduct_query = tep_db_query("select orders_products_id, products_quantity from " . TABLE_PREORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($order_id) . "'");
    while ($o_product = tep_db_fetch_array($orderproduct_query)) {
      $opID = $o_product['orders_products_id'];
      $zaiko = $o_product['products_quantity'];

      $order_attributes_query = tep_db_query("select attributes_id from " .  TABLE_PREORDERS_PRODUCTS_ATTRIBUTES . " where orders_products_id = '" . $opID . "'");
      $order_attributes_result = tep_db_fetch_array($order_attributes_query);
      $att_id = $order_attributes_result['attributes_id'];
    }
  }
}
/*---------------------------------------------------------
 功能: 预约删除订单
 参数: $order_id(string) 订单编号值
 参数: $restock(boolean) 库存
 返回值: 无
 --------------------------------------------------------*/
function tep_preorder_remove_order($order_id, $restock = false) {
  tep_db_query("delete from " . TABLE_PREORDERS . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_PREORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_PREORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_PREORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_PREORDERS_TOTAL . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_PREORDERS_TO_BUTTONS . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from preorders_products_download where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from ".TABLE_PREORDERS_OA_FORMVALUE." where orders_id = '".tep_db_input($order_id)."'");
}
/*----------------------------------------------------------
 功能: 预约相关信息更新                       
 参数: $orders_id(string) 订单编号值 
 返回值: 无
 ---------------------------------------------------------*/
function preorders_updated($orders_id) {
  tep_db_query("update ".TABLE_PREORDERS." set language_id = ( select language_id from ".TABLE_PREORDERS_STATUS." where preorders_status.orders_status_id=preorders.orders_status ) where orders_id='".$orders_id."'");
  
  tep_db_query("update ".TABLE_PREORDERS." set finished = ( select finished from ".TABLE_PREORDERS_STATUS." where preorders_status.orders_status_id=preorders.orders_status ) where orders_id='".$orders_id."'");
  
  tep_db_query("update ".TABLE_PREORDERS." set orders_status_name = ( select orders_status_name from ".TABLE_PREORDERS_STATUS." where preorders_status.orders_status_id=preorders.orders_status ) where orders_id='".$orders_id."'");
  
  tep_db_query("update ".TABLE_PREORDERS." set orders_status_image = ( select orders_status_image from ".TABLE_PREORDERS_STATUS." where preorders_status.orders_status_id=preorders.orders_status ) where orders_id='".$orders_id."'");
  
  tep_db_query("update ".TABLE_PREORDERS_PRODUCTS." set torihiki_date = ( select torihiki_date from ".TABLE_PREORDERS." where preorders.orders_id=preorders_products.orders_id ) where orders_id='".$orders_id."'");
}
/*----------------------------------------------------------
 功能: 更新预约等待标识
 参数: $orders_id(string) 订单编号值 
 返回值: 无
 ---------------------------------------------------------*/
function preorders_wait_flag($orders_id) {
  $orders_query = tep_db_query("select * from " . TABLE_PREORDERS . " where orders_id = '".$orders_id."'");
  $orders       = tep_db_fetch_array($orders_query);
  if ($orders['orders_wait_flag']) {
    $orders_status_query = tep_db_query("select * from " . TABLE_PREORDERS_STATUS . " where orders_status_id='".$orders['orders_status']."'");
    $orders_status       = tep_db_fetch_array($orders_status_query);
    if ($orders_status['finished']) {
      tep_db_query("update ".TABLE_PREORDERS." set orders_wait_flag = '0' where orders_id='".$orders_id."'");
    }
  }
}
/*--------------------------------------------------------
 功能: 预约订单总计格式化输出 
 参数: $orders_id(string) 订单编号值
 返回值: 总计输出(string)
 -------------------------------------------------------*/
function tep_get_pre_ot_total_by_orders_id($orders_id, $single = false) {
  if ($single) {
    global $currencies; 
  }
  $query = tep_db_query("select value from " . TABLE_PREORDERS_TOTAL . " where class='ot_total' and orders_id='".$orders_id."'");
  $result = tep_db_fetch_array($query);
  if($result['value'] > 0){
    if ($single) {
      return "<b>".$currencies->format(abs($result['value']))."</b>";
    } else {
      return "<b>".abs($result['value']).TEXT_MONEY_SYMBOL."</b>";
    }
  }else{
    if ($single) {
      return "<b><font color='ff0000'>".$currencies->format(abs($result['value']))."</font></b>";
    } else {
      return "<b><font color='ff0000'>".abs($result['value']).TEXT_MONEY_SYMBOL."</font></b>";
    }
  }
}
/*------------------------------------------------------
 功能: 获取该订单的网站名称
 参数: $id(string) 订单编号值 
 返回值: 网站名称(string)
 -----------------------------------------------------*/
function tep_get_pre_site_name_by_order_id($id){
  $order_query = tep_db_query("
      select s.name
      from " . TABLE_PREORDERS . " o, ".TABLE_SITES." s
      where o.orders_id = '".$id."'
      and s.id = o.site_id
      ");
  $order = tep_db_fetch_array($order_query);
  return isset($order['name'])?$order['name']:'';
}
/*-----------------------------------------------------
 功能: 获取该订单的网站id 
 参数: $orders_id(string) 订单编号值
 返回值: 网站id(string/boolean) 
 ----------------------------------------------------*/
function tep_get_pre_site_id_by_orders_id($orders_id) {
  $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$orders_id."'"));
  if ($order) {
    return $order['site_id'];
  } else {
    return false;
  }
}
/*---------------------------------------------------
 功能: 获取该订单产品的交易人物名信息 
 参数: $orders_id(string) 订单编号值 
 参数: $allorders(string) 所有订单
 参数: $site_id(string) SITE_ID值
 返回值: 信息(string)                      
 --------------------------------------------------*/
function preorders_a($orders_id, $allorders = null, $site_id = 0)
{
  static $products;
  $str = "";
  if ($allorders && $products === null) {
    foreach($allorders as $o) {
      $allorders_ids[] = $o['orders_id'];
    }
    $sql = "select pd.products_name,p.products_attention_5,p.products_id from ".TABLE_PREORDERS_PRODUCTS." op, ".TABLE_PRODUCTS_DESCRIPTION." pd,".TABLE_PRODUCTS." p WHERE op.products_id=pd.products_id and p.products_id=pd.products_id and `orders_id` IN ('".join("','", $allorders_ids)."') and pd.site_id = '".$site_id."'";
    $orders_products_query = tep_db_query($sql);
    while ($product = tep_db_fetch_array($orders_products_query)) {
      $products[$product['orders_id']][] = $product;
    }
  }
  if (isset($products[$orders_id]) && $products[$orders_id]) {
    foreach($products[$orders_id] as $p){
      $str .= $p['products_name'] . " ".FINAL_PREORDERS_MAIL_NOTICE_TEXT."\n";
      $str .= $p['products_attention_5'] . "\n";
    }
  } else {
    $sql = "select * from `".TABLE_PREORDERS_PRODUCTS."` WHERE `orders_id`='".$orders_id."'";
    $orders_products_query = tep_db_query($sql);
    while ($orders_products = tep_db_fetch_array($orders_products_query)){
      $sql = "select pd.products_name,p.products_attention_5,p.products_id from `".TABLE_PRODUCTS_DESCRIPTION."` pd,".TABLE_PRODUCTS." p WHERE p.products_id=pd.products_id and p.`products_id`='".$orders_products['products_id']."' and pd.site_id = '".$site_id."'";
      $products_description = tep_db_fetch_array(tep_db_query($sql));
      if ($products_description['products_attention_5']) {
        $str .= $orders_products['products_name']." ".FINAL_PREORDERS_MAIL_NOTICE_TEXT."\n";
        $str .= $products_description['products_attention_5'] . "\n";
      }
    }
  }
  return $str;
}
/*---------------------------------------------------
 功能: 获取该订单商品的价格 
 参数: $orders_products_id(string) 订单商品ID值
 返回值: 商品价格(float/boolean) 
 --------------------------------------------------*/
function tep_get_pre_product_by_op_id($orders_products_id,$type=''){
  if($type=='pid'){
    $sql = "select p.products_price as price from ".
      TABLE_PREORDERS_PRODUCTS." op,"
      .TABLE_PRODUCTS." p  
      where p.products_id ='".$orders_products_id."' 
      limit 1";
  }else{
    $sql = "select p.products_price as price from ".
      TABLE_PREORDERS_PRODUCTS." op,"
      .TABLE_PRODUCTS." p  
      where op.orders_products_id ='".$orders_products_id."' 
      and op.products_id = p.products_id limit 1";
  }
  $res = tep_db_query($sql);
  if($row = tep_db_fetch_array($res)){
    return $row['price'];
  }else{
    return false;
  }
}
/*----------------------------------------------------
 功能: 检查订单类型 
 参数: $oID(string) 订单编号值
 返回值: 订单类型(int)
----------------------------------------------------*/
function tep_check_pre_order_type($oID)
{
  $sql = "  SELECT avg( products_bflag ) bflag FROM preorders_products op, products p  WHERE 1 AND p.products_id = op.products_id AND op.orders_id = '".$oID."'";

  $avg  = tep_db_fetch_array(tep_db_query($sql));
  $avg = $avg['bflag'];

  if($avg == 0){
    return 1;
  }
  if($avg == 1){
    return 2;
  }
  return 3;

}
/*----------------------------------------------------
 功能: 获得指定订单的支付方式 
 参数: $oID(string) 订单编号值
 返回值: 支付方式(string)
 ---------------------------------------------------*/
function tep_get_pre_payment_code_by_order_id($oID)
{
  $orders_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$oID."'");
  $orders_res = tep_db_fetch_array($orders_raw);
  return $orders_res['payment_method'];
}
/*----------------------------------------------------
 功能: 订单状态的变化 
 参数: $oID(string) 订单编号值
 返回值: 无
 ---------------------------------------------------*/
function   tep_pre_order_status_change($oID,$status){
  require_once("pre_oa/HM_Form.php");
  require_once("pre_oa/HM_Group.php");
  require_once("pre_oa/HM_Item_Checkbox.php");
  require_once("pre_oa/HM_Item_Autocalculate.php");
  require_once("pre_oa/HM_Item_Text.php");
  require_once("pre_oa/HM_Item_Specialbank.php");
  require_once("pre_oa/HM_Item_Date.php");
  require_once("pre_oa/HM_Item_Myname.php");
  $order_id = $oID;
  $formtype = 4;
  $payment_romaji = tep_get_pre_payment_code_by_order_id($order_id); 
  $oa_form_sql = "select * from ".TABLE_OA_FORM." where formtype = '".$formtype."' and payment_romaji = '".$payment_romaji."'";
  
  if ($status == '9') {
    tep_db_query("update `".TABLE_PREORDERS."` set `confirm_payment_time` = '".date('Y-m-d H:i:s', time())."' where `orders_id` = '".$oID."'");
  }
  
  $form = tep_db_fetch_object(tep_db_query($oa_form_sql), "HM_Form");
  //如果存在，把每个元素找出来，看是否有自动更新
  if($form){
    $form->loadOrderValue($order_id);
    foreach ($form->groups as $group){
      foreach ($group->items as $item){
        if ($item->instance->status == $status){
          $item->instance->statusChange($order_id,$form->id,$group->id,$item->id);
          continue;
        }
      }
    }
  }
}
/*-----------------------------------------
 功能: 该订单的详细信息
 参数: $orders(array) 订单SQL
 参数: $single(boolean) 是否html输出 
 参数: $popup(boolean) 是否弹出
 参数: $param_str(string) 自定义参数
 返回值: 详细信息(string)    
 -----------------------------------------*/
function tep_get_pre_orders_products_string($orders, $single = false, $popup = false, $param_str = '') {
  global $ocertify;

  $str = '';


  $str .= '<table border="0" cellpadding="2" cellspacing="0" class="popup_order_title" width="100%">';
  $str .= '<tr>';
  $str .= '<td width="22">'.tep_image(DIR_WS_IMAGES.'icon_info.gif',IMAGE_ICON_INFO,16,16).'&nbsp;</td>'; 
  $str .= '<td align="left">['.$orders['orders_id'].']&nbsp;&nbsp;'.tep_datetime_short_torihiki($orders['date_purchased']).'</td>'; 
  $str .= '<td align="right"><a href="javascript:void(0);" onclick="hideOrdersInfo(1);">X</a></td></tr>';
  $str .= '</table>'; 
  $str .= tep_draw_form('preorders', FILENAME_PREORDERS, urldecode($param_str).'&oID='.$orders['orders_id'].'&action=deleteconfirm'); 
  $str .= '<table border="0" cellpadding="0" cellspacing="0" class="popup_order_info" width="100%">';
  if (ORDER_INFO_TRANS_NOTICE == 'true') {
    if ($orders['orders_care_flag']) {
      $str .= '<tr>'; 
      $str .= '<td class="main" colspan="2"><font color="red">';
      $str .= RIGHT_ORDER_INFO_TRANS_NOTICE; 
      $str .= '</font></td>'; 
      $str .= '</tr>'; 
    }
  }
  
  if (ORDER_INFO_TRANS_WAIT == 'true') {
    if ($orders['orders_wait_flag']) {
      $str .= '<tr>'; 
      $str .= '<td class="main" colspan="2"><font color="red">';
      $str .= RIGHT_ORDER_INFO_TRANS_WAIT; 
      $str .= '</font></td>'; 
      $str .= '</tr>'; 
    } 
  }
  
  if (ORDER_INFO_INPUT_FINISH == 'true') {
    if ($orders['orders_inputed_flag']) {
      $str .= '<tr>'; 
      $str .= '<td class="main" colspan="2"><font color="red">';
      $str .= RIGHT_ORDER_INFO_INPUT_FINISH; 
      $str .= '</font></td>'; 
      $str .= '</tr>'; 
    } 
  }
  $str .= '<tr><td class="main" width="120">'.TEXT_PREORDER_PAYMENT_METHOD.'</td><td class="main" style="color:darkred;">'.$orders['payment_method'].'</td></tr>';
  if ($orders['confirm_payment_time'] != '0000-00-00 00:00:00') {
    $time_str = date('Y'.YEAR_TEXT.'n'.MONTH_TEXT.'j'.DAY_TEXT, strtotime($orders['confirm_payment_time'])); 
  } else {
    $time_str = TEXT_PREORDER_NOT_COST; 
  }
  if($time_str) {
    $str .= '<tr><td class="main">'.TEXT_PREORDER_COST_DATE.'</td><td class="main" style="color:red;">'.$time_str.'</td></tr>';
  }
  if(trim($orders['torihiki_houhou']) != ''){
    $str .= '<tr><td class="main">'.RIGHT_ORDER_INFO_ORDER_OPTION.'</td><td class="main" style="color:blue;">'.$orders['torihiki_houhou'].'</td></tr>';
  }

  $orders_products_query = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." op,".TABLE_PRODUCTS." p where p.products_id = op.products_id and op.orders_id = '".$orders['orders_id']."'");
  $autocalculate_arr = array();
  $autocalculate_sql = "select oaf.value as arr_str from ".TABLE_PREORDERS_OA_FORMVALUE." oaf,".
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
    $products_attributes_query = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id='".$p['orders_products_id']."'");
    if(in_array(array($p['products_id'],$p['orders_products_id']),$autocalculate_arr)&& !empty($autocalculate_arr)){
      $str .= '<tr><td class="main">'.TEXT_PREORDER_PRODUCTS_NAME.'<font color="red">「'.TEXT_PREORDER_PRODUCTS_ENTRANCE.'」</font></td><td class="main">'.$p['products_name'].'&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript:window.open(\'orders.php?'.urldecode($param_str).'&oID='.$orders['orders_id'].'&pID='.$p['products_id'].'&action=show_manual_info\');">'.tep_html_element_button(BUTTON_MANUAL).'</a></td></tr>';
    }else{
      $str .= '<tr><td class="main">'.TEXT_PREORDER_PRODUCTS_NAME.'<font color="red">「'.TEXT_PREORDER_PRODUCTS_NOENTRANCE.'」</font></td><td class="main">'.$p['products_name'].'&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript:window.open(\'orders.php?'.urldecode($param_str).'&oID='.$orders['orders_id'].'&pID='.$p['products_id'].'&action=show_manual_info\');">'.tep_html_element_button(BUTTON_MANUAL).'</a></td></tr>';
    }
    $str .= '<tr><td class="main">'.TEXT_PREORDER_PRODUCTS_NUM.'</td><td class="main">'.$p['products_quantity'].TEXT_PREORDER_PRODUCTS_UNIT.tep_get_full_count2($p['products_quantity'], $p['products_id'], $p['products_rate']).'</td></tr>';
    $names = tep_get_buttons_names_by_preorders_id($orders['orders_id']);
    if ($names) {
      $str .= '<tr><td class="main">PC：</td><td class="main">'.implode('&nbsp;,&nbsp;', $names).'</td></tr>';
    }
    $i++;
  }
  }
  
  
  if (ORDER_INFO_ORDER_INFO == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_FROM.'</td>'; 
    $str .= '<td class="main">';
    $str .= tep_get_pre_site_name_by_order_id($orders['orders_id']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.TEXT_PREORDER_ENSURE_DATE.'</td>';
    $str .= '<td class="main">';
    $str .= $orders['ensure_deadline']; 
    $str .= '</td>'; 
    $str .= '</tr>';
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_FETCH_TIME.'</td>';
    $str .= '<td class="main">';
    $str .= $orders['torihiki_date']; 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    if(trim($orders['torihiki_houhou']) != ''){ 
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_OPTION.'</td>';
      $str .= '<td class="main">';
      $str .= $orders['torihiki_houhou'];    
      $str .= '</td>'; 
      $str .= '</tr>'; 
    }
  
    $str .= '<tr>'; 
    $str .= '<td class="main">'.TEXT_PREORDER_ID_NUM.'</td>';
    $str .= '<td class="main">';
    $str .= $orders['orders_id']; 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $str .= '<tr>'; 
    $str .= '<td class="main">'.TEXT_PREORDER_DATE_TEXT.'</td>';
    $str .= '<td class="main">';
    $str .= tep_date_long($orders['date_purchased']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_CUSTOMER_TYPE.'</td>';
    $str .= '<td class="main">';
     
    $tmp_customers_type .= get_guest_chk($orders['customers_id']); 
    if ($tmp_customers_type == 0) {
      $str .= TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_MEMBER; 
    } else {
      $str .= TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_CUSTOMER; 
    }
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_CUSTOMER_NAME.'</td>';
    $str .= '<td class="main">';
    $str .= '<a href="">'.$orders['customers_name'].'</a>'; 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $ostGetPara = array( "name"=>urlencode($orders['customers_name']),
                         "topicid"=>urlencode(constant("SITE_TOPIC_".$orders['site_id'])),
                         "source"=>urlencode('Email'), 
                         "email"=>urlencode($orders['customers_email_address']));
    $parmStr = '';
    foreach($ostGetPara as $key=>$value){
      $parmStr.= '&'.$key.'='.$value; 
    }
    $remoteurl = (defined('OST_SERVER')?OST_SERVER:'scp')."/tickets.php?a=open2".$parmStr."";
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_EMAIL.'</td>';
    $str .= '<td class="main">';
    $str .= tep_output_string_protected($orders['customers_email_address']).'&nbsp;&nbsp;<a title="'.RIGHT_TICKIT_ID_TITLE.'" href="'.$remoteurl.'" target="_blank">'.RIGHT_TICKIT_EMAIL.'</a>&nbsp;&nbsp;<a href="telecom_unknow.php?keywords='.tep_output_string_protected($orders['customers_email_address']).'">'.RIGHT_TICKIT_CARD.'</a>'; 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    if ( (($orders['cc_type']) || ($orders['cc_owner']) || ($orders['cc_number'])) ) {  
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_TYPE.'</td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_type']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
      
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_OWNER.'</td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_owner']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
      
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_ID.'</td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_number']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
      
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_EXPIRE_TIME.'</td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_expires']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
  } 
  }
  if (ORDER_INFO_CUSTOMER_INFO == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_IP.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_ip'] ?  $orders['orders_ip'] : 'UNKNOW',IP_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_HOST.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_host_name']?'<font'.($orders['orders_host_name'] == $orders['orders_ip'] ? ' color="red"':'').'>'.$orders['orders_host_name'].'</font>':'UNKNOW',HOST_NAME_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_USER_AGEMT.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_user_agent'] ?  $orders['orders_user_agent'] : 'UNKNOW',USER_AGENT_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  if ($orders['orders_user_agent']) { 
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_OS.'</td>';
      $str .= '<td class="main">';
      $str .= tep_high_light_by_keywords(getOS($orders['orders_user_agent']),OS_LIGHT_KEYWORDS); 
      $str .= '</td>'; 
      $str .= '</tr>'; 
      
      $browser_info = getBrowserInfo($orders['orders_user_agent']); 
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_BROWSE_TYPE.'</td>';
      $str .= '<td class="main">';
      $str .= tep_high_light_by_keywords($browser_info['longName'] . ' ' .  $browser_info['version'],BROWSER_LIGHT_KEYWORDS); 
      $str .= '</td>'; 
      $str .= '</tr>'; 
  } 
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_BROWSE_LAN.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_http_accept_language'] ?  $orders['orders_http_accept_language'] : 'UNKNOW',HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_COMPUTER_LAN.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_system_language'] ?  $orders['orders_system_language'] : 'UNKNOW',SYSTEM_LANGUAGE_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_USER_LAN.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_user_language'] ?  $orders['orders_user_language'] : 'UNKNOW',USER_LANGUAGE_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_PIXEL.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_screen_resolution'] ?  $orders['orders_screen_resolution'] : 'UNKNOW',SCREEN_RESOLUTION_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_COLOR.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_color_depth'] ?  $orders['orders_color_depth'] : 'UNKNOW',COLOR_DEPTH_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_FLASH.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_flash_enable'] === '1' ?  'YES' : ($orders['orders_flash_enable'] === '0' ? 'NO' : 'UNKNOW'),FLASH_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
  if ($orders['orders_flash_enable']) {
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_FLASH_VERSION.'</td>';
      $str .= '<td class="main">';
      $str .= tep_high_light_by_keywords($orders['orders_flash_version'],FLASH_VERSION_LIGHT_KEYWORDS); 
      $str .= '</td>'; 
      $str .= '</tr>'; 
  }
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_DIRECTOR.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_director_enable'] === '1' ? 'YES' : ($orders['orders_director_enable'] === '0' ? 'NO' : 'UNKNOW'),DIRECTOR_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_QUICK_TIME.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_quicktime_enable'] === '1' ? 'YES' : ($orders['orders_quicktime_enable'] === '0' ? 'NO' : 'UNKNOW'),QUICK_TIME_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_REAL_PLAYER.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_realplayer_enable'] === '1' ?  'YES' : ($orders['orders_realplayer_enable'] === '0' ? 'NO' : 'UNKNOW'),REAL_PLAYER_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_WINDOWS_MEDIA.'</td>';
    $str .= '<td class="main">'; $str .= tep_high_light_by_keywords($orders['orders_windows_media_enable'] === '1' ? 'YES' : ($orders['orders_windows_media_enable'] === '0' ?  'NO' : 'UNKNOW'),WINDOWS_MEDIA_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_PDF.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_pdf_enable'] === '1' ?  'YES' : ($orders['orders_pdf_enable'] === '0' ? 'NO' : 'UNKNOW'),PDF_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_JAVA.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_java_enable'] === '1' ?  'YES' : ($orders['orders_java_enable'] === '0' ? 'NO' : 'UNKNOW'),JAVA_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  }
 
  if (ORDER_INFO_REFERER_INFO == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">Referer Info:</td>';
    $str .= '<td class="main">';
    $str .= urldecode($orders['orders_ref']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    if ($orders['orders_ref_keywords']) {
      $str .= '<tr>'; 
      $str .= '<td class="main">KEYWORDS:</td>';
      $str .= '<td class="main">';
      $str .= $orders['orders_ref_keywords']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
    }
  }
  
  if (ORDER_INFO_ORDER_HISTORY == 'true') {
    $order_history_list_raw = tep_db_query("select * from ".TABLE_PREORDERS." where customers_email_address = '".$orders['customers_email_address']."' order by date_purchased desc limit 5"); 
    if (tep_db_num_rows($order_history_list_raw)) {
      $str .= '<tr>';      
      $str .= '<td class="main" colspan="2">';      
      $str .= '<table width="100%" border="0" cellspacing="0" cellpadding="2">'; 
      $str .= '<tr>'; 
      $str .= '<td colspan="4">Order History:</td>'; 
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
        $str .= strip_tags(tep_get_pre_ot_total_by_orders_id($order_history_list['orders_id'], true)); 
        $str .= '</td>'; 
        $str .= '<td>'; 
        $str .= $order_history_list['orders_status_name']; 
        $str .= '</td>'; 
        $str .= '</tr>'; 
      }
      $str .= '</table>'; 
      $str .= '</td>';      
      $str .= '</tr>';      
    }
  }
  
  if (ORDER_INFO_REPUTAION_SEARCH == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">';
    $str .= RIGHT_ORDER_INFO_REPUTAION_SEARCH; 
    $str .= '</td>';
    $str .= '<td class="main">';
    $str .= tep_get_customers_fax_by_id($orders['customers_id']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  }
  
  if (ORDER_INFO_ORDER_COMMENT == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">';
    $str .= RIGHT_ORDER_COMMENT_TITLE; 
    $str .= '</td>';
    $str .= '<td class="main">';
    $str .= nl2br($orders['orders_comment']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  }
   if(tep_not_null($orders['user_added'])|| tep_not_null($orders['customers_name'])){
  $str .= '<tr>';
  $str .= '<td>';
  $str .= TEXT_USER_ADDED;
  $str .= '</td>';
  $str .= '<td>';
if(isset($orders['user_added']) && $orders['user_added'] != ""){
   $str .= $orders['user_added'];	
	}else{
   $str .= $orders['customers_name'];	
	}	
  $str .= '</td>';
  $str .= '</tr>';
  }else{
  $str .= '<tr>';
  $str .= '<td>';
  $str .= TEXT_USER_ADDED;
  $str .= '</td>';
  $str .= '<td>';
  $str .= TEXT_UNSET_DATA;
  $str .= '</td>';
  $str .= '</tr>';
  }
  if(tep_not_null($orders['date_purchased'])){
  $str .= '<tr>';
  $str .= '<td>';
  $str .= TEXT_DATE_ADDED;
  $str .= '</td>';
  $str .= '<td>';
  $str .= $orders['date_purchased'];
  $str .= '</td>';
  $str .= '</tr>';
  }else{
  $str .= '<tr>';
  $str .= '<td>';
  $str .= TEXT_DATE_ADDED;
  $str .= '</td>';
  $str .= '<td>';
  $str .= TEXT_UNSET_DATA;
  $str .= '</td>';
  $str .= '</tr>';
  }
  if(tep_not_null($orders['user_update']) || tep_not_null($orders['customers_name'])){
  $str .= '<tr>';
  $str .= '<td>';
  $str .= TEXT_USER_UPDATE;
  $str .= '</td>';
  $str .= '<td>';
  if(isset($orders['user_update']) && $orders['user_update'] != ""){
  $str .= $orders['user_update'];
  }else{
  $str .= $orders['customers_name'];
  }
  $str .= '</td>';
  $str .= '</tr>';
  }else{
  $str .= '<tr>';
  $str .= '<td>';
  $str .= TEXT_USER_UPDATE;
  $str .= '</td>';
  $str .= '<td>';
  $str .= TEXT_UNSET_DATA;
  $str .= '</td>';
  $str .= '</tr>';
  }
  if(tep_not_null($orders['last_modified']) || tep_not_null($orders['date_purchased'])){
  $str .= '<tr>';
  $str .= '<td>';
  $str .= TEXT_DATE_UPDATE;
  $str .= '</td>';
  $str .= '<td>';
  if(isset($orders['last_modified']) && $orders['last_modified'] != ""){
  $str .= $orders['last_modified'];
  }else{
  $str .= $orders['date_purchased'];
  }
  $str .= '</td>';
  $str .= '</tr>';
  }else{
  $str .= '<tr>';
  $str .= '<td>';
  $str .= TEXT_DATE_UPDATE;
  $str .= '</td>';
  $str .= '<td>';
  $str .= TEXT_UNSET_DATA;
  $str .= '</td>';
  $str .= '</tr>';
  }

  $str .= '<tr><td class="main" colspan="2" align="center">'; 
  $str .= '<div id="order_del">';
  if ($orders['is_active'] == 1) {
    $str .= '<a href="'.tep_href_link(FILENAME_PREORDERS, urldecode($param_str).'&oID='.$orders['orders_id'].'&action=edit').'">'.tep_html_element_button(IMAGE_DETAILS).'</a>'; 
  }
  if ($ocertify->npermission >= 15) {
    $str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="delete_preorder_info(\''.$orders['orders_id'].'\', \''.urlencode($param_str).'\')"').'</a>'; 
  }
  $str .= '</div>';
  $str .= '</td></tr>'; 
   $str .= '</table>';
  $str .= '</form>';
  $str=str_replace("\n","",$str);
  $str=str_replace("\r","",$str);
  if ($single) {
    echo $str; 
  } else {
    return htmlspecialchars($str);
  }
}

/*---------------------------------------------------
 功能: 预约标志
 参数: $orders_id(string) 订单编号
 返回值: 数值(string)      
 --------------------------------------------------*/
function tep_preorders_finishqa($orders_id) {
  $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PREORDERS." where orders_id='".$orders_id."'"));
  return $order['flag_qaf'];
}
/*--------------------------------------------------
 功能: 预约状态ID 
 参数: $orders_id(string) 订单编号
 参数: $language_id(string) 语言ID 
 返回值: 订单状态(string)
 -------------------------------------------------*/
function tep_get_preorders_status_id($orders_id, $language_id = '') {
  global $languages_id;

  if (!$language_id) $language_id = $languages_id;
  $orders_query = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id='".$orders_id."'");
  $orders = tep_db_fetch_array($orders_query);
  return $orders['orders_status'];
}
/*-------------------------------------------------
 功能: 判断预约是否完成
 参数: $orders_id(string) 订单编号
 返回值: 是否完成boolean)
 ------------------------------------------------*/
function tep_get_preorder_canbe_finish($orders_id){
  //  如果是取消的可以结束 
  
  if (tep_preorders_finishqa($orders_id)) {
    return false;
  }
  $status =  tep_get_preorders_status_id($orders_id);
  if($status == 6 or $status == 8){
    return true;
  }
  $formtype = 4;
  $payment_romaji = tep_get_pre_payment_code_by_order_id($orders_id); 
  $oa_form_sql = "select * from ".TABLE_OA_FORM."   where formtype = '".$formtype."' and payment_romaji = '".$payment_romaji."'";
  $res = tep_db_fetch_array(tep_db_query($oa_form_sql));
  $form_id = $res['id'] ;
  $sql = 'select i.* from oa_form_group fg ,oa_item i where  i.group_id = fg.group_id and i.option like "%require%" and fg.form_id = "'.$form_id .'"';
  $res3  = tep_db_query($sql);
  while($item = tep_db_fetch_array($res3)){
    $sql2 =  'select value from preorders_oa_formvalue where item_id = '.$item['id'] .' and orders_id ="'.$orders_id.'" and form_id = "'.$form_id.'"';
    $res2 = tep_db_fetch_array(tep_db_query($sql2));
    if (!$res2){
      return false;
    }else {
      if ($res2['value']==''){
      return false;
      }
    }
    $res2 = '';
  }
  
return true;
}
/*------------------------------------------------------
 功能: 预约商品名称
 参数: $orders_id(string) 订单编号
 返回值: 产品名称(string)       
 -----------------------------------------------------*/
function tep_get_preorders_products_names($orders_id) {
  $str = '';
  $orders_products_query = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$orders_id."'");
  while ($p = tep_db_fetch_array($orders_products_query)) {
    $str .= $p['products_name'].' ';
  }
  return $str;
}
/*-----------------------------------------------
 功能: 预约id的最后两位
 参数: 无 
 返回值: 最后两位(string)  
 ----------------------------------------------*/
function tep_get_preorder_end_num() 
{
  $last_orders_raw = tep_db_query("select * from ".TABLE_PREORDERS." order by orders_id desc limit 1"); 
  $last_orders = tep_db_fetch_array($last_orders_raw);
  
  if ($last_orders) {
    $last_orders_num = substr($last_orders['orders_id'], -2); 
    
    if (((int)$last_orders_num < 99) && ((int)$last_orders_num > 0)) {
      $next_orders_num = (int)$last_orders_num + 1; 
    } else {
      $next_orders_num = 1; 
    }
    return sprintf('%02d', $next_orders_num); 
  }
  
  return '01';
}
/*--------------------------------------------------
 功能: 预约最后一个客户操作
 参数: 无
 返回值: 无
 -------------------------------------------------*/
function preorder_last_customer_action() {
  tep_db_query("update ".TABLE_CONFIGURATION." set configuration_value=now() where configuration_key='PREORDER_LAST_CUSTOMER_ACTION'");
}
/*-------------------------------------------------
 功能: 预约订单总价的输出
 参数: $orders_id(string) 订单编号
 参数: $single(boolean) 是否格式化
 返回值: 总价输出(string)
 ------------------------------------------------*/
function tep_get_pre_ot_total_by_orders_id_no_abs($orders_id, $single = false) {
  if ($single) {
    global $currencies; 
  }
  $query = tep_db_query("select value from " . TABLE_PREORDERS_TOTAL . " where class='ot_total' and orders_id='".$orders_id."'");
  $result = tep_db_fetch_array($query);
  if($result['value'] > 0){
    if ($single) {
      return
        "<b>".$currencies->format($result['value'],true,DEFAULT_CURRENCY,'',false)."</b>";
    } else {
      return "<b>".$result['value']."</b>";
    }
  }else{
    if ($single) {
      if ($result['value'] < 0) {
        return "<b><font color='#ff0000'>".str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($result['value'],true,DEFAULT_CURRENCY,'',false))."</font>".TEXT_MONEY_SYMBOL."</b>";
      } else {
        return "<b>".$currencies->format($result['value'],true,DEFAULT_CURRENCY,'',false)."</b>";
      }
    } else {
      return "<b><font color='#ff0000'>".$result['value']."".TEXT_MONEY_SYMBOL."</font></b>";
    }
  }
}
/*--------------------------------------------
 功能: 预约的商品属性邮件内容
 参数: $orders_id(string) 订单编号
 返回值: 属性邮件内容(string)  
 -------------------------------------------*/
function tep_get_preorder_attr_mail($orders_id)
{
  $max_op_len = 0;
  $max_info_array = array();
  $attr_list_array = array();
  $return_str = '';
  
  $preorder_attr_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$orders_id."'");

  while ($preorder_attr_res = tep_db_fetch_array($preorder_attr_raw)) {
    $input_op = @unserialize(stripslashes($preorder_attr_res['option_info'])); 
    $max_info_array[] = mb_strlen($input_op['title'], 'utf-8');
    $attr_list_array[] = array('title' => $input_op['title'], 'value' => $input_op['value']);
  }
  
  if (!empty($max_info_array)) {
    $max_op_len = max($max_info_array); 
  }
  
  if (!empty($attr_list_array)) {
    foreach ($attr_list_array as $key => $value) {
      $return_str .= $value['title'].str_repeat('　', intval($max_op_len - mb_strlen($value['title'], 'utf-8'))).'：'.$value['value']."\n"; 
    }
  }
 
  return $return_str;
}
/*---------------------------------------------
 功能: 检查产品类型
 参数: $orders_products_id(string) 订单产品编号 
 返回值: 产品类型(int)
 --------------------------------------------*/
function tep_check_pre_product_type($orders_products_id)
{
  $orders_products_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_products_id = '".$orders_products_id."'");
  $orders_products = tep_db_fetch_array($orders_products_raw);
 
  $product_query = tep_db_query("select products_bflag from " . TABLE_PRODUCTS . " where products_id = '" . $orders_products['products_id'] . "'");
  $product = tep_db_fetch_array($product_query);
  
  if ($product) {
    return $product['products_bflag'];
  } else {
    if ($orders_products['products_price'] < 0) {
      return 1; 
    }
  } 
  
  return 0;
}
/*------------------------------------------
 功能: 检查预约商品的option是否不足
 参数: $products_id(string) 产品编号
 参数: $pro_attr_info(array) 产品属性信息
 返回值: 是否不足(boolean)
 -----------------------------------------*/
function tep_pre_check_less_option_product($products_id, $pro_attr_info)
{
  $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>"); 
  $exists_product_raw = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$products_id."'"); 
  $exists_product = tep_db_fetch_array($exists_product_raw); 
  if ($exists_product) {
    $item_list_array = array(); 
    $item_list_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where group_id = '".$exists_product['belong_to_option']."' and status = '1' and place_type = '0'");
    if (tep_db_num_rows($item_list_query)) {
      while ($item_list = tep_db_fetch_array($item_list_query)) {
        $item_list_array[] = $item_list; 
      }
      $op_num = count($item_list_array); 
      if (!empty($pro_attr_info)) {
        $op_tmp_num = count($pro_attr_info);
      } else {
        $op_tmp_num = 0; 
      }
      if ($op_num != $op_tmp_num) {
        return true; 
      }
      foreach ($pro_attr_info as $p_key => $p_value) {
        $item_info_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$p_value['option_item_id']."' and status = '1' and place_type = '0'"); 
        $item_info = tep_db_fetch_array($item_info_query);
        if ($item_info) {
          $ao_option = @unserialize($item_info['option']); 
          if ($item_info['type'] == 'radio') {
            $aop_single = false;  
            foreach ($ao_option['radio_image'] as $r_key => $r_value) {
              if (trim(str_replace($replace_arr, '', nl2br(stripslashes($r_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($p_value['option_info']['value']))))) {
                $aop_single = true;
                break;
              }
            }
            if (!$aop_single) {
              return true; 
            }
          } else if ($item_info['type'] == 'text') {
            if (trim(str_replace($replace_arr, '', nl2br(stripslashes($ao_option['itextarea'])))) != trim(str_replace($replace_arr, '', nl2br(stripslashes($p_value['option_info']['value']))))) {
              return true; 
            } 
          } else if ($item_info['type'] == 'select') {
            if (!empty($ao_option['se_option'])) {
              $ao_se_single = false;
              foreach ($ao_option['se_option'] as $se_key => $se_value) {
                if (trim($se_value) == trim($p_value['option_info']['value'])) {
                  $ao_se_single = true;
                  break; 
                }
              }
              if (!$ao_se_single) {
                return true; 
              }
            } else {
              return true; 
            }
          }
        } else {
          return true; 
        } 
      }
    } else {
      if (!empty($pro_attr_info)) {
        return true; 
      }
    }
  } else {
    return true; 
  }
  return false;
}

/* -------------------------------------
    功能: 判断预约订单是否结束 
    参数: $osid(string) 订单状态id
    返回值: 预约订单是否结束(boolean) 
------------------------------------ */
function tep_preorders_status_finished($osid){
    $query = tep_db_query("
        select * 
        from  ".TABLE_PREORDERS_STATUS."
        where orders_status_id = '".(int)$osid."'
        ");
    $os = tep_db_fetch_array($query);
    return isset($os['finished']) && $os['finished'];
}

/*-----------------------------------
    功能: 删除超时的未转正式的预约订单 
    参数: 无 
    返回值: 无 
-----------------------------------*/
function tep_preorders_to_orders_timeout()
{
  $preorder_id_array = array();
  $preorder_query = tep_db_query("select orders_id from ".TABLE_PREORDERS." where is_active = '0' and datediff(now(),date_purchased)>3");
  while($preorder_array = tep_db_fetch_array($preorder_query)){

    $preorder_id_array[] = $preorder_array['orders_id'];
  }
  tep_db_free_result($preorder_query);
  $preorder_id_str = implode("','",$preorder_id_array);

  if(!empty($preorder_id_array)){
    //删除关联数据
    tep_db_query("delete from ".TABLE_PREORDERS." where orders_id in ('".$preorder_id_str."')"); 
    tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS." where orders_id in ('".$preorder_id_str."')"); 
    tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id in ('".$preorder_id_str."')"); 
    tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_DOWNLOAD." where orders_id in ('".$preorder_id_str."')"); 
    tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_TO_ACTOR." where orders_id in ('".$preorder_id_str."')"); 
    tep_db_query("delete from ".TABLE_PREORDERS_QUESTIONS." where orders_id in ('".$preorder_id_str."')"); 
    tep_db_query("delete from ".TABLE_PREORDERS_QUESTIONS_PRODUCTS." where orders_id in ('".$preorder_id_str."')"); 
    tep_db_query("delete from ".TABLE_PREORDERS_STATUS_HISTORY." where orders_id in ('".$preorder_id_str."')"); 
    tep_db_query("delete from ".TABLE_PREORDERS_TOTAL." where orders_id in ('".$preorder_id_str."')"); 
    tep_db_query("delete from ".TABLE_PREORDERS_TO_BUTTONS." where orders_id in ('".$preorder_id_str."')"); 
    tep_db_query("delete from ".TABLE_PREORDERS_OA_FORMVALUE." where orders_id in ('".$preorder_id_str."')");
  }      
}
