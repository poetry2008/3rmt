<?php
/*
  $Id$
*/
//如果信用卡支付成功并生成了订单，直接跳转到注文成功页面
if(isset($_SESSION['preorder_credit_flag']) && $_SESSION['preorder_credit_flag'] == '1'){
  unset($_SESSION['preorder_credit_flag']);
  tep_redirect(tep_href_link('change_preorder_success.php', '', 'SSL'));
}

if (!isset($_SESSION['preorder_info_id'])) {
  //判断是否有预约订单id 
  forward404();
}

require(DIR_WS_FUNCTIONS . 'visites.php');
require_once(DIR_WS_CLASSES . 'payment.php');

if (isset($preorder_real_point)) {
  $preorder_point = $preorder_real_point;
}

include(DIR_WS_LANGUAGES . $language . '/change_preorder_process.php');



$preorder_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$_SESSION['preorder_info_id']."' and site_id = '".SITE_ID."'");
$preorder = tep_db_fetch_array($preorder_raw);

if ($preorder) {
$customer_error = false;
if(isset($preorder['customers_id']) && $preorder['customers_id']!='' && isset($_SESSION['customer_emailaddress'])){
  $flag_customer_info = tep_is_customer_by_id($preorder['customers_id']);
  if(!$flag_customer_info ||
    strtolower($flag_customer_info['customers_email_address']) != strtolower($_SESSION['customer_emailaddress'])){
    $customer_error = true;
  }
}
if(!isset($_SESSION['preorder_info_date']) || !isset($_SESSION['preorder_info_hour']) || !isset($_SESSION['preorder_info_min']) || $customer_error){
//判断配送时间信息丢失或顾客信息错误就弹出错误页面
/* -------------------------------------
    功能: 高亮显示指定字符 
    参数: $str(string) 字符串   
    参数: $keywords(string) 高亮显示的字符串   
    返回值: 处理后的字符串(string) 
------------------------------------ */
  function tep_high_light_by_keywords($str, $keywords){ 
      $k = $rk= explode('|',$keywords);
      foreach($k as $key => $value){
           $rk[$key] = '<font style="background:red;">'.$value.'</font>';
      }
      return str_replace($k, $rk, $str);
  }

  //错误通知邮件
  $error_mail_array = tep_get_mail_templates('ORDERS_EMPTY_EMAIL_TEXT',SITE_ID);
  //错误邮件标题
  $orders_mail_title = $error_mail_array['title'].'　'.date('Y-m-d H:i:s');
  $orders_mail_text = $error_mail_array['contents'];
  $orders_mail_text = str_replace('${ERROR_NUMBER}','001',$orders_mail_text);
  $orders_mail_text = str_replace('${ERROR_TIME}',date('Y-m-d H:i:s'),$orders_mail_text); 

  //错误邮件正文
  $orders_error_contents = "\n\n";
  //orders site
  $orders_error_contents .= ORDERS_SITE." ".STORE_NAME."\n";
  //shipping time
  $orders_error_contents .= ORDERS_TIME." ".$_SESSION['preorder_info_date'].' '. $_SESSION['preorder_info_start_hour'] .':'. $_SESSION['preorder_info_start_min'] .':00'."\n";
  //orders date
  $orders_error_contents .= CREATE_ORDERS_DATE." ".date('Y-m-d H:i:s')."\n";
  //customers type
  $customer_query = tep_db_query("select customers_guest_chk from " . TABLE_CUSTOMERS . " where customers_id = '" . $preorder['customers_id'] . "'");
  $customer_array = tep_db_fetch_array($customer_query);
  tep_db_free_result($customer_query);
  $customer_type = $customer_array['customers_guest_chk'] == 1 ? TABLE_HEADING_MEMBER_TYPE_GUEST : TEXT_MEMBER;
  $orders_error_contents .= CUSTOMER_TYPE." ".$customer_type."\n";
  //customers name
  $customer_name = $preorder['customers_name'];
  $orders_error_contents .= CUSTOMER_NAME." ".$customer_name."\n";
  $orders_error_contents .= ORDERS_EMAIL." ".$_SESSION['customer_emailaddress']."\n";
  //orders products list
  $products_ordered_text = ''; 
  
  $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
  $preorder_product_res = tep_db_fetch_array($preorder_product_raw); 
  $search_products = tep_get_product_by_id($preorder_product_res['products_id'], 0, $languages_id,true,'product_info');

  //预约订单option的显示 
  if (isset($_SESSION['preorder_option_info'])) {
    foreach ($_SESSION['preorder_option_info'] as $cl_key => $cl_value) {
      $cl_key_info = explode('_', $cl_key);
      $cl_attr_query = tep_db_query("select front_title from ".TABLE_OPTION_ITEM." where name = '".$cl_key_info['1']."' and id = '".$cl_key_info[3]."'");
      $cl_attr_values = tep_db_fetch_array($cl_attr_query); 
    }
  }
  $old_attr_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$_SESSION['preorder_info_id']."'");

  while ($old_attr_res = tep_db_fetch_array($old_attr_raw)) {
    $old_attr_info = @unserialize(stripslashes($old_attr_res['option_info'])); 
  }
  
  $show_products_name = tep_get_products_name($preorder_product_res['products_id']); 
  $products_ordered_text .= TEXT_ORDERS_PRODUCTS.': '.(tep_not_null($show_products_name) ? $show_products_name : $preorder_product_res['products_name']);
  if (tep_not_null($preorder_product_res['products_model'])) {
    $products_ordered_text .= ' ('.$preorder_product_res['products_model'].')'; 
  }
 
  if ($preorder_product_res['products_price'] != '0') {
    $products_ordered_text .= '('.$currencies->display_price($preorder_product_res['products_price'], $preorder_product_res['products_tax']).')'; 
  } else if ($preorder_product_res['final_price'] != '0') {
    $products_ordered_text .= '('.$currencies->display_price($preorder_product_res['final_price'], $preorder_product_res['products_tax']).')'; 
  }
  $products_ordered_atttibutes_text = '';

  //预约订单时option信息
  $mold_attr_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$_SESSION['preorder_info_id']."'");
  while ($mold_attr_res = tep_db_fetch_array($mold_attr_raw)) {
    $mold_attr_info = @unserialize(stripslashes($mold_attr_res['option_info'])); 

    $products_ordered_attributes .= "\n"
        .$mold_attr_info['title']
        .': '.str_replace($replace_arr, "", $mold_attr_info['value']);
      if ($mold_attr_res['options_values_price'] != '0') {
        if ($preorder_product_res['products_price'] != '0') {
          $products_ordered_attributes .= '　('.$currencies->format($mold_attr_res['options_values_price']).')';
        } 
      }

  }

  if (isset($_SESSION['preorder_option_info'])) {
    //预约转正式时的option信息
    foreach ($_SESSION['preorder_option_info'] as $op_key => $op_value) {
      $op_key_info = explode('_', $op_key);
      $option_attr_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_key_info['1']."' and id = '".$op_key_info[3]."'");
      $option_attr_values = tep_db_fetch_array($option_attr_query);
      
      if ($option_attr_values) {

      $input_option_array = array('title' => $option_attr_values['front_title'], 'value' => str_replace(array("<BR>"), "<br>", $op_value));
      $ao_price = 0; 
      if ($option_attr_values['type'] == 'radio') {
         $ao_option_array = @unserialize($option_attr_values['option']);
         if (!empty($ao_option_array['radio_image'])) {
           foreach ($ao_option_array['radio_image'] as $or_key => $or_value) {
             if (trim(str_replace($replace_arr, '', nl2br(stripslashes($or_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_value))))) {
               $ao_price = $or_value['money']; 
               break; 
             }
           }
         } 
      } else if ($option_attr_values['type'] == 'textarea') {
        $to_option_array = @unserialize($option_attr_values['option']);
        $to_tmp_single = false; 
        if ($to_option_array['require'] == '0') {
          if ($op_value == MSG_TEXT_NULL) {
            $to_tmp_single = true; 
          }
        }
        if ($to_tmp_single) {
          $ao_price = 0; 
        } else {
          $ao_price = $option_attr_values['price']; 
        }
      } else {
        $ao_price = $option_attr_values['price']; 
      }
        
      $products_ordered_attributes .= "\n"
        .$option_attr_values['front_title'] .': '.str_replace($replace_arr, "", $op_value);
      if ($ao_price != '0') {
        $products_ordered_attributes .= '　('.$currencies->format($ao_price).')';
      }
    }
    }
  }

  $products_ordered_text .= $products_ordered_attributes;

  //products list
  $products_ordered_text .= "\n".TEXT_ORDERS_PRODUCTS_NUMBER.': ' .  $preorder_product_res['products_quantity'] . NUM_UNIT_TEXT .  tep_get_full_count2($preorder_product_res['products_quantity'], $preorder_product_res['products_id'])."\n";
  $products_ordered_text .= TEXT_ORDERS_PRODUCTS_PRICE.': ' .  $currencies->display_price(isset($option_info_array['final_price'])?$option_info_array['final_price']:$preorder_product_res['final_price'], $preorder_product_res['products_tax']) . "\n";
  $products_ordered_text .= TEXT_ORDERS_PRODUCTS_SUBTOTAL.': ' .  $currencies->display_price(isset($option_info_array['final_price'])?$option_info_array['final_price']:$preorder_product_res['final_price'], $preorder_product_res['products_tax'], $preorder_product_res['products_quantity']) . "\n";
  //total
  $orders_total = str_replace(array(',',JPMONEY_UNIT_TEXT),'',$currencies->display_price(isset($option_info_array['final_price'])?$option_info_array['final_price']:$preorder_product_res['final_price'], $preorder_product_res['products_tax'], $preorder_product_res['products_quantity']));
  //orders subtotal
  $orders_error_contents .= TEXT_ORDERS_PRODUCTS_SUBTOTAL.": ".$currencies->format($orders_total)."\n";
  //自定义费用
  $preorder_total_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$_SESSION['preorder_info_id']."'");
  
  $totals_custom_array = array();
  while ($preorder_total_res = tep_db_fetch_array($preorder_total_raw)) {
    if($preorder_total_res['class'] == 'ot_custom' && trim($preorder_total_res['title']) != ''){

      $totals_custom_array[] = array('title'=>$preorder_total_res['title'],'value'=>$preorder_total_res['value']);
    }
  } 
  if(!empty($totals_custom_array)){
    foreach($totals_custom_array as $totals_custom_value){

      $orders_error_contents .= $totals_custom_value['title'].': '.$currencies->format($totals_custom_value['value'])."\n";
      $orders_total += $totals_custom_value['value'];
    }
  }
  //point
  if ($preorder_point){
    $orders_error_contents .= TEXT_ORDERS_PRODUCTS_POINT." ".$currencies->format(abs($preorder_point))."\n";
    $orders_total -= abs($preorder_point);
  }
  if (isset($_SESSION['preorder_campaign_fee'])) {
    $orders_error_contents .= TEXT_ORDERS_PRODUCTS_POINT." ".$currencies->format(abs($_SESSION['preorder_campaign_fee']))."\n";
    $orders_total -= abs($_SESSION['preorder_campaign_fee']);
  }
  //handle code
  if(!isset($_SESSION['preorders_code_fee'])){
    $orders_error_contents .= TEXT_ORDERS_PRODUCTS_HANDLE_FEE." 0".JPMONEY_UNIT_TEXT."\n"; 
  }else{
    $orders_error_contents .= TEXT_ORDERS_PRODUCTS_HANDLE_FEE." ".$_SESSION['preorders_code_fee'].JPMONEY_UNIT_TEXT."\n";
    $orders_total += abs($_SESSION['preorders_code_fee']);
  } 
  //orders total
  $orders_error_contents .= TEXT_ORDERS_PRODUCTS_TOTAL." ".$currencies->format($orders_total)."\n";
  //earn points
  $orders_error_contents .= TEXT_ORDERS_EARN_POINTS." ".str_replace(JPMONEY_UNIT_TEXT,'',$currencies->format((int)$_SESSION['preorder_get_point']))." P\n";
  $orders_payment = $preorder['payment_method'];
  $orders_error_contents .= ORDERS_PAYMENT." ".$orders_payment."\n"; 
  //orders products
  $orders_error_contents .= TEXT_ORDERS_PRODUCTS_LINE.$products_ordered_text.TEXT_ORDERS_PRODUCTS_LINE;
  //orders comments
  $orders_error_contents .= TEXT_ORDERS_COMMENTS.' '.trim($preorder['comment_msg'])."\n";
  //referer info
  $orders_error_contents .= 'Referer Info'.": ".$_SESSION['referer']."\n";
  //获取顾客的IP地址、PC的信息
  $orders_error_contents .= CUSTOMER_IP." ".$_SERVER['REMOTE_ADDR']."\n";
  $orders_error_contents .= HOST_NAME." ".trim(strtolower(@gethostbyaddr($_SERVER['REMOTE_ADDR'])))."\n";
  $orders_error_contents .= USER_AGENT." ".$_SERVER["HTTP_USER_AGENT"]."\n";
  $orders_error_contents .= CUSTOMER_OS." ".tep_high_light_by_keywords(getOS($_SERVER["HTTP_USER_AGENT"]),OS_LIGHT_KEYWORDS)."\n";
  $browser_info = getBrowserInfo($_SERVER["HTTP_USER_AGENT"]);
  $browser_type = tep_high_light_by_keywords($browser_info['longName'] . ' ' . $browser_info['version'],BROWSER_LIGHT_KEYWORDS);
  $orders_error_contents .= BROWSER_TYPE." ".$browser_type."\n";
  $browser_language = tep_high_light_by_keywords($_SERVER['HTTP_ACCEPT_LANGUAGE'] ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'UNKNOW',HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS);
  $orders_error_contents .= BROWSER_LANGUAGE." ".$browser_language."\n";
  $browser_pc = tep_high_light_by_keywords($_SESSION['systemLanguage'] ? $_SESSION['systemLanguage'] : 'UNKNOW',SYSTEM_LANGUAGE_LIGHT_KEYWORDS);
  $orders_error_contents .= BROWSER_PC_LANGUAGE." ".$browser_pc."\n";
  $browser_user = tep_high_light_by_keywords($_SESSION['userLanguage'] ? $_SESSION['userLanguage'] : 'UNKNOW',USER_LANGUAGE_LIGHT_KEYWORDS);
  $orders_error_contents .= BROWSER_USER_LANGUAGE." ".$browser_user."\n";

  $orders_mail_text = str_replace('${ERROR_CONTENTS}',$orders_error_contents,$orders_mail_text);
  $orders_mail_text = tep_replace_mail_templates($orders_mail_text,$_SESSION['customer_emailaddress'],$customer_name);

  $message = new email(array('X-Mailer: iimy Mailer'));
  //错误订单 详细信息
   function arr_foreach ($arr) {
     $str = '';
     if (!is_array ($arr)&&!is_object($arr)) {
       return false;
     }
     foreach ($arr as $key => $val ) {
       if (is_array ($val)||is_object($val)) {
         $str .= arr_foreach($val);
       } else {
         $str .=  $key.' :'.$val."\n";
       }
     }
     return $str;
  }
  $orders_mail_text .= "\n-----------------session-------------\n";
  $orders_mail_text .= arr_foreach($_SESSION);
  if(!empty($flag_customer_info)){

    $orders_mail_text .= "\n-----------------Data-------------\n";
    $orders_mail_text .= arr_foreach($flag_customer_info);
  }
  $text = $orders_mail_text;
  $message->add_html(nl2br($orders_mail_text), $text);
  $message->build_message();
  //给管理员发信
  $message->send(STORE_OWNER,IP_SEAL_EMAIL_ADDRESS,STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS,$orders_mail_title);
  $customer_email = $_SESSION['customer_emailaddress'];
   
  //当错误发生时，清除SESSION
  
  //customer session destroy
  tep_session_unregister('customer_id');
  tep_session_unregister('customer_default_address_id');
  tep_session_unregister('customer_first_name');
  tep_session_unregister('customer_last_name');
  tep_session_unregister('customer_country_id');
  tep_session_unregister('customer_zone_id');
  tep_session_unregister('comments');
  tep_session_unregister('customer_emailaddress');

  //products session destroy
  tep_session_unregister('shipping');
  tep_session_unregister('payment');
  tep_session_unregister('comments');
  tep_session_unregister('point');
  tep_session_unregister('get_point');
  tep_session_unregister('real_point');
  tep_session_unregister('torihikihouhou');
  tep_session_unregister('date');
  tep_session_unregister('hour');
  tep_session_unregister('min');
  tep_session_unregister('insert_torihiki_date');
  unset($_SESSION['character']);
  unset($_SESSION['option']);
  unset($_SESSION['referer_adurl']);
  unset($_SESSION['campaign_fee']);
  unset($_SESSION['camp_id']);
  tep_session_unregister('h_code_fee');
  tep_session_unregister('h_point');

  //shipping session destroy
  tep_session_unregister('start_hour');
  tep_session_unregister('start_min');
  tep_session_unregister('end_hour');
  tep_session_unregister('end_min');
  tep_session_unregister('ele');
  tep_session_unregister('address_option');
  tep_session_unregister('insert_torihiki_date_end');
  tep_session_unregister('address_show_list');
  unset($_SESSION['options']);
  unset($_SESSION['options_type_array']);
  unset($_SESSION['weight_fee']);
  unset($_SESSION['free_value']);
  tep_session_unregister('hc_point');
  tep_session_unregister('hc_camp_point');
  unset($_SESSION['shipping_page_str']);
  unset($_SESSION['shipping_session_flag']);

  //preorder session destroy
  tep_session_unregister('preorder_info_tori');
  tep_session_unregister('preorder_info_date');
  tep_session_unregister('preorder_info_hour');
  tep_session_unregister('preorder_info_min');
  tep_session_unregister('preorder_info_id');
  tep_session_unregister('preorder_info_pay');
  tep_session_unregister('preorder_option_info');
  tep_session_unregister('preorder_information');
  tep_session_unregister('preorder_shipping_fee');
  if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
    tep_session_unregister('preorder_point');
    tep_session_unregister('preorder_real_point');
    tep_session_unregister('preorder_get_point');
  }
  unset($_SESSION['insert_id']);
  unset($_SESSION['preorder_option']);
  unset($_SESSION['referer_adurl']);

  unset($_SESSION['preorder_campaign_fee']);
  unset($_SESSION['preorder_camp_id']);
  unset($_SESSION['preorders_code_fee']);
  unset($_SESSION['preorder_payment_info']);

  //清空购物车
  $cart->reset();

  $site_romaji = tep_get_site_romaji_by_id(SITE_ID);
  $oconfig_raw = tep_db_query("select value from ".TABLE_OTHER_CONFIG." where keyword = 'css_random_string' and site_id = '".SITE_ID."'");
  $oconfig_res = tep_db_fetch_array($oconfig_raw);
  tep_db_free_result($oconfig_raw);
  if($oconfig_res){
     $css_random_str = substr($oconfig_res['value'], 0, 4);
  }else{
     $css_random_str = date('YmdHi', time());
  }

  //临时生成SESSION
  $_SESSION['error_name'] = $customer_name;
  $_SESSION['error_email'] = $customer_email; 
  $_SESSION['error_subject'] = $orders_mail_title; 
  $_SESSION['error_message'] = strip_tags($orders_mail_text);
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<?php
  if(isset($_SESSION['preorder_credit_flag'])){
?>
<link rel="stylesheet" type="text/css" href="<?php echo '../css/'.$site_romaji.'.css?v='.$css_random_str;?>">
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
function close_popup_notice(){
  $("#popup_notice").css("display", "none");
  $("#greybackground").remove();
}

function update_notice(url){
  $.ajax({
    url: '../ajax_notice.php?action=process',    
    type:'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $("#popup_notice").css("display", "none");
      $("#greybackground").remove();
      window.location.href='../'+url;
    }
  });
}   
</script>
<?php
  }else{
?>
<link rel="stylesheet" type="text/css" href="<?php echo 'css/'.$site_romaji.'.css?v='.$css_random_str;?>">
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/notice.js"></script>
<?php
  }
?>
<script type="text/javascript">
$(document).ready(function() {
//ajax submit
$.ajax({
  url: '<?php echo isset($_SESSION['preorder_credit_flag']) ? '../ajax_confirm_session_error.php?action=session' : 'ajax_confirm_session_error.php?action=session';?>',
  data: '',
  type: 'POST',
  dataType: 'text',
  async : false,
  success: function(data){ 
  }
});
var docheight = $(document).height();
var screenwidth, screenheight, mytop, getPosLeft, getPosTop
screenwidth = $(window).width();
screenheight = $(window).height();
mytop = $(document).scrollTop();
getPosLeft = screenwidth / 2 - 276;
getPosTop = 50;

$("#popup_notice").css('display', 'block');
$("#popup_notice").css({ "left": getPosLeft, "top": getPosTop })

$(window).resize(function() {
           screenwidth = $(window).width();
           screenheight = $(window).height();
           mytop = $(document).scrollTop();
           getPosLeft = screenwidth / 2 - 276;
           getPosTop = 50;
           $("#popup_notice").css({ "left": getPosLeft, "top": getPosTop + mytop });

});


$("body").append("<div id='greybackground'></div>");
$("#greybackground").css({ "opacity": "0.5", "height": docheight });
});
</script>
</head>
<body>
<div id="popup_notice" style="display:none;">
<div class="popup_notice_text">
<?php echo TEXT_ORDERS_ERROR;?>
</div>
<div class="popup_notice_middle">
<?php 
echo TEXT_ORDERS_EMPTY_COMMENT;
?>
</div>
<div align="center" class="popup_notice_button">
<a href="javascript:void(0);" onClick="update_notice('index.php');"><img alt="<?php echo LOCATION_HREF_INDEX;?>" src="<?php echo isset($_SESSION['preorder_credit_flag']) ? '../images/design/href_home.gif' : 'images/design/href_home.gif';?>"></a>&nbsp;&nbsp;
<a href="javascript:void(0);" onClick="update_notice('contact_us.php');"><img alt="<?php echo CONTACT_US;?>" src="<?php echo isset($_SESSION['preorder_credit_flag']) ? '../images/design/contact_us.gif' : 'images/design/contact_us.gif';?>"></a>
</div>
</div>
</body>
</html>

<?php
  unset($_SESSION['preorder_credit_flag']);
  exit;
  }

  $seal_user_sql = "select is_seal, is_send_mail from ".TABLE_CUSTOMERS." where customers_id ='".$preorder['customers_id']."' limit 1";
  $seal_user_query = tep_db_query($seal_user_sql);
  if ($seal_user_row = tep_db_fetch_array($seal_user_query)){
    if($seal_user_row['is_seal']){
      //判断该顾客是否可以下订单 
      tep_redirect(tep_href_link('change_preorder_confirm.php', '', 'SSL')); 
      exit;
    }
  }
  $orders_id = date('Ymd').'-'.date('His').tep_get_order_end_num(); 
  $orders_id = tep_is_has_order($orders_id);
  $payment_modules = payment::getInstance($preorder['site_id']);   
  $cpayment_code = payment::changeRomaji($preorder['payment_method'], PAYMENT_RETURN_TYPE_CODE);   
  
  $option_info_array = get_preorder_total_info($cpayment_code, $preorder['orders_id'], $preorder_option_info);
  $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>");
  
  $torihikihouhou_date_str = $_SESSION['preorder_info_date'].' '. $_SESSION['preorder_info_start_hour'] .':'. $_SESSION['preorder_info_start_min'] .':00';
  $torihikihouhou_date_end_str = $_SESSION['preorder_info_date'].' '. $_SESSION['preorder_info_end_hour'] .':'. $_SESSION['preorder_info_end_min'] .':00';
  //获取相应支付方式的默认注文订单状态
  $orders_status_id = get_configuration_by_site_id('MODULE_PAYMENT_'.strtoupper($cpayment_code).'_ORDER_STATUS_ID',SITE_ID);
  $orders_status_id = $orders_status_id != 0 ? $orders_status_id : DEFAULT_ORDERS_STATUS_ID;
  $default_status_raw = tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$orders_status_id."'");
  $default_status_res = tep_db_fetch_array($default_status_raw); 
  $preorder_cus_id = $preorder['customers_id']; 
  $sql_data_array = array('orders_id' => $orders_id,
                           'site_id' => $preorder['site_id'], 
                           'customers_id' => $preorder_cus_id, 
                           'customers_name' => $preorder['customers_name'], 
                           'customers_name_f' => $preorder['customers_name_f'], 
                           'customers_company' => $preorder['customers_company'], 
                           'customers_street_address' => $preorder['customers_street_address'], 
                           'customers_suburb' => $preorder['customers_suburb'], 
                           'customers_city' => $preorder['customers_city'], 
                           'customers_postcode' => $preorder['customers_postcode'], 
                           'customers_state' => $preorder['customers_state'], 
                           'customers_country' => $preorder['customers_country'], 
                           'customers_telephone' => $preorder['customers_telephone'], 
                           'customers_email_address' => $preorder['customers_email_address'], 
                           'customers_address_format_id' => $preorder['customers_address_format_id'], 
                           'delivery_name' => $preorder['delivery_name'], 
                           'delivery_name_f' => $preorder['delivery_name_f'], 
                           'delivery_company' => $preorder['delivery_company'], 
                           'delivery_street_address' => $preorder['delivery_street_address'], 
                           'delivery_suburb' => $preorder['delivery_suburb'], 
                           'delivery_city' => $preorder['delivery_city'], 
                           'delivery_postcode' => $preorder['delivery_postcode'], 
                           'delivery_state' => $preorder['delivery_state'], 
                           'delivery_country' => $preorder['delivery_country'], 
                           'delivery_telephone' => $preorder['delivery_telephone'], 
                           'delivery_address_format_id' => $preorder['delivery_address_format_id'], 
                           'billing_name' => $preorder['billing_name'], 
                           'billing_name_f' => $preorder['billing_name_f'], 
                           'billing_company' => $preorder['billing_company'], 
                           'billing_street_address' => $preorder['billing_street_address'], 
                           'billing_suburb' => $preorder['billing_suburb'], 
                           'billing_city' => $preorder['billing_city'], 
                           'billing_postcode' => $preorder['billing_postcode'], 
                           'billing_state' => $preorder['billing_state'], 
                           'billing_country' => $preorder['billing_country'], 
                           'billing_telephone' => $preorder['billing_telephone'], 
                           'billing_address_format_id' => $preorder['billing_address_format_id'], 
                           'payment_method' => $preorder['payment_method'], 
                           'cc_type' => $preorder['cc_type'], 
                           'cc_owner' => $preorder['cc_owner'], 
                           'cc_number' => $preorder['cc_number'], 
                           'cc_expires' => $preorder['cc_expires'], 
                           'last_modified' => $preorder['last_modified'], 
                           'date_purchased' => 'now()', 
                           'orders_status' => $orders_status_id, 
                           'orders_date_finished' => $preorder['orders_date_finished'], 
                           'currency' => $preorder['currency'], 
                           'currency_value' => $preorder['currency_value'], 
                           'torihiki_Bahamut' => $preorder['torihiki_Bahamut'], 
                           'torihiki_houhou' => $_SESSION['preorder_info_tori'], 
                           'torihiki_date' => $torihikihouhou_date_str, 
                           'torihiki_date_end' => $torihikihouhou_date_end_str,
                           'code_fee' => (isset($option_info_array['fee']))?$option_info_array['fee']:$_SESSION['preorders_code_fee'], 
                           'shipping_fee' => $_SESSION['preorder_shipping_fee'],
                           'language_id' => $preorder['language_id'], 
                           'orders_status_name' => $default_status_res['orders_status_name'], 
                           'orders_status_image' => $preorder['orders_status_image'],
                           'finished' => $preorder['finished'], 
                           'orders_ref' => $preorder['orders_ref'], 
                           'orders_ref_site' => $preorder['orders_ref_site'], 
                           'orders_ip' => $_SERVER['REMOTE_ADDR'], 
                           'orders_host_name' => trim(strtolower(@gethostbyaddr($_SERVER['REMOTE_ADDR']))), 
                           'orders_user_agent' => $_SERVER['HTTP_USER_AGENT'], 
                           'orders_comment' => $preorder['orders_comment'], 
                           'orders_important_flag' => $preorder['orders_important_flag'], 
                           'orders_care_flag' => $preorder['orders_care_flag'], 
                           'orders_wait_flag' => '1', 
                           'orders_inputed_flag' => '0', 
                           'orders_screen_resolution' => $_SESSION['screenResolution'], 
                           'orders_color_depth' => $_SESSION['colorDepth'], 
                           'orders_flash_enable' => $_SESSION['flashEnable'], 
                           'orders_flash_version' => $_SESSION['flashVersion'], 
                           'orders_director_enable' => $_SESSION['directorEnable'], 
                           'orders_quicktime_enable' => $_SESSION['quicktimeEnable'], 
                           'orders_realplayer_enable' => $_SESSION['realPlayerEnable'], 
                           'orders_windows_media_enable' => $_SESSION['windowsMediaEnable'], 
                           'orders_pdf_enable' => $_SESSION['pdfEnable'], 
                           'orders_java_enable' => $_SESSION['javaEnable'], 
                           'orders_http_accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'], 
                           'orders_system_language' => $_SESSION['systemLanguage'], 
                           'orders_user_language' => $_SESSION['userLanguage'], 
                           'orders_work' => '', 
                           'q_8_1' => $preorder['q_8_1'], 
                           'telecom_option' => $_SESSION['preorder_option'], 
                           'orders_ref_keywords' => $preorder['orders_ref_keywords'], 
                           'flag_qaf' => $preorder['flag_qaf'], 
                           'end_user' => $preorder['end_user'], 
                           'confirm_payment_time' => $preorder['confirm_payment_time'],
                           'orders_type' => 1,
                           'is_gray' => $preorder['is_gray']
                          );
  
  if (isset($_SESSION['referer_adurl']) && $_SESSION['referer_adurl']) {
    $sql_data_array['orders_adurl'] = $_SESSION['referer_adurl'];
  }
  
  $customers_type_info_raw = tep_db_query("select customers_guest_chk from " . TABLE_CUSTOMERS . " where customers_id = '" . $preorder_cus_id . "'");
  $customers_type_info_res = tep_db_fetch_array($customers_type_info_raw); 
  if ($customers_type_info_res) {
    if ($customers_type_info_res['customers_guest_chk'] == '1') {
      $sql_data_array['is_guest'] = '1';
    }
  } else {
    $sql_data_array['is_guest'] = '1';
  }
  
  $telecom_option_ok = $payment_modules->preorderDealUnknow($sql_data_array, $cpayment_code); 
  
  $insert_sql_data_array = $sql_data_array;

  //住所信息录入
  $add_list = array();
  foreach($_SESSION['preorder_information'] as $address_key=>$address_value){
    if(substr($address_key,0,3) == 'ad_'){
      $address_query = tep_db_query("select id,name,name_flag from ". TABLE_ADDRESS ." where name_flag='". substr($address_key,3) ."'");
      $address_array = tep_db_fetch_array($address_query);
      tep_db_free_result($address_query);
      $address_id = $address_array['id'];
      $add_list[] = array($address_array['name'],$address_value);
      $address_add_query = tep_db_query("insert into ". TABLE_ADDRESS_ORDERS ." value(NULL,'$orders_id',{$preorder_cus_id},$address_id,'{$address_array['name_flag']}','".addslashes($address_value)."','0')");
      tep_db_free_result($address_add_query);
    }
  }

  $address_show_array = array(); 
  $address_show_list_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_show_list_array = tep_db_fetch_array($address_show_list_query)){

    $address_show_array[$address_show_list_array['id']] = $address_show_list_array['name_flag'];
  }
  tep_db_free_result($address_show_list_query);
  $address_temp_str = '';
  foreach($_SESSION['preorder_information'] as $address_his_key=>$address_his_value){
    if(substr($address_his_key,0,3) == 'ad_'){
    
      if(in_array(substr($address_his_key,3),$address_show_array)){

         $address_temp_str .= $address_his_value;
      }

    } 
  }
  
  $address_error = false;
  $orders_id_temp = '';
  $address_sh_his_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id='$preorder_cus_id' group by orders_id");
  while($address_sh_his_array = tep_db_fetch_array($address_sh_his_query)){

    $address_sh_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where customers_id='$preorder_cus_id' and orders_id='". $address_sh_his_array['orders_id'] ."' order by id");
    $add_temp_str = '';
    while($address_sh_array = tep_db_fetch_array($address_sh_query)){
     
      if(in_array($address_sh_array['name'],$address_show_array)){

        $add_temp_str .= $address_sh_array['value'];
      }  
    }
    if($address_temp_str == $add_temp_str){

      $address_error = true;
      $orders_id_temp = $address_sh_his_array['orders_id'];
      break;
    }
    tep_db_free_result($address_sh_query);
  }
  tep_db_free_result($address_sh_his_query);
  //update address info
  if($address_error == true && $orders_id_temp != ''){

    tep_db_query("update ". TABLE_ADDRESS_HISTORY ." set orders_id='".$orders_id."' where orders_id='".$orders_id_temp."'"); 
  }
if($address_error == false && $customers_type_info_res['customers_guest_chk'] == '0'){
  if ($preorder['is_gray'] != '1') { 
    foreach($_SESSION['preorder_information'] as $address_history_key=>$address_history_value){
      if(substr($address_history_key,0,3) == 'ad_'){
        $address_history_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where name_flag='". substr($address_history_key,3) ."'");
        $address_history_array = tep_db_fetch_array($address_history_query);
        tep_db_free_result($address_history_query);
        $address_history_id = $address_history_array['id'];
        $address_history_add_query = tep_db_query("insert into ". TABLE_ADDRESS_HISTORY ." value(NULL,'$orders_id',{$preorder_cus_id},$address_history_id,'{$address_history_array['name_flag']}','".addslashes($address_history_value)."','0')");
        tep_db_free_result($address_history_add_query);
      }
    }
  }
}
  //获取是否开启了帐单邮寄地址功能
  $billing_address_show = get_configuration_by_site_id('BILLING_ADDRESS_SETTING',SITE_ID);
  $billing_address_show = $billing_address_show == '' ? get_configuration_by_site_id('BILLING_ADDRESS_SETTING',0) : $billing_address_show; 
  $billing_address_list_flag = false;
  if($billing_address_show == 'true' && $_SESSION['preorder_information']['preorders_billing_select'] == '1'){
    //把帐单邮寄地址的数据存入数据库
    $billing_address_list = array();
    $billing_address_list_flag = true;
    foreach($_SESSION['preorder_information'] as $address_key=>$address_value){
    if(substr($address_key,0,8) == 'billing_'){
      $address_query = tep_db_query("select id,name,name_flag from ". TABLE_ADDRESS ." where name_flag='". substr($address_key,8) ."'");
      $address_array = tep_db_fetch_array($address_query);
      tep_db_free_result($address_query);
      $address_id = $address_array['id'];
      $address_add_query = tep_db_query("insert into ". TABLE_ADDRESS_ORDERS ." value(NULL,'$orders_id',{$preorder_cus_id},$address_id,'{$address_array['name_flag']}','".addslashes($address_value)."','1')");
      tep_db_free_result($address_add_query);
      $billing_address_list[$address_id] = $address_value;
    }
  }     
  }
  //住所信息录入结束

  $preorder_total_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$_SESSION['preorder_info_id']."'");
  
  $totals_email_str = '';
  $totals_print_email_str = '';
  $totals_custom_array = array();
  $totals_info_array = array();
  while ($preorder_total_res = tep_db_fetch_array($preorder_total_raw)) {
    if ($preorder_total_res['class'] == 'ot_total') {
      //总价 
      if (isset($_SESSION['preorder_campaign_fee'])) {
        if (isset($option_info_array['total'])) {
          $preorder_total_num = $option_info_array['total'] + (int)$_SESSION['preorder_campaign_fee']+(int)$_SESSION['preorder_shipping_fee']; 
          $preorder_total_print_num = $option_info_array['total'] + (int)$_SESSION['preorder_campaign_fee']+(int)$_SESSION['preorder_shipping_fee']; 
        } else {
          $preorder_total_num = $preorder_total_res['value'] + (int)$_SESSION['preorder_campaign_fee']+(int)$_SESSION['preorder_shipping_fee']; 
          $preorder_total_print_num = $preorder_total_res['value'] + (int)$_SESSION['preorder_campaign_fee']+(int)$_SESSION['preorder_shipping_fee']; 
        }
      } else {
        if (isset($option_info_array['total'])) {
          $preorder_total_num = $option_info_array['total'] - (int)$preorder_point+(int)$_SESSION['preorder_shipping_fee']; 
          $preorder_total_print_num = $option_info_array['total'] - (int)$preorder_point+(int)$_SESSION['preorder_shipping_fee']; 
        } else {
          $preorder_total_num = $preorder_total_res['value'] - (int)$preorder_point+(int)$_SESSION['preorder_shipping_fee']; 
          $preorder_total_print_num = $preorder_total_res['value'] - (int)$preorder_point+(int)$_SESSION['preorder_shipping_fee']; 
        }
      }
    } else if ($preorder_total_res['class'] == 'ot_point') {
      //点数 
      $preorder_total_num = (int)$preorder_point; 
    } else if ($preorder_total_res['class'] == 'ot_subtotal') {
      //小计 
      if (isset($option_info_array['subtotal'])) {
        $preorder_total_num = $option_info_array['subtotal']; 
      } else {
        $preorder_total_num = $preorder_total_res['value']; 
      }
    } else {
      //其它 
      $preorder_total_num = $preorder_total_res['value']; 
    }
    
    $_SESSION['insert_id'] = $insert_id;
    if ($preorder_total_res['class'] == 'ot_total') {
      $preorder_total_num += (int)$_SESSION['preorders_code_fee'];
    }
    $sql_data_array = array('orders_id' => $orders_id,
                            'title' => $preorder_total_res['title'], 
                            'text' => $preorder_total_res['text'], 
                            'value' => $preorder_total_num, 
                            'class' => $preorder_total_res['class'], 
                            'sort_order' => $preorder_total_res['sort_order'], 
        ); 
    if ($preorder_total_res['class'] == 'ot_total') {
      if ($telecom_option_ok != true) {
        $telecom_option_ok = $payment_modules->getPreexpress((int)$preorder_total_num, $orders_id, $cpayment_code); 
      }
    }
    $totals_info_array[] = $sql_data_array;

    //total customer email
    if($preorder_total_res['class'] == 'ot_custom' && trim($preorder_total_res['title']) != ''){

      $totals_custom_array[] = array('title'=>$preorder_total_res['title'],'value'=>$preorder_total_res['value']);
    }
  }

  //检测订单ID是否重复 
  $success_flag = true; 
  $orders_query = tep_db_query("select orders_id from ".TABLE_ORDERS." where orders_id='".$orders_id."'");  
  if(tep_db_num_rows($orders_query) > 0){

    $success_flag = false;
  }

  if($success_flag == false){

    tep_redirect(tep_href_link('change_preorder_success.php', '', 'SSL'));
    exit;
  }
  tep_db_perform(TABLE_ORDERS, $insert_sql_data_array);
  if(isset($_SESSION['paypal_order_info'])&&is_array($_SESSION['paypal_order_info'])&&!empty($_SESSION['paypal_order_info'])){
    tep_db_perform(TABLE_ORDERS, $_SESSION['paypal_order_info'],'update', "orders_id='".$orders_id."'");
  }

  foreach($totals_info_array as $t_info_arr){
    tep_db_perform(TABLE_ORDERS_TOTAL, $t_info_arr);
  }
  $customer_i = 0;
  foreach($totals_custom_array as $totals_custom_value){

    if(count($totals_custom_array)-1 != $customer_i){
      $totals_email_str .= TEXT_ORDERS_CUSTOMER_STRING.$totals_custom_value['title'].'：'.$currencies->format($totals_custom_value['value'])."\n";
    }else{
      $totals_email_str .= TEXT_ORDERS_CUSTOMER_STRING.$totals_custom_value['title'].'：'.$currencies->format($totals_custom_value['value']); 
    }
    $customer_i++;
  }
  foreach($totals_custom_array as $totals_print_custom_value){

    $totals_print_email_str .= $totals_print_custom_value['title'].'：'.$currencies->format($totals_print_custom_value['value'])."\n";
  } 
  $order_comment_str = '';
  
  
  $order_comment_str = $payment_modules->get_preorder_add_info($cpayment_code, $preorder); 
  
  $customer_notification = (SEND_EMAILS == 'true') ? '1' : '0'; 
  $sql_data_array = array('orders_id' => $orders_id,
                          'orders_status_id' => $orders_status_id, 
                          'date_added' => date('Y-m-d H:i:s', time()), 
                          'customer_notified' => $customer_notification, 
                          'comments' => $order_comment_str,
                          'user_added' => $preorder['customers_name']
      ); 
  tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
 
  if ($telecom_option_ok) {
    orders_updated($orders_id);
  }
  $products_ordered_text = ''; 
  
  $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
  $preorder_product_res = tep_db_fetch_array($preorder_product_raw); 
  $search_products = tep_get_product_by_id($preorder_product_res['products_id'], 0, $languages_id,true,'product_info');
  $sql_data_array = array('orders_id' => $orders_id,
                          'products_id' => $preorder_product_res['products_id'],
                          'products_model' => $preorder_product_res['products_model'], 
                          'products_name' => $search_products['products_name'], 
                          'products_price' => $preorder_product_res['products_price'], 
                          'final_price' => (isset($option_info_array['final_price']))?$option_info_array['final_price']:$preorder_product_res['final_price'], 
                          'products_tax' => $preorder_product_res['products_tax'], 
                          'products_quantity' => $preorder_product_res['products_quantity'], 
                          'products_rate' => $preorder_product_res['products_rate'], 
                          'torihiki_date' => $torihikihouhou_date_str, 
                          'site_id' => SITE_ID
      );
  tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
  $order_products_id = tep_db_insert_id();

  
  $cl_max_len = 0; 
if (isset($_SESSION['preorder_option_info'])) {
  $cl_len_array = array();  
  foreach ($_SESSION['preorder_option_info'] as $cl_key => $cl_value) {
    $cl_key_info = explode('_', $cl_key);
    $cl_attr_query = tep_db_query("select front_title from ".TABLE_OPTION_ITEM." where name = '".$cl_key_info['1']."' and id = '".$cl_key_info[3]."'");
    $cl_attr_values = tep_db_fetch_array($cl_attr_query);
    if ($cl_attr_values) {
      $cl_len_array[] = mb_strlen($cl_attr_values['front_title'], 'utf-8'); 
    }
  }
  
  
}
$old_attr_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$_SESSION['preorder_info_id']."'");

while ($old_attr_res = tep_db_fetch_array($old_attr_raw)) {
  $old_attr_info = @unserialize($old_attr_res['option_info']); 
  $cl_len_array[] = mb_strlen($old_attr_info['title'], 'utf-8');
}

if (!empty($cl_len_array)) {
  $cl_max_len = max($cl_len_array); 
}

if($cl_max_len < 4) {
  $cl_max_len = 4;
}
  
  $show_products_name = tep_get_products_name($preorder_product_res['products_id']); 
  $products_ordered_text .= TEXT_ORDERS_PRODUCTS.str_repeat('　', intval(($cl_max_len-mb_strlen(TEXT_ORDERS_PRODUCTS,'utf-8')))).'：'.(tep_not_null($show_products_name) ? $show_products_name : $preorder_product_res['products_name']);
  if (tep_not_null($preorder_product_res['products_model'])) {
    $products_ordered_text .= ' ('.$preorder_product_res['products_model'].')'; 
  }
 
  if ($preorder_product_res['products_price'] != '0') {
    $products_ordered_text .= '('.$currencies->display_price($preorder_product_res['products_price'], $preorder_product_res['products_tax']).')'; 
  } else if ($preorder_product_res['final_price'] != '0') {
    $products_ordered_text .= '('.$currencies->display_price($preorder_product_res['final_price'], $preorder_product_res['products_tax']).')'; 
  }
  $products_ordered_atttibutes_text = '';

//option信息
$mold_attr_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$_SESSION['preorder_info_id']."'");
while ($mold_attr_res = tep_db_fetch_array($mold_attr_raw)) {
  $mold_attr_info = @unserialize($mold_attr_res['option_info']); 

  $sql_data_array = array('orders_id' => $orders_id,
                          'orders_products_id' => $order_products_id,
                          'options_values_price' => $mold_attr_res['options_values_price'],
                          'option_info' => tep_db_input(serialize($mold_attr_info)),
                          'option_group_id' => $mold_attr_res['option_group_id'],
                          'option_item_id' => $mold_attr_res['option_item_id'],
                          ); 
  tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);
  
  if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
    $sql_data_array = array('orders_id' => $orders_id, 
                            'orders_products_id' => $order_products_id, 
                            'orders_products_filename' => $attributes_values['products_attributes_filename'], 
                            'download_maxdays' => $attributes_values['products_attributes_maxdays'], 
                            'download_count' => $attributes_values['products_attributes_maxcount']);
    tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
  }

  $products_ordered_attributes .= "\n"
        .$mold_attr_info['title']
        .str_repeat('　', intval(($cl_max_len-mb_strlen($mold_attr_info['title'],'utf-8'))))
        .'：'.str_replace($replace_arr, "", $mold_attr_info['value']);
      if ($mold_attr_res['options_values_price'] != '0') {
        if ($preorder_product_res['products_price'] != '0') {
          $products_ordered_attributes .= '　('.$currencies->format($mold_attr_res['options_values_price']).')';
        } 
      }

}

if (isset($_SESSION['preorder_option_info'])) {
   //预约转正式时的option信息
   foreach ($_SESSION['preorder_option_info'] as $op_key => $op_value) {
      $op_key_info = explode('_', $op_key);
      $option_attr_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_key_info['1']."' and id = '".$op_key_info[3]."'");
      $option_attr_values = tep_db_fetch_array($option_attr_query);
      
      if ($option_attr_values) {

      $input_option_array = array('title' => $option_attr_values['front_title'], 'value' => str_replace(array("<BR>"), "<br>", $op_value));
      $ao_price = 0; 
      if ($option_attr_values['type'] == 'radio') {
         $ao_option_array = @unserialize($option_attr_values['option']);
         if (!empty($ao_option_array['radio_image'])) {
           foreach ($ao_option_array['radio_image'] as $or_key => $or_value) {
             if (trim(str_replace($replace_arr, '', nl2br(stripslashes($or_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_value))))) {
               $ao_price = $or_value['money']; 
               break; 
             }
           }
         } 
      } else if ($option_attr_values['type'] == 'textarea') {
        $to_option_array = @unserialize($option_attr_values['option']);
        $to_tmp_single = false; 
        if ($to_option_array['require'] == '0') {
          if ($op_value == MSG_TEXT_NULL) {
            $to_tmp_single = true; 
          }
        }
        if ($to_tmp_single) {
          $ao_price = 0; 
        } else {
          $ao_price = $option_attr_values['price']; 
        }
      } else {
        $ao_price = $option_attr_values['price']; 
      }
      $sql_data_array = array('orders_id' => $orders_id,
                              'orders_products_id' => $order_products_id,
                              'options_values_price' => $ao_price,
                              'option_info' => tep_db_input(serialize($input_option_array)),
                              'option_group_id' => $option_attr_values['group_id'],
                              'option_item_id' => $option_attr_values['id'],
                              ); 
      tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);
      
      if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
        $sql_data_array = array('orders_id' => $orders_id, 
                                'orders_products_id' => $order_products_id, 
                                'orders_products_filename' => $attributes_values['products_attributes_filename'], 
                                'download_maxdays' => $attributes_values['products_attributes_maxdays'], 
                                'download_count' => $attributes_values['products_attributes_maxcount']);
        tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
      }
      
      $products_ordered_attributes .= "\n"
        .$option_attr_values['front_title'] .str_repeat('　', intval(($cl_max_len-mb_strlen($option_attr_values['front_title'],'utf-8')))) .'：'.str_replace($replace_arr, "", $op_value);
      if ($ao_price != '0') {
        $products_ordered_attributes .= '　('.$currencies->format($ao_price).')';
      }
   }
   }
}

$preorder_oa_raw = tep_db_query("select * from ".TABLE_PREORDERS_OA_FORMVALUE." where orders_id = '".$_SESSION['preorder_info_id']."'");

while ($preorder_oa_res = tep_db_fetch_array($preorder_oa_raw)) {
   $sql_data_array = array('orders_id' => $orders_id,
                           'form_id' => $preorder_oa_res['form_id'], 
                           'item_id' => $preorder_oa_res['item_id'], 
                           'group_id' => $preorder_oa_res['group_id'], 
                           'name' => $preorder_oa_res['name'], 
                           'value' => $preorder_oa_res['value'], 
       );
    tep_db_perform(TABLE_OA_FORMVALUE, $sql_data_array);
 
}

$products_ordered_text .= $products_ordered_attributes;

$products_ordered_text .= "\n".TEXT_ORDERS_PRODUCTS_NUMBER.str_repeat('　', intval(($cl_max_len-mb_strlen(TEXT_ORDERS_PRODUCTS_NUMBER,'utf-8')))).'：' .  $preorder_product_res['products_quantity'] . NUM_UNIT_TEXT .  tep_get_full_count2($preorder_product_res['products_quantity'], $preorder_product_res['products_id'])."\n";
$products_ordered_text .= TEXT_ORDERS_PRODUCTS_PRICE.str_repeat('　', intval(($cl_max_len-mb_strlen(TEXT_ORDERS_PRODUCTS_PRICE,'utf-8')))).'：' .  $currencies->display_price(isset($option_info_array['final_price'])?$option_info_array['final_price']:$preorder_product_res['final_price'], $preorder_product_res['products_tax']) . "\n";
$products_ordered_text .= TEXT_ORDERS_PRODUCTS_SUBTOTAL.str_repeat('　', intval(($cl_max_len-mb_strlen(TEXT_ORDERS_PRODUCTS_SUBTOTAL,'utf-8')))).'：' .  $currencies->display_price(isset($option_info_array['final_price'])?$option_info_array['final_price']:$preorder_product_res['final_price'], $preorder_product_res['products_tax'], $preorder_product_res['products_quantity']) . "\n";


$products_ordered_text .= TEXT_ORDERS_PRODUCTS_LINE;
if (tep_get_cflag_by_product_id($preorder_prodct_res['products_id'])) {
  if (tep_get_bflag_by_product_id($preorder_prodct_res['products_id'])) {
    $products_ordered_text .= TEXT_ORDERS_PRODUCTS_ORDERED;
  } else {
    $products_ordered_text .= TEXT_ORDERS_PRODUCTS_ORDERED_TEXT;
  }
}

$mailoption['ORDER_NUMBER']         = $orders_id;
$mailoption['ORDER_DATE']       = tep_date_long(time())  ;
$mailoption['USER_NAME']        = $preorder['customers_name'];
$mailoption['USER_MAIL'] = $preorder['customers_email_address'];
if($totals_email_str != ''){
  $mailoption['CUSTOMIZED_FEE'] = $totals_email_str;
}
$shipping_fee_value = !empty($_SESSION['preorder_shipping_fee']) ? $_SESSION['preorder_shipping_fee'] : 0; 
$mailoption['SHIPPING_FEE']      = str_replace(JPMONEY_UNIT_TEXT,'',$currencies->format(abs($shipping_fee_value)));
$mailoption['ORDER_TOTAL']      = str_replace(JPMONEY_UNIT_TEXT,'',$currencies->format(abs($preorder_total_print_num+$_SESSION['preorders_code_fee'])));

$mailoption['TORIHIKIHOUHOU']   = $_SESSION['preorder_info_tori'];
$mailoption['PAYMENT']    = $preorder['payment_method'];
$mailoption['SHIPPING_TIME']      =  str_string($_SESSION['preorder_info_date']) .  $_SESSION['preorder_info_start_hour'] . TIME_HOUR_TEXT . $_SESSION['preorder_info_start_min'] .  TEXT_ORDERS_PRODUCTS_LINK. $_SESSION['preorder_info_end_hour'].TIME_HOUR_TEXT. $_SESSION['preorder_info_end_min'].TEXT_ORDERS_PRODUCTS_TWENTY_HOUR;

$mailoption['EXTRA_COMMENT']   = '';
$mailoption['ORDER_PRODUCTS']   = $products_ordered_text;
$mailoption['SHIPPING_METHOD']    = $torihikihouhou_date_str;
$mailoption['SITE_NAME']        = STORE_NAME;
$mailoption['SITE_MAIL']        = SUPPORT_EMAIL_ADDRESS;
$mailoption['SITE_URL']         = HTTP_SERVER;

$payment_modules->preorder_deal_mailoption($mailoption, $cpayment_code, $preorder); 


$mailoption['ORDER_COUNT'] = $preorder_product_res['products_quantity'];
$mailoption['ORDER_LTOTAL'] = number_format((isset($option_info_array['final_price'])?$option_info_array['final_price']:$preorder_product_res['final_price'])*$preorder_product_res['products_quantity'], 0, '.', '');

$mailoption['ORDER_ACTORNAME'] = '';
if ($preorder_point){
  $mailoption['POINT']            = str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format(abs($preorder_point)));
}else {
    $mailoption['POINT']            = 0;
}

if (isset($_SESSION['preorder_campaign_fee'])) {
  $mailoption['POINT']          = str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format(abs($_SESSION['preorder_campaign_fee'])));
}

if (!empty($_SESSION['preorders_code_fee'])) {
  $mailoption['MAILFEE']          = str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format(isset($option_info_array['fee'])?abs($option_info_array['fee']):abs($_SESSION['preorders_code_fee'])));
} else {
  $mailoption['MAILFEE']          = '0';
}

$email_order_text = '';

$mailoption['ORDER_COMMENT']    = trim($preorder['comment_msg']);
$mailoption['ADD_INFO'] = trim($_SESSION['preorder_payment_info']);

$email_order_text = $payment_modules->getOrderMailString($cpayment_code, $mailoption); 

if(!empty($add_list)){
  $address_len_array = array();
  foreach($add_list as $address_value){

    $address_len_array[] = strlen($address_value[0]);
  }
  $maxlen = max($address_len_array);
  $email_address_str = "";
  $email_address_str .= TEXT_ORDERS_PRODUCTS_LINE;
  $maxlen = 9;
  foreach($add_list as $ad_value){
    $ad_len = mb_strlen($ad_value[0],'utf8');
    $temp_str = str_repeat('　',$maxlen-$ad_len);
    if(trim($ad_value[0]) != '' && trim($ad_value[1]) != ''){
      $email_address_str .= $ad_value[0].$temp_str.'：'.$ad_value[1]."\n";
    }
  }
  $email_address_str .= TEXT_ORDERS_PRODUCTS_LINE;
  $email_order_text = str_replace('${USER_ADDRESS}',$email_address_str,$email_order_text);
}else{
  $email_order_text = str_replace("\n".'${USER_ADDRESS}','',$email_order_text);
  $email_order_text = str_replace('${USER_ADDRESS}','',$email_order_text);
  $email_order_text = str_replace("\n".TEXT_ORDERS_PRODUCTS_ADDRESS_INFO,'',$email_order_text);
}
if($totals_email_str == ''){
  $email_order_text = str_replace("\n".'${CUSTOMIZED_FEE}','',$email_order_text);
  $email_order_text = str_replace('${CUSTOMIZED_FEE}','',$email_order_text);
}

//帐单邮寄地址
$address_list_query = tep_db_query("select id,name from ". TABLE_ADDRESS ." where status='0' order by sort");
$address_array = array();
while($address_list_array = tep_db_fetch_array($address_list_query)){

  $address_array[$address_list_array['id']] = $address_list_array['name'];
}
tep_db_free_result($address_list_query);
if($billing_address_show == 'true' && $billing_address_list_flag == true){

  $billing_address_len_array = array();
  foreach($billing_address_list as $billing_address_key=>$billing_address_value){

    $billing_address_len_array[] = strlen($address_array[$billing_address_key]);
  }
  $maxlen = max($billing_address_len_array);
  $email_billing_address_str = "";
  $email_billing_address_str .= TEXT_ORDERS_PRODUCTS_LINE;
  $maxlen = 9;
  foreach($billing_address_list as $billing_key=>$billing_value){
    $billing_len = mb_strlen($address_array[$billing_key],'utf8');
    $temp_str = str_repeat('　',$maxlen-$billing_len);
    if(trim($address_array[$billing_key]) != '' && trim($billing_value) != ''){
      $email_billing_address_str .= $address_array[$billing_key].$temp_str.'：'.$billing_value."\n";
    }
  }
  $email_billing_address_str .= TEXT_ORDERS_PRODUCTS_LINE;
  $email_order_text = str_replace('${BILLING_ADDRESS}',$email_billing_address_str,$email_order_text);
}else{

  $email_order_text = str_replace("\n".'${BILLING_ADDRESS}','',$email_order_text); 
  $email_order_text = str_replace('${BILLING_ADDRESS}','',$email_order_text);
  $email_order_text = str_replace("\n".TEXT_ORDERS_CUSTOMER_STRING.TEXT_BILLING_ADDRESS,'',$email_order_text);
}
$email_order_text = tep_replace_mail_templates($email_order_text,$preorder['customers_email_address'],$preorder['customers_name']);
//订单邮件
$orders_mail_templates = tep_get_mail_templates('MODULE_PAYMENT_'.strtoupper($cpayment_code).'_MAILSTRING',SITE_ID);
$subject = $orders_mail_templates['title'];
$title_mode_array = array(
                             '${SITE_NAME}' 
                           );
$title_replace_array = array(
                             STORE_NAME 
                           );
$subject = str_replace($title_mode_array,$title_replace_array,$subject);
if ($seal_user_row['is_send_mail'] != '1') {
  //是否给该顾客发送邮件 
  tep_mail($preorder['customers_name'], $preorder['customers_email_address'], $subject, $email_order_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '');
}
  
if (SENTMAIL_ADDRESS != '') {
  //给管理者发送邮件   
  tep_mail('', SENTMAIL_ADDRESS, $subject, $email_order_text, $preorder['customers_name'], $preorder['customers_email_address'], '');
}

//打印邮件
$orders_print_mail_templates = tep_get_mail_templates('MODULE_PAYMENT_'.strtoupper($cpayment_code).'_PRINT_MAILSTRING',SITE_ID);
$payment_mode = array(
                        '${USER_NAME}',
                        '${SITE_NAME}',
                        '${YEAR}',
                        '${ORDER_NUMBER}',
                        '${ORDER_DATE}',
                        '${USER_MAIL}',
                        '${BANK_FOR_TRANSFER}',
                        '${POINT}',
                        '${SHIPPING_FEE}',
                        '${COMMISSION}',
                        '${ORDER_TOTAL}',
                        '${ORDER_PRODUCTS}',
                        '${SHIPPING_TIME}',
                        '${ORDER_COMMENT}',
                        '${ADD_INFO}',
                        '${USER_INFO}',
                        '${CREDIT_RESEARCH}',
                        '${ORDER_HISTORY}',
                        '${TOTAL}',
                        '${SHIPPING_METHOD}'
                      );
if (isset($_SESSION['preorder_campaign_fee'])) {
  if (abs($_SESSION['preorder_campaign_fee']) > 0) {
      $print_point = abs($_SESSION['preorder_campaign_fee']);
  }
} else {
  if ($preorder_point > 0) {
      $print_point = (int)$preorder_point;
  }else{
      $print_point = 0;
  }
}

if (!empty($option_info_array['fee'])) {
  $print_handle_fee = $option_info_array['fee'];
} else {
  if (!empty($_SESSION['preorders_code_fee'])) {
    $print_handle_fee = $_SESSION['preorders_code_fee'];
  }
}
//customer info
$customer_printing_order .= SENDMAIL_TEXT_IP_ADDRESS . $_SERVER["REMOTE_ADDR"] . "\n";
$customer_printing_order .= SENDMAIL_TEXT_HOST . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
$customer_printing_order .= SENDMAIL_TEXT_USER_AGENT . $_SERVER["HTTP_USER_AGENT"] . "\n";

//credit inquiry
$credit_inquiry_query = tep_db_query("select customers_fax, customers_guest_chk from " . TABLE_CUSTOMERS . " where customers_id = '" . $preorder_cus_id . "'");
$credit_inquiry       = tep_db_fetch_array($credit_inquiry_query);

//orders history
$email_orders_history = "";
if ($credit_inquiry['customers_guest_chk'] == '1') { 
  $email_orders_history .= TABLE_HEADING_MEMBER_TYPE_GUEST."\n"; 
} else { 
  $email_orders_history .= TEXT_MEMBER."\n"; 
}
     
  $order_history_query_raw = "select o.orders_id, o.customers_name, o.customers_id, o.date_purchased, s.orders_status_name, ot.value as order_total_value from " .  TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" .  tep_db_input($preorder_cus_id) . "' and o.orders_status = s.orders_status_id and s.language_id = '" . $languages_id . "' and ot.class = 'ot_total' order by o.date_purchased DESC limit 0,5";  
    $order_history_query = tep_db_query($order_history_query_raw);
    while ($order_history = tep_db_fetch_array($order_history_query)) {
        $email_orders_history .= $order_history['date_purchased'] . '　　' .  tep_output_string_protected($order_history['customers_name']) . '　　' .  $currencies->format(abs(intval($order_history['order_total_value']))) . '　　' .  $order_history['orders_status_name'] . "\n";
    }
$payment_replace = array(
                        $preorder['customers_name'],
                        STORE_NAME, 
                        date('Y'),
                        $orders_id,  
                        tep_date_long(time()),
                        $preorder['customers_email_address'],
                        $_SESSION['preorder_payment_info'],
                        $print_point,
                        $shipping_fee_value,
                        $print_handle_fee,
                        str_replace(JPMONEY_UNIT_TEXT,"",$currencies->format(abs($preorder_total_print_num+$_SESSION['preorders_code_fee']))),
                        $products_ordered_text,
                        tep_date_long($_SESSION['preorder_info_date']) . $_SESSION['preorder_info_start_hour'] . SENDMAIL_TEXT_HOUR . $_SESSION['preorder_info_start_min'] . SENDMAIL_TEXT_MIN.SENDMAIL_TEXT_TIME_LINK. $_SESSION['preorder_info_end_hour'] .SENDMAIL_TEXT_HOUR. $_SESSION['preorder_info_end_min'] .SENDMAIL_TEXT_MIN.SENDMAIL_TEXT_TWENTY_FOUR_HOUR,
                        strpos($orders_print_mail_templates['contents'],'${BANK_FOR_TRANSFER}') == true ? $preorder['comment_msg'] : $order_comment_str,
                        '',
                        $customer_printing_order,
                        $credit_inquiry['customers_fax'],
                        $email_orders_history,
                        abs($preorder_total_print_num+$_SESSION['preorders_code_fee']),
                        ''
                      );
$email_printing_order = str_replace($payment_mode,$payment_replace,$orders_print_mail_templates['contents']);
//自定义费用
if($totals_email_str == ''){
  $email_printing_order = str_replace("\n".'${CUSTOMIZED_FEE}','',$email_printing_order);
  $email_printing_order = str_replace('${CUSTOMIZED_FEE}','',$email_printing_order);
}else{
  $email_printing_order = str_replace('${CUSTOMIZED_FEE}',str_replace(TEXT_ORDERS_CUSTOMER_STRING,'',$totals_email_str),$email_printing_order);
}
//住所
if($email_address_str != ''){
  $email_printing_order = str_replace('${USER_ADDRESS}',str_replace(TEXT_ORDERS_CUSTOMER_STRING,'',$email_address_str),$email_printing_order);
}else{
  $email_printing_order = str_replace("\n".'${USER_ADDRESS}','',$email_printing_order);
  $email_printing_order = str_replace('${USER_ADDRESS}','',$email_printing_order);
  $email_printing_order = str_replace("\n".str_replace(TEXT_ORDERS_CUSTOMER_STRING,'',TEXT_ORDERS_PRODUCTS_ADDRESS_INFO),'',$email_printing_order);
}
//帐单邮寄地址
if($email_billing_address_str != ''){
  $email_printing_order = str_replace('${BILLING_ADDRESS}',str_replace(TEXT_ORDERS_CUSTOMER_STRING,'',$email_billing_address_str),$email_printing_order);
}else{
  $email_printing_order = str_replace("\n".'${BILLING_ADDRESS}','',$email_printing_order);
  $email_printing_order = str_replace('${BILLING_ADDRESS}','',$email_printing_order);
  $email_printing_order = str_replace("\n".TEXT_BILLING_ADDRESS,'',$email_printing_order);
}

$email_printing_order = tep_replace_mail_templates($email_printing_order,$preorder['customers_email_address'],$preorder['customers_name']);
if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
  //发送打印邮件 
  tep_mail('', PRINT_EMAIL_ADDRESS, str_replace('${SITE_NAME}',STORE_NAME,$orders_print_mail_templates['title']), $email_printing_order, $preorder['customers_name'], $preorder['customers_email_address'], '');
}

$check_status_info = $payment_modules->check_insert_status_history($cpayment_code, $_SESSION['preorder_option'], $orders_id);
if (!empty($check_status_info)) {
  $sql_data_array = array('orders_id' => $orders_id, 
                        'orders_status_id' => $orders_status_id, 
                        'date_added' => $check_status_info[0], 
                        'customer_notified' => '0',
                        'comments' => $check_status_info[1],
                        'user_added' => $check_status_info[2]
                        );
  tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
  $last_order_history_raw = tep_db_query("select * from ".TABLE_ORDERS_STATUS_HISTORY." where date_added > '".$check_status_info[0]."' and orders_id = '".$orders_id."'");
  if (!tep_db_num_rows($last_order_history_raw)) {
    $order_status_info_raw = tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$orders_status_id."'");    
    $order_status_info_res = tep_db_fetch_array($order_status_info_raw); 
    tep_db_query("update ".TABLE_ORDERS." set orders_status = '".$orders_status_id."', orders_status_name = '".$order_status_info_res['orders_status_name']."' where orders_id = '".$orders_id."' and site_id = '".SITE_ID."'"); 
    orders_updated($orders_id);
  }
}

if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  if(MODULE_ORDER_TOTAL_POINT_ADD_STATUS == '0') {
    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " .  intval($preorder_get_point - $preorder_point) . " where customers_id = " . $preorder_cus_id );
  } else {
    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point - " .  intval($preorder_point) . " where customers_id = " . $preorder_cus_id );
  }
}

$link_customer_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$preorder_cus_id."' and site_id = '".SITE_ID."'");
$link_customer_res = tep_db_fetch_array($link_customer_raw);

if ($link_customer_res) {
  if ($link_customer_res['customers_guest_chk'] == '1') {
    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = '0' where customers_id = " . $preorder_cus_id );
  }
}

if (isset($_SESSION['preorder_campaign_fee'])) {
  $campaign_raw = tep_db_query("select * from ".TABLE_CAMPAIGN." where id = '".$_SESSION['preorder_camp_id']."' and (site_id = '".SITE_ID."' or site_id = '0')"); 
  $campaign = tep_db_fetch_array($campaign_raw); 
  $sql_data_array = array(
      'customer_id' => $preorder_cus_id,
      'campaign_id' => $_SESSION['preorder_camp_id'],
      'orders_id' => $orders_id,
      'campaign_fee' => $_SESSION['preorder_campaign_fee'],
      'campaign_title' => $campaign['title'],
      'campaign_name' => $campaign['name'],
      'campaign_keyword' => $campaign['keyword'],
      'campaign_start_date' => $campaign['start_date'],
      'campaign_end_date' => $campaign['end_date'],
      'campaign_max_use' => $campaign['max_use'],
      'campaign_point_value' => $campaign['point_value'],
      'campaign_limit_value' => $campaign['limit_value'],
      'campaign_type' => $campaign['type'],
      'site_id' => SITE_ID
      );
  tep_db_perform(TABLE_CUSTOMER_TO_CAMPAIGN, $sql_data_array);
}
//预约转正式之后删除该预约订单的所有信息
tep_db_query("delete from ".TABLE_PREORDERS." where orders_id = '".$_SESSION['preorder_info_id']."' and site_id = '".SITE_ID."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_DOWNLOAD." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_TO_ACTOR." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_QUESTIONS." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_QUESTIONS_PRODUCTS." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_STATUS_HISTORY." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
//把预约订单的按钮信息，关联到新生成的订单
$preorders_buttons_query = tep_db_query("select * from ".TABLE_PREORDERS_TO_BUTTONS." where orders_id = '".$_SESSION['preorder_info_id']."'");
while($preorders_buttons_array = tep_db_fetch_array($preorders_buttons_query)){

  tep_db_query("insert into ".TABLE_ORDERS_TO_BUTTONS." values('".$orders_id."','".$preorders_buttons_array['buttons_id']."')");
}
tep_db_free_result($preorders_buttons_query);
tep_db_query("delete from ".TABLE_PREORDERS_TO_BUTTONS." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_OA_FORMVALUE." where orders_id = '".$_SESSION['preorder_info_id']."'"); 

last_customer_action();

}



tep_session_unregister('preorder_info_tori');
tep_session_unregister('preorder_info_date');
tep_session_unregister('preorder_info_hour');
tep_session_unregister('preorder_info_min');
tep_session_unregister('preorder_info_id');
tep_session_unregister('preorder_info_pay');
tep_session_unregister('preorder_option_info');
tep_session_unregister('preorder_information');
tep_session_unregister('preorder_shipping_fee');
if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  tep_session_unregister('preorder_point');
  tep_session_unregister('preorder_real_point');
  tep_session_unregister('preorder_get_point');
}
$_SESSION['customer_id'] = $preorder['customers_id'];

unset($_SESSION['insert_id']);
unset($_SESSION['preorder_option']);
unset($_SESSION['referer_adurl']);
if(isset($_SESSION['paypal_order_info'])){
  unset($_SESSION['paypal_order_info']);
}

unset($_SESSION['preorder_campaign_fee']);
unset($_SESSION['preorder_camp_id']);
unset($_SESSION['preorders_code_fee']);
unset($_SESSION['preorder_payment_info']);

if(!isset($_SESSION['preorder_credit_flag'])){
  tep_redirect(tep_href_link('change_preorder_success.php', '', 'SSL'));
}







