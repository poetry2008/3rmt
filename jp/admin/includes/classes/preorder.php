<?php

  class preorder {
    var $info, $totals, $products, $customer, $delivery;
/*-----------------------
 功能：查询预约
 参数：$order_id(number) 订单编号
 返回值：无
 ----------------------*/
    function preorder($order_id) {
      $this->info     = array();
      $this->totals   = array();
      $this->products = array();
      $this->customer = array();
      $this->delivery = array();

      $this->query($order_id);
    }
/*---------------------------
 功能：预约订单的详细信息
 参数：$order_id(number) 订单编号
 返回值：无
 --------------------------*/
    function query($order_id) {
      $order_query = tep_db_query("
        select * 
        from " . TABLE_PREORDERS . " 
        where orders_id = '" . tep_db_input($order_id) . "'
      ");
      $order = tep_db_fetch_array($order_query);

      $totals_query = tep_db_query("
        select title, text,value  
        from " . TABLE_PREORDERS_TOTAL . " 
        where orders_id = '" . tep_db_input($order_id) . "' 
        order by sort_order
      ");
      while ($totals = tep_db_fetch_array($totals_query)) {
        $this->totals[] = array('title' => $totals['title'],
                                'value' => $totals['value'],
                                'text'  => $totals['text']);
      }
    
      $this->tori = array('Bahamut' => $order['torihiki_Bahamut'],
                          'houhou'  => $order['torihiki_houhou'],
                          'date'    => $order['torihiki_date']);

      $this->info = array('currency'              => $order['currency'],
                          'currency_value'        => $order['currency_value'],
                          'payment_method'        => $order['payment_method'],
                          'cc_type'               => $order['cc_type'],
                          'cc_owner'              => $order['cc_owner'],
                          'cc_number'             => $order['cc_number'],
                          'cc_expires'            => $order['cc_expires'],
                          'date_purchased'        => $order['date_purchased'],
                          'orders_status'         => $order['orders_status'],
                          'orders_id'             => tep_db_input($order_id),
                          'code_fee'              => tep_db_input($order['code_fee']),
                          'site_id'               => tep_db_input($order['site_id']),
                          'orders_ip'             => $order['orders_ip'],
                          'orders_host_name'      => $order['orders_host_name'],
                          'orders_user_agent'     => $order['orders_user_agent'],
                          'orders_ref'            => $order['orders_ref'],
                          'orders_ref_keywords'   => $order['orders_ref_keywords'],
                          'orders_comment'        => $order['orders_comment'],
                          'orders_important_flag' => $order['orders_important_flag'],
                          'orders_care_flag'      => $order['orders_care_flag'],
                          'orders_wait_flag'      => $order['orders_wait_flag'],
                          'orders_inputed_flag'   => $order['orders_inputed_flag'],
                          'orders_http_accept_language' => $order['orders_http_accept_language'],
                          'orders_system_language'      => $order['orders_system_language'],
                          'orders_user_language'        => $order['orders_user_language'],
                          'orders_work'                 => $order['orders_work'],
        
                          'orders_screen_resolution'    => $order['orders_screen_resolution'],
                          'orders_color_depth'          => $order['orders_color_depth'],
                          'orders_flash_enable'         => $order['orders_flash_enable'],
                          'orders_flash_version'        => $order['orders_flash_version'],
                          'orders_director_enable'      => $order['orders_director_enable'],
                          'orders_quicktime_enable'     => $order['orders_quicktime_enable'],
                          'orders_realplayer_enable'    => $order['orders_realplayer_enable'],
                          'orders_windows_media_enable' => $order['orders_windows_media_enable'],
                          'orders_pdf_enable'           => $order['orders_pdf_enable'],
                          'orders_java_enable'          => $order['orders_java_enable'],
                            
                          'telecom_name'      => $order['telecom_name'],
                          'telecom_tel'       => $order['telecom_tel'],
                          'telecom_email'     => $order['telecom_email'],
                          'telecom_money'     => $order['telecom_money'],
                          'telecom_clientip'  => $order['telecom_clientip'],
                          'telecom_option'    => $order['telecom_option'],
                          'telecom_cont'      => $order['telecom_cont'],
                          'telecom_sendid'    => $order['telecom_sendid'],

                          'last_modified'     => $order['last_modified'],
'paypal_paymenttype' => $order['paypal_paymenttype'],
'paypal_payerstatus' => $order['paypal_payerstatus'],
'paypal_paymentstatus' => $order['paypal_paymentstatus'],
'paypal_countrycode' => $order['paypal_countrycode'],
'ensure_deadline'=> $order['ensure_deadline'],
'predate'=> $order['predate'],
'flag_qaf' => $order['flag_qaf']);

      $this->customer = array('name'           => $order['customers_name'],
                              'id'             => $order['customers_id'],
                              'name_f'         => $order['customers_name_f'],
                              'company'        => $order['customers_company'],
                              'street_address' => $order['customers_street_address'],
                              'suburb'         => $order['customers_suburb'],
                              'city'           => $order['customers_city'],
                              'postcode'       => $order['customers_postcode'],
                              'state'          => $order['customers_state'],
                              'country'        => $order['customers_country'],
                              'format_id'      => $order['customers_address_format_id'],
                              'telephone'      => $order['customers_telephone'],
                              'email_address'  => $order['customers_email_address'],
                              //'fax'            => $order['customers_fax'],
                              'date'           => $order['date_purchased'],
'paypal_paymenttype' => $order['paypal_paymenttype'],
'paypal_payerstatus' => $order['paypal_payerstatus'],
'paypal_paymentstatus' => $order['paypal_paymentstatus'],
'paypal_countrycode' => $order['paypal_countrycode']);

      $this->delivery = array('name'           => $order['delivery_name'],
                              'name_f'         => $order['delivery_name_f'],
                              'company'        => $order['delivery_company'],
                              'street_address' => $order['delivery_street_address'],
                              'suburb'         => $order['delivery_suburb'],
                              'city'           => $order['delivery_city'],
                              'postcode'       => $order['delivery_postcode'],
                              'state'          => $order['delivery_state'],
                              'country'        => $order['delivery_country'],
                              'telephone'      => $order['delivery_telephone'],
                              'format_id'      => $order['delivery_address_format_id'],
                              'date'           => $order['date_purchased']);


      $this->billing = array('name'           => $order['billing_name'],
                             'name_f'         => $order['billing_name_f'],
                             'company'        => $order['billing_company'],
                             'street_address' => $order['billing_street_address'],
                             'suburb'         => $order['billing_suburb'],
                             'city'           => $order['billing_city'],
                             'postcode'       => $order['billing_postcode'],
                             'state'          => $order['billing_state'],
                             'country'        => $order['billing_country'],
                             'telephone'      => $order['billing_telephone'],
                             'format_id'      => $order['billing_address_format_id'],
                             'date'           => $order['date_purchased']);

      $index = 0;
      $orders_products_query = tep_db_query("
        select * 
        from " . TABLE_PREORDERS_PRODUCTS . " 
        where orders_id = '" . tep_db_input($order_id) . "'
      ");
      while ($orders_products = tep_db_fetch_array($orders_products_query)) {
        $this->products[$index] = array('id'          => $orders_products['products_id'],
                                        'qty'         => $orders_products['products_quantity'],
                                        'name'        => $orders_products['products_name'],
                                        'model'       => $orders_products['products_model'],
                                        'tax'         => $orders_products['products_tax'],
                                        'price'       => $orders_products['products_price'],
                                        'final_price' => $orders_products['final_price'],
                                        'rate'        => $orders_products['products_rate'],
                                        //'character'   => $orders_products['products_character']
                                        );

        $subindex = 0;
        $attributes_query = tep_db_query("
          select * from " . TABLE_PREORDERS_PRODUCTS_ATTRIBUTES . " 
          where orders_id = '" . tep_db_input($order_id) . "' 
            and orders_products_id = '" . $orders_products['orders_products_id'] . "'
        ");
        if (tep_db_num_rows($attributes_query)) {
          while ($attributes = tep_db_fetch_array($attributes_query)) {
            $this->products[$index]['attributes'][$subindex] = array(
                         'id'             => $attributes['orders_products_attributes_id'],
                         'option_info'    => @unserialize(stripslashes($attributes['option_info'])),
                         'option_group_id'=> $attributes['option_group_id'],
                         'option_item_id' => $attributes['option_item_id'],
                         'price'          => $attributes['options_values_price']);
            $subindex++;
          }
        }
        $index++;
      }
    }
  }
