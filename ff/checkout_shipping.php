<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require('includes/classes/http_client.php');
  require(DIR_WS_ACTIONS.'checkout_shipping.php');
  $page_url_array = explode('/',$_SERVER['REQUEST_URI']);
  $_SESSION['shipping_page_str'] = end($page_url_array);

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

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
    $sendto = $customer_default_address_id;
  } else {
// verify the selected shipping address
//ccdd
    $check_address_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customer_id . "' and address_book_id = '" . $sendto . "'");
    $check_address = tep_db_fetch_array($check_address_query);

    if ($check_address['total'] != '1') {
      $sendto = $customer_default_address_id;
      if (tep_session_is_registered('shipping')) tep_session_unregister('shipping');
    }
  }

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order;

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
  $date = tep_db_prepare_input($_POST['date']);
  $hour = tep_db_prepare_input($_POST['hour']);
  $min = tep_db_prepare_input($_POST['min']);
  $start_hour = tep_db_prepare_input($_POST['start_hour']);
  $start_min = tep_db_prepare_input($_POST['start_min']);
  $end_hour = tep_db_prepare_input($_POST['end_hour']);
  $end_min = tep_db_prepare_input($_POST['end_min']);
  $address_option_value = tep_db_prepare_input($_POST['address_option']);
  $ele = tep_db_prepare_input($_POST['ele']);
  $address_show_list = $_POST['address_show_list'];

  //住所
  $options_required = array();
  $options_type_limit = array();
  $options_type_len = array();
  $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type='textarea' and status='0' order by sort");
  while($address_required = tep_db_fetch_array($address_query)){
    
    $options_type[] = $address_required['type'];
    $options_required[] = array($address_required['name'],$address_required['required']);
    $options_type_array = unserialize($address_required['type_comment']);
    $options_type_limit[] = array($address_required['name'],$options_type_array['type_limit']);
    $options_type_len[] = array($address_required['name'],$address_required['num_limit']);
    $options_comment[$address_required['name_flag']] = $address_required['comment'];
  }
  tep_db_free_result($address_query);

  

  //住所信息处理 
  $weight_count = $cart->weight;
  $option_info_array = array(); 
  if (!$hm_option->check()) {
    foreach ($_POST as $p_key => $p_value) {
      $op_single_str = substr($p_key, 0, 3);
      if ($op_single_str == 'op_') {
        if($options_comment[substr($p_key, 3)] == $p_value){

          $p_value = '';
        }
        $option_info_array[$p_key] = $p_value; 
      } 
    }
  }else{
    $error_str = true;
  }
  
  
  $insert_torihiki_date = $date . ' ' . $start_hour . ':' . $start_min . ':00';
  $insert_torihiki_date_end = $date . ' ' . $end_hour . ':' . $end_min . ':00';
  
  
  $error = false;
  
  
  if($date == '') {
    $error = true;
    $date_error = TEXT_ERROR_DATE;
  }else{
  
    if($hour == '') {
      $error = true;
      $time_error = TEXT_ERROR_TIME;
    }
  
    if($min == '') {
      $error = true;
      $time_error = TEXT_ERROR_TIME;
    }
  }
    

  if($error_str == true){

    $error = true;
  }
  if($error == false) {
    tep_session_register('date');
    tep_session_register('hour');
    tep_session_register('min');
    tep_session_register('start_hour');
    tep_session_register('start_min');
    tep_session_register('end_hour');
    tep_session_register('end_min');
    tep_session_register('ele');
    tep_session_register('address_show_list');
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
  
  $keys = array_keys($_SESSION['cart']->contents);
  $product_ids = array();
  foreach($keys as $akey){
    $arr = explode('_', $akey);
    if (!empty($arr[0])) {
      $product_ids[] = $arr[0];
    }
  }
?>
<?php page_head();?>
<script type="text/javascript"><!--
<?php
if($cart->weight > 0){
  $address_fixed_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where fixed_option!='0' and status='0'");
  while($address_fixed_array = tep_db_fetch_array($address_fixed_query)){

    switch($address_fixed_array['fixed_option']){

    case '1':
      echo 'var country_fee_id = "op_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_fee_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_fee_id = 'op_'.$address_fixed_array['name_flag'];
      break;
    case '2':
      echo 'var country_area_id = "op_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_area_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_area_id = 'op_'.$address_fixed_array['name_flag'];
      break;
      break;
    case '3':
      echo 'var country_city_id = "op_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_city_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_city_id = 'op_'.$address_fixed_array['name_flag'];
      break;
      break;
    }
  }
}
?>

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

<?php
if($cart->weight > 0){
?>
function check(select_value){

  var arr = new Array();
  <?php 
    $country_fee_query = tep_db_query("select id,name from ". TABLE_COUNTRY_FEE ." where status='0' order by id");
    while($country_fee_array = tep_db_fetch_array($country_fee_query)){

      echo 'arr["'.$country_fee_array['name'].'"] = "'. $country_fee_array['name'] .'";'."\n";
    }
    tep_db_free_result($country_fee_query);
   ?>
  if(document.getElementById(country_fee_id)){
    var country_fee = document.getElementById(country_fee_id);
    country_fee.options.length = 0;

    var i = 0;
    for(x in arr){

      country_fee.options[country_fee.options.length]=new Option(arr[x], x,x==select_value,x==select_value);
      i++;
    }
    if(i == 0){ 
      $("#td_"+country_fee_id_one).hide();
    }else{

      $("#td_"+country_fee_id_one).show();
    }
  }
}
function country_check(value,select_value){
   
   var arr = new Array();
  <?php 
    $country_array = array();
    $country_area_query = tep_db_query("select id,fid,name from ". TABLE_COUNTRY_AREA ." where status='0' order by sort");
    while($country_area_array = tep_db_fetch_array($country_area_query)){
      
      $country_fee_fid_query = tep_db_query("select name from ". TABLE_COUNTRY_FEE ." where id='".$country_area_array['fid']."'"); 
      $country_fee_fid_array = tep_db_fetch_array($country_fee_fid_query);
      tep_db_free_result($country_fee_fid_query);
      $country_array[$country_fee_fid_array['name']][$country_area_array['name']] = $country_area_array['name'];
      
    }
    tep_db_free_result($country_area_query);
    foreach($country_array as $country_key=>$country_value){
      
      echo 'arr["'.$country_key.'"] = new Array();'."\n";
      foreach($country_value as $c_key=>$c_value){
      
        echo 'arr["'.$country_key.'"]["'.$c_key.'"] = "'. $c_value .'";'."\n";

      }

    }
   ?>
 if(document.getElementById(country_area_id)){
    var country_area = document.getElementById(country_area_id);
    country_area.options.length = 0;
    var i = 0;
    for(x in arr[value]){

      country_area.options[country_area.options.length]=new Option(arr[value][x], x,x==select_value, x==select_value);
      i++;
    }

    if(i == 0){ 
      $("#td_"+country_area_id_one).hide();
    }else{
      
      $("#td_"+country_area_id_one).show();
    }

 }

}

function country_area_check(value,select_value){
   
   var arr = new Array();
  <?php
    $country_array = array();
    $country_city_query = tep_db_query("select id,fid,name from ". TABLE_COUNTRY_CITY ." where status='0' order by sort");
    while($country_city_array = tep_db_fetch_array($country_city_query)){
      
      $country_area_fid_query = tep_db_query("select name from ". TABLE_COUNTRY_AREA ." where id='".$country_city_array['fid']."'"); 
      $country_area_fid_array = tep_db_fetch_array($country_area_fid_query);
      tep_db_free_result($country_area_fid_query); 
      $country_array[$country_area_fid_array['name']][$country_city_array['name']] = $country_city_array['name'];
      
    }
    tep_db_free_result($country_city_query);
    foreach($country_array as $country_key=>$country_value){
      
      echo 'arr["'.$country_key.'"] = new Array();'."\n";
      foreach($country_value as $c_key=>$c_value){
      
        echo 'arr["'.$country_key.'"]["'.$c_key.'"] = "'. $c_value .'";'."\n";

      }

    }
  ?>

  if(document.getElementById(country_city_id)){
    var country_city = document.getElementById(country_city_id);
    country_city.options.length = 0;
    var i = 0;
    for(x in arr[value]){

      country_city.options[country_city.options.length]=new Option(arr[value][x], x,x==select_value,x==select_value);
      i++;
    }
    
    if(i == 0){ 
      $("#td_"+country_city_id_one).hide();
    }else{
      
      $("#td_"+country_city_id_one).show();

    }
 
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
      if(document.getElementById("l_"+x)){
        if($("#l_"+x).val() == 'true'){
          $("#r_"+x).html('&nbsp;*<?php echo TEXT_REQUIRED;?>');
        }
      }
   }
    $("#error_"+country_fee_id_one).html('');
    $("#prompt_"+country_fee_id_one).html('');
    $("#error_"+country_area_id_one).html('');
    $("#prompt_"+country_area_id_one).html('');
    $("#error_"+country_city_id_one).html('');
    $("#prompt_"+country_city_id_one).html('');   
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
        $value = str_replace("\n","",$value);
        $value = str_replace("\r","",$value);
        echo 'arr_old['. $address_num .']["'. $key .'"] = "'. $value .'";';
      }
      $address_num++;
  }
 
  tep_db_free_result($address_orders_query); 
  }
}
?>
  if(document.getElementById("address_show_list")){
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
        <?php 
         if(!isset($_POST['address_option']) || $_POST['address_option'] == 'new'){
        ?> 
        if(document.getElementById("l_"+x)){
        if($("#l_"+x).val() == 'true'){
          $("#r_"+x).html('&nbsp;*<?php echo TEXT_REQUIRED;?>');
        }
        }
        <?php
        }
        ?>
        //$("#error_"+x).html('');
    }
    if(arr_str != ''){
      ++j_num;
      if(j_num == 1){first_num = i;}
        <?php
  if(isset($_POST['address_show_list']) && $_POST['address_show_list'] != ''){

    echo 'var address_show_list_one = "'. $_POST['address_show_list'] .'";'."\n"; 
  }elseif(isset($_SESSION['address_show_list']) && $_SESSION['address_show_list'] != ''){
    echo 'var address_show_list_one = "'. $_SESSION['address_show_list'] .'";'."\n";
  }else{
    echo 'var address_show_list_one = first_num;'."\n"; 
  }
        ?>
      address_show_list.options[address_show_list.options.length]=new Option(arr_str,i,i==address_show_list_one,i==address_show_list_one);
    }

  }
  }
    //address_option_list(first_num);  
    break;
  }
}

function address_option_list(value){
  $("#td_"+country_fee_id_one).hide();
  $("#td_"+country_area_id_one).hide();
  $("#td_"+country_city_id_one).hide();

  //clear
  var country_fee = document.getElementById(country_fee_id);
  country_fee.options.length = 0;   
  var country_area = document.getElementById(country_area_id);
  country_area.options.length = 0;
  var country_city = document.getElementById(country_city_id);
  country_city.options.length = 0;

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
  
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where orders_id='". $address_orders_group_array['orders_id'] ."' order by id");
  
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
        $value = str_replace("\n","",$value);
        $value = str_replace("\r","",$value);
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
   if(document.getElementById("op_"+x)){
    var list_option = document.getElementById("op_"+x);
    if('<?php echo $country_fee_id;?>' == 'op_'+x){
      check(arr_list[value][x]);
    }else if('<?php echo $country_area_id;?>' == 'op_'+x){
      country_check(document.getElementById(country_fee_id).value,arr_list[value][x]);
     
    }else if('<?php echo $country_city_id;?>' == 'op_'+x){
      country_area_check(document.getElementById(country_area_id).value,arr_list[value][x]);
    }else{
      list_option.style.color = '#000';
      list_option.value = arr_list[value][x];
    }
     
    $("#error_"+x).html('');
    ii++; 
   }
  }
    $("#error_"+country_fee_id_one).html('');
    $("#prompt_"+country_fee_id_one).html('');
    $("#error_"+country_area_id_one).html('');
    $("#prompt_"+country_area_id_one).html('');
    $("#error_"+country_city_id_one).html('');
    $("#prompt_"+country_city_id_one).html('');
}

function session_value(){
  var session_array = new Array();
<?php
  foreach($_SESSION['options'] as $see_key=>$see_value){
    $see_value[1] = str_replace("\n","",$see_value[1]);
    $see_value[1] = str_replace("\r","",$see_value[1]);
    echo 'session_array["'. $see_key .'"] = "'. $see_value[1] .'";';
  }
?>
  for(x in session_array){
   if(document.getElementById("op_"+x)){
    var list_option = document.getElementById("op_"+x);
    
    if('<?php echo $country_fee_id;?>' == 'op_'+x){
      check(session_array[x]);
    }else if('<?php echo $country_area_id;?>' == 'op_'+x){
      country_check(document.getElementById(country_fee_id).value,session_array[x]);
     
    }else if('<?php echo $country_city_id;?>' == 'op_'+x){
      country_area_check(document.getElementById(country_area_id).value,session_array[x]);
    }else{

      list_option.style.color = '#000'; 
      list_option.value = session_array[x];
    }
   }
  }
}
<?php
}
?>
--></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/date_time.js"></script>
<?php
if($cart->weight > 0){
//判断用户是否是会员
    $address_quest_query = tep_db_query("select customers_guest_chk from ". TABLE_CUSTOMERS ." where customers_id={$_SESSION['customer_id']}");
    $address_quest_array = tep_db_fetch_array($address_quest_query);
    tep_db_free_result($address_quest_query);
    $address_quest_flag = $address_quest_array['customers_guest_chk'];
    if($address_quest_flag == 0){

      $address_history_flag_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where customers_id='". $_SESSION['customer_id'] ."'");
      $address_history_flag_num = tep_db_num_rows($address_history_flag_query);
      tep_db_free_result($address_history_flag_query);
    }
}
?>
<script type="text/javascript"> 
<?php
if($cart->weight > 0){
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
      <?php
      if($address_quest_flag == 0){
      ?>
     address_option_show('old'); 
     address_option_list(first_num); 
     <?php
      }
     if(isset($_SESSION['options'])){ 
     ?>
     session_value();
     <?php
     } 
     ?>
    });
<?php
  }
}
?>
$(document).ready(function(){
document.onclick=function(e){  
  var shipping_hour = $("input[name='hour']").val();
  var e=e?e:window.event;  
  var tar = e.srcElement||e.target;  
  if(tar.id!="hour"+shipping_hour){  
    if($(tar).attr("id")!="shipping_time_id"){  
      if($(tar).attr("class")!="time_radio"){
        if($(tar).attr("class")!="time_label"){
          if($(tar).attr("name")!="min"){
            if($(tar).attr("href")!="javascript:void(0);"){
              check_out(shipping_hour); 
            }
          }
        }
      }
    }  
  }  
}

<?php
if($cart->weight > 0){
if(isset($_SESSION['shipping_session_flag']) && $_SESSION['shipping_session_flag'] == true){
?>
  alert("<?php echo TEXT_SESSION_ERROR_ALERT;?>");
<?php
unset($_SESSION['shipping_session_flag']);
}
?>
  $("#"+country_fee_id).change(function(){
    country_check($("#"+country_fee_id).val());
    country_area_check($("#"+country_area_id).val());
    $("#error_"+country_fee_id_one).html('');
    $("#prompt_"+country_fee_id_one).html('');
    $("#error_"+country_area_id_one).html('');
    $("#prompt_"+country_area_id_one).html('');
    $("#error_"+country_city_id_one).html('');
    $("#prompt_"+country_city_id_one).html('');
  }); 
  $("#"+country_area_id).change(function(){
    country_area_check($("#"+country_area_id).val());
    $("#error_"+country_fee_id_one).html('');
    $("#prompt_"+country_fee_id_one).html('');
    $("#error_"+country_area_id_one).html('');
    $("#prompt_"+country_area_id_one).html('');
    $("#error_"+country_city_id_one).html('');
    $("#prompt_"+country_city_id_one).html('');
  });
  <?php
    if(isset($_POST[$country_fee_id])){
  ?>  
    check("<?php echo isset($_POST[$country_fee_id]) ? $_POST[$country_fee_id] : '';?>");
  <?php
   }elseif(isset($_SESSION['options'])){
  ?>
    check("<?php echo $_SESSION['options'][substr($country_fee_id,3)][1];?>");
  <?php
  }else{
  ?>
    check();     
  <?php
  }
  ?>
  <?php
    if(isset($_POST[$country_area_id])){
  ?>
    country_check($("#"+country_fee_id).val(),"<?php echo $_POST[$country_area_id];?>");
  <?php
   }elseif(isset($_SESSION['options'])){
  ?>
    country_check($("#"+country_fee_id).val(),"<?php echo $_SESSION['options'][substr($country_area_id,3)][1];?>");
  <?php
   }else{
  ?>
    country_check($("#"+country_fee_id).val());
  <?php
  }
  ?>
  <?php
    if(isset($_POST[$country_city_id])){
  ?>
     
     country_area_check($("#"+country_area_id).val(),"<?php echo $_POST[$country_city_id];?>");
  <?php
   }elseif(isset($_SESSION['options'])){
  ?>
     country_area_check($("#"+country_area_id).val(),"<?php echo $_SESSION['options'][substr($country_city_id,3)][1];?>");
  <?php
  }else{
  ?>
    country_area_check($("#"+country_area_id).val());
  <?php
  }
  ?>
  <?php
    if(!isset($_POST[$country_fee_id]) && !isset($_SESSION['options']) && $address_quest_flag == 0 && $address_history_flag_num > 0){
  ?>    
    address_option_list(first_num); 
  <?php
    }
}
  ?>
});
</script>
</head>
<body><div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"><?php echo tep_draw_form('order', tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL')) . tep_draw_hidden_field('action', 'process'); ?> 
        <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1>      
        <div class="comment">
        <table border="0" width="100%" cellspacing="0" cellpadding="0" class="product_info_box"> 

          <tr> 
            <td><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                <tr> 
                    <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                        <tr> 
                          <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                        </tr> 
                      </table></td> 
                    <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
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
                    </table></td> 
                </tr> 
                <tr> 
                  <td align="center" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL') .  '" class="checkoutBarFrom">' . CHECKOUT_BAR_OPTION . '</a>'; ?></td> 
                  <td align="center" width="20%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_DELIVERY; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_PAYMENT; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td> 
                </tr> 
              </table></td> 
          </tr>
          <tr> 
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="cg_pay_info"> 
                <tr> 
                  <td class="main"><b><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE; ?></b><br><?php echo TEXT_CONTINUE_CHECKOUT_PROCEDURE;?></td> 
                  <td class="main" align="right"><?php echo tep_image_submit('button_continue02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                </tr> 
              </table></td> 
          </tr> 
<?php
  //根据购物车中的商品来生成取引时间
  $cart_array = (array)$cart;
  $cart_products_id = array();
  foreach($cart_array['contents'] as $cart_key=>$cart_value){
    
    $cart_temp = explode('{',$cart_key);
    if(count($cart_temp) == 1){

      $cart_temp = explode('_',$cart_key);
    }

    if($cart_temp[0] != ''){
      $cart_products_id[] = $cart_temp[0]; 
    }
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
  foreach($shipping_time_start as $shipping_key=>$shipping_value){
    foreach($shipping_value as $sh_key=>$sh_value){
      
      $sh_start_array = explode(':',$sh_value);
      $sh_end_array = explode(':', $shipping_time_end[$shipping_key][$sh_key]);
      for($i = (int)$sh_start_array[0];$i <= (int)$sh_end_array[0];$i++){
        if(isset($ship_time_array[$shipping_key][$i]) && $ship_time_array[$shipping_key][$i] != ''){
          $ship_time_array[$shipping_key][$i] .= '|'.$sh_value.','.$shipping_time_end[$shipping_key][$sh_key];
        }else{
          $ship_time_array[$shipping_key][$i] = $sh_value.','.$shipping_time_end[$shipping_key][$sh_key]; 
        }
      } 
    }  
  }


  $ship_count_array = array();
  foreach($ship_time_array as $ship_key=>$ship_value){

    foreach($ship_value as $ship_k=>$ship_v){
      $ship_temp_array = array();
      $ship_temp_array = explode('|',$ship_v);
      $ship_time_array[$ship_key][$ship_k] = $ship_temp_array;
    }
    $ship_count_array[$ship_key] = count($ship_value);
  } 

  $ship_min_value = array_search(min($ship_count_array),$ship_count_array);
  $shipp_time_array = array();
  foreach($ship_time_array[$ship_min_value] as $ship_hour_key=>$ship_hour_value){

    foreach($ship_hour_value as $ship_hour_k=>$ship_hour_v){

      $ship_hour_array = explode(',',$ship_hour_v);
      foreach($ship_time_array as $ship_t_k=>$ship_t_v){

          if($ship_t_k == $ship_min_value){continue;}
            if(isset($ship_t_v[$ship_hour_key])){
   
            foreach($ship_t_v[$ship_hour_key] as $ship_tt_k=>$ship_tt_v){

               $ship_hour_temp_array = array();
               $ship_hour_temp_array = explode(',',$ship_tt_v); 
               if($ship_hour_array[0] <= $ship_hour_temp_array[1]){

                 $ship_start_time = max($ship_hour_array[0],$ship_hour_temp_array[0]); 
                 $ship_end_time = min($ship_hour_array[1],$ship_hour_temp_array[1]);
                 $ship_start_time_value = str_replace(':','',$ship_start_time);
                 $ship_end_time_value = str_replace(':','',$ship_end_time);
                 if(!in_array($ship_start_time.','.$ship_end_time,$shipp_time_array[$ship_hour_key]) && (int)$ship_start_time_value < (int)$ship_end_time_value){
                   $shipp_time_array[$ship_hour_key][] = $ship_start_time.','.$ship_end_time;
                 }
               }
            }
          }
      }
    }
  }
  
  
  $shipp_flag_array = $ship_time_array[$ship_min_value];

  foreach($shipp_time_array as $shipp_flag_k=>$shipp_flag_v){

    $shipp_temp_start_array = array();
    $shipp_temp_end_array = array(); 
   if(isset($shipp_flag_array[$shipp_flag_k])){
    foreach($shipp_flag_array[$shipp_flag_k] as $shipp_flag_key=>$shipp_flag_value){
 
      $shipp_temp_all_array = array();
      $shipp_temp_all_array = explode(',',$shipp_flag_value);
      $shipp_temp_start_array[] = $shipp_temp_all_array[0];
      $shipp_temp_end_array[] = $shipp_temp_all_array[1];
    }
    $shipp_temp_start_num = str_replace(':','',min($shipp_temp_start_array));
    $shipp_temp_end_num = str_replace(':','',max($shipp_temp_end_array));
    foreach($shipp_flag_v as $shipp_f_k=>$shipp_f_v){

      $shipp_t_all_array = array();
      $shipp_t_all_array = explode(',',$shipp_f_v);
      $shipp_t_start_num = str_replace(':','',$shipp_t_all_array[0]);
      $shipp_t_end_num = str_replace(':','',$shipp_t_all_array[1]);

      if(!($shipp_t_start_num >= $shipp_temp_start_num && $shipp_t_end_num <= $shipp_temp_end_num)){

        unset($shipp_time_array[$shipp_flag_k][$shipp_f_k]);
      }
    }
   }else{

     unset($shipp_time_array[$shipp_flag_k]);
   }

  }
 


  
  $ship_new_array = array(); 
  $shipp_array = array();
  foreach($shipp_time_array as $shipp_time_k=>$shpp_time_v){

      $ship_new_str = implode('|',$shpp_time_v);
      $ship_new_array[] = $ship_new_str;
      $shipp_array[] = $shipp_time_k;

  } 

  foreach($ship_new_array as $_s_key=>$_s_value){
      $s_temp_array = explode('|',$_s_value);    
      sort($s_temp_array);
      $ship_new_array[$_s_key] = implode('|',$s_temp_array); 
  } 

  foreach($ship_new_array as $s_key=>$s_val){
    $ss_array = array();
    $ss_array = explode(',',$s_val);
    $ss_start = str_replace(':','',$ss_array[0]);
    $ss_end = str_replace(':','',$ss_array[1]);
    if($ss_start > $ss_end){

      unset($ship_new_array[$s_key]);
      unset($shipp_array[$s_key]);
    }
  }

  $max_time_str_old = implode('||',$shipp_array);
  $min_time_str_old = implode('||',$ship_new_array);
  //当日起几日后可以收货
  $db_set_day = max($shipping_time_array['db_set_day']);
  //可选收货期限
  $shipping_time = max($shipping_time_array['shipping_time']);

  $now_time_date = date('Y-m-d',strtotime("+".$shipping_time." minutes"));
  $now_time_hour = date('Hi',strtotime("+".$shipping_time." minutes"));
  $now_time = date('H:i',strtotime("+".$db_set_day." minutes"));
  $now_time = str_replace(':','',$now_time); 
  $now_flag = false;
  if(date('Ymd') == date('Ymd',strtotime("+".$shipping_time." minutes"))){
    $now_time_end = date('H:i',strtotime("+".$shipping_time." minutes"));
    $now_time_end = str_replace(':','',$now_time_end);
    $now_flag = true;
  }

  $ship_new_end_array = array();
  $ship_new_end_array = $ship_new_array;
  $shipp_end_array = array();
  $shipp_end_array = $shipp_array;



  foreach($ship_new_array as $s_k=>$s_v){
    $ss_array = array();
    $ss_array = explode(',',$s_v);
    $ss_start = str_replace(':','',$ss_array[0]);
    $ss_end = str_replace(':','',$ss_array[1]);

    if($ss_end > $now_time_hour){

      unset($ship_new_end_array[$s_k]);
      unset($shipp_end_array[$s_k]);
    }
    if($ss_start > $ss_end || $ss_start < $now_time || ($now_flag == true && $ss_end > $now_time_end)){

      unset($ship_new_array[$s_k]);
      unset($shipp_array[$s_k]);
    }
  }

  $max_time_str = implode('||',$shipp_array);
  $min_time_str = implode('||',$ship_new_array);
  $max_time_end_str = implode('||',$shipp_end_array);
  $min_time_end_str = implode('||',$ship_new_end_array);
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

    foreach($shi_time_array as $_s_key=>$_s_value){
      $s_temp_array = explode('|',$_s_value);    
      sort($s_temp_array);
      $shi_time_array[$_s_key] = implode('|',$s_temp_array); 
    }
   $max_time_str_old = implode('||',array_keys($shi_time_array));
    $min_time_str_old = implode('||',$shi_time_array);


    $now_time_date = date('Y-m-d',strtotime("+".$shipping_time." minutes"));
    $now_time_hour = date('Hi',strtotime("+".$shipping_time." minutes"));
    $now_time = date('H:i',strtotime("+".$db_set_day." minutes"));
    $now_time = str_replace(':','',$now_time);
    $now_flag = false;
    if(date('Ymd') == date('Ymd',strtotime("+".$shipping_time." minutes"))){
      $now_time_end = date('H:i',strtotime("+".$shipping_time." minutes"));
      $now_time_end = str_replace(':','',$now_time_end);
      $now_flag = true;
    }

    $shi_time_end_array = array();
    $shi_time_end_array = $shi_time_array;

    foreach($shi_time_array as $s_k=>$s_v){
      $ss_array = array();
      $ss_end_array = array();
      $ss_str = '';
      $ss_array = explode('|',$s_v);
      $ss_end_array = explode('|',$s_v);

      foreach($ss_array as $ss_k=>$ss_v){

        $now_array = array();
        $now_array = explode(',',$ss_v);
        $ss_start = str_replace(':','',$now_array[0]);
        $ss_end = str_replace(':','',$now_array[1]); 

        if($ss_end > $now_time_hour){
           
            unset($ss_end_array[$ss_k]);
        }

        if($ss_start < $now_time || ($now_flag == true && $ss_end > $now_time_end)){
 
          unset($ss_array[$ss_k]);
        }else{
          $now_hour = date('H');
          if($s_k <  $now_hour){

            unset($ss_array[$ss_k]);
          }

        }
      }
      $ss_str = implode('|',$ss_array);
      $ss_end_str = implode('|',$ss_end_array);
      $shi_time_array[$s_k] = $ss_str; 
      $shi_time_end_array[$s_k] = $ss_end_str;
    }    

    foreach($shi_time_array as $shi_k=>$shi_v){

      if($shi_v == ''){

        unset($shi_time_array[$shi_k]);
      }

    }

    foreach($shi_time_end_array as $shi_end_k=>$shi_end_v){
     
       if($shi_end_v == ''){
     
           unset($shi_time_end_array[$shi_end_k]);
       }
     
    }

    $max_time_str = implode('||',array_keys($shi_time_array));
    $min_time_str = implode('||',$shi_time_array);
    $max_time_end_str = implode('||',array_keys($shi_time_end_array));
    $min_time_end_str = implode('||',$shi_time_end_array);
  }
  

  //可配送时间区域
  $work_start = $max_time_str;
  $work_end = $min_time_str;
  $work_start_old = $max_time_str_old;
  $work_end_old = $min_time_str_old;
  $work_start_exit = $max_time_end_str;
  $work_end_exit = $min_time_end_str; 

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

    //判断用户是否是会员
    $quest_query = tep_db_query("select customers_guest_chk from ". TABLE_CUSTOMERS ." where customers_id={$_SESSION['customer_id']}");
    $quest_array = tep_db_fetch_array($quest_query);
    tep_db_free_result($quest_query);
?>
  <tr><td width="70%"><b><?php echo TABLE_ADDRESS_TITLE; ?></b></td></tr> 
  <?php
    if($quest_array['customers_guest_chk'] == 0){
      $address_history_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where customers_id='". $_SESSION['customer_id'] ."'");
      $address_history_num = tep_db_num_rows($address_history_query);
      tep_db_free_result($address_history_query);
      if($address_history_num > 0){

        $address_option_list_flag = 'address_option_list(first_num);';
      }
      if($address_history_num == 0 && !isset($_POST['address_option'])){
        $checked_str_old = '';
        $checked_str_new = 'checked';
  ?>
     <script type="text/javascript">
     $(document).ready(function(){

       address_option_show('new'); 
       session_value();
     }); 
     </script>
  <?php
      }
  ?>
  <tr><td> 
    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea"> 
                      <tr>
                        <td>
                          <table border="0" width="100%" cellspacing="0" cellpadding="2">
                            <tr>
                            <td class="main">
                            <input type="radio" name="address_option" value="old" onClick="address_option_show('old');<?php echo $address_option_list_flag;?>" <?php echo $checked_str_old;?>><?php echo TABLE_OPTION_OLD; ?>
                            <input type="radio" name="address_option" value="new" onClick="address_option_show('new');" <?php echo $checked_str_new;?>><?php echo TABLE_OPTION_NEW; ?> 
                            </td></tr>
                          </table>
                       </td>
                      </tr>
    </table>
  </td></tr>
  <?php 
     }
  ?>
  <tr><td height="6"></td></tr>
          <tr>
            <td>
            <table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea"> 
              <tr>
                        <td>
                          <table border="0" width="100%" cellspacing="0" cellpadding="2" id="address_show">
                          <?php
                            if($quest_array['customers_guest_chk'] == 0){
                          ?>
                          <tr id="address_show_id" style="display:<?php echo $show_flag;?>;">
<td class="main"><?php echo TABLE_ADDRESS_SHOW; ?></td>
<td class="main">
<select name="address_show_list" id="address_show_list" onChange="address_option_list(this.value);">
<option value="">--</option>
</select>
</td></tr>
<?php
                            }
    $hm_option->render('', false, true);
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
			<tr><td class="main"><b><?php echo TABLE_HEADING_SHIPPING_ADDRESS; ?></b></td></tr>

          <tr> 
            <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="formArea"> 
                <tr> 
                  <td>
          
<table width="100%" border="0" cellspacing="0" cellpadding="2">
<?php
if (!isset($torihikihouhou_error)) $torihikihouhou_error = NULL ; //del notice
  if($torihikihouhou_error != '') {
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
  <td class="main" width="30%"><?php echo TEXT_EXPECT_TRADE_DATE; ?></td>
    <td class="main" width="70%">
<?php
    $today = getdate();
    $m_num = $today['mon'];
    $d_num = date('d',strtotime("+".$db_set_day." minutes"));
    $shipping_time = strtotime("+".$shipping_time." minutes");
    $year = $today['year'];
    
    $hours = date('H');
    $mimutes = date('i');
?>
  <select name="date" onChange="selectDate('<?php echo $work_start; ?>', '<?php echo $work_end; ?>',this.value,'<?php echo $work_start_old; ?>','<?php echo $work_end_old; ?>','<?php echo date('Y-m-d');?>','<?php echo $work_start_exit; ?>','<?php echo $work_end_exit; ?>','<?php echo $now_time_date;?>');$('#date_error').remove();">
    <option value=""><?php echo EXPECT_DATE_SELECT;?></option>
    <?php
          $oarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
          $newarr = array(TEXT_DATE_MONDAY, TEXT_DATE_TUESDAY, TEXT_DATE_WEDNESDAY, TEXT_DATE_THURSDAY, TEXT_DATE_FRIDAY, TEXT_DATE_STATURDAY, TEXT_DATE_SUNDAY);
          $date_session_flag = false; 
          $j = 0;
          $j_shipping = time();
  while($j_shipping <= $shipping_time){
    if(!($work_start == '' && $work_end == '' && date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)) == date("Y-m-d"))){
     
     if(!(date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year))== $now_time_date && date('Hi') >= $now_time_hour)){
      if(isset($_POST['date']) && $_POST['date'] != ""){
        $selected_str = date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)) == $_POST['date'] ? 'selected' : ''; 
      }elseif(isset($_SESSION['date']) && $_SESSION['date'] != ''){
        $selected_str = date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)) == $_SESSION['date'] ? 'selected' : '';
      }
      if(date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)) == $_SESSION['date']){

        $date_session_flag = true;
      }
      if(date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)) == $now_time_date && $min_time_end_str == ''){
        break;
      }
      echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)).'" '. $selected_str .'>'.str_replace($oarr, $newarr, date("Y".DATE_YEAR_TEXT."m".DATE_MONTH_TEXT."d".DATE_DAY_TEXT."（l）", mktime(0,0,0,$m_num,$d_num+$j,$year))).'</option>' . "\n";
     }
    }
     $j_shipping += 86400;
     $j++;
     if(date('Y-m-d',$j_shipping) == $now_time_date && $min_time_end_str != ''){

       echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)).'" '. $selected_str .'>'.str_replace($oarr, $newarr, date("Y".DATE_YEAR_TEXT."m".DATE_MONTH_TEXT."d".DATE_DAY_TEXT."（l）", mktime(0,0,0,$m_num,$d_num+$j,$year))).'</option>' . "\n";
       break;
     }
  }
    ?>
  </select>
  </td>
  </tr>
<?php
if (!isset($date_error)) $date_error= NULL ; //del notice
  if($date_error != '') {
?>
  <tr id="date_error">
  <td class="main">&nbsp;</td>
    <td class="main"><?php echo $date_error; ?></td>
  </tr>
<?php
  }
?>
  <tr id="shipping_list" style="display:none;">
  <td class="main"><?php echo TEXT_EXPECT_TRADE_TIME; ?></td>
    <td class="main" id="shipping_list_show">
  </td>
  </tr>
</table>
<table border="0" cellpadding="2" cellspacing="0" style=" position:absolute; width:497px;">
<tr id="shipping_list_min" style="display:none;">
<td class="main" width="146" style="*width:145px;">&nbsp;</td>
 <td class="main" id="shipping_list_show_min">
 </td>
 </tr>
</table>
<table>
<?php
  if((isset($_POST['date']) && $_POST['date'] != '') || (isset($_SESSION['date']) && $_SESSION['date'] != '' && $date_session_flag == true)){
    $post_date = isset($_POST['date']) ? $_POST['date'] : $_SESSION['date'];
    echo '<script>selectDate(\''. $work_start .' \', \''. $work_end .'\',\''.$post_date.'\',\''. $work_start_old .' \', \''. $work_end_old .'\',\''.date('Y-m-d').'\',\''.$work_start_exit.'\',\''.$work_end_exit.'\',\''.$now_time_date.'\');$("#shipping_list").show();</script>';
  }
  if((isset($_POST['min']) && $_POST['min'] != '') || (isset($_SESSION['min']) && $_SESSION['min'] != '' && $date_session_flag == true)){
    $post_hour = isset($_SESSION['hour']) && $_SESSION['hour'] != '' ? $_SESSION['hour'] : $_POST['hour'];
    $post_min = isset($_SESSION['min']) && $_SESSION['min'] != '' ? $_SESSION['min'] : $_POST['min'];
    $ele = isset($_SESSION['ele']) && $_SESSION['ele'] != '' ? $_SESSION['ele'] : $_POST['ele'];
    if(!(date("Y-m-d", strtotime("+".$db_set_day." minutes")) == $post_date)){  
        $work_start = $work_start_old;
        $work_end = $work_end_old;
    }
    $hour_show_flag = false;
    $hour_show_array = explode('||',$work_start);
    if(!in_array($post_hour,$hour_show_array)){

      $hour_show_flag = true;
    }
    if($hour_show_flag == false){
      echo '<script>selectHour(\''. $work_start .' \', \''. $work_end .'\',\''. $post_hour .'\','. $post_min .',\''.$ele.'\');$("#shipping_list_min").show();$("#h_c_'.$post_hour.'").val('.$post_min.');</script>';
    }
  }
  if(isset($time_error) && $time_error != '') {
?>
  <tr id="time_error">
  <td class="main" width="146">&nbsp;</td>
    <td class="main"><?php echo $time_error; ?></td>
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
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="cg_pay_info"> 
                      <tr> 
                        <td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td> 
                        <td class="main" align="right"><?php echo tep_image_submit('button_continue02.gif', IMAGE_BUTTON_CONTINUE); ?></td>
                </tr> 
              </table></td> 
          </tr> 
          <tr> 
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr>  
          </table>      
          </div>
          </form>
          <p class="pageBottom"></p>
      </td>
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> 
      </td> 
    </tr>
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
