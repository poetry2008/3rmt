<?php
/*
   $Id$
*/

  require('includes/application_top.php');
  
  $preorder_id = $_GET['oID'];
  $insert_id = date("Ymd").'-'.date("His").tep_get_preorder_end_num(); 
  
  $preorder_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$preorder_id."'");

  $preorder = tep_db_fetch_array($preorder_raw);
  
  if ($preorder) {
    unset($_SESSION['create_preorder']); 
    $sql_data_array = array('orders_id'     => $insert_id,
            'customers_id'                => $preorder['customers_id'],
            'customers_name'              => $preorder['customers_name'],
            'customers_company'           => $preorder['customers_company'],
            'customers_street_address'    => $preorder['customers_street_address'],
            'customers_suburb'            => $preorder['customers_suburb'],
            'customers_city'              => $preorder['customers_city'],
            'customers_postcode'          => $preorder['customers_postcode'],
            'customers_state'             => $preorder['customers_state'],
            'customers_country'           => $preorder['customers_country'],
            'customers_telephone'         => $preorder['customers_telephone'],
            'customers_email_address'     => $preorder['customers_email_address'],
            'customers_address_format_id' => $preorder['customers_address_format_id'],
            'delivery_company'            => $preorder['delivery_company'],
            'delivery_street_address'     => $preorder['delivery_street_address'],
            'delivery_suburb'             => $preorder['delivery_suburb'],
            'delivery_city'               => $preorder['delivery_city'],
            'delivery_postcode'           => $preorder['delivery_postcode'],
            'delivery_state'              => $preorder['delivery_state'],
            'delivery_country'            => $preorder['delivery_country'],
            'delivery_address_format_id'  => $preorder['delivery_address_format_id'],
            'billing_name'                => $preorder['billing_name'],
            'billing_company'             => $preorder['billing_company'],
            'billing_street_address'      => $preorder['billing_street_address'],
            'billing_suburb'              => $preorder['billing_suburb'],
            'billing_city'                => $preorder['billing_city'],
            'billing_postcode'            => $preorder['billing_postcode'],
            'billing_state'               => $preorder['billing_state'],
            'billing_country'             => $preorder['billing_country'],
            'billing_address_format_id'   => $preorder['billing_address_format_id'],
            'date_purchased'              => 'now()', 
            'orders_status'               => '1',
            'currency'                    => $preorder['currency'],
            'currency_value'              => $preorder['currency_value'],
            'payment_method'              => $preorder['payment_method'],
            'site_id'                     => $preorder['site_id'],
            'is_active'                   => '1',
            'orders_wait_flag'            => '1',
            'cemail_text' => $preorder['cemail_text'],
            'raku_text' => $preorder['raku_text']
            ); 
    
    $predate_arr = explode(' ', $preorder['predate']);
    $sql_data_array['predate'] = $predate_arr[0];
    $sql_data_array['code_fee'] = $preorder['code_fee']; 
   
    $customer_raw = tep_db_query("select customers_fax from ".TABLE_CUSTOMERS." where customers_id = '".$preorder['customers_id']."'");
    
    $customer_res = tep_db_fetch_array($customer_raw);
    
    if ($customer_res) {
      $_SESSION['create_preorder']['customer_fax'] = $customer_res['customers_fax']; 
    } else {
      $_SESSION['create_preorder']['customer_fax'] = ''; 
    }
    
    $_SESSION['create_preorder']['orders'] = $sql_data_array;

    $preorders_total_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$preorder_id."' order by sort_order asc"); 
    while ($preorders_total = tep_db_fetch_array($preorders_total_raw)) {
      $sql_data_array = array('orders_id' => $insert_id,
                              'title' => $preorders_total['title'], 
                              'text' => $preorders_total['text'], 
                              'value' => $preorders_total['value'], 
                              'class' => $preorders_total['class'], 
                              'sort_order' => $preorders_total['sort_order'], 
                             ); 
      $_SESSION['create_preorder']['orders_total'][$preorders_total['class']] = $sql_data_array; 
    }
    $preorders_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$preorder_id."'"); 
    $preorders_product = tep_db_fetch_array($preorders_product_raw);
    
    if ($preorders_product) {
      $_SESSION['create_preorder']['orders_products'][$preorders_product['products_id']] = array(
          'orders_id' => $insert_id,
          'products_id' => $preorders_product['products_id'],
          'products_model' => $preorders_product['products_model'],
          'products_name' => $preorders_product['products_name'],
          'products_character' => $preorders_product['products_character'],
          'products_price' => $preorders_product['products_price'],
          'final_price' => $preorders_product['final_price'],
          'products_tax' => $preorders_product['products_tax'],
          'site_id' => $preorders_product['site_id'],
          'products_rate' => $preorders_product['products_rate'],
          'products_quantity' => $preorders_product['products_quantity'],
          ); 
    }
    
    tep_redirect(tep_href_link('edit_new_preorders.php', 'oID='.$insert_id.'&action=edit&dtype=1'));
  } else {
    tep_redirect(tep_href_link('final_preorders.php', 'oID='.$_GET['oID'].'&action=edit'));
  }
