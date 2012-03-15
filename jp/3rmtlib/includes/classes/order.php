<?php
/*
  $Id$
*/

  class order {
    var $info, $totals, $products, $customer, $delivery, $content_type;

    function order($order_id = '') {
      $this->info = array();
      $this->totals = array();
      $this->products = array();
      $this->customer = array();
      $this->delivery = array();

      if (tep_not_null($order_id)) {
        $this->query($order_id);
      } else {
        $this->cart();
      }
    }

    function query($order_id) {
      global $languages_id;

      $order_id = tep_db_prepare_input($order_id);
//ccdd
      $order_query = tep_db_query("
          select customers_id, 
                 customers_name, 
                 customers_name_f, 
                 customers_company, 
                 customers_street_address, 
                 customers_suburb, 
                 customers_city, 
                 customers_postcode, 
                 customers_state, 
                 customers_country, 
                 customers_telephone, 
                 customers_email_address, 
                 customers_address_format_id, 
                 delivery_name, 
                 delivery_name_f, 
                 delivery_company, 
                 delivery_street_address, 
                 delivery_suburb, 
                 delivery_city, 
                 delivery_postcode, 
                 delivery_state, 
                 delivery_country, 
                 delivery_telephone, 
                 delivery_address_format_id, 
                 billing_name, 
                 billing_name_f, 
                 billing_company, 
                 billing_street_address, 
                 billing_suburb, 
                 billing_city, 
                 billing_postcode, 
                 billing_state, 
                 billing_country, 
                 billing_telephone, 
                 billing_address_format_id, 
                 payment_method, 
                 cc_type, 
                 cc_owner, 
                 cc_number, 
                 cc_expires, 
                 currency, 
                 currency_value, 
                 date_purchased, 
                 orders_status, 
                 last_modified ,
                 code_fee,
                 shipping_fee
          from " . TABLE_ORDERS . " 
          where orders_id = '" .  tep_db_input($order_id) . "' 
            and site_id = ".SITE_ID
      );
      $order = tep_db_fetch_array($order_query);
//ccdd
      $totals_query = tep_db_query("
          select title, text ,value, class 
          from " . TABLE_ORDERS_TOTAL . " 
          where orders_id = '" . tep_db_input($order_id) . "' 
          order by sort_order
      ");
      while ($totals = tep_db_fetch_array($totals_query)) {
        $this->totals[] = array('title' => $totals['title'],
                                'value' => $totals['value'],
                                'text' => $totals['text'],
                                'class' => $totals['class']);
      }
//ccdd
      $order_total_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $order_id . "' and class = 'ot_total'");
      $order_total = tep_db_fetch_array($order_total_query);
//ccdd
      $shipping_method_query = tep_db_query("select title from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $order_id . "' and class = 'ot_shipping'");
      $shipping_method = tep_db_fetch_array($shipping_method_query);
//ccdd
      $order_status_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . $order['orders_status'] . "' and language_id = '" . $languages_id . "'");
      $order_status = tep_db_fetch_array($order_status_query);

      $this->info = array('currency' => $order['currency'],
                          'currency_value' => $order['currency_value'],
                          'payment_method' => $order['payment_method'],
                          'cc_type' => $order['cc_type'],
                          'cc_owner' => $order['cc_owner'],
                          'cc_number' => $order['cc_number'],
                          'cc_expires' => $order['cc_expires'],
                          'date_purchased' => $order['date_purchased'],
                          'orders_status' => $order_status['orders_status_name'],
                          'last_modified' => $order['last_modified'],
                          'code_fee' => $order['code_fee'],
                          'shipping_fee' => $order['shipping_fee'],
                          'total' => strip_tags($order_total['value']).'円',
                          'shipping_method' => ((substr($shipping_method['title'], -1) == ':') ? substr(strip_tags($shipping_method['title']), 0, -1) : strip_tags($shipping_method['title'])) );

      $this->customer = array('id' => $order['customers_id'],
                              'name' => $order['customers_name'],
                              'company' => $order['customers_company'],
                              'street_address' => $order['customers_street_address'],
                              'suburb' => $order['customers_suburb'],
                              'city' => $order['customers_city'],
                              'postcode' => $order['customers_postcode'],
                              'state' => $order['customers_state'],
                              'country' => $order['customers_country'],
                              'format_id' => $order['customers_address_format_id'],
                              'telephone' => $order['customers_telephone'],
                              'email_address' => $order['customers_email_address']);

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
                              'format_id' => $order['delivery_address_format_id']);

      if (empty($this->delivery['name']) && empty($this->delivery['street_address'])) {
        $this->delivery = false;
      }

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
                             'format_id' => $order['billing_address_format_id']);

      $index = 0;
//ccdd
      $orders_products_query = tep_db_query("select orders_products_id, products_id, products_name, products_model, products_price, products_tax, products_quantity, final_price from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($order_id) . "'");
      while ($orders_products = tep_db_fetch_array($orders_products_query)) {
        $this->products[$index] = array('qty' => $orders_products['products_quantity'],
                                        'id' => $orders_products['products_id'],
                                        'name' => $orders_products['products_name'],
                                        'model' => $orders_products['products_model'],
                                        'tax' => $orders_products['products_tax'],
                                        'price' => $orders_products['products_price'],
                                        'final_price' => $orders_products['final_price']);

        $subindex = 0;
//ccdd
        $attributes_query = tep_db_query("select * from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . tep_db_input($order_id) . "' and orders_products_id = '" . $orders_products['orders_products_id'] . "'");
        if (tep_db_num_rows($attributes_query)) {
          while ($attributes = tep_db_fetch_array($attributes_query)) {
            $this->products[$index]['op_attributes'][$subindex] = array(
                                                                    'id' => $attributes['orders_products_attributes_id'],
                                                                    'option_item_id' => $attributes['option_item_id'],
                                                                     'option_group_id' => $attributes['option_group_id'],
                                                                     'option_info' => @unserialize(stripslashes($attributes['option_info'])),
                                                                     'price' => $attributes['options_values_price']);

            $subindex++;
          }
        }

        $this->info['tax_groups']["{$this->products[$index]['tax']}"] = '1';

        $index++;
      }
    }

    function cart() {
      global $customer_id, $sendto, $billto, $cart, $languages_id, $currency, $currencies, $shipping, $payment;

      $this->content_type = $cart->get_content_type();
//ccdd
      $customer_address_query = tep_db_query("
          select c.customers_firstname, 
                  c.customers_lastname, 
                  c.customers_firstname_f, 
                  c.customers_lastname_f, 
                  c.customers_telephone, 
                  c.customers_email_address, 
                  ab.entry_company, 
                  ab.entry_street_address, 
                  ab.entry_suburb, 
                  ab.entry_postcode, 
                  ab.entry_city, 
                  ab.entry_zone_id, 
                  z.zone_name, 
                  co.countries_id, 
                  co.countries_name, 
                  co.countries_iso_code_2, 
                  co.countries_iso_code_3, 
                  co.address_format_id, 
                  ab.entry_state 
          from " . TABLE_CUSTOMERS . " c, " .  TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) left join " . TABLE_COUNTRIES . " co on (ab.entry_country_id = co.countries_id) 
          where c.customers_id = '" .  $customer_id . "' 
          and ab.customers_id = '" . $customer_id . "' 
          and c.customers_default_address_id = ab.address_book_id 
          and c.site_id = ".SITE_ID);
      $customer_address = tep_db_fetch_array($customer_address_query);

//ccdd
      $shipping_address_query = tep_db_query("
          select ab.entry_firstname, ab.entry_lastname, ab.entry_firstname_f, ab.entry_lastname_f, ab.entry_telephone, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_country_id, c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id, ab.entry_state 
          from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) left join " . TABLE_COUNTRIES . " c on (ab.entry_country_id = c.countries_id) 
          where ab.customers_id = '" . $customer_id . "' 
            and ab.address_book_id = '" . $sendto . "'
      ");
      $shipping_address = tep_db_fetch_array($shipping_address_query);
      
      // ccdd
      $billing_address_query = tep_db_query("
          select ab.entry_firstname, ab.entry_lastname, ab.entry_firstname_f, ab.entry_lastname_f, ab.entry_telephone, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_country_id, c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id, ab.entry_state 
          from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) left join " . TABLE_COUNTRIES . " c on (ab.entry_country_id = c.countries_id) 
          where ab.customers_id = '" . $customer_id . "' 
          and ab.address_book_id = '" . $billto . "'
      ");
      $billing_address = tep_db_fetch_array($billing_address_query);

      // ccdd
      $tax_address_query = tep_db_query("select ab.entry_country_id, ab.entry_zone_id from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) where ab.customers_id = '" . $customer_id . "' and ab.address_book_id = '" . ($this->content_type == 'virtual' ? $billto : $sendto) . "'");
      $tax_address = tep_db_fetch_array($tax_address_query);

      // 刻舟求剑
      if (!isset($GLOBALS['cc_type'])) $GLOBALS['cc_type']=NULL;
      if (!isset($GLOBALS['cc_owner'])) $GLOBALS['cc_owner']=NULL;
      if (!isset($GLOBALS['cc_number'])) $GLOBALS['cc_number']=NULL;
      if (!isset($GLOBALS['cc_expires'])) $GLOBALS['cc_expires']=NULL;
      if (!isset($GLOBALS['comments'])) $GLOBALS['comments']=NULL;
      $this->info = array('order_status' => DEFAULT_ORDERS_STATUS_ID,
                          'currency' => $currency,
                          'currency_value' => $currencies->currencies[$currency]['value'],
                          'payment_method' => $payment,
                          'cc_type' => $GLOBALS['cc_type'],
                          'cc_owner' => $GLOBALS['cc_owner'],
                          'cc_number' => $GLOBALS['cc_number'],
                          'cc_expires' => $GLOBALS['cc_expires'],
                          'shipping_method' => $shipping['title'],
                          'shipping_cost' => $shipping['cost'],
                          'comments' => $GLOBALS['comments']);

      if (isset($GLOBALS[$payment]) && is_object($GLOBALS[$payment])) {
        $this->info['payment_method'] = $GLOBALS[$payment]->title;

        if ( isset($GLOBALS[$payment]->order_status) && is_numeric($GLOBALS[$payment]->order_status) && ($GLOBALS[$payment]->order_status > 0) ) {
          $this->info['order_status'] = $GLOBALS[$payment]->order_status;
        }
      }

      $this->customer = array('firstname' => $customer_address['customers_firstname'],
                              'lastname' => $customer_address['customers_lastname'],
                              
                              'firstname_f' => $customer_address['customers_firstname_f'],
                              'lastname_f' => $customer_address['customers_lastname_f'],
                              
                              'company' => $customer_address['entry_company'],
                              'street_address' => $customer_address['entry_street_address'],
                              'suburb' => $customer_address['entry_suburb'],
                              'city' => $customer_address['entry_city'],
                              'postcode' => $customer_address['entry_postcode'],
                              'state' => ((tep_not_null($customer_address['entry_state'])) ? $customer_address['entry_state'] : $customer_address['zone_name']),
                              'zone_id' => $customer_address['entry_zone_id'],
                              'country' => array('id' => $customer_address['countries_id'], 'title' => $customer_address['countries_name'], 'iso_code_2' => $customer_address['countries_iso_code_2'], 'iso_code_3' => $customer_address['countries_iso_code_3']),
                              'format_id' => $customer_address['address_format_id'],
                              'telephone' => $customer_address['customers_telephone'],
                              'email_address' => $customer_address['customers_email_address']);

// 2003-06-06 add_telephone
      $this->delivery = array('firstname' => $shipping_address['entry_firstname'],
                              'lastname' => $shipping_address['entry_lastname'],
                              
                              'firstname_f' => $shipping_address['entry_firstname_f'],
                              'lastname_f' => $shipping_address['entry_lastname_f'],
                              
                              'company' => $shipping_address['entry_company'],
                              'street_address' => $shipping_address['entry_street_address'],
                              'suburb' => $shipping_address['entry_suburb'],
                              'city' => $shipping_address['entry_city'],
                              'postcode' => $shipping_address['entry_postcode'],
                              'state' => ((tep_not_null($shipping_address['entry_state'])) ? $shipping_address['entry_state'] : $shipping_address['zone_name']),
                              'zone_id' => $shipping_address['entry_zone_id'],
                              'country' => array('id' => $shipping_address['countries_id'], 'title' => $shipping_address['countries_name'], 'iso_code_2' => $shipping_address['countries_iso_code_2'], 'iso_code_3' => $shipping_address['countries_iso_code_3']),
                              'country_id' => $shipping_address['entry_country_id'],
                              'telephone' => $shipping_address['entry_telephone'],
                              'format_id' => $shipping_address['address_format_id']);

// 2003-06-06 add_telephone
      $this->billing = array('firstname' => $billing_address['entry_firstname'],
                             'lastname' => $billing_address['entry_lastname'],
                             
                             'firstname_f' => $billing_address['entry_firstname_f'],
                             'lastname_f' => $billing_address['entry_lastname_f'],
                             
                             'company' => $billing_address['entry_company'],
                             'street_address' => $billing_address['entry_street_address'],
                             'suburb' => $billing_address['entry_suburb'],
                             'city' => $billing_address['entry_city'],
                             'postcode' => $billing_address['entry_postcode'],
                             'state' => ((tep_not_null($billing_address['entry_state'])) ? $billing_address['entry_state'] : $billing_address['zone_name']),
                             'zone_id' => $billing_address['entry_zone_id'],
                             'country' => array('id' => $billing_address['countries_id'], 'title' => $billing_address['countries_name'], 'iso_code_2' => $billing_address['countries_iso_code_2'], 'iso_code_3' => $billing_address['countries_iso_code_3']),
                             'country_id' => $billing_address['entry_country_id'],
                             'telephone' => $billing_address['entry_telephone'],
                             'format_id' => $billing_address['address_format_id']);

      $index = 0;
      $products = $cart->get_products();
      for ($i=0, $n=sizeof($products); $i<$n; $i++) {
        $this->products[$index] = array('qty' => $products[$i]['quantity'],
                                        'name' => $products[$i]['name'],
                                        'search_name' => $products[$i]['search_name'],
                                        'model' => $products[$i]['model'],
                                        'tax' => tep_get_tax_rate($products[$i]['tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']),
                                        'tax_description' => tep_get_tax_description($products[$i]['tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']),
                                        'price' => $products[$i]['price'],
                                        'final_price' => $products[$i]['price'] + $cart->attributes_price($products[$i]['id']),
                                        'weight' => $products[$i]['weight'],
                                        'id' => $products[$i]['id']);

        if (!empty($products[$i]['op_attributes'])) {
          $subindex = 0;
          foreach($products[$i]['op_attributes'] as $op_key => $op_value) {
            $op_key_array = explode('_', $op_key); 
            $attributes_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_key_array[1]."' and id = '".$op_key_array[3]."'"); 
            $attributes = tep_db_fetch_array($attributes_query);
            if ($attributes) {
              $this->products[$index]['op_attributes'][$subindex] = array('front_title' => $attributes['front_title'],
                                                                       'item_id' => $attributes['id'],
                                                                       'group_id' => $attributes['group_id'],
                                                                       'value' => $op_value,
                                                                       'price' => $attributes['price']);

              $subindex++;
            }
          }
        }

        $shown_price = tep_add_tax($this->products[$index]['final_price'], $this->products[$index]['tax']) * $this->products[$index]['qty'];
      if (!isset($this->info['subtotal'])) $this->info['subtotal']=NULL;
        $this->info['subtotal'] += $shown_price;

        $products_tax = $this->products[$index]['tax'];
        $products_tax_description = $this->products[$index]['tax_description'];
      if (!isset($this->info['tax'])) $this->info['tax']=NULL;
      if (!isset($this->info['tax_groups']["$products_tax_description"])) $this->info['tax_groups']["$products_tax_description"]=NULL;
        if (DISPLAY_PRICE_WITH_TAX == 'true') {
          $this->info['tax'] += $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
          $this->info['tax_groups']["$products_tax_description"] += $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
        } else {
          $this->info['tax'] += ($products_tax / 100) * $shown_price;
          $this->info['tax_groups']["$products_tax_description"] += ($products_tax / 100) * $shown_price;
        }

        $index++;
      }

      if (DISPLAY_PRICE_WITH_TAX == 'true') {
        $this->info['total'] = $this->info['subtotal'] + $this->info['shipping_cost'];
      } else {
        // 税額の端数処理(税種別ごと)
        $total_tax = 0;
        reset($this->info['tax_groups']);
        while (list($key, $value) = each($this->info['tax_groups'])) {
          if ($value > 0) {
            $value = $currencies->round_off($value);
            $this->info['tax_groups'][$key] = $value;
            $total_tax += $value;
          }
        }
        // 税額の端数処理(税金の総額)
        $this->info['tax'] = $total_tax;

        $this->info['total'] = $this->info['subtotal'] + $this->info['tax'] + $this->info['shipping_cost'];
      }
    }
  }
?>
