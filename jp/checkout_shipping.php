<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require('includes/classes/http_client.php');
  require(DIR_WS_ACTIONS.'checkout_shipping.php');

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL')); }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  }

// Stock Check
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
    $products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      if (tep_check_stock((int)$products[$i]['id'], $products[$i]['quantity'])) {
        tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
        break;
      }
    }
  }

// if no shipping destination address was selected, use the customers own address as default
  if (!tep_session_is_registered('sendto')) {
    tep_session_register('sendto');
    $sendto = $customer_default_address_id; } else {
// verify the selected shipping address
//ccdd
    $check_address_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customer_id . "' and address_book_id = '" . $sendto . "'");
    $check_address = tep_db_fetch_array($check_address_query);

    if ($check_address['total'] != '1') {
      $sendto = $customer_default_address_id;
      if (tep_session_is_registered('shipping')) tep_session_unregister('shipping');
    }
  }

  require(DIR_WS_CLASSES . 'order.php'); $order = new order;

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
  if (!tep_session_is_registered('cartID')) tep_session_register('cartID');
  $cartID = $cart->cartID;

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
  if ($order->content_type == 'virtual') {
    if (!tep_session_is_registered('shipping')) tep_session_register('shipping');
    $shipping = false;
    $sendto = false;
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
  }

  $total_weight = $cart->show_weight();
  $total_count = $cart->count_contents();

// load all enabled shipping modules
  require(DIR_WS_CLASSES . 'shipping.php');
  $shipping_modules = new shipping;

  if ( defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true') ) {
    switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
      case 'national':
        if ($order->delivery['country_id'] == STORE_COUNTRY) $pass = true; break;
      case 'international':
        if ($order->delivery['country_id'] != STORE_COUNTRY) $pass = true; break;
      case 'both':
        $pass = true; break;
      default:
        $pass = false; break;
    }

    $free_shipping = false;
    if ( ($pass == true) && ($order->info['subtotal'] >= MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER) ) {
      $free_shipping = true;

      include(DIR_WS_LANGUAGES . $language . '/modules/order_total/ot_shipping.php');
    }
  } else {
    $free_shipping = false;
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_SHIPPING);

// process the selected shipping method
  if ( isset($_POST['action']) && ($_POST['action'] == 'process') ) {
    if (!tep_session_is_registered('comments')) tep_session_register('comments');

    if (!tep_session_is_registered('shipping')) tep_session_register('shipping');
  //$torihikihouhou = tep_db_prepare_input($_POST['torihikihouhou']);
  $date = tep_db_prepare_input($_POST['date']);
  $hour = tep_db_prepare_input($_POST['hour']);
  $min = tep_db_prepare_input($_POST['min']);
  $start_hour = tep_db_prepare_input($_POST['start_hour']);
  $start_min = tep_db_prepare_input($_POST['start_min']);
  $end_hour = tep_db_prepare_input($_POST['end_hour']);
  $end_min = tep_db_prepare_input($_POST['end_min']);
  $address_option_value = tep_db_prepare_input($_POST['address_option']);

  //住所
  $options_required = array();
  $options_type_limit = array();
  $options_type_len = array();
  $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type!='text' and status='0' order by sort");
  while($address_required = tep_db_fetch_array($address_query)){
    
    $options_type[] = $address_required['type'];
    $options_required[] = array($address_required['name'],$address_required['required']);
    $options_type_array = unserialize($address_required['type_comment']);
    $options_type_limit[] = array($address_required['name'],$options_type_array['type_limit']);
    $options_type_len[] = array($address_required['name'],$address_required['num_limit']);
    $options_comment[] = $address_required['comment'];
  }

  //获取配送费用
  $weight_fee = tep_db_prepare_input($_POST['weight_fee']);
  //获取免除配送费用的金额
  $free_value = tep_db_prepare_input($_POST['free_value']);

  //住所信息处理 
  $option_info_array = array(); 
  if (!$hm_option->check()) {
    foreach ($_POST as $p_key => $p_value) {
      $op_single_str = substr($p_key, 0, 3);
      if ($op_single_str == 'op_') {
        $option_info_array[$p_key] = $p_value; 
      } 
    }
  }else{
    $error_str = true;
  }
   
  
  $insert_torihiki_date = $date . ' ' . $start_hour . ':' . $start_min . ':00';
  $insert_torihiki_date_end = $date . ' ' . $end_hour . ':' . $end_min . ':00';
  
  $error = false;
  //if($torihikihouhou == '') {
    //$error = true;
    //$torihikihouhou_error = TEXT_ERROR_TORIHIKIHOUHOU;
  //}
  
  if($date == '') {
    $error = true;
    $date_error = TEXT_ERROR_DATE;
  }else{
  
    if($hour == '') {
      $error = true;
      $jikan_error = TEXT_ERROR_JIKAN;
    }
  
    if($min == '') {
      $error = true;
      $jikan_error = TEXT_ERROR_JIKAN;
    }
  }

  if($error_str == true){

    $error = true;
  }
    
  
  if($error == false) {
    tep_session_register('torihikihouhou');
    tep_session_register('date');
    tep_session_register('hour');
    tep_session_register('min');
    tep_session_register('start_hour');
    tep_session_register('start_min');
    tep_session_register('end_hour');
    tep_session_register('end_min');
    //tep_session_register('address_option');
    $_SESSION['address_option'] = $address_option_value;
    tep_session_register('insert_torihiki_date');
    tep_session_register('insert_torihiki_date_end');
    //住所信息 session
    
    $options = array();
    $options_type_array = array();
    foreach($option_info_array as $key=>$value){

      $options[substr($key,3)] = array($_POST[substr($key,3)],$value);
    }

    foreach($_POST as $post_key=>$post_value){

      if(substr($post_key,0,5) == "type_"){

        $options_type_array[substr($post_key,5)] = $post_value;
      }
    }

    tep_session_register('options');
    tep_session_register('options_type_array');
    tep_session_register('weight_fee');
    tep_session_register('free_value');

    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
  }
  }

// get all available shipping quotes
  $quotes = $shipping_modules->quote();

// if no shipping method has been selected, automatically select the cheapest method.
// if the modules status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
  if ( !tep_session_is_registered('shipping') || ( tep_session_is_registered('shipping') && ($shipping == false) && (tep_count_shipping_modules() > 1) ) ) $shipping = $shipping_modules->cheapest();


  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  
  $torihiki_array = explode("\n", DS_TORIHIKI_HOUHOU);
  $torihiki_list[] = array('id' => '', 'text' => TEXT_PRESE_SELECT);
  for($i=0; $i<sizeof($torihiki_array); $i++) {
    $torihiki_list[] = array('id' => $torihiki_array[$i],
                           'text' => $torihiki_array[$i]
               );
  }
  
  //print_r($_SESSION);
  //print_r($_SESSION['cart']->contents);
  $keys = array_keys($_SESSION['cart']->contents);
  $product_ids = array();
  foreach($keys as $akey){
    $arr = explode('_', $akey);
    if (!empty($arr[0])) {
      $product_ids[] = $arr[0];
    }
  }
  //print_r($_COOKIES);
?>
<?php page_head();?>
<script type="text/javascript"><!--
var selected;

function selectRowEffect(object, buttonSelect) {
  if (!selected) {
    if (document.getElementById) {
      selected = document.getElementById('defaultSelected');
    } else {
      selected = document.all['defaultSelected'];
    }
  }

  if (selected) selected.className = 'moduleRow';
  object.className = 'moduleRowSelected';
  selected = object;

// one button is not an array
  if (document.checkout_address.shipping[0]) {
    document.checkout_address.shipping[buttonSelect].checked=true;
  } else {
    document.checkout_address.shipping.checked=true;
  }
}

function rowOverEffect(object) {
  if (object.className == 'moduleRow') object.className = 'moduleRowOver';
}

function rowOutEffect(object) {
  if (object.className == 'moduleRowOver') object.className = 'moduleRow';
}

function check(value){
  var arr  = new Array();
  var arr_set = new Array();
<?php
  $options_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type='option' and status='0' order by sort");
  $json_array = array();
  $json_set_value = array();
  while($options_array = tep_db_fetch_array($options_query)){
    if(!isset($otpions_array_temp['select_value']) && $otpions_array_temp['select_value'] == ''){
        $show_array[] = unserialize($options_array['type_comment']);
    }
  }

  foreach($show_array as $show_value){
    foreach($show_value as $show_key=>$show_val){

      $json_array[$show_key] = $show_val;
      $json_set_value[$show_key] = $show_val['select_value'];
    } 
  }

  tep_db_free_result($options_query);
  foreach($json_array as $key=>$value_temp){
    echo 'arr["'. $key .'"] = new Array();';
    echo 'arr_set["'. $key .'"] = new Array();';
    $value_temp['option_list'] = array_values($value_temp['option_list']);
    foreach($value_temp['option_list'] as $k=>$val){

      echo 'arr["'. $key .'"]['. $k .'] = "'. $val .'";';
    } 
    echo 'arr_set["'. $key .'"] = "'. $json_set_value[$key] .'";';

  }  
?>
  
  var option_id = document.getElementById("list_option5");
  option_id.options.length = 0;
  len = arr[value].length;
  option_id.options[option_id.options.length]=new Option('--',''); 
  for(i = 0;i < len;i++){
    if(arr_set[value] == arr[value][i]){

      option_id.options[option_id.options.length]=new Option(arr[value][i], arr[value][i]);
    }     
  } 
  for(i = 0;i < len;i++){
    if(arr_set[value] == arr[value][i]){
      continue; 
    }
    option_id.options[option_id.options.length]=new Option(arr[value][i], arr[value][i]);    
  } 
}
// start in_array
function in_array(value,arr){

  for(vx in arr){
    if(value == arr[vx]){

      return true;
    } 
  }
  return false;
}
// end in_array
var first_num = 0;
function address_option_show(action){
  switch(action){

  case 'new' :
    arr_new = new Array();
    arr_color = new Array();
    $("#address_show_id").hide();
    
<?php 
  $address_new_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type!='text' and status='0' order by sort");
  while($address_new_array = tep_db_fetch_array($address_new_query)){
    $address_new_arr = unserialize($address_new_array['type_comment']);
    if($address_new_array['type'] == 'textarea'){
      if($address_new_arr['set_value'] != ''){
        echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "'. $address_new_arr['set_value'] .'";';
        echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#000";';
      }else{
        echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "'. $address_new_array['comment'] .'";';
        echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#999";';
      }
    }elseif($address_new_array['type'] == 'option' && $address_new_arr['select_value'] !=''){
      echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "'. $address_new_arr['select_value'] .'";';
      echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#000";';
    }else{

      echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "";';
      echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#000";';


    }
  }
  tep_db_free_result($address_new_query);
?>
  for(x in arr_new){
     
      var list_options = document.getElementById("op_"+x);
      list_options.value = arr_new[x];
      list_options.style.color = arr_color[x];
      $("#error_"+x).html('');
    }
    break;
  case 'old' :
    $("#address_show_id").show();
    var arr_old  = new Array();
    var arr_name = new Array();
<?php
if(isset($_SESSION['customer_id']) && $_SESSION['customer_id'] != ''){

  //根据后台的设置来显示相应的地址列表
  $address_list_arr = array();
  $address_i = 0;
  $address_list_query = tep_db_query("select name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_list_array = tep_db_fetch_array($address_list_query)){

    $address_list_arr[] = $address_list_array['name_flag'];
    echo 'arr_name['. $address_i .'] = "'. $address_list_array['name_flag'] .'";';
    $address_i++;
  }
  tep_db_free_result($address_list_query);
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id=". $_SESSION['customer_id'] ." group by orders_id order by orders_id desc");
  
   
  $address_num = 0;
  $json_str_array = array();
  $json_old_array = array();

  while($address_orders_group_array = tep_db_fetch_array($address_orders_group_query)){
  
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where orders_id='". $address_orders_group_array['orders_id'] ."' order by id asc");

   
  $json_str_list = '';
  unset($json_old_array);
  while($address_orders_array = tep_db_fetch_array($address_orders_query)){
    
    if(in_array($address_orders_array['name'],$address_list_arr)){

      $json_str_list .= $address_orders_array['value'];
    }
    
    $json_old_array[$address_orders_array['name']] = $address_orders_array['value'];
        
  }

  
  //这里判断，如果有重复的记录只显示一个
  if(!in_array($json_str_list,$json_str_array)){
      
      $json_str_array[$address_num] = $json_str_list; 
      echo 'arr_old['. $address_num .'] = new Array();';
      foreach($json_old_array as $key=>$value){
        echo 'arr_old['. $address_num .']["'. $key .'"] = "'. $value .'";';
      }
      $address_num++;
  }
 
  tep_db_free_result($address_orders_query); 
  }
}
?>
  var address_show_list = document.getElementById("address_show_list");

  address_show_list.options.length = 0;

  len = arr_old.length;
  j_num = 0;
  for(i = 0;i < len;i++){
    arr_str = '';
    for(x in arr_old[i]){
        if(in_array(x,arr_name)){
          arr_str += arr_old[i][x];
        }
        //$("#error_"+x).html('');
    }
    if(arr_str != ''){
      ++j_num;
      if(j_num == 1){first_num = i;}
      address_show_list.options[address_show_list.options.length]=new Option(arr_str,i);
    }

  }
    //address_option_list(first_num);  
    break;
  }
}

function address_option_list(value){
  var arr_list = new Array();
<?php
  //根据后台的设置来显示相应的地址列表
  $address_list_arr = array();
  $address_list_query = tep_db_query("select name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_list_array = tep_db_fetch_array($address_list_query)){

    $address_list_arr[] = $address_list_array['name_flag'];
  }
  tep_db_free_result($address_list_query);
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id=". $_SESSION['customer_id'] ." group by orders_id order by orders_id desc");
  
   
  $address_num = 0;
  $json_str_list = '';
  $json_str_array = array();
  
  while($address_orders_group_array = tep_db_fetch_array($address_orders_group_query)){
  
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where orders_id='". $address_orders_group_array['orders_id'] ."'");
  
  while($address_orders_array = tep_db_fetch_array($address_orders_query)){
    
    if(in_array($address_orders_array['name'],$address_list_arr)){

      $json_str_list .= $address_orders_array['value'];
    }
    
    $json_old_array[$address_orders_array['name']] = $address_orders_array['value'];
        
  }

  
  //这里判断，如果有重复的记录只显示一个
  if(!in_array($json_str_list,$json_str_array)){
      
      $json_str_array[] = $json_str_list; 
      echo 'arr_list['. $address_num .'] = new Array();';
      foreach($json_old_array as $key=>$value){
        echo 'arr_list['. $address_num .']["'. $key .'"] = "'. $value .'";';
      }
      $address_num++;
    }
    $json_str_list = '';
 
  tep_db_free_result($address_orders_query); 
  }
?>
  ii = 0;
  for(x in arr_list[value]){
    var list_option = document.getElementById("op_"+x);
    list_option.style.color = '#000';
    list_option.value = arr_list[value][x];
    $("#error_"+x).html('');
    ii++; 
  }

}

function session_value(){
  var session_array = new Array();
<?php
  foreach($_SESSION['options'] as $see_key=>$see_value){

    echo 'session_array["'. $see_key .'"] = "'. $see_value[1] .'";';
  }
?>
  for(x in session_array){
    var list_option = document.getElementById("op_"+x);
    list_option.style.color = '#000';
    list_option.value = session_array[x];
  }
}
--></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/date_time.js"></script>
<script type="text/javascript" src="js/address_search.js"></script>
<script type="text/javascript">
 function fee(address_value){

  var address = document.getElementById("op_acgijkcoqrtivwyz").value;
  var country = document.getElementById("op_acgijknoqrtuvwyz").value;
 
  if(address_value != ''){

    address = address_value;
  }
  $.ajax({
       url: 'address_fee_ajax.php',
       data: {country:country,address:address,weight:<?php echo $cart->weight; ?>},
       type: 'POST',
       dataType: 'text',
       async : false,
       success: function(data){
           $("#address_fee").html(''); 
           $("#address_fee").html(data);
       }
    }); 
 }
<?php
  if(isset($_POST['address_option'])){
    if($_POST['address_option'] == 'old'){
?>
      $(document).ready(function(){
    
        address_option_show('old'); 
      }); 
<?php
   }else{
?>
      $(document).ready(function(){
        $("#address_show_id").hide(); 
      });
<?php
   }
  }elseif(isset($_SESSION['address_option'])){
    if($_SESSION['address_option'] == 'old'){
?>
      $(document).ready(function(){
        address_option_show('old'); 
        session_value(); 
      });    
<?php
    }else{
?>
      $(document).ready(function(){
        $("#address_show_id").hide(); 
        session_value();
      });
<?php
    }
?>
<?php
  }else{
?>
    $(document).ready(function(){

     address_option_show('old'); 
     address_option_list(first_num); 
    });
<?php
  }
?>

</script>
</head>
<body> 
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof --> 
  <!-- body --> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof --> </td> 
      <!-- body_text --> 
      <td valign="top" id="contents"><?php echo tep_draw_form('order', tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL')) . tep_draw_hidden_field('action', 'process'); ?> 
        <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1>
  <table border="0" width="100%" cellspacing="0" cellpadding="0"> 

          <tr> 
            <td>
  
               <table border="0" width="97%" cellspacing="0" cellpadding="0"> 
                <tr> 
                    <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                        <tr> 
                          <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                        </tr> 
                      </table></td> 
                  <td width="20%">
                  <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                      <tr> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                        <td><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                      </tr> 
                    </table></td> 
                  <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                  <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                  <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                      <tr> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
                      </tr> 
                    </table>
                </td>
                </tr>
                <tr>
                  <td align="center" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_OPTION . '</a>'; ?></td> 
                  <td align="center" width="20%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_DELIVERY; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_PAYMENT; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td> 
                </tr> 
  </table>
    
            </td> 
          </tr> 
          <tr> 
            <td>
                    <table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
                      <tr> 
                        <td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td>  
                        <td align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                      </tr> 
                    </table>
			</td> 
          </tr>
<?php
  //根据购物车中的商品来生成取引时间
  $cart_array = (array)$cart;
  $cart_products_id = array();
  foreach($cart_array['contents'] as $cart_key=>$cart_value){
    
    $cart_temp = explode('{',$cart_key);
    $cart_products_id[] = $cart_temp[0]; 
  }

  //根据$cart_products_id数组中的商品ID来获取每个商品的取引时间
  $cart_shipping_time = array();
  foreach($cart_products_id as $cart_products_value){
    
    $shipping_time_query = tep_db_query("select * from ". TABLE_PRODUCTS ." where products_id=".(int)$cart_products_value);
    $shipping_time_array = tep_db_fetch_array($shipping_time_query);
    tep_db_free_result($shipping_time_query);
    $cart_shipping_time[] = $shipping_time_array['products_shipping_time'];
  }
   
  $cart_shipping_time = array_unique($cart_shipping_time); 
  
  $products_num = count($cart_shipping_time); 
  $shipping_time_array = array();
  foreach($cart_shipping_time as $cart_shipping_value){

    $shipping_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where id=".$cart_shipping_value);
    $shipping_array = tep_db_fetch_array($shipping_query);
    $shipping_time_array['work'][] = unserialize($shipping_array['work']);
    $shipping_time_array['db_set_day'][] = $shipping_array['db_set_day'];
    $shipping_time_array['shipping_time'][] = $shipping_array['shipping_time'];

  }
  
  //work
  $shipping_time_start = array();
  $shipping_time_end = array();
  foreach($shipping_time_array['work'] as $shipping_time_key=>$shipping_time_value){

    foreach($shipping_time_value as $k=>$val){

      $shipping_time_start[$shipping_time_key][] = $val[0]; 
      $shipping_time_end[$shipping_time_key][] = $val[1];
    } 
  }
   
  
  $ship_array = array();
  $ship_time_array = array();
  $j = 0;
  foreach($shipping_time_start as $shipping_key=>$shipping_value){
    foreach($shipping_value as $sh_key=>$sh_value){
      
      $sh_start_array = explode(':',$sh_value);
      $sh_end_array = explode(':', $shipping_time_end[$shipping_key][$sh_key]);
      for($i = (int)$sh_start_array[0];$i <= (int)$sh_end_array[0];$i++){
        if(isset($ship_time_array[$i]) && $ship_time_array[$i] != ''){
          if($ship_temp_array[$i] != $j){$ship_array[$i]++;}
          $ship_time_array[$i] .= '|'.$sh_value.','.$shipping_time_end[$shipping_key][$sh_key];
        }else{
          $ship_time_array[$i] = $sh_value.','.$shipping_time_end[$shipping_key][$sh_key]; 
          $ship_temp_array[$i] = $j;
        }
      } 
    }
    
    $j++;  
  }

  $s_array = array();
  foreach($ship_array as $ship_k=>$ship_v){
    if($ship_v >= $products_num-1){
      $s_array[$ship_k] = $ship_v;
    } 
  } 
  $ship_array = $s_array;
  $shipp_array = array_keys($ship_array);
  sort($shipp_array);
  $ship_new_array = array();
  foreach($shipp_array as $shipp_key=>$shipp_value){
  
    $ship_1_array = explode('|',$ship_time_array[$shipp_value]);
    foreach($ship_1_array as $ship_1_value){

      $ship_2_array = explode(',',$ship_1_value);
      $ship_3_array[$shipp_key][] = $ship_2_array[0];
      $ship_4_array[$shipp_key][] = $ship_2_array[1];
    } 
  }

  foreach($ship_3_array as $ship_3_key=>$ship_3_value){

    natsort($ship_3_array[$ship_3_key]); 
    natsort($ship_4_array[$ship_3_key]);
    $ship_new_array[] = end($ship_3_array[$ship_3_key]).','.current($ship_4_array[$ship_3_key]);
  }

  $max_time_str = implode('||',$shipp_array);
  $min_time_str = implode('||',$ship_new_array);
  //----------
  if(count($shipping_time_array['work']) == 1){
    
    $shi_time_array = array();
    foreach($shipping_time_start[0] as $shi_key=>$shi_value){

      $shi_start_array = explode(':',$shi_value);
      $shi_end_array = explode(':',$shipping_time_end[0][$shi_key]);

      for($shi_i = (int)$shi_start_array[0];$shi_i <= (int)$shi_end_array[0];$shi_i++){

        if(isset($shi_time_array[$shi_i]) && $shi_time_array[$shi_i] != ''){

          
          $shi_time_array[$shi_i] .= '|'.$shi_value.','.$shipping_time_end[0][$shi_key]; 
        }else{

          $shi_time_array[$shi_i] = $shi_value.','.$shipping_time_end[0][$shi_key]; 
        }
      }
    }
    $max_time_str = implode('||',array_keys($shi_time_array));
    $min_time_str = implode('||',$shi_time_array);
  }
  

  //可配送时间区域
  $work_start = $max_time_str;
  $work_end = $min_time_str;

  //当日起几日后可以收货
  $db_set_day = max($shipping_time_array['db_set_day']);
  //可选收货期限
  $shipping_time = max($shipping_time_array['shipping_time']);

  $weight = $cart->weight;
  if($weight > 0){
    $checked_str_old = '';
    $checked_str_new = '';
    $show_flag = '';
    if(isset($_POST['address_option'])){

      if($_POST['address_option'] == 'old'){

        $checked_str_old = 'checked';
        $show_flag = 'block';
      }else{

        $checked_str_new = 'checked';
        $show_flag = 'none';
      }
    }elseif(isset($_SESSION['address_option'])){

      if($_SESSION['address_option'] == 'old'){

        $checked_str_old = 'checked';
        $show_flag = 'block';
      }else{

        $checked_str_new = 'checked';
        $show_flag = 'none';
      }
    }else{

      $checked_str_old = 'checked';
      $show_flag = 'block';
    }
    $show_flag = $show_flag == 'block' ? '' : $show_flag; 
?>
  <tr><td width="70%"><b><?php echo TABLE_ADDRESS_TITLE; ?></b></td></tr>
  <tr><td>
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBox"> 
                      <tr class="infoBoxContents">
                        <td>
                          <table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <tr>
                            <td><?php tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                            <td class="main">
                            <input type="radio" name="address_option" value="old" onClick="address_option_show('old');address_option_list(first_num);" <?php echo $checked_str_old;?>><?php echo TABLE_OPTION_OLD; ?>
                            <input type="radio" name="address_option" value="new" onClick="address_option_show('new');" <?php echo $checked_str_new;?>><?php echo TABLE_OPTION_NEW; ?> 
                            </td></tr>
                          </table>
                       </td>
                      </tr>
    </table>
  </td></tr>
  <tr><td height="6"></td></tr>

         <tr> 
            <td>
                    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBox"> 
                      <tr class="infoBoxContents">
                        <td>
                          <table border="0" width="100%" cellspacing="0" cellpadding="2" id="address_show">
                          <tr id="address_show_id" style="display:<?php echo $show_flag;?>;"><td width="10"><?php tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
<td class="main"><?php echo TABLE_ADDRESS_SHOW; ?></td>
<td class="main">
<select name="address_show_list" id="address_show_list" onChange="address_option_list(this.value);">
<option value="">--</option>
</select>
</td></tr>
<?php
    $hm_option->render('');
    //echo '<tr><td width="10">'. tep_draw_separator('pixel_trans.gif', '10', '1') .'</td><td class="main" width="100%" height="30" colspan="2" style="word-break:break-all;"><span id="address_fee"></span></td></tr>'; 
?>
                          </table>
                        </td>
                      </tr> 
                    </table>
	    </td> 
          </tr>
        <tr><td>&nbsp;</td></tr>
<?php
  }
?>
          <tr>
          <td><b><?php echo TABLE_HEADING_SHIPPING_ADDRESS; ?></b></td></tr>
          <tr>
          <td width="10" height="5"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
          </tr>
          <tr> 
            <td><table border="0" width="100%" cellspacing="1" cellpadding="2"> 
                <tr> 
                  <td>
          
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<!--
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_TORIHIKIHOUHOU; ?></td>
    <td class="main"><?php echo tep_get_torihiki_select_by_products($product_ids);//tep_draw_pull_down_menu('torihikihouhou', $torihiki_list, $torihikihouhou); ?></td>
  </tr>
-->
<?php
  if(isset($torihikihouhou_error) && $torihikihouhou_error != '') { //delnotice
?>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main">&nbsp;</td>
    <td class="main"><?php echo $torihikihouhou_error; ?></td>
  </tr>
<?php
  }
?>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_TORIHIKIKIBOUBI; ?></td>
    <td class="main">
<?php
    $today = getdate();
      $m_num = $today['mon'];
      $d_num = $today['mday']+$db_set_day;
      $year = $today['year'];
    
    $hours = date('H');
    $mimutes = date('i');
?>
  <select name="date" onChange="selectDate('<?php echo $work_start; ?>', '<?php echo $work_end; ?>');">
    <option value="">希望日を選択してください</option>
    <?php
          $oarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
          $newarr = array('月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日', '日曜日');
     
    for($j = 0;$j < $shipping_time;$j++){
      if(isset($_POST['date']) && $_POST['date'] != ""){
        $selected_str = date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)) == $_POST['date'] ? 'selected' : ''; 
      }elseif(isset($_SESSION['date']) && $_SESSION['date'] != ''){
        $selected_str = date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)) == $_SESSION['date'] ? 'selected' : '';
      }
      echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)).'" '. $selected_str .'>'.str_replace($oarr, $newarr, date("Y年m月d日（l）", mktime(0,0,0,$m_num,$d_num+$j,$year))).'</option>' . "\n";

    }

    /*
    for($i=0; $i<7; $i++) {
      //echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year)).'">'.strftime("%Y年%m月%d日（%a）", mktime(0,0,0,$m_num,$d_num+$i,$year)).'</option>' . "\n";
      echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year)).'">'.str_replace($oarr, $newarr, date("Y年m月d日（l）", mktime(0,0,0,$m_num,$d_num+$i,$year))).'</option>' . "\n";
    }
     */
    
    ?>
  </select>
  </td>
  </tr>
<?php
  if(isset($date_error) && $date_error != '') { //del notice
?>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main">&nbsp;</td>
    <td class="main"><?php echo $date_error; ?></td>
  </tr>
<?php
  } 
?>
  <tr id="shipping_list" style="display:none;">
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_TORIHIKIKIBOUJIKAN; ?></td>
    <td class="main" id="shipping_list_show">
<!--
  <select name="hour" onChange="selectHour('<?php //echo $hours; ?>', '<?php //echo $mimutes; ?>')">
    <option value="">--</option>
  </select>
  &nbsp;時&nbsp;
  <select name="min">
    <option value="">--</option>
  </select>
  &nbsp;分&nbsp;
  <?php //echo TEXT_CHECK_24JI; ?>
-->
  </td>
  </tr>
 <tr id="shipping_list_min" style="display:none;">
 <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
 <td class="main">&nbsp;</td>
 <td class="main" id="shipping_list_show_min">
 </td>
 </tr>

<?php
  if((isset($_POST['date']) && $_POST['date'] != '') || (isset($_SESSION['date']) && $_SESSION['date'] != '')){

    echo '<script>selectDate(\''. $work_start .' \', \''. $work_end .'\');$("#shipping_list").show();</script>';
  }
  if((isset($_POST['min']) && $_POST['min'] != '') || (isset($_SESSION['min']) && $_SESSION['min'] != '')){
    $post_hour = isset($_SESSION['hour']) && $_SESSION['hour'] != '' ? $_SESSION['hour'] : $_POST['hour'];
    $post_min = isset($_SESSION['min']) && $_SESSION['min'] != '' ? $_SESSION['min'] : $_POST['min'];
    echo '<script>selectHour(\''. $work_start .' \', \''. $work_end .'\','. $post_hour .','. $post_min .');$("#shipping_list_min").show();</script>';
  }
  if(isset($jikan_error) && $jikan_error != '') {
?>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main">&nbsp;</td>
    <td class="main"><?php echo $jikan_error; ?></td>
  </tr>
<?php
  }
?>
</table>
          </td> 
                </tr> 
              </table></td> 
          </tr> 
          <tr> 
            <td class="main">
        <br>
          「指定した時間より早くできるなら早く来てほしい」をご指定いただきましたお客様へ<br>
          ご入金確認後、最短にて目的地へお届けにまいります。<br>
          お客様がいらっしゃらない場合は、ご指定いただきました日時へ変更させていただきます。<br>
        <br>
      </td> 
          </tr> 
          <tr> 
            <td>
            <table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
                      <tr> 
                        <td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td> 
                        <td class="main" align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                      </tr> 
                    </table>
            </td> 
          </tr> 
          <tr> 
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr> 
          </table>      </form> 
      </td>
      <!-- body_text_eof --> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof --> </td> 
  </table> 
  <!-- body_eof --> 
  <!-- footer --> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof --> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
