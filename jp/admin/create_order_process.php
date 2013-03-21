<?php
/*
  $Id$
*/
require('includes/application_top.php');
require('includes/step-by-step/new_application_top.php');

//此页能是POST过来 ，如果不是 则 跳转 到 CREATE_ORDER
if(isGet()){
  tep_redirect(tep_redirect(tep_href_link(FILENAME_CREATE_ORDER, null, 'SSL')));
}

require(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_CREATE_ORDER_PROCESS);

$oID = tep_db_prepare_input($_POST['oID']);
$_SESSION['oID'] = $oID;
$customer_id    = tep_db_prepare_input($_POST['customers_id']);
$_SESSION['customer_id'] = $customer_id;
$firstname      = tep_db_prepare_input($_POST['firstname']);
$_SESSION['firstname'] = $firstname;
$lastname       = tep_db_prepare_input($_POST['lastname']);
$_SESSION['lastname'] = $lastname;
$email_address  = tep_db_prepare_input($_POST['email_address']);
$_SESSION['email_address'] = $email_address;
$telephone      = isset($_POST['telephone']) ? tep_db_prepare_input($_POST['telephone']) : '';
$_SESSION['telephone'] = $telephone;
$fax            = tep_db_prepare_input($_POST['fax']);
$_SESSION['fax'] = $fax;
$street_address = isset($_POST['street_address']) ? tep_db_prepare_input($_POST['street_address']) : '';
$_SESSION['street_address'] = $street_address;
$company        = isset($_POST['company']) ? tep_db_prepare_input($_POST['company']) : '';
$_SESSION['company'] = $company;
$suburb         = isset($_POST['suburb']) ? tep_db_prepare_input($_POST['suburb']) : '';
$_SESSION['suburb'] = $suburb;
$postcode       = isset($_POST['postcode']) ? tep_db_prepare_input($_POST['postcode']) : '';
$_SESSION['postcode'] = $postcode;
$city           = isset($_POST['city']) ? tep_db_prepare_input($_POST['city']) : '';
$_SESSION['city'] = $city;
$zone_id        = isset($_POST['zone_id']) ? tep_db_prepare_input($_POST['zone_id']) : '';
$_SESSION['zone_id'] = $zone_id;
$state          = isset($_POST['state']) ? tep_db_prepare_input($_POST['state']) : '';
$_SESSION['state'] = $state;
$country        = isset($_POST['country']) ? tep_db_prepare_input($_POST['country']) : '';
$_SESSION['country'] = $country;
$site_id        = tep_db_prepare_input($_POST['site_id']);
$_SESSION['sites_id_flag'] = $site_id;
$format_id      = "1";
$_SESSION['format_id'] = $format_id;
$size           = "1";
$_SESSION['size'] = $size;
$new_value      = "1";
$_SESSION['new_value'] = $new_value;
$error          = false; // reset error flag
$temp_amount    = "0";
$temp_amount    = number_format($temp_amount, 2, '.', '');
$_SESSION['temp_amount'] = $temp_amount;
$currency_text  = DEFAULT_CURRENCY . ",1";
$currency_array = explode(",", $currency_text);
$currency = $currency_array[0];
$currency_value = $currency_array[1];
$_SESSION['currency'] = $currency;
$_SESSION['currency_value'] = $currency_value;


//{{检查信息是否全
/*
  需要检查的信息有
  1.customer_id
  2.firstname
  3.lastname
  4.email_address
  5.site_id
  6.payment 支付方法,支付方法对应信息
  7.取引方法将不会被支持
*/

if(!$_POST['email_address'] && !isset($_POST['update_products'])){

  $error = true;
  $products_error = true;
  $customer_error = true;  
}

if(!$_POST['email_address']){

  $error = true;
  $customer_error = true;
}

if(!isset($_POST['update_products'])){
  
  $error = true;
  $products_error = true;  
}else{
    $qty = 0;
    $weight_count = 0;
    foreach($_POST['update_products'] as $update_key=>$update_value){
      $orders_products_query = tep_db_query("select products_id from ". TABLE_ORDERS_PRODUCTS ." where orders_products_id='". $update_key ."'");
      $orders_products_array = tep_db_fetch_array($orders_products_query);
      tep_db_free_result($orders_products_query);
      $products_weight_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id='". $orders_products_array['products_id'] ."'");
      $products_weight_array = tep_db_fetch_array($products_weight_query);
      $weight_count += $products_weight_array['products_weight'] * $update_value['qty'];
      tep_db_free_result($products_weight_query);
      $qty += $update_value['qty']; 

      if($update_value['qty'] == 0){

        tep_db_query("delete from ". TABLE_ORDERS_PRODUCTS ." where orders_products_id='". $update_key ."'");
      }
    }

    if($qty == 0){
      $error = true;
      $products_error = true;   
    } 
  }

//v customer_id
if($customer_id == '') {
  $error = true;
} elseif(!is_numeric($customer_id)) {
  $error = true;
}

//v firstname
if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
  $error = true;
  $entry_firstname_error = true;
} else {
  $entry_firstname_error = false;
}
//v lastname
if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
  $error = true;
  $entry_lastname_error = true;
} else {
  $entry_lastname_error = false;
}
//v email_address
if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
  $error = true;
  $entry_email_address_error = true;
} else {
  $entry_email_address_error = false;
}
// v email_address
if (!tep_validate_email($email_address)) {
  $error = true;
  $entry_email_address_check_error = true;
} else {
  $entry_email_address_check_error = false;
}

//如果不通过 或是 有其它错误
if ($error){
  require_once 'create_order.php';
  exit(); }
$insert_id = $oID;
$payment_bank_info = array(); 

// 1.3.1 Update orders_products Table
      $products_delete = false;
      foreach ($update_products as $orders_products_id => $products_details) {
        // 1.3.1.1 Update Inventory Quantity
        $op_query = tep_db_query("
            select products_id, 
            products_quantity
            from " . TABLE_ORDERS_PRODUCTS . " 
            where orders_id = '" . tep_db_input($oID) . "'
            and orders_products_id='".$orders_products_id."'
            ");
        $order = tep_db_fetch_array($op_query);
        if ($products_details["qty"] != $order['products_quantity'] && $products_details["qty"] != 0) {
          $quantity_difference = ($products_details["qty"] - $order['products_quantity']);
          $p = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$order['products_id']."'"));
          $pr_quantity = $p['products_real_quantity'];
          $pv_quantity = $p['products_virtual_quantity'];
          // 增加库存
          if($quantity_difference < 0){
            if ($_POST['update_products_real_quantity'][$orders_products_id]) {
              // 增加实数
              $pr_quantity = $pr_quantity - $quantity_difference;
            } else {
              // 增加架空
              $pv_quantity = $pv_quantity - $quantity_difference;
            }
            // 减少库存
          } else {
            // 实数卖空
            if ($pr_quantity - $quantity_difference < 0) {
              $pr_quantity = 0;
              $pv_quantity += ($pr_quantity - $quantity_difference);
            } else {
              $pr_quantity -= $quantity_difference;
            }
          }
          // 如果是业者，不更新
        }

        if($products_details["qty"] > 0) { 
          // a.) quantity found --> add to list & sum    
          $Query = "update " . TABLE_ORDERS_PRODUCTS . " set
            products_model = '" . $products_details["model"] . "',
            products_name = '" . str_replace("'", "&#39;", $products_details["name"]) . "',
            products_price = '" .  (tep_check_product_type($orders_products_id) ? 0 - $products_details["p_price"] : $products_details["p_price"]) . "',
            final_price = '" . (tep_get_bflag_by_product_id((int)$order['products_id']) ? 0 - $products_details["final_price"] : $products_details["final_price"]) . "',
                           products_tax = '" . $products_details["tax"] . "',
                           products_quantity = '" . $products_details["qty"] . "'
                             where orders_products_id = '$orders_products_id';";
          tep_db_query($Query);

          $RunningSubTotal += $products_details["qty"] * $products_details["final_price"]; // version WITHOUT tax
          $RunningTax += (($products_details["tax"]/100) * ($products_details["qty"] * $products_details["final_price"]));

          // Update Any Attributes
          if (IsSet($products_details[attributes])) {
            foreach ($products_details["attributes"] as $orders_products_attributes_id => $attributes_details) {
              $input_option = array('title' => $attributes_details['option'], 'value'=> $attributes_details['value']); 
              $Query = "update " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set option_info = '" .tep_db_input(serialize($input_option)) . "',options_values_price = '".$attributes_details['price']."' where orders_products_attributes_id = '$orders_products_attributes_id';";
              tep_db_query($Query);
            }
          }
        }else{ 
          // b.) null quantity found --> delete
          $Query = "delete from " . TABLE_ORDERS_PRODUCTS . " where orders_products_id
            = '$orders_products_id';";
          tep_db_query($Query);
          $Query = "delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where
            orders_products_id = '$orders_products_id';";
          tep_db_query($Query);
          $products_delete = true;
        }
      }

      //更新 orders_total 
      $orders_price_total = 0;
      $orders_total_query = tep_db_query("select final_price,products_quantity from ". TABLE_ORDERS_PRODUCTS ." where orders_id='". $oID ."'"); 
      while($orders_total_array = tep_db_fetch_array($orders_total_query)){

        $orders_price_total += $orders_total_array['final_price']*$orders_total_array['products_quantity'];
      }
      tep_db_free_result($orders_total_query);
      tep_db_query("update ". TABLE_ORDERS_TOTAL ." set value=". $orders_price_total ." where orders_id='". $oID ."' and class='ot_total'");
      tep_db_query("update ". TABLE_ORDERS_TOTAL ." set value=". $orders_price_total ." where orders_id='". $oID ."' and class='ot_subtotal'"); 
      tep_redirect(tep_href_link(FILENAME_EDIT_NEW_ORDERS, 'oID=' . $insert_id, 'SSL'));



require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
