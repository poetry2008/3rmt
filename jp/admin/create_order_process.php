<?php
/*
  $Id$
*/
require('includes/application_top.php');
require('includes/step-by-step/new_application_top.php');

//此页能是POST过来 ，如果不是 则 跳转 到 CREATE_ORDER
if(isGet()){
  tep_redirect(tep_redirect(tep_href_link(FILENAME_CREATE_ORDER, null, 'SSL')));
}else{

  if(!$_POST['email_address'] && !isset($_POST['update_products'])){
     
    tep_redirect(tep_redirect(tep_href_link(FILENAME_CREATE_ORDER.'?error=1', null, 'SSL')));
  }

  if(!$_POST['email_address']){

    tep_redirect(tep_redirect(tep_href_link(FILENAME_CREATE_ORDER.'?oID='.tep_db_prepare_input($_POST['oID']), null, 'SSL')));
  }

  if(!isset($_POST['update_products'])){
    
    tep_redirect(tep_redirect(tep_href_link(FILENAME_CREATE_ORDER.'?Customer_mail='.tep_db_prepare_input($_POST['email_address']).'&site_id='.tep_db_prepare_input($_POST['site_id']).'&error=1', null, 'SSL')));
  }else{
    $qty = 0;
    foreach($_POST['update_products'] as $update_value){
      $qty += $update_value['qty']; 
    }

    if($qty == 0){

       
      tep_redirect(tep_redirect(tep_href_link(FILENAME_CREATE_ORDER.'?oID='.tep_db_prepare_input($_POST['oID']).'&Customer_mail='.tep_db_prepare_input($_POST['email_address']).'&site_id='.tep_db_prepare_input($_POST['site_id']).'&error=1', null, 'SSL')));
    } 
  }
}

require(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_CREATE_ORDER_PROCESS);

$oID = tep_db_prepare_input($_POST['oID']);
$customer_id    = tep_db_prepare_input($_POST['customers_id']);
$firstname      = tep_db_prepare_input($_POST['firstname']);
$lastname       = tep_db_prepare_input($_POST['lastname']);
$email_address  = tep_db_prepare_input($_POST['email_address']);

$telephone      = isset($_POST['telephone']) ? tep_db_prepare_input($_POST['telephone']) : '';
$fax            = tep_db_prepare_input($_POST['fax']);
$street_address = isset($_POST['street_address']) ? tep_db_prepare_input($_POST['street_address']) : '';
$company        = isset($_POST['company']) ? tep_db_prepare_input($_POST['company']) : '';
$suburb         = isset($_POST['suburb']) ? tep_db_prepare_input($_POST['suburb']) : '';
$postcode       = isset($_POST['postcode']) ? tep_db_prepare_input($_POST['postcode']) : '';
$city           = isset($_POST['city']) ? tep_db_prepare_input($_POST['city']) : '';
$zone_id        = isset($_POST['zone_id']) ? tep_db_prepare_input($_POST['zone_id']) : '';
$state          = isset($_POST['state']) ? tep_db_prepare_input($_POST['state']) : '';
$country        = isset($_POST['country']) ? tep_db_prepare_input($_POST['country']) : '';

$site_id        = tep_db_prepare_input($_POST['site_id']);
$format_id      = "1";
$size           = "1";
$new_value      = "1";
$error          = false; // reset error flag
$temp_amount    = "0";
$temp_amount    = number_format($temp_amount, 2, '.', '');


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

//开始更新订单

$insert_id = $oID;
$sql_data_array = array('customers_id'                => $customer_id,
			'customers_name'              => tep_get_fullname($firstname,$lastname),
			'customers_company'           => $company,
			'customers_street_address'    => $street_address,
			'customers_suburb'            => $suburb,
			'customers_city'              => $city,
			'customers_postcode'          => $postcode,
			'customers_state'             => $state,
			'customers_country'           => $country,
			'customers_telephone'         => $telephone,
			'customers_email_address'     => $email_address,
			'customers_address_format_id' => $format_id,
			'delivery_company'            => $company,
			'delivery_street_address'     => $street_address,
			'delivery_suburb'             => $suburb,
			'delivery_city'               => $city,
			'delivery_postcode'           => $postcode,
			'delivery_state'              => $state,
			'delivery_country'            => $country,
			'delivery_address_format_id'  => $format_id,
			'billing_name'                => tep_get_fullname($firstname,$lastname),
			'billing_company'             => $company,
			'billing_street_address'      => $street_address,
			'billing_suburb'              => $suburb,
			'billing_city'                => $city,
			'billing_postcode'            => $postcode,
			'billing_state'               => $state,
			'billing_country'             => $country,
			'billing_address_format_id'   => $format_id,
			'orders_status'               => DEFAULT_ORDERS_STATUS_ID,
			'site_id'                     => $site_id,
			'orders_wait_flag'            => '1'
			); 
$_SESSION['payment_bank_info'][$insert_id] = $comment_arr['payment_bank_info'];
if(isset($comment_arr['payment_bank_info']['add_info'])&&
    $comment_arr['payment_bank_info']['add_info']){
$sql_data_array['orders_comment'] = $comment_arr['comment'];
}
//更新订单
tep_db_perform(TABLE_ORDERS, $sql_data_array,'update','orders_id=\''.$oID.'\'');

last_customer_action();
orders_updated($insert_id);

//$sql_data_array = array('orders_id' => $insert_id, 
              //'orders_status_id' => $new_value, 
              //'date_added' => 'now()', 
              //'customer_notified' => '1',
              //'comments' => $comment_arr['comment']);
//tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

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
        if ($products_details["qty"] != $order['products_quantity'] ) {
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
          if(!tep_is_oroshi($check_status['customers_id']))
            tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = ".$pr_quantity.", products_virtual_quantity = ".$pv_quantity.", products_ordered = products_ordered + " . $quantity_difference . " where products_id = '" . (int)$order['products_id'] . "'");
          tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = 0 where products_real_quantity < 0 and products_id = '" . (int)$order['products_id'] . "'");
          tep_db_query("update " . TABLE_PRODUCTS . " set products_virtual_quantity = 0 where products_virtual_quantity < 0 and products_id = '" . (int)$order['products_id'] . "'");
        }

        if($products_details["qty"] > 0) { // a.) quantity found --> add to list & sum    
          $Query = "update " . TABLE_ORDERS_PRODUCTS . " set
            products_model = '" . $products_details["model"] . "',
                           products_name = '" . str_replace("'", "&#39;", $products_details["name"]) . "',
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
              $Query = "update " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set option_info = '" .tep_db_input(serialize($input_option)) . "' where orders_products_attributes_id = '$orders_products_attributes_id';";
              tep_db_query($Query);
            }
          }
        }else{ // b.) null quantity found --> delete
          $Query = "delete from " . TABLE_ORDERS_PRODUCTS . " where orders_products_id
            = '$orders_products_id';";
          tep_db_query($Query);
          $Query = "delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where
            orders_products_id = '$orders_products_id';";
          tep_db_query($Query);
          $products_delete = true;
        }
      }

      $orders_type_str = tep_get_order_type_info($oID);
      tep_db_query("update `".TABLE_ORDERS."` set `orders_type` = '".$orders_type_str."' where orders_id = '".tep_db_input($oID)."'");

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
