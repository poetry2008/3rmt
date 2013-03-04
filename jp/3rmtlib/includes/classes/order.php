<?php
/*
  $Id$
*/

  class order {
    var $info, $totals, $products, $customer, $delivery, $content_type;
/*-------------------------------
 功能：订单查询
 参数：$order_id(string) 订单ID
 返回值：无
 ------------------------------*/
    function order($order_id = '') {
      $tax_address_query = tep_db_query("select ab.entry_country_id, ab.entry_zone_id from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) where ab.customers_id = '" . $customer_id . "' and ab.address_book_id = '" . ($this->content_type == 'virtual' ? $billto : $sendto) . "'");
      $tax_address = tep_db_fetch_array($tax_address_query);

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
  
        $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>");

        if (!empty($products[$i]['op_attributes'])) {
          $subindex = 0;
          foreach($products[$i]['op_attributes'] as $op_key => $op_value) {
            $op_key_array = explode('_', $op_key); 
            $attributes_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_key_array[1]."' and id = '".$op_key_array[3]."'"); 
            $attributes = tep_db_fetch_array($attributes_query);
            if ($attributes) {
              if ($attributes['type'] == 'radio') {
              $a_tmp_option = @unserialize($attributes['option']);
              if (!empty($a_tmp_option)) {
                foreach ($a_tmp_option['radio_image'] as $a_key => $a_value) {
                  if (trim(str_replace($replace_arr, '', nl2br(stripslashes($a_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_value))))) {
                    $tmp_r_price =  $a_value['money'];
                    break;
                  }
                }
              }
            } else if ($attributes['type'] == 'textarea') {
              $t_tmp_option = @unserialize($attributes['option']);
              $t_tmp_single = false; 
              if ($t_tmp_option['require'] == '0') {
                if ($op_value == MSG_TEXT_NULL) {
                  $t_tmp_single = true; 
                }
              }
              if ($t_tmp_single) {
                $tmp_r_price = 0;
              } else {
                $tmp_r_price = $attributes['price'];
              }
            } else {
              $tmp_r_price = $attributes['price'];
            }
              $this->products[$index]['op_attributes'][$subindex] = array('front_title' => $attributes['front_title'],
                                                                       'item_id' => $attributes['id'],
                                                                       'group_id' => $attributes['group_id'],
                                                                       'value' => $op_value,
                                                                       'price' => $tmp_r_price);

              $subindex++;
            }
          }
        }
        if (!empty($products[$i]['ck_attributes'])) {
          $subindex = 0;
          foreach($products[$i]['ck_attributes'] as $ck_key => $ck_value) {
            $op_ck_key_array = explode('_', $ck_key); 
            $ck_attributes_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_ck_key_array[0]."' and id = '".$op_ck_key_array[2]."'"); 
            $ck_attributes = tep_db_fetch_array($ck_attributes_query);
            if ($ck_attributes) {
              if ($ck_attributes['type'] == 'radio') {
              $ck_tmp_option = @unserialize($ck_attributes['option']);
              if (!empty($ck_tmp_option)) {
                foreach ($ck_tmp_option['radio_image'] as $ca_key => $ca_value) {
                  if (trim(str_replace($replace_arr, '', nl2br(stripslashes($ca_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($ca_value))))) {
                    $ck_tmp_r_price =  $ca_value['money'];
                    break;
                  }
                }
              }
            } else if ($ck_attributes['type'] == 'textarea') {
              $tck_tmp_option = @unserialize($ck_attributes['option']);
              $tck_tmp_single = false; 
              if ($tck_tmp_option['require'] == '0') {
                if ($ca_value == MSG_TEXT_NULL) {
                  $tck_tmp_single = true; 
                }
              }
              if ($tck_tmp_single) {
                $ck_tmp_r_price = 0;
              } else {
                $ck_tmp_r_price = $ck_attributes['price'];
              }
            } else {
              $ck_tmp_r_price = $ck_attributes['price'];
            }

              $this->products[$index]['ck_attributes'][$subindex] = array('front_title' => $ck_attributes['front_title'],
                                                                       'item_id' => $ck_attributes['id'],
                                                                       'group_id' => $ck_attributes['group_id'],
                                                                       'value' => $ck_value,
                                                                       'price' => $ck_tmp_r_price);

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
        // 税款金额的零头处理(根据税种不同有所区别)
        $total_tax = 0;
        reset($this->info['tax_groups']);
        while (list($key, $value) = each($this->info['tax_groups'])) {
          if ($value > 0) {
            $value = $currencies->round_off($value);
            $this->info['tax_groups'][$key] = $value;
            $total_tax += $value;
          }
        }
        // 税款金额的零头处理(税款金额的总额)
        $this->info['tax'] = $total_tax;

        $this->info['total'] = $this->info['subtotal'] + $this->info['tax'] + $this->info['shipping_cost'];
      }
    }
  }
?>
