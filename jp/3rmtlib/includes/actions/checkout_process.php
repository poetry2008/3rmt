<?php
/*
  $Id$
 */
header("Content-type:text/html;charset=utf-8");
ini_set("display_errors","Off");
require(DIR_WS_FUNCTIONS . 'visites.php');
// load selected payment module
require_once(DIR_WS_CLASSES . 'payment.php');
//如果信用卡支付成功并生成了订单，直接跳转到注文成功页面
if(isset($_SESSION['orders_credit_flag']) && $_SESSION['orders_credit_flag'] == '1'){
  unset($_SESSION['orders_credit_flag']);
  tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS,'','SSL'),'T');
  exit;
}  
//当购物车为空时，跳转到购物车页面
if ($cart->count_contents(true) < 1) {
  tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
}
if(isset($real_point)){
// user new point value it from checkout_confirmation.php 
  $point = $real_point;
}
$customer_error = false;
if (!tep_session_is_registered('customer_id')) {
// if the customer is not logged on, redirect them to the login page
  $navigation->set_snapshot(array('mode' => 'SSL', 'page' => FILENAME_CHECKOUT_PAYMENT));
  tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  exit;
} else {
  if(tep_session_is_registered('customer_id')){
    $flag_customer_info = tep_is_customer_by_id($customer_id);
    if(!$flag_customer_info ||
        strtolower($flag_customer_info['customers_email_address']) != strtolower($_SESSION['customer_emailaddress'])){
      $customer_error = true;
    }
  }
}
if(!isset($_SESSION['cart']) || !isset($_SESSION['date']) || !isset($_SESSION['hour']) || !isset($_SESSION['min'])||$customer_error){
//判断购物车信息或者配送时间信息丢失就弹出错误页面
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
  $orders_mail_title = $error_mail_array['title'].'　'.date('Y-m-d H:i:s');
  $orders_mail_text = $error_mail_array['contents'];
  $orders_mail_text = str_replace('${ERROR_NUMBER}','001',$orders_mail_text);
  $orders_mail_text = str_replace('${ERROR_TIME}',date('Y-m-d H:i:s'),$orders_mail_text); 

  $orders_error_contents = "\n\n";
  $orders_error_contents .= ORDERS_SITE." ".STORE_NAME."\n";
  $orders_error_contents .= ORDERS_TIME." ".$_SESSION['insert_torihiki_date']."\n";
  $orders_error_contents .= CREATE_ORDERS_DATE." ".date('Y-m-d H:i:s')."\n";
  $customer_query = tep_db_query("select customers_guest_chk from " . TABLE_CUSTOMERS . " where customers_id = '" . $_SESSION['customer_id'] . "'");
  $customer_array = tep_db_fetch_array($customer_query);
  tep_db_free_result($customer_query);
  $customer_type = $customer_array['customers_guest_chk'] == 1 ? TABLE_HEADING_MEMBER_TYPE_GUEST : TEXT_MEMBER;
  $orders_error_contents .= CUSTOMER_TYPE." ".$customer_type."\n";
  $customer_name = tep_get_fullname($_SESSION['customer_first_name'],$_SESSION['customer_last_name']);
  $orders_error_contents .= CUSTOMER_NAME." ".$customer_name."\n";
  $orders_error_contents .= ORDERS_EMAIL." ".$_SESSION['customer_emailaddress']."\n";
  //total
  $orders_total = $cart->total;
  //point
  if ($point){
    $orders_error_contents .= TEXT_ORDERS_PRODUCTS_POINT." ".$currencies->format(abs($point))."\n";
    $orders_total -= abs($point);
  }
  if (isset($_SESSION['campaign_fee'])) {
    $orders_error_contents .= TEXT_ORDERS_PRODUCTS_POINT." ".$currencies->format(abs($_SESSION['campaign_fee']))."\n";
    $orders_total -= abs($_SESSION['campaign_fee']);
  }
  //handle code
  if(!isset($_SESSION['mailfee'])){
    $orders_error_contents .= TEXT_ORDERS_PRODUCTS_HANDLE_FEE." 0".JPMONEY_UNIT_TEXT."\n"; 
  }else{
    $orders_error_contents .= TEXT_ORDERS_PRODUCTS_HANDLE_FEE." ".$_SESSION['mailfee']."\n";
    $orders_total += abs($_SESSION['mailfee']);
  }
  //orders subtotal
  $orders_error_contents .= TEXT_ORDERS_PRODUCTS_SUBTOTAL.": ".$currencies->format($cart->total)."\n";
  //orders total
  $orders_error_contents .= TEXT_ORDERS_PRODUCTS_TOTAL." ".$currencies->format($orders_total)."\n";
  //earn points
  $orders_error_contents .= TEXT_ORDERS_EARN_POINTS." ".str_replace(JPMONEY_UNIT_TEXT,'',$currencies->format($_SESSION['get_point']))." P\n";  
  $orders_payment = $_SESSION['payment'];
  $orders_payment = payment::changeRomaji($_SESSION['payment'], PAYMENT_RETURN_TYPE_TITLE);
  $orders_error_contents .= ORDERS_PAYMENT." ".$orders_payment."\n";
  //orders products list
  $products_ordered = '';
  $total_tax = 0;

  //获取订单相关信息
  require(DIR_WS_CLASSES . 'order.php');
  $order = new order;
  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {

    //products attributes
    $attributes_exist = '0';
    $products_ordered_attributes = '';
    $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>"); 
     
    if (!empty($order->products[$i]['op_attributes'])) {
      //商品的option信息 
      $attributes_exist = '1';
     
      foreach ($order->products[$i]['op_attributes'] as $op_key => $op_value) {
       
      
        $input_option_array = array('title' => $op_value['front_title'], 'value' => $op_value['value']);
        $option_item_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$op_value['item_id']."'"); 
        $option_item_res = tep_db_fetch_array($option_item_raw); 
        $op_price = 0; 
      
        if ($option_item_res) {
          if ($option_item_res['type'] == 'radio') {
             $ao_option_array = @unserialize($option_item_res['option']);
             if (!empty($ao_option_array['radio_image'])) {
               foreach ($ao_option_array['radio_image'] as $or_key => $or_value) {
                 if (trim(str_replace($replace_arr, '', nl2br(stripslashes($or_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_value['value']))))) {
                   $op_price = $or_value['money']; 
                   break; 
                 }
               }
             } 
          } else if ($option_item_res['type'] == 'textarea') {
            $to_option_array = @unserialize($option_item_res['option']);
            $tmp_to_single = false; 
            if ($to_option_array['require'] == '0') {
              if ($op_value['value'] == MSG_TEXT_NULL) {
                $tmp_to_single = true; 
              }
            }
            if ($tmp_to_single) {
              $op_price = 0; 
            } else {
              $op_price = $option_item_res['price']; 
            }
          } else {
            $op_price = $option_item_res['price']; 
          }
        } else {
          $op_price = $op_value['price']; 
        }
       
        $products_ordered_attributes .= "\n" 
          . $op_value['front_title'] 
          . ': ' . str_replace($replace_arr, "", $op_value['value']);
      
        if ($op_price != '0') {
          $products_ordered_attributes .= '　('.$currencies->format($op_price).')'; 
        }
      }
    }
  
    if (!empty($order->products[$i]['ck_attributes'])) {
      //登录后选择商品的option信息 
      foreach ($order->products[$i]['ck_attributes'] as $ck_key => $ck_value) {
        $input_option_array = array('title' => $ck_value['front_title'], 'value' => $ck_value['value']);
      
        $coption_item_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$ck_value['item_id']."'"); 
        $coption_item_res = tep_db_fetch_array($coption_item_raw); 
        $c_op_price = 0; 
      
        if ($coption_item_res) {
          if ($coption_item_res['type'] == 'radio') {
            $aco_option_array = @unserialize($coption_item_res['option']);
            if (!empty($aco_option_array['radio_image'])) {
              foreach ($aco_option_array['radio_image'] as $cor_key => $cor_value) {
                if (trim(str_replace($replace_arr, '', nl2br(stripslashes($cor_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($ck_value['value']))))) {
                 $c_op_price = $cor_value['money']; 
                 break; 
                }
              }
            } 
          } else if ($coption_item_res['type'] == 'textarea') {
            $aco_option_array = @unserialize($coption_item_res['option']);
            $tco_tmp_single = false;
            if ($aco_option_array['require'] == '0') {
              if ($ck_value['value'] == MSG_TEXT_NULL) {
                $tco_tmp_single = true;
              }
            }
            if ($tco_tmp_single) {
              $c_op_price = 0; 
            } else {
              $c_op_price = $coption_item_res['price']; 
            }
          } else {
            $c_op_price = $coption_item_res['price']; 
          }
        } else {
          $c_op_price = $ck_value['price']; 
        }
       
        $products_ordered_attributes .= "\n" 
          . $ck_value['front_title'] 
          . ': ' . str_replace($replace_arr, "", $ck_value['value']);
      
        if ($c_op_price != '0') {
          $products_ordered_attributes .= '　('.$currencies->format($c_op_price).')'; 
        }
      }
    }
   
    $total_weight += ($order->products[$i]['qty'] * $order->products[$i]['weight']);
    $total_tax += tep_calculate_tax($total_products_price, $products_tax) * $order->products[$i]['qty'];
    $total_cost += $total_products_price;

    $products_ordered .= TEXT_ORDERS_PRODUCTS.': ' . $order->products[$i]['name'];
    if(tep_not_null($order->products[$i]['model'])) {
      $products_ordered .= ' (' . $order->products[$i]['model'] . ')';
    }
    if ($order->products[$i]['price'] != '0') {
      $products_ordered .= ' ('.$currencies->display_price($order->products[$i]['price'], $order->products[$i]['tax']).')'; 
    }
    $products_ordered .= $products_ordered_attributes . "\n";
    $products_ordered .= TEXT_ORDERS_PRODUCTS_NUMBER.': ' . $order->products[$i]['qty'] . NUM_UNIT_TEXT .  tep_get_full_count2($order->products[$i]['qty'], (int)$order->products[$i]['id']) . "\n";
    $products_ordered .= TEXT_ORDERS_PRODUCTS_PRICE.': ' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax']) . "\n";
    $products_ordered .= TEXT_ORDERS_PRODUCTS_SUBTOTAL.': ' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . "\n";
    $products_ordered .= TEXT_ORDERS_PRODUCTS_LINE; 
  } 
  //orders products
  $orders_error_contents .= TEXT_ORDERS_PRODUCTS_LINE.$products_ordered;
  //orders comments
  $orders_error_contents .= TEXT_ORDERS_COMMENTS.' '.$_SESSION['comments']."\n";
  //referer info
  $orders_error_contents .= 'Referer Info'.": ".$_SESSION['referer']."\n";
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
  $orders_mail_text = tep_replace_mail_templates($orders_mail_text,$_SESSION['customer_emailaddress'],tep_get_fullname($_SESSION['customer_first_name'],$_SESSION['customer_last_name']));
 
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
  if(!empty($_SESSION['cart'])){
    $orders_mail_text .= "\n-----------------cart-------------\n";
    $orders_mail_text .= arr_foreach($_SESSION['cart']);
  }else{
    $orders_mail_text .= "\n cart is null\n";
  }
  $text = $orders_mail_text;  
  $message->add_html(nl2br($orders_mail_text), $text);
  $message->build_message();
  //Administrator
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

  //注销其他session变量
  unset($_SESSION['insert_id']);
  unset($_SESSION['option_list']);
  unset($_SESSION['campaign_fee']); 
  unset($_SESSION['camp_id']); 
  unset($_SESSION['new_payment_error']);
  unset($_SESSION['comments']);
  unset($_SESSION['payment_validated']);
  unset($_SESSION['mailcomments']);
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
  unset($_SESSION['billing_select']);
  unset($_SESSION['billing_options']);
  unset($_SESSION['billing_address_option']);
  unset($_SESSION['billing_address_show_list']);

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
  $_SESSION['error_name'] = $customer_name;
  $_SESSION['error_email'] = $customer_email; 
  $_SESSION['error_subject'] = $orders_mail_title; 
  $_SESSION['error_message'] = strip_tags($orders_mail_text); 
  if(isset($_SESSION['orders_credit_flag'])){
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
$.ajax({
  url: '<?php echo isset($_SESSION['orders_credit_flag']) ? '../ajax_confirm_session_error.php?action=session' : 'ajax_confirm_session_error.php?action=session';?>',
  data: '',
  type: 'POST',
  dataType: 'text',
  async : false,
  success: function(data){ 
  }
});
$(document).ready(function() {
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
<a href="javascript:void(0);" onClick="update_notice('index.php');"><img alt="<?php echo LOCATION_HREF_INDEX;?>" src="<?php echo isset($_SESSION['orders_credit_flag']) ? '../images/design/href_home.gif' : 'images/design/href_home.gif';?>"></a>&nbsp;&nbsp;
<a href="javascript:void(0);" onClick="update_notice('contact_us.php');"><img alt="<?php echo CONTACT_US;?>" src="<?php echo isset($_SESSION['orders_credit_flag']) ? '../images/design/contact_us.gif' : 'images/design/contact_us.gif';?>"></a>
</div>
</div>

<?php
  unset($_SESSION['orders_credit_flag']);
  exit;
}
$seal_user_sql = "select is_seal from ".TABLE_CUSTOMERS." where customers_id
='".$customer_id."' limit 1";
$seal_user_query = tep_db_query($seal_user_sql);
if ($seal_user_row = tep_db_fetch_array($seal_user_query)){
  if($seal_user_row['is_seal']){
    //判断该顾客是否能下订单 
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_CONFIRMATION, 'is_finish=1', 'SSL')); 
    exit;
  }
}
if ((tep_not_null(MODULE_PAYMENT_INSTALLED)) && (!tep_session_is_registered('payment')) ) {
  tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL')); 
  exit;
}
if (isset($cart->cartID) && tep_session_is_registered('cartID')) {
// avoid hack attempts during the checkout procedure by checking the internal cartID
  if ($cart->cartID != $cartID) {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    exit;
  }
}
if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
// Stock Check
  $products = $cart->get_products();
  for ($i=0, $n=sizeof($products); $i<$n; $i++) {
    if (tep_check_stock((int)$products[$i]['id'], $products[$i]['quantity'])) {
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
      exit;
      break;
    }
  }
}

include(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PROCESS);
$payment_modules = payment::getInstance(SITE_ID);
$insert_id = date("Ymd") . '-' . date("His") . tep_get_order_end_num();
$insert_id = tep_is_has_order($insert_id);
# Check
$NewOidQuery = tep_db_query("select count(*) as cnt from ".TABLE_ORDERS." where orders_id = '".$insert_id."' and site_id = '".SITE_ID."'");
$NewOid = tep_db_fetch_array($NewOidQuery);
if($NewOid['cnt'] > 0) {
  # OrderNo
    $insert_id = date("Ymd") . '-' . date("His") . tep_get_order_end_num();
}
  if (isset($_SESSION['payment_validated']) &&$_SESSION['payment_validated']==false){
    unset($_SESSION['comments']);
    $_SESSION['payment_error'] = $_SESSION['new_payment_error'];
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '' , 'SSL'));
    exit;
  }
$comments_info = $payment_modules->dealComment($payment,$comments);
if (is_array($comments_info)) {
  $comments = $comments_info['comment'];
} else {
  $comments = $comments_info;
}
require(DIR_WS_CLASSES . 'order.php');
$order = new order;

// load the before_process function from the payment modules
$payment_modules->before_process($payment);

require(DIR_WS_CLASSES . 'order_total.php');
$order_total_modules = new order_total;

$order_totals = $order_total_modules->process();
$valadate_total = $order->info['total'];
if ($point){
  $valadate_total -= abs($point);
}
if (isset($_SESSION['campaign_fee'])) {
  $valadate_total -= abs($_SESSION['campaign_fee']);
}
if(isset($_SESSION['mailfee'])){
  $valadate_total += abs($_SESSION['mailfee']);
}
if($payment_modules->moneyInRange($payment,$valadate_total)){
  tep_redirect(tep_href_link(FILENAME_CHECKOUT_UNSUCCESS));
  exit;
}
$customers_referer_query = tep_db_query("select referer, is_send_mail, is_calc_quantity, is_quited, quited_date from ".TABLE_CUSTOMERS." where customers_id='".$customer_id."'");
$customers_referer_array = tep_db_fetch_array($customers_referer_query);
$referer = $customers_referer_array['referer'];
tep_db_query("update ".TABLE_CUSTOMERS." set referer=''   where customers_id='".$customer_id."'");
# Select

$_SESSION['insert_id'] = $insert_id;
$sql_data_array = array('orders_id'         => $insert_id,
                        'customers_id'      => $customer_id,
                        'customers_name'    => tep_get_fullname($order->customer['firstname'],$order->customer['lastname']),
                        'customers_name_f'  => tep_get_fullname($order->customer['firstname_f'],$order->customer['lastname_f']),
                        'customers_company' => $order->customer['company'],
                        'customers_street_address' => $order->customer['street_address'],
                        'customers_suburb' => $order->customer['suburb'],
                        'customers_city' => $order->customer['city'],
                        'customers_postcode' => $order->customer['postcode'], 
                        'customers_state' => $order->customer['state'], 
                        'customers_country' => $order->customer['country']['title'], 
                        'customers_telephone' => $order->customer['telephone'],
                        'customers_email_address' => $order->customer['email_address'],
                        'customers_address_format_id' => $order->customer['format_id'], 
                        'delivery_name'    => tep_get_fullname($order->delivery['firstname'],$order->delivery['lastname']),
                        'delivery_name_f'  => tep_get_fullname($order->delivery['firstname_f'],$order->delivery['lastname_f']),
                        'delivery_company' => $order->delivery['company'],
                        'delivery_street_address' => $order->delivery['street_address'], 
                        'delivery_suburb'    => $order->delivery['suburb'], 
                        'delivery_city'      => $order->delivery['city'], 
                        'delivery_postcode'  => $order->delivery['postcode'], 
                        'delivery_state'     => $order->delivery['state'], 
                        'delivery_country'   => $order->delivery['country']['title'], 
                        'delivery_telephone' => $order->delivery['telephone'], 
                        'delivery_address_format_id' => $order->delivery['format_id'], 
                        'billing_name' => tep_get_fullname($order->billing['firstname'],$order->billing['lastname']),
                        'billing_name_f' => tep_get_fullname($order->billing['firstname_f'],$order->billing['lastname_f']),
                        'billing_company' => $order->billing['company'],
                        'billing_street_address' => $order->billing['street_address'], 
                        'billing_suburb'   => $order->billing['suburb'], 
                        'billing_city'     => $order->billing['city'], 
                        'billing_postcode' => $order->billing['postcode'], 
                        'billing_state' => $order->billing['state'], 
                        'billing_country' => $order->billing['country']['title'], 
                        'billing_telephone' => $order->billing['telephone'], 
                        'billing_address_format_id' => $order->billing['format_id'], 
                        'payment_method' => payment::changeRomaji($order->info['payment_method'], PAYMENT_RETURN_TYPE_TITLE), 
                        'cc_type'    => $order->info['cc_type'], 
                        'cc_owner'   => $order->info['cc_owner'], 
                        'cc_number'  => $order->info['cc_number'], 
                        'cc_expires' => $order->info['cc_expires'], 
                        'date_purchased'    => 'now()', 
                        'orders_status'     => $order->info['order_status'], 
                        'currency'          => $order->info['currency'], 
                        'currency_value'    => $order->info['currency_value'],
                        'torihiki_houhou'   => $torihikihouhou,
                        'site_id'           => SITE_ID,
                        'torihiki_date'     => $insert_torihiki_date,
                        'torihiki_date_end' => $insert_torihiki_date_end,
                        'orders_ref'        => $referer,
                        'orders_ref_site'   => tep_get_domain($referer),
                        'orders_ref_keywords' => strtolower(SBC2DBC(parseKeyword($referer))),
                        'orders_ip'         => $_SERVER['REMOTE_ADDR'],
                        'orders_host_name'  => trim(strtolower(@gethostbyaddr($_SERVER['REMOTE_ADDR']))),
                        'orders_user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'orders_wait_flag'  => 1,
                        'orders_screen_resolution'    => $_SESSION['screenResolution'],
                        'orders_color_depth'          => $_SESSION['colorDepth'],
                        'orders_flash_enable'         => $_SESSION['flashEnable'],
                        'orders_flash_version'        => $_SESSION['flashVersion'],
                        'orders_director_enable'      => $_SESSION['directorEnable'],
                        'orders_quicktime_enable'     => $_SESSION['quicktimeEnable'],
                        'orders_realplayer_enable'    => $_SESSION['realPlayerEnable'],
                        'orders_windows_media_enable' => $_SESSION['windowsMediaEnable'],
                        'orders_pdf_enable'           => $_SESSION['pdfEnable'],
                        'orders_java_enable'          => $_SESSION['javaEnable'],
                        'orders_system_language'      => $_SESSION['systemLanguage'],
                        'orders_user_language'        => $_SESSION['userLanguage'],
                        'orders_http_accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
                        'telecom_option'              => $_SESSION['option'],
                      );
//作所信息入库开始
foreach($_SESSION['options'] as $op_key=>$op_value){
  
  if($_SESSION['options_type_array'][$op_key] == 'num'){
     
    $input_text_str = $op_value[1];
    $mode = array('/\s/','/－/','/－/','/-/');
    $replace = array('','','','');
    $mode_ban = array('1','2','3','4','5','6','7','8','9','0');
    $mode_quan = array('/１/','/２/','/３/','/４/','/５/','/６/','/７/','/８/','/９/','/０/');
    $input_text_str = preg_replace($mode,$replace,$input_text_str);
    $input_text_str = preg_replace($mode_quan,$mode_ban,$input_text_str);
    $op_value[1] = $input_text_str;
  } 
  $address_options_query = tep_db_query("select id from ". TABLE_ADDRESS ." where name_flag='". $op_key ."'");
  $address_options_array = tep_db_fetch_array($address_options_query);
  tep_db_free_result($address_options_query);
  $address_query = tep_db_query("insert into ". TABLE_ADDRESS_ORDERS ." values(NULL,'$insert_id',$customer_id,{$address_options_array['id']},'$op_key','".addslashes($op_value[1])."','0')");
  tep_db_free_result($address_query);
}
  //获取是否开启了帐单邮寄地址功能
  $billing_address_show = get_configuration_by_site_id('BILLING_ADDRESS_SETTING',SITE_ID);
  $billing_address_show = $billing_address_show == '' ? get_configuration_by_site_id('BILLING_ADDRESS_SETTING',0) : $billing_address_show; 
  if($billing_address_show == 'true' && $_SESSION['billing_select'] == '1'){
    
     foreach($_SESSION['billing_options'] as $op_key=>$op_value){
 
       $address_options_query = tep_db_query("select id from ". TABLE_ADDRESS ." where name_flag='". $op_key ."'");
       $address_options_array = tep_db_fetch_array($address_options_query);
       tep_db_free_result($address_options_query);
       $address_query = tep_db_query("insert into ". TABLE_ADDRESS_ORDERS ." values(NULL,'$insert_id',$customer_id,{$address_options_array['id']},'$op_key','".addslashes($op_value[1])."','1')");
       tep_db_free_result($address_query);
     }  
  }

  $address_show_array = array(); 
  $address_show_list_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_show_list_array = tep_db_fetch_array($address_show_list_query)){

    $address_show_array[$address_show_list_array['id']] = $address_show_list_array['name_flag'];
  }
  tep_db_free_result($address_show_list_query);
  $address_temp_str = '';
  foreach($_SESSION['options'] as $address_his_key=>$address_his_value){
    
      if(in_array($address_his_key,$address_show_array)){

         $address_temp_str .= $address_his_value[1];
      }
  }
  
  $address_error = false;
  $orders_id_temp = '';
  $address_sh_his_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id='$customer_id' group by orders_id");
  while($address_sh_his_array = tep_db_fetch_array($address_sh_his_query)){

    $address_sh_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where customers_id='$customer_id' and orders_id='". $address_sh_his_array['orders_id'] ."' order by id");
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

    tep_db_query("update ". TABLE_ADDRESS_HISTORY ." set orders_id='".$insert_id."' where orders_id='".$orders_id_temp."'"); 
  }
if($address_error == false && $_SESSION['guestchk'] == '0'){
  foreach($_SESSION['options'] as $address_history_key=>$address_history_value){
      $address_history_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where name_flag='". $address_history_key ."'");
      $address_history_array = tep_db_fetch_array($address_history_query);
      tep_db_free_result($address_history_query);
      $address_history_id = $address_history_array['id'];
      $address_history_add_query = tep_db_query("insert into ". TABLE_ADDRESS_HISTORY ." value(NULL,'$insert_id',{$customer_id},$address_history_id,'{$address_history_array['name_flag']}','".addslashes($address_history_value[1])."','0')");
      tep_db_free_result($address_history_add_query);
  }
}



//作所信息入库结束
  
if (isset($_SESSION['referer_adurl']) && $_SESSION['referer_adurl']) {
  $sql_data_array['orders_adurl'] = $_SESSION['referer_adurl'];
}
$telecom_option_ok = $payment_modules->dealUnknow($payment,$sql_data_array);
//所有的费用 应该都叫 code_fee
if (isset($_POST['code_fee'])) {
  $sql_data_array['code_fee'] = intval($_POST['code_fee']);
} else{
  $sql_data_array['code_fee'] = 0;
}
if(isset($_SESSION['h_shipping_fee'])){
//配送费用
  $sql_data_array['shipping_fee'] = intval($_SESSION['h_shipping_fee']);
}else{
  $sql_data_array['shipping_fee'] = 0;
}

$bflag_single = ds_count_bflag();

if ($bflag_single == 'View') {
  $orign_hand_fee = $sql_data_array['code_fee'];
  $buy_handle_fee = $payment_modules->handle_calc_fee($payment,$order->info['total']); 
  $sql_data_array['code_fee'] = $orign_hand_fee + $buy_handle_fee; 
  $new_handle_fee = $sql_data_array['code_fee'];
}
if ($customers_referer_array['is_quited'] == '1') {
  $sql_data_array['is_gray'] = '2';
}
if ($_SESSION['guestchk'] == '1') {
  $sql_data_array['is_guest'] = '1';
}

$order_sql_data_array = $sql_data_array;
$total_data_arr = array();
for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
  $sql_data_array = array('orders_id' => $insert_id,
                          'title' => $order_totals[$i]['title'],
                          'text' => $order_totals[$i]['text'],
                          'value' => $order_totals[$i]['value'], 
                          'class' => $order_totals[$i]['code'], 
                          'sort_order' => $order_totals[$i]['sort_order'],
                        );
  $payment_flag = false;
  if($telecom_option_ok!=true){
    $telecom_option_ok = $payment_modules->getExpress($payment,$order_totals,$i);
    $payment_flag = true;
  }
  $total_data_arr[] = $sql_data_array;
}

//检测订单ID是否重复 
$success_flag = true;
if($telecom_option_ok == true && $payment_flag == true){
  $telecom_unknow_query = tep_db_query("select id from telecom_unknow where `option`='".$insert_id."'");
  if(tep_db_num_rows($telecom_unknow_query) == 0){

    $success_flag = false;
  }
}
$orders_id_array = array();
$time_array = array();
$orders_query = tep_db_query("select orders_id from ".TABLE_ORDERS." where orders_id='".$insert_id."'");  
if(tep_db_num_rows($orders_query) > 0){
  $orders_id_array[] = $insert_id;
  $time_array[] = date('Y-m-d H:i:s');
  $success_flag = false;
  //如果订单ID存在的话，最多循环10次生成新订单ID，最后如果还存在的话，跳转到注文失败页面，并发电子邮件
  for($orders_num = 0;$orders_num < 10;$orders_num++){
    $nid = date('Ymd-His').ds_makeRandStr(2);
    $orders_query = tep_db_query("select orders_id from ".TABLE_ORDERS." where orders_id='".$nid."'");  
    if(tep_db_num_rows($orders_query) == 0){
      $insert_id = $nid;
      $_SESSION['insert_id'] = $insert_id;
      $success_flag = true;
      break;
    }else{
      $orders_id_array[] = $nid;
      $time_array[] = date('Y-m-d H:i:s');
    }
  }
}
if($success_flag == false){

  //发送电子邮件
  $message = new email(array('X-Mailer: iimy Mailer'));
  $orders_mail_title = 'orders error';
  $orders_mail_text = 'PAYMENT: '.$payment."\n\n";
  foreach($orders_id_array as $key=>$value){
    $orders_mail_text .= 'ID: '.$value."\n";
    $orders_mail_text .= 'TIME: '.$time_array[$key]."\n\n";
  }
  $text = $orders_mail_text;  
  $message->add_html(nl2br($orders_mail_text), $text);
  $message->build_message();
  //Administrator
  $message->send(STORE_OWNER,IP_SEAL_EMAIL_ADDRESS,STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS,$orders_mail_title);
  tep_redirect(tep_href_link(FILENAME_CHECKOUT_UNSUCCESS));
  exit;
}

tep_db_perform(TABLE_ORDERS, $order_sql_data_array);
if(isset($_SESSION['paypal_order_info'])&&is_array($_SESSION['paypal_order_info'])&&!empty($_SESSION['paypal_order_info'])){
  tep_db_perform(TABLE_ORDERS, $_SESSION['paypal_order_info'],'update', "orders_id='".$insert_id."'");
}

tep_order_status_change($insert_id,$order_sql_data_array['orders_status']);

foreach ($total_data_arr as $sql_data_array){
  tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
}


$customer_notification = (SEND_EMAILS == 'true') ? '1' : '0';
$sql_data_array = array('orders_id' => $insert_id, 
                        'orders_status_id' => $order->info['order_status'], 
                        'date_added' => 'now()', 
                        'customer_notified' => $customer_notification,
                        'comments' => $order->info['comments'],
                        'user_added' => tep_get_fullname($order->customer['firstname'],$order->customer['lastname']));
tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

  
//# 添加部分（买取信息）

if ($telecom_option_ok == true) {
  orders_updated($insert_id);
}

  

// initialized for the email confirmation
$products_ordered = '';
$subtotal = 0;
$total_tax = 0;

for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
  // Stock Update - Joao Correia
  if (STOCK_LIMITED == 'true') {
    if ($customers_referer_array['is_calc_quantity'] != '1') {
      $stock_query = tep_db_query("select products_real_quantity,products_virtual_quantity from " . TABLE_PRODUCTS .  " where products_id = '" . (int)$order->products[$i]['id'] . "'");
      $radices = tep_get_radices((int)$order->products[$i]['id']);
      if (tep_db_num_rows($stock_query) > 0) {
        $stock_values = tep_db_fetch_array($stock_query);
        if ($order->products[$i]['qty']*$radices > $stock_values['products_real_quantity']) {
          tep_db_perform(
                         'products',
                         array(
                               'products_virtual_quantity' => $stock_values['products_virtual_quantity'] - ($order->products[$i]['qty'] - (int)($stock_values['products_real_quantity']/$radices)),
                               'products_real_quantity'    => ($stock_values['products_real_quantity']%$radices) 
                               ),
                         'update',
                         "products_id = '" . (int)$order->products[$i]['id'] . "'"
                         );
        } else {
          tep_db_perform(
                         'products',
                         array(
                               'products_real_quantity' => $stock_values['products_real_quantity'] - $order->products[$i]['qty']*$radices,
                               ),
                         'update',
                         "products_id = '" . (int)$order->products[$i]['id'] . "'"
                         );
        }
      }
    }
  }

  $chara = '';
  $sql_data_array = array('orders_id' => $insert_id, 
                          'products_id' => (int)$order->products[$i]['id'], 
                          'products_model' => $order->products[$i]['model'], 
                          'products_name' => $order->products[$i]['search_name'], // for search, insert products_name where site_id = 0
                          'products_price' => $order->products[$i]['price'], 
                          'final_price' => $order->products[$i]['final_price'], 
                          'products_tax' => $order->products[$i]['tax'], 
                          'products_quantity' => $order->products[$i]['qty'],
                          'products_rate' => tep_get_products_rate((int)$order->products[$i]['id']),
                          'site_id' => SITE_ID,
                          );
  tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
  $order_products_id = tep_db_insert_id();



  //------insert customer choosen option to order--------
  $attributes_exist = '0';
  $products_ordered_attributes = '';
  $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>");
  
  $attribute_max_len = 0; 
  $attribute_len_arrary = array(); 
  if (!empty($order->products[$i]['op_attributes'])) {
    foreach ($order->products[$i]['op_attributes'] as $op_m_key => $op_m_value) {
      $attribute_len_array[] = mb_strlen($op_m_value['front_title'], 'utf-8'); 
    }
  }
  
  if (!empty($order->products[$i]['ck_attributes'])) {
    foreach ($order->products[$i]['ck_attributes'] as $ck_m_key => $ck_m_value) {
      $attribute_len_array[] = mb_strlen($ck_m_value['front_title'], 'utf-8'); 
    } 
  } 
   
  if (!empty($attribute_len_array)) {
    $attribute_max_len = max($attribute_len_array); 
  }
  
  if ($attribute_max_len < 4) {
    $attribute_max_len = 4; 
  }
  
  if (!empty($order->products[$i]['op_attributes'])) {
    //商品的option信息 
    $attributes_exist = '1';
     
    foreach ($order->products[$i]['op_attributes'] as $op_key => $op_value) {
       
      
      $input_option_array = array('title' => $op_value['front_title'], 'value' => $op_value['value']);
      $option_item_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$op_value['item_id']."'"); 
      $option_item_res = tep_db_fetch_array($option_item_raw); 
      $op_price = 0; 
      
      if ($option_item_res) {
        if ($option_item_res['type'] == 'radio') {
           $ao_option_array = @unserialize($option_item_res['option']);
           if (!empty($ao_option_array['radio_image'])) {
             foreach ($ao_option_array['radio_image'] as $or_key => $or_value) {
               if (trim(str_replace($replace_arr, '', nl2br(stripslashes($or_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_value['value']))))) {
                 $op_price = $or_value['money']; 
                 break; 
               }
             }
           } 
        } else if ($option_item_res['type'] == 'textarea') {
          $to_option_array = @unserialize($option_item_res['option']);
          $tmp_to_single = false; 
          if ($to_option_array['require'] == '0') {
            if ($op_value['value'] == MSG_TEXT_NULL) {
              $tmp_to_single = true; 
            }
          }
          if ($tmp_to_single) {
            $op_price = 0; 
          } else {
            $op_price = $option_item_res['price']; 
          }
        } else {
          $op_price = $option_item_res['price']; 
        }
      } else {
        $op_price = $op_value['price']; 
      }
      
      $sql_data_array = array('orders_id' => $insert_id, 
                              'orders_products_id' => $order_products_id, 
                              'options_values_price' => $op_price, 
                              'option_info' => tep_db_input(serialize($input_option_array)),  
                              'option_group_id' => $op_value['group_id'], 
                              'option_item_id' => $op_value['item_id'] 
                              );
      tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

      if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
        $sql_data_array = array('orders_id' => $insert_id, 
                                'orders_products_id' => $order_products_id, 
                                'orders_products_filename' => $attributes_values['products_attributes_filename'], 
                                'download_maxdays' => $attributes_values['products_attributes_maxdays'], 
                                'download_count' => $attributes_values['products_attributes_maxcount']);
        tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
      }
      $products_ordered_attributes .= "\n" 
        . $op_value['front_title'] 
        . str_repeat('　',intval(($attribute_max_len-mb_strlen($op_value['front_title'], 'utf-8'))))
        . '：' . tep_db_input(str_replace($replace_arr, "", $op_value['value']));
      
      if ($op_price != '0') {
        $products_ordered_attributes .= '　('.$currencies->format($op_price).')'; 
      }
    }
  }
  
  if (!empty($order->products[$i]['ck_attributes'])) {
    //登录后选择商品的option信息 
    foreach ($order->products[$i]['ck_attributes'] as $ck_key => $ck_value) {
      $input_option_array = array('title' => $ck_value['front_title'], 'value' => $ck_value['value']);
      
      $coption_item_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$ck_value['item_id']."'"); 
      $coption_item_res = tep_db_fetch_array($coption_item_raw); 
      $c_op_price = 0; 
      
      if ($coption_item_res) {
        if ($coption_item_res['type'] == 'radio') {
           $aco_option_array = @unserialize($coption_item_res['option']);
           if (!empty($aco_option_array['radio_image'])) {
             foreach ($aco_option_array['radio_image'] as $cor_key => $cor_value) {
               if (trim(str_replace($replace_arr, '', nl2br(stripslashes($cor_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($ck_value['value']))))) {
                 $c_op_price = $cor_value['money']; 
                 break; 
               }
             }
           } 
        } else if ($coption_item_res['type'] == 'textarea') {
          $aco_option_array = @unserialize($coption_item_res['option']);
          $tco_tmp_single = false;
          if ($aco_option_array['require'] == '0') {
            if ($ck_value['value'] == MSG_TEXT_NULL) {
              $tco_tmp_single = true;
            }
          }
          if ($tco_tmp_single) {
            $c_op_price = 0; 
          } else {
            $c_op_price = $coption_item_res['price']; 
          }
        } else {
          $c_op_price = $coption_item_res['price']; 
        }
      } else {
        $c_op_price = $ck_value['price']; 
      }
      
      
      $sql_data_array = array('orders_id' => $insert_id, 
                              'orders_products_id' => $order_products_id, 
                              'options_values_price' => $c_op_price, 
                              'option_info' => tep_db_input(serialize($input_option_array)),  
                              'option_group_id' => $ck_value['group_id'], 
                              'option_item_id' => $ck_value['item_id'] 
                              );
      tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

      if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
        $sql_data_array = array('orders_id' => $insert_id, 
                                'orders_products_id' => $order_products_id, 
                                'orders_products_filename' => $attributes_values['products_attributes_filename'], 
                                'download_maxdays' => $attributes_values['products_attributes_maxdays'], 
                                'download_count' => $attributes_values['products_attributes_maxcount']);
        tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
      }
      $products_ordered_attributes .= "\n" 
        . $ck_value['front_title'] 
        . str_repeat('　',intval(($attribute_max_len-mb_strlen($ck_value['front_title'], 'utf-8'))))
        . '：' . tep_db_input(str_replace($replace_arr, "", $ck_value['value']));
      
      if ($c_op_price != '0') {
        $products_ordered_attributes .= '　('.$currencies->format($c_op_price).')'; 
      }
    }
  }
  
  
  
  //------insert customer choosen option eof ----
  $total_weight += ($order->products[$i]['qty'] * $order->products[$i]['weight']);
  $total_tax += tep_calculate_tax($total_products_price, $products_tax) * $order->products[$i]['qty'];
  $total_cost += $total_products_price;

  $products_ordered .= TEXT_ORDERS_PRODUCTS.str_repeat('　',intval(($attribute_max_len-mb_strlen(TEXT_ORDERS_PRODUCTS, 'utf-8')))).'：' . $order->products[$i]['name'];
  if(tep_not_null($order->products[$i]['model'])) {
    $products_ordered .= ' (' . $order->products[$i]['model'] . ')';
  }
  if ($order->products[$i]['price'] != '0') {
    $products_ordered .= ' ('.$currencies->display_price($order->products[$i]['price'], $order->products[$i]['tax']).')'; 
  }
  $products_ordered .= $products_ordered_attributes . "\n";
  $products_ordered .= TEXT_ORDERS_PRODUCTS_NUMBER.str_repeat('　',intval(($attribute_max_len-mb_strlen(TEXT_ORDERS_PRODUCTS_NUMBER, 'utf-8')))).'：' . $order->products[$i]['qty'] . NUM_UNIT_TEXT .  tep_get_full_count2($order->products[$i]['qty'], (int)$order->products[$i]['id']) . "\n";
  $products_ordered .= TEXT_ORDERS_PRODUCTS_PRICE.str_repeat('　',intval(($attribute_max_len-mb_strlen(TEXT_ORDERS_PRODUCTS_PRICE, 'utf-8')))).'：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax']) . "\n";
  $products_ordered .= TEXT_ORDERS_PRODUCTS_SUBTOTAL.str_repeat('　',intval(($attribute_max_len-mb_strlen(TEXT_ORDERS_PRODUCTS_SUBTOTAL, 'utf-8')))).'：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . "\n";
  $products_ordered .= TEXT_ORDERS_PRODUCTS_LINE;
  if (tep_get_cflag_by_product_id((int)$order->products[$i]['id'])) {
    if (tep_get_bflag_by_product_id((int)$order->products[$i]['id'])) {
      $products_ordered .= TEXT_ORDERS_PRODUCTS_ORDERED;
    } else {
      $products_ordered .= TEXT_ORDERS_PRODUCTS_ORDERED_TEXT;
    }
  }
}

$order_type_str = tep_check_order_type($insert_id);
tep_db_query("update `".TABLE_ORDERS."` set `orders_type` = '".$order_type_str."' where orders_id = '".$insert_id."'");

orders_updated($insert_id);

$otq = tep_db_query("select * from ".TABLE_ORDERS_TOTAL." where class = 'ot_total' and orders_id = '".$insert_id."'");
$ot = tep_db_fetch_array($otq);

// mail oprion like mailprint
$email_credit_research = ''; 
$credit_inquiry_query = tep_db_query("select customers_fax, customers_guest_chk from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
$credit_inquiry       = tep_db_fetch_array($credit_inquiry_query);
$email_credit_research .= $credit_inquiry['customers_fax'] . "\n";
$email_credit_research .= TEXT_ORDERS_PRODUCTS_THICK;
$email_orders_history = '';
  
if ($credit_inquiry['customers_guest_chk'] == '1') { 
  $email_orders_history .= TABLE_HEADING_MEMBER_TYPE_GUEST; 
} else { 
  $email_orders_history .= TEXT_MEMBER; 
}
  
$email_orders_history .= "\n";
  
$order_history_query_raw = "select o.orders_id, o.customers_name, o.customers_id,
  o.date_purchased, s.orders_status_name, ot.value as order_total_value from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . tep_db_input($customer_id) . "' and o.orders_status = s.orders_status_id and s.language_id = '" . $languages_id . "' and ot.class = 'ot_total' order by o.date_purchased DESC limit 0,5";  
$order_history_query = tep_db_query($order_history_query_raw);
while ($order_history = tep_db_fetch_array($order_history_query)) {
  $email_orders_history .= $order_history['date_purchased'] . '　　' .
    tep_output_string_protected($order_history['customers_name']) . '　　' .
    $currencies->format(abs(intval($order_history['order_total_value']))) . '　　' . $order_history['orders_status_name'] . "\n";
}
  
$email_orders_history .= TEXT_ORDERS_PRODUCTS_THICK . "\n\n";

# 邮件正文调整 --------------------------------------{

//mailoption {
$mailoption['ORDER_NUMBER']         = $insert_id;
$mailoption['ORDER_DATE']       = tep_date_long(time())  ;
$mailoption['USER_NAME']        = tep_get_fullname($order->customer['firstname'],$order->customer['lastname'])  ;
$mailoption['USER_MAIL'] = $order->customer['email_address'];
//配送料
$shipping_fee_value = isset($_SESSION['h_shipping_fee']) ? $_SESSION['h_shipping_fee'] : 0; 
$mailoption['SHIPPING_FEE']      = str_replace(JPMONEY_UNIT_TEXT,'',$currencies->format(abs($shipping_fee_value)));
$mailoption['ORDER_TOTAL']      = str_replace(JPMONEY_UNIT_TEXT,'',$currencies->format(abs($ot['value'])));
@$payment_class = $payment_modules->getModule($payment);

$mailoption['TORIHIKIHOUHOU']   = $torihikihouhou;
$mailoption['PAYMENT']    = $payment_class->title ;
$mailoption['SHIPPING_TIME']      =  str_string($date) . $start_hour . TIME_HOUR_TEXT . $start_min . TEXT_ORDERS_PRODUCTS_LINK. $end_hour .TIME_HOUR_TEXT. $end_min .TEXT_ORDERS_PRODUCTS_TWENTY_HOUR ;
$mailoption['ORDER_COMMENT']    = tep_db_prepare_input($_SESSION['mailcomments']);//
unset($_SESSION['comments']);
$mailoption['ADD_INFO']    = $comments_info['payment_info'];
$mailoption['ORDER_PRODUCTS']   = tep_db_prepare_input($products_ordered) ;
$mailoption['SHIPPING_METHOD']    = $insert_torihiki_date;
$mailoption['SITE_NAME']        = STORE_NAME ;
$mailoption['SITE_MAIL']        = SUPPORT_EMAIL_ADDRESS ;
$mailoption['SITE_URL']         = HTTP_SERVER ;
$payment_modules->deal_mailoption($mailoption, $payment);

if ($point){
  $mailoption['POINT']            = str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format(abs($point)));
}else {
  $mailoption['POINT']            = 0;
}
if (isset($_SESSION['campaign_fee'])) {
  $mailoption['POINT']            = str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format(abs($_SESSION['campaign_fee'])));
}
if(!isset($_SESSION['mailfee'])){
  $total_mail_fee =0;
}else{
  $total_mail_fee = str_replace(JPMONEY_UNIT_TEXT,'',$_SESSION['mailfee']);
}

$mailoption['MAILFEE']          = str_replace(JPMONEY_UNIT_TEXT,'',$total_mail_fee);
$email_order = '';
$email_order = $payment_modules->getOrderMailString($payment,$mailoption);

if(isset($_SESSION['options']) && !empty($_SESSION['options'])){
  $address_len_array = array();
  foreach($_SESSION['options'] as $address_value){

    $address_len_array[] = strlen($address_value[0]);
  }
  $maxlen = max($address_len_array);
  $email_address_str = "";
  $email_address_str .= TEXT_ORDERS_PRODUCTS_LINE;
  $maxlen = 9;
  foreach($_SESSION['options'] as $ad_value){
    $ad_len = mb_strlen($ad_value[0],'utf8');
    $temp_str = str_repeat('　',$maxlen-$ad_len);
   $email_address_str .= $ad_value[0].$temp_str.'：'.$ad_value[1]."\n";
  }
  $email_address_str .= TEXT_ORDERS_PRODUCTS_LINE;
  $email_order = str_replace('${USER_ADDRESS}',$email_address_str,$email_order);
}else{
  $email_order = str_replace("\n".'${USER_ADDRESS}','',$email_order); 
  $email_order = str_replace('${USER_ADDRESS}','',$email_order);
  $email_order = str_replace("\n".TEXT_ORDERS_PRODUCTS_ADDRESS_INFO,'',$email_order);
}
$email_order = str_replace("\n".'${CUSTOMIZED_FEE}','',$email_order);
$email_order = str_replace('${CUSTOMIZED_FEE}','',$email_order);
$email_order = tep_replace_mail_templates($email_order,$order->customer['email_address'],tep_get_fullname($order->customer['firstname'],$order->customer['lastname']));
//订单邮件
$orders_mail_templates = tep_get_mail_templates('MODULE_PAYMENT_'.strtoupper($payment).'_MAILSTRING',SITE_ID);
$subject = $orders_mail_templates['title'];
$title_mode_array = array(
                             '${SITE_NAME}' 
                           );
$title_replace_array = array(
                             STORE_NAME 
                           );
$subject = str_replace($title_mode_array,$title_replace_array,$subject);
if ($customers_referer_array['is_send_mail'] != '1') {
  //判断是否给该顾客发送邮件 
  tep_mail(tep_get_fullname($order->customer['firstname'],$order->customer['lastname']), $order->customer['email_address'], $subject, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '');
}
  
if (SENTMAIL_ADDRESS != '') {
  //给管理者发送邮件 
  tep_mail('', SENTMAIL_ADDRESS, $subject, $email_order, tep_get_fullname($order->customer['firstname'],$order->customer['lastname']), $order->customer['email_address'], '');
}
  
last_customer_action();

//打印邮件
$orders_print_mail_templates = tep_get_mail_templates('MODULE_PAYMENT_'.strtoupper($payment).'_PRINT_MAILSTRING',SITE_ID);
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
if (isset($_SESSION['campaign_fee'])) {
  if (abs($_SESSION['campaign_fee']) > 0) {
    $print_point = abs((int)$_SESSION['campaign_fee']);
  }
} else if ($point > 0) {
  $print_point = (int)$point;
}else{
  $print_point = 0;
}

//customer info
$customer_printing_order .= SENDMAIL_TEXT_IP_ADDRESS . $_SERVER["REMOTE_ADDR"] . "\n";
$customer_printing_order .= SENDMAIL_TEXT_HOST . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
$customer_printing_order .= SENDMAIL_TEXT_USER_AGENT . $_SERVER["HTTP_USER_AGENT"] . "\n";
$payment_replace = array(
                        tep_get_fullname($order->customer['firstname'],$order->customer['lastname']), 
                        STORE_NAME, 
                        date('Y'),
                        $insert_id,  
                        tep_date_long(time()),
                        $order->customer['email_address'],
                        $comments_info['payment_info'],
                        $print_point,
                        isset($_SESSION['h_shipping_fee']) ? $_SESSION['h_shipping_fee'] : 0,
                        $total_mail_fee,
                        str_replace(JPMONEY_UNIT_TEXT,"",$currencies->format(abs($ot['value']))),
                        $products_ordered,
                        tep_date_long($date) . $start_hour . SENDMAIL_TEXT_HOUR . $start_min . SENDMAIL_TEXT_MIN.SENDMAIL_TEXT_TIME_LINK. $end_hour .SENDMAIL_TEXT_HOUR. $end_min .SENDMAIL_TEXT_MIN.SENDMAIL_TEXT_TWENTY_FOUR_HOUR,
                        strpos($orders_print_mail_templates['contents'],'${BANK_FOR_TRANSFER}') == true ? $_SESSION['mailcomments'] : $order->info['comments'],
                        '',
                        $customer_printing_order,
                        $email_credit_research,
                        $email_orders_history,
                        abs($ot['value']),
                        ''
                      );
$email_printing_order = str_replace($payment_mode,$payment_replace,$orders_print_mail_templates['contents']);
//自定义费用
$email_printing_order = str_replace("\n".'${CUSTOMIZED_FEE}','',$email_printing_order);
$email_printing_order = str_replace('${CUSTOMIZED_FEE}','',$email_printing_order);
//住所
if($email_address_str != ''){
  $email_printing_order = str_replace('${USER_ADDRESS}',str_replace(TEXT_ORDERS_CUSTOMER_STRING,'',$email_address_str),$email_printing_order);
}else{
  $email_printing_order = str_replace("\n".'${USER_ADDRESS}','',$email_printing_order);
  $email_printing_order = str_replace('${USER_ADDRESS}','',$email_printing_order);
  $email_printing_order = str_replace("\n".str_replace(TEXT_ORDERS_CUSTOMER_STRING,'',TEXT_ORDERS_PRODUCTS_ADDRESS_INFO),'',$email_printing_order);
}

# ------------------------------------------
$email_printing_order = tep_replace_mail_templates($email_printing_order,$order->customer['email_address'],tep_get_fullname($order->customer['firstname'],$order->customer['lastname']));
if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
  //发送打印邮件 
  tep_mail('', PRINT_EMAIL_ADDRESS, str_replace('${SITE_NAME}',STORE_NAME,$orders_print_mail_templates['title']), $email_printing_order, tep_get_fullname($order->customer['firstname'],$order->customer['lastname']), $order->customer['email_address'], '');
}

$check_status_info = $payment_modules->check_insert_status_history($payment, $_SESSION['option'], $insert_id);
if (!empty($check_status_info)) {
  $sql_data_array = array('orders_id' => $insert_id, 
                        'orders_status_id' => $order->info['order_status'], 
                        'date_added' => $check_status_info[0], 
                        'customer_notified' => '0',
                        'comments' => $check_status_info[1],
                        'user_added' => $check_status_info[2]
                        );
  tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
  $last_order_history_raw = tep_db_query("select * from ".TABLE_ORDERS_STATUS_HISTORY." where date_added > '".$check_status_info[0]."' and orders_id = '".$insert_id."'");
  if (!tep_db_num_rows($last_order_history_raw)) {
    $order_status_info_raw = tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$order->info['order_status']."'");    
    $order_status_info_res = tep_db_fetch_array($order_status_info_raw); 
    tep_db_query("update ".TABLE_ORDERS." set orders_status = '".$order->info['order_status']."', orders_status_name = '".$order_status_info_res['orders_status_name']."' where orders_id = '".$insert_id."' and site_id = '".SITE_ID."'"); 
    orders_updated($insert_id);
  }
}

// load the after_process function from the payment modules
$payment_modules->after_process($payment);
$payment_modules->reset_information($payment, false);

$cart->reset(true);

if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
//Add point
  if(MODULE_ORDER_TOTAL_POINT_ADD_STATUS == '0') {

    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " . intval($get_point - $point) . " where customers_id = " . $customer_id );
  } else {

    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point - " . intval($point) . " where customers_id = " . $customer_id );
  }
  
  if (isset($_SESSION['campaign_fee'])) {
    $campaign_raw = tep_db_query("select * from ".TABLE_CAMPAIGN." where id = '".$_SESSION['camp_id']."' and (site_id = '".SITE_ID."' or site_id = '0')"); 
    $campaign = tep_db_fetch_array($campaign_raw); 
    $sql_data_array = array(
        'customer_id' => $customer_id,
        'campaign_id' => $_SESSION['camp_id'],
        'orders_id' => $insert_id,
        'campaign_fee' => $_SESSION['campaign_fee'],
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
}
  
  
if($guestchk == '1') {
// 游客购买的时候重新设置点数
  tep_db_query("update ".TABLE_CUSTOMERS." set point = '0' where customers_id = '".$customer_id."'");
}  
  
  

// unregister session variables used during checkout
tep_session_unregister('sendto');
tep_session_unregister('billto');
tep_session_unregister('shipping');
tep_session_unregister('payment');
tep_session_unregister('comments');
if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  tep_session_unregister('point');
  tep_session_unregister('get_point');
  tep_session_unregister('real_point');
}
  
tep_session_unregister('date');
tep_session_unregister('hour');
tep_session_unregister('min');
tep_session_unregister('start_hour');
tep_session_unregister('start_min');
tep_session_unregister('end_hour');
tep_session_unregister('end_min');
tep_session_unregister('ele');
tep_session_unregister('address_option');
tep_session_unregister('insert_torihiki_date');
tep_session_unregister('insert_torihiki_date_end');
tep_session_unregister('address_show_list');

unset($_SESSION['insert_id']);
unset($_SESSION['option_list']);
unset($_SESSION['character']);
unset($_SESSION['option']);
unset($_SESSION['referer_adurl']);
if(isset($_SESSION['paypal_order_info'])){
  unset($_SESSION['paypal_order_info']);
}

  
unset($_SESSION['campaign_fee']); 
unset($_SESSION['camp_id']); 
unset($_SESSION['options']);
unset($_SESSION['options_type_array']);
unset($_SESSION['weight_fee']);
unset($_SESSION['free_value']);
unset($_SESSION['shipping_page_str']);
unset($_SESSION['new_payment_error']);
unset($_SESSION['comments']);
unset($_SESSION['payment_validated']);
unset($_SESSION['mailcomments']);
unset($_SESSION['billing_select']);
unset($_SESSION['billing_options']);
unset($_SESSION['billing_address_option']);
unset($_SESSION['billing_address_show_list']);

tep_session_unregister('h_code_fee');
tep_session_unregister('h_point');
tep_session_unregister('hc_point');
tep_session_unregister('hc_camp_point');
tep_session_unregister('h_shipping_fee');

if(!isset($_SESSION['orders_credit_flag'])){
  tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS,'','SSL'),'T');
  exit;
}
    
require(DIR_WS_INCLUDES . 'application_bottom.php');

