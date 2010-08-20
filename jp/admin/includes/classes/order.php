<?php
/*
  JP、GM共通ファイル
*/

  class order {
    var $info, $totals, $products, $customer, $delivery;

    function order($order_id) {
      $this->info = array();
      $this->totals = array();
      $this->products = array();
      $this->customer = array();
      $this->delivery = array();

      $this->query($order_id);
    }

    function query($order_id) {
// 2003-06-06 add_telephone
      $order_query = tep_db_query("select site_id, customers_name, customers_id, customers_name_f, customers_company, customers_street_address, customers_suburb, customers_city, customers_postcode, customers_state, customers_country, customers_telephone, customers_email_address, customers_address_format_id, delivery_name, delivery_name_f, delivery_company, delivery_street_address, delivery_suburb, delivery_city, delivery_postcode, delivery_state, delivery_country, delivery_telephone, delivery_address_format_id, billing_name, billing_name_f, billing_company, billing_street_address, billing_suburb, billing_city, billing_postcode, billing_state, billing_country, billing_telephone, billing_address_format_id, payment_method, cc_type, cc_owner, cc_number, cc_expires, currency, currency_value, date_purchased, orders_status, last_modified, torihiki_Bahamut, torihiki_houhou, torihiki_date, code_fee from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($order_id) . "'");
      $order = tep_db_fetch_array($order_query);

      $totals_query = tep_db_query("select title, text from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . tep_db_input($order_id) . "' order by sort_order");
      while ($totals = tep_db_fetch_array($totals_query)) {
        $this->totals[] = array('title' => $totals['title'],
                                'text' => $totals['text']);
      }
    
    $this->tori = array('Bahamut' => $order['torihiki_Bahamut'],
                        'houhou' => $order['torihiki_houhou'],
              'date' => $order['torihiki_date']
              );

      $this->info = array('currency' => $order['currency'],
                          'currency_value' => $order['currency_value'],
                          'payment_method' => $order['payment_method'],
                          'cc_type' => $order['cc_type'],
                          'cc_owner' => $order['cc_owner'],
                          'cc_number' => $order['cc_number'],
                          'cc_expires' => $order['cc_expires'],
                          'date_purchased' => $order['date_purchased'],
                          'orders_status' => $order['orders_status'],
                          'orders_id' => tep_db_input($order_id),
                          'code_fee' => tep_db_input($order['code_fee']),
                          'site_id' => tep_db_input($order['site_id']),
                          'last_modified' => $order['last_modified']);

      $this->customer = array('name' => $order['customers_name'],
                              'id' => $order['customers_id'],
                              'name_f' => $order['customers_name_f'],
                              'company' => $order['customers_company'],
                              'street_address' => $order['customers_street_address'],
                              'suburb' => $order['customers_suburb'],
                              'city' => $order['customers_city'],
                              'postcode' => $order['customers_postcode'],
                              'state' => $order['customers_state'],
                              'country' => $order['customers_country'],
                              'format_id' => $order['customers_address_format_id'],
                              'telephone' => $order['customers_telephone'],
                              'email_address' => $order['customers_email_address'],
                'date' => $order['date_purchased']);

// 2003-06-06 add_telephone
      $this->delivery = array('name' => $order['delivery_name'],
                              'name_f' => $order['delivery_name_f'],
                'company' => $order['delivery_company'],
                              'street_address' => $order['delivery_street_address'],
                              'suburb' => $order['delivery_suburb'],
                              'city' => $order['delivery_city'],
                              'postcode' => $order['delivery_postcode'],
                              'state' => $order['delivery_state'],
                              'country' => $order['delivery_country'],
                              'telephone' => $order['delivery_telephone'],
                              'format_id' => $order['delivery_address_format_id'],
                'date' => $order['date_purchased']);

// 2003-06-06 add_telephone
      $this->billing = array('name' => $order['billing_name'],
                             'name_f' => $order['billing_name_f'],
               'company' => $order['billing_company'],
                             'street_address' => $order['billing_street_address'],
                             'suburb' => $order['billing_suburb'],
                             'city' => $order['billing_city'],
                             'postcode' => $order['billing_postcode'],
                             'state' => $order['billing_state'],
                             'country' => $order['billing_country'],
                             'telephone' => $order['billing_telephone'],
                             'format_id' => $order['billing_address_format_id'],
               'date' => $order['date_purchased']);

      $index = 0;
      $orders_products_query = tep_db_query("select products_id, orders_products_id, products_name, products_model, products_price, products_tax, products_quantity, final_price, products_character from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($order_id) . "'");
      while ($orders_products = tep_db_fetch_array($orders_products_query)) {
        $this->products[$index] = array('id' => $orders_products['products_id'],
                                        'qty' => $orders_products['products_quantity'],
                                        'name' => $orders_products['products_name'],
                                        'model' => $orders_products['products_model'],
                                        'tax' => $orders_products['products_tax'],
                                        'price' => $orders_products['products_price'],
                                        'final_price' => $orders_products['final_price'],
                    'character' => $orders_products['products_character']);

        $subindex = 0;
        $attributes_query = tep_db_query("select orders_products_attributes_id, attributes_id, products_options, products_options_values, options_values_price, price_prefix from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . tep_db_input($order_id) . "' and orders_products_id = '" . $orders_products['orders_products_id'] . "'");
        if (tep_db_num_rows($attributes_query)) {
          while ($attributes = tep_db_fetch_array($attributes_query)) {
            // maker 2009-4-14
            $option = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS_OPTIONS." where products_options_name='".$attributes['products_options']."'"));
            $value  = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS_OPTIONS_VALUES." where products_options_values_name='".$attributes['products_options_values']."'"));
            
            $this->products[$index]['attributes'][$subindex] = array(
                                                                     'id' => $attributes['orders_products_attributes_id'],
                                                                     'attributes_id' => $attributes['attributes_id'],
                                                                     'option' => $option['products_options_name'],
                                           'option_id' => $option['products_options_id'],
                                                                     'value' => $attributes['products_options_values'],
                                           'value_id' => $value['products_options_values_id'],
                                                                     'prefix' => $attributes['price_prefix'],
                                                                     'price' => $attributes['options_values_price']);

            $subindex++;
          }
        }
        $index++;
      }
    }
  }
?>
