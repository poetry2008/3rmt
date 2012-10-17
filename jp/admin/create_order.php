<?php
require_once('includes/application_top.php');
require_once('includes/step-by-step/new_application_top.php');
require(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_CREATE_ORDER);
include(DIR_WS_CLASSES . 'order.php');
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies(2);
$action = tep_db_prepare_input($_GET['action']);
$step = tep_db_prepare_input($_POST['step']);
$oID = tep_db_prepare_input($_GET['oID']); 
$Customer_mail = tep_db_prepare_input($_GET['Customer_mail']);
$site_id = tep_db_prepare_input($_GET['site_id']);
if(isset($Customer_mail) && $Customer_mail != '' && isset($site_id) && $site_id != ''){

  $param_str = "&Customer_mail=$Customer_mail&site_id=$site_id";
}
switch($action){
case 'add_product':
        if($step == 5)
        {
          // 2.1 GET ORDER INFO #####
          $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>", "'", "\"");

          $oID = tep_db_prepare_input($_GET['oID']);
          $order = new order($oID);
          $payment_modules = payment::getInstance($order->info['site_id']);

          $AddedOptionsPrice = 0;
                    
          foreach ($_POST as $op_key => $op_value) {
            $op_pos = substr($op_key, 0, 3);
            if ($op_pos == 'op_') {
              $op_tmp_value = str_replace(' ', '', $op_value);
              $op_tmp_value = str_replace('　', '', $op_value);
              if ($op_tmp_value == '') {
                continue; 
              }
              $op_info_array = explode('_', $op_key);
              $op_item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_info_array[1]."' and id = '".$op_info_array[3]."'");
              $op_item_res = tep_db_fetch_array($op_item_query);
              if ($op_item_res) {
                if ($op_item_res['type'] == 'radio') {
                  $o_option_array = @unserialize($op_item_res['option']);
                  if (!empty($o_option_array['radio_image'])) {
                    foreach ($o_option_array['radio_image'] as $or_key => $or_value) {
                      if (trim(str_replace($replace_arr, '', nl2br(stripslashes($or_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_value))))) {
                        $AddedOptionsPrice += $or_value['money'];
                      }
                    }
                  }
                } else {
                  $AddedOptionsPrice += $op_item_res['price'];
                }
              }
            }
          }
          // 2.1.1 Get Product Attribute Info
          // 2.1.2 Get Product Info
          $InfoQuery = "
            select p.products_model, 
                   p.products_price, 
                   pd.products_name, 
                   p.products_tax_class_id, 
                   p.products_small_sum,
                   p.products_price_offset
                     from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on pd.products_id=p.products_id 
                     where p.products_id='$add_product_products_id' 
                     and pd.site_id = '0'
                     and pd.language_id = '" . (int)$languages_id . "'";
          $result = tep_db_query($InfoQuery);

          $row = tep_db_fetch_array($result);
          extract($row, EXTR_PREFIX_ALL, "p");
          
          $add_product_price = (int)$_POST['add_product_price'];
          $p_products_price = $add_product_price;
          // 特価を適用
          $p_products_price = tep_get_final_price($p_products_price, $p_products_price_offset, $p_products_small_sum, (int)$add_product_quantity);

          // Following functions are defined at the bottom of this file
          $CountryID = tep_get_country_id($order->delivery["country"]);
          $ZoneID = tep_get_zone_id($CountryID, $order->delivery["state"]);

          $ProductsTax = tep_get_tax_rate($p_products_tax_class_id, $CountryID, $ZoneID);
          
          // 2.2 UPDATE ORDER #####
          $Query = "insert into " . TABLE_ORDERS_PRODUCTS . " set
            orders_id = '$oID',
                      products_id = $add_product_products_id,
                      products_model = '$p_products_model',
                      products_name = '" . str_replace("'", "&#39;", $p_products_name) . "',
                      products_price = '$p_products_price',
                      final_price = '" . ($p_products_price + $AddedOptionsPrice) . "',
                      products_tax = '$ProductsTax',
                      site_id = '".tep_get_site_id_by_orders_id($oID)."',
                      products_rate = '".tep_get_products_rate($add_product_products_id)."',
                      products_quantity = '" . (int)$add_product_quantity . "';";
          tep_db_query($Query);
          $new_product_id = tep_db_insert_id();


          orders_updated($oID);


          // 2.2.1 Update inventory Quantity
          $p = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$add_product_products_id."'"));
          /* 
          if ((int)$add_product_quantity > $p['products_real_quantity']) {
            // 买取商品大于实数
            tep_db_perform('products',array(
                  //'products_quantity' => $p['products_quantity'] - (int)$add_product_quantity,
                  'products_real_quantity' => 0,
                  // 'products_virtual_quantity' => $p['products_virtual_quantity'] - ((int)$add_product_quantity - $p['products_real_quantity'])
                  'products_virtual_quantity' => $p['products_virtual_quantity'] - (int)$add_product_quantity + $p['products_real_quantity']
                  ),
                'update',
                "products_id = '" . $add_product_products_id . "'");
          } else {
            tep_db_perform('products',array(
                  //'products_quantity' => $p['products_quantity'] - (int)$add_product_quantity,
                  'products_real_quantity' => $p['products_real_quantity'] - (int)$add_product_quantity
                  // 'products_real_quantity' => $p['products_virtual_quantity'] - (int)$add_product_quantity
                  ),
                'update',
                "products_id = '" . $add_product_products_id . "'");
          }
          // 增加销售量
          tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . (int)$add_product_quantity . " where products_id = '" . $add_product_products_id . "'");
          // 处理负数问题
          //tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = 0 where products_quantity < 0 and products_id = '" . $add_product_products_id . "'");
          tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = 0 where products_real_quantity < 0 and products_id = '" . $add_product_products_id . "'");
          tep_db_query("update " . TABLE_PRODUCTS . " set products_virtual_quantity = 0 where products_virtual_quantity < 0 and products_id = '" . $add_product_products_id . "'");
          */ 
            foreach($_POST as $op_i_key => $op_i_value) {
              $op_pos = substr($op_i_key, 0, 3);
              if ($op_pos == 'op_') {
                $op_i_tmp_value = str_replace(' ', '', $op_i_value);
                $op_i_tmp_value = str_replace('　', '', $op_i_value);
                if ($op_i_tmp_value == '') {
                  continue; 
                }
                $i_op_array = explode('_', $op_i_key);
                $ioption_item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$i_op_array[1]."' and id = '".$i_op_array[3]."'"); 
                $ioption_item_res = tep_db_fetch_array($ioption_item_query);
                if ($ioption_item_res) {
                  $input_option_array = array('title' => $ioption_item_res['front_title'], 'value' => str_replace("<BR>", "<br>", stripslashes($op_i_value))); 
                  $op_price = 0; 
                  if ($ioption_item_res['type'] == 'radio') {
                    $io_option_array = @unserialize($ioption_item_res['option']);
                    if (!empty($io_option_array['radio_image'])) {
                      foreach ($io_option_array['radio_image'] as $ior_key => $ior_value) {
                        if (trim(str_replace($replace_arr, '', nl2br(stripslashes($ior_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_i_value))))) {
                          $op_price = $ior_value['money']; 
                          break; 
                        }
                      }
                    } 
                  } else {
                    $op_price = $ioption_item_res['price']; 
                  }
                  $Query = "insert into " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set
                    orders_id = '$oID',
                              orders_products_id = $new_product_id,
                              options_values_price = '" .  tep_db_input($op_price) . "',
                              option_group_id = '" .  $ioption_item_res['group_id'] . "',
                              option_item_id = '" .  $ioption_item_res['id'] . "',
                              option_info = '".tep_db_input(serialize($input_option_array))."';";
                  tep_db_query($Query);
                }
              }
            }
          // 2.2.2 Calculate Tax and Sub-Totals
          // TOTAL START
      $order_total_query = tep_db_query("select * from ". TABLE_ORDERS_TOTAL ." where orders_id='".$oID."'");
      if(!tep_db_num_rows($order_total_query)){
          //require(DIR_FS_CATALOG . 'includes/classes/order.php');
          $order = new order($oID);
          //require(DIR_WS_CLASSES . 'currencies.php');
          $currencies = new currencies;
          require(DIR_WS_CLASSES . 'order_total.php');
          $order_total = new order_total($site_id);
          $order_total_array = $order_total->process();
          $order_totals = $order_total_array;
          for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
          $sql_data_array = array('orders_id' => $oID,
			  'title' => $order_totals[$i]['title'],
			  'value' => $order_totals[$i]['value'], 
			  'text'=> "",
			  'class' => $order_totals[$i]['code'], 
			  'sort_order' => $order_totals[$i]['sort_order']);
         tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
         }
  
         if($ot_tax_status == true) {
           include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/' . $module_type . '/ot_tax.php');
           include($module_directory . 'ot_tax.php');
           $ot_tax = new ot_tax;
           $sql_data_array = array('orders_id' => $oID,
			  'title' => $ot_tax->title,
			  'value' => 0, 
			  'text' => "",
			  'class' => $ot_tax->code, 
			  'sort_order' => $ot_tax->sort_order);
          tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
         }
      }
      tep_db_free_result($order_total_query);

          // TOTAL END
          $order = new order($oID);
          $RunningSubTotal = 0;
          $RunningTax = 0;
          for ($i=0; $i<sizeof($order->products); $i++) {
            if (DISPLAY_PRICE_WITH_TAX == 'true') {
              $RunningSubTotal += (tep_add_tax(($order->products[$i]['qty'] * $order->products[$i]['final_price']), $order->products[$i]['tax']));
            } else {
              $RunningSubTotal += ($order->products[$i]['qty'] * $order->products[$i]['final_price']);
            }

            $RunningTax += (($order->products[$i]['tax'] / 100) * ($order->products[$i]['qty'] * $order->products[$i]['final_price']));     
          }


          $new_subtotal = $RunningSubTotal;
          $new_tax = $RunningTax;
          //subtotal
          /*
             , text = '".$currencies->format($new_subtotal, true, $order->info['currency'])."'
           */
          tep_db_query("update " . TABLE_ORDERS_TOTAL . " set value = '".$new_subtotal."' where class='ot_subtotal' and orders_id = '".$oID."'");

          //tax
          $plustax_query = tep_db_query("select count(*) as cnt from " . TABLE_ORDERS_TOTAL . " where class = 'ot_tax' and orders_id = '".$oID."'");
          $plustax = tep_db_fetch_array($plustax_query);
          if($plustax['cnt'] > 0) {
            /*
               , text = '".tep_insert_currency_text($currencies->format($new_tax, true, $order->info['currency']))."'
             */
            tep_db_query("update " . TABLE_ORDERS_TOTAL . " set value = '".tep_insert_currency_value($new_tax)."' where class='ot_tax' and orders_id = '".$oID."'");
          }

          //total
          $total_query = tep_db_query("select sum(value) as total_value from " . TABLE_ORDERS_TOTAL . " where class != 'ot_total' and orders_id = '".$oID."'");
          $total_value = tep_db_fetch_array($total_query);

          if($plustax['cnt'] == 0) {
            $newtotal = $total_value["total_value"] + $new_tax;
          } else {
            if(DISPLAY_PRICE_WITH_TAX == 'true') {
              $newtotal = $total_value["total_value"] - $new_tax;
            } else {
              $newtotal = $total_value["total_value"];
            }
          }
          //$handle_fee = new_calc_handle_fee($order->info['payment_method'], $newtotal, $oID);
          //$handle_fee = $payment_modules->handle_calc_fee(payment::changeRomaji($order->info['payment_method'],PAYMENT_RETURN_TYPE_CODE), $newtotal);

          $newtotal = $newtotal+$handle_fee+$shipping_fee;    
          /*
             , text = '<b>".$currencies->ot_total_format(intval(floor($newtotal)), true, $order->info['currency'])."</b>'
           */
          $totals = "update " . TABLE_ORDERS_TOTAL . " set value = '".intval(floor($newtotal))."' where class='ot_total' and orders_id = '".$oID."'";
          tep_db_query($totals);
          // shipping total
          $update_orders_sql = "update ".TABLE_ORDERS." set code_fee = '".$handle_fee."' where orders_id = '".$oID."'";
          tep_db_query($update_orders_sql);
          $_SESSION['orders_update_products']['ot_subtotal'] += tep_add_tax(($p_products_price + $AddedOptionsPrice)*(int)$add_product_quantity,$ProductsTax);
          $_SESSION['orders_update_products']['ot_total'] += tep_add_tax(($p_products_price + $AddedOptionsPrice)*(int)$add_product_quantity,$ProductsTax);
          tep_redirect(tep_href_link("create_order.php?oID=$oID$param_str",''));
        }
        break;

      }

$order_exists = true;
/*
if (isset($_GET['oID'])) {
    $oID = tep_db_prepare_input($_GET['oID']);

    $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
    if (!tep_db_num_rows($orders_query)) {
      $order_exists = false;
      $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
    }
}
*/
if(isset($_SESSION['payment_bank_info'])){
  unset($_SESSION['payment_bank_info']); 
}
//处理本身表单 查找customer{{
if(isset($_GET['site_id']) and isset($_GET['Customer_mail'] )){
  $email = $_GET['Customer_mail'];
  $site_id = $_GET['site_id'];
  $customerId = tep_get_customer_id_by_email($email,$site_id);
  

  if(!$customerId){
    //如果不存在则跳转到新建用户的页面
    tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT, 'site_id='.$site_id.'email_address=' . $email, 'SSL'));
  }
}
//}}
//{{列出下一页面需要用的变量
if(!isset($customerId)){
  if(isset($_POST['customers_id']) && $_POST['customers_id'] != ''){
    $customerId = $_POST['customers_id'];
  }
}
if(isset($customerId)){
$lastemail      = $email;
$account        = tep_get_customer_by_id($customerId);
$address        = tep_get_address_by_cid($customer);
  }
$customer_id    = isset($account['customers_id'])           ? $account['customers_id']:'';  //d
$firstname      = isset($account['customers_firstname'])    ? $account['customers_firstname']:'';//d
$lastname       = isset($account['customers_lastname'])     ? $account['customers_lastname']:'';//d
$email_address  = isset($account['customers_email_address'])? $account['customers_email_address']:'';//d
$telephone      = isset($account['customers_telephone'])    ? $account['customers_telephone']:'';//n
$fax            = isset($account['customers_fax'])          ? $account['customers_fax']:'';//n
$zone_id        = isset($account['entry_zone_id'])          ? $account['entry_zone_id']:'';//n
//$site_id        = isset($account['site_id'])                ? $account['site_id']:'';
$street_address = isset($address['entry_street_address'])   ? $address['entry_street_address']:'';//n
$company        = isset($address['entry_company'])          ? $address['entry_company']:'';//n
$suburb         = isset($address['entry_suburb'])           ? $address['entry_suburb']:'';//n
$postcode       = isset($address['entry_postcode'])         ? $address['entry_postcode']:'';//n
$city           = isset($address['entry_city'])             ? $address['entry_city']:'';//n
$state          = isset($address['entry_zone_id'])          ? tep_get_zone_name($address['entry_zone_id']):'';//n
$country        = isset($address['entry_country_id'])       ? tep_get_country_name($address['entry_country_id']):'';//n
$customers_guest_chk = isset($account['customers_guest_chk']) ? $account['customers_guest_chk'] : '';

$cpayment = payment::getInstance((int)$_GET['site_id']);
$payment_array = payment::getPaymentList();
if(!isset($selections)){
$selections = $cpayment->admin_selection();
}
$payment_list[] = array('id' => 'payment_null', 'text' => TEXT_PAYMENT_NULL_TXT);
//}}
require_once(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_CREATE_ORDER);
require_once("includes/step-by-step/create_order_new_first.php");
?>
</script>


