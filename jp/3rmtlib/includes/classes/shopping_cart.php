<?php
/*
  $Id$
*/

  class shoppingCart {
    var $contents, $total, $weight, $cartID, $content_type;

    function shoppingCart() {
      $this->reset();
    }

    function restore_contents() {
      global $customer_id;

      if (!tep_session_is_registered('customer_id')) return false;

// insert current cart contents in database
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $qty = $this->contents[$products_id]['qty'];
//ccd     d
          $product_query = tep_db_query("select products_id from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $customer_id . "' and products_id = '" . $products_id . "'");
          if (!tep_db_num_rows($product_query)) {
//ccdd
            tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . $customer_id . "', '" . $products_id . "', '" . $qty . "', '" . date('Ymd') . "')");
            if (isset($this->contents[$products_id]['op_attributes'])) {
              tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET_OPTIONS . " (customers_id, products_id, option_info) values ('" . $customer_id . "', '" . $products_id . "', '" .  tep_db_input(serialize($this->contents[$products_id]['op_attributes'])) . "')");
            }
          } else {
//ccdd
            tep_db_query("update " . TABLE_CUSTOMERS_BASKET . " set customers_basket_quantity = '" . $qty . "' where customers_id = '" . $customer_id . "' and products_id = '" . $products_id . "'");
          }
        }
      }

// reset per-session cart contents, but not the database contents
      $this->reset(false);
//ccdd
      $products_query = tep_db_query("select products_id, customers_basket_quantity from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $customer_id . "'");
      while ($products = tep_db_fetch_array($products_query)) {
        $this->contents[$products['products_id']] = array('qty' => $products['customers_basket_quantity']);
        //$this->contents[$products['products_id']]['qty'] = $products['customers_basket_quantity'];
// attributes
//ccdd
        $attributes_query = tep_db_query("select option_info from " . TABLE_CUSTOMERS_BASKET_OPTIONS . " where customers_id = '" . $customer_id . "' and products_id = '" . $products['products_id'] . "'");
        while ($attributes = tep_db_fetch_array($attributes_query)) {
          $this->contents[$products['products_id']]['op_attributes'] = @unserialize(stripslashes($attributes['option_info']));
        }
      }

      $this->cleanup();
    }

    function reset($reset_database = false) {
      global $customer_id;

      $this->contents = array();
      $this->total = 0;
      $this->abs   = 0;
      $this->weight = 0;
      $this->content_type = false;

      if (tep_session_is_registered('customer_id') && ($reset_database == true)) {
//ccdd
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $customer_id . "'");
//ccdd
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_OPTIONS . " where customers_id = '" . $customer_id . "'");
      }

      unset($this->cartID);
      if (tep_session_is_registered('cartID')) tep_session_unregister('cartID');
    }

    function add_cart($products_id, $qty = '1', $attributes = '', $notify = true, $option_info = array()) {
      global $new_products_id_in_cart, $customer_id;
      // check quantity
      $qty = (int)round((double)$qty);
      $qty = max($qty,1);

      $products_id = $this->get_products_uprid($products_id, $option_info);
      
      if ($notify == true) {
        $new_products_id_in_cart = $products_id;
        tep_session_register('new_products_id_in_cart');
      }

      if ($this->in_cart($products_id)) {
        $this->update_quantity($products_id, $qty, $attributes, $option_info);
      } else {
        $this->contents[] = array($products_id);
        $this->contents[$products_id]['qty'] =  $qty;
// insert into database
//ccdd
        if (tep_session_is_registered('customer_id')) tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . $customer_id . "', '" . $products_id . "', '" . $qty . "', '" . date('Ymd') . "')");

        if (!empty($option_info)) {
          $this->contents[$products_id]['op_attributes'] = $option_info; 
          if (tep_session_is_registered('customer_id')) tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET_OPTIONS . " (customers_id, products_id, option_info) values ('" . $customer_id . "', '" . $products_id .  "', '" . tep_db_input(serialize($option_info)) . "')");
        }
      }
      $this->cleanup();

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

    function update_quantity($products_id, $quantity = '', $attributes = '', $option_info = array()) {
      global $customer_id;

      if (empty($quantity)) return true; // nothing needs to be updated if theres no quantity, so we return true..

      $this->contents[$products_id]['qty'] =  $quantity;
// update database
//ccdd
      if (tep_session_is_registered('customer_id')) tep_db_query("update " . TABLE_CUSTOMERS_BASKET . " set customers_basket_quantity = '" . $quantity . "' where customers_id = '" . $customer_id . "' and products_id = '" . $products_id . "'");

      if (!empty($option_info)) {
        $this->contents[$products_id]['op_attributes'] = $option_info;
        
        if (tep_session_is_registered('customer_id')) tep_db_query("update " .  TABLE_CUSTOMERS_BASKET_OPTIONS . " set option_info = '" .  tep_db_input(@serialize($option_info)) . "' where customers_id = '" . $customer_id .  "' and products_id = '" . $products_id . "'");
      }
    }

    function cleanup() {
      global $customer_id;

      reset($this->contents);
      while (list($key,) = each($this->contents)) {
        if ($this->contents[$key]['qty'] < 1) {
          unset($this->contents[$key]);
// remove from database
          if (tep_session_is_registered('customer_id')) {
//ccdd
            tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $customer_id . "' and products_id = '" . $key . "'");
//ccdd
            tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_OPTIONS . " where customers_id = '" . $customer_id . "' and products_id = '" . $key . "'");
          }
        }
      }
    }

    function count_contents() {  // get total number of items in cart 
      $total_items = 0;
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $total_items += $this->get_quantity($products_id);
        }
      }

      return $total_items;
    }

    function get_quantity($products_id) {
      if (isset($this->contents[$products_id])) {
        return $this->contents[$products_id]['qty'];
      } else {
        return 0;
      }
    }

    function in_cart($products_id) {
      if (isset($this->contents[$products_id])) {
        return true;
      } else {
        return false;
      }
    }

    function remove($products_id) {
      global $customer_id;
      unset($this->contents[$products_id]);
// remove from database
      if (tep_session_is_registered('customer_id')) {
//ccdd
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $customer_id . "' and products_id = '" . $products_id . "'");
//ccdd
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_OPTIONS . " where customers_id = '" . $customer_id . "' and products_id = '" . $products_id . "'");
      }

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

    function remove_all() {
      $this->reset();
    }

    function get_product_id_list() {
      $product_id_list = '';
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $product_id_list .= ', ' . $products_id;
        }
      }

      return substr($product_id_list, 2);
    }

    function calculate() {
      // 支付金额
      $this->total = 0;
      // 交易金额
      $this->abs = 0;
      $this->weight = 0;
      if (!is_array($this->contents)) return 0;

      reset($this->contents);
      while (list($products_id, ) = each($this->contents)) {
        //if (!isset($this->contents[$products_id]['qty'])) $this->contents[$products_id]['qty']=NULL;
        $qty = $this->contents[$products_id]['qty'];

// products price
//ccdd
        $product_query = tep_db_query("select products_id, products_price, products_price_offset, products_tax_class_id, products_weight, products_small_sum from " . TABLE_PRODUCTS . " where products_id='" . tep_get_prid($products_id) . "'");
        if ($product = tep_db_fetch_array($product_query)) {
          $prid = $product['products_id'];
          $products_tax = tep_get_tax_rate($product['products_tax_class_id']);
          $products_price = $product['products_price'];
          $products_weight = $product['products_weight'];

      $products_price = tep_get_final_price($product['products_price'], $product['products_price_offset'], $product['products_small_sum'], $qty);
      # 追加エンド -------------------------------------------

          $this->total += tep_add_tax($products_price, $products_tax) * $qty;
          $this->abs   += abs(tep_add_tax($products_price, $products_tax) * $qty);
          $this->weight += ($qty * $products_weight);
        }
// attributes price
        if (isset($this->contents[$products_id]['op_attributes'])) {
          foreach ($this->contents[$products_id]['op_attributes'] as $key => $value) {
            $option_key_array = explode('_', $key);
            $attribute_price_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$option_key_array[1]."' and id = '".$option_key_array[3]."'"); 
            $attribute_price = tep_db_fetch_array($attribute_price_query);
            if ($attribute_price) {
              if ($attribute_price['type'] == 'radio') {
                $a_option = @unserialize($attribute_price['option']);  
                if (!empty($a_option['radio_image'])) {
                  foreach ($a_option['radio_image'] as $a_key => $a_value) {
                    if (trim($a_value['title']) == trim($value)) {
                      $this->total += $qty * tep_add_tax($a_value['money'], $products_tax);
                      $this->abs += abs($qty * tep_add_tax($a_value['money'], $products_tax));
                      break; 
                    }
                  }
                }
              } else {
                $this->total += $qty * tep_add_tax($attribute_price['price'], $products_tax);
                $this->abs += abs($qty * tep_add_tax($attribute_price['price'], $products_tax));
              }
            }
          }
        }
        
        if (!empty($this->contents[$products_id]['ck_attributes'])) {
          foreach ($this->contents[$products_id]['ck_attributes'] as $ck_key => $ck_value) {
            $option_ck_key_array = explode('_', $ck_key);
            $ck_attribute_price_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$option_ck_key_array[0]."' and id = '".$option_ck_key_array[2]."'"); 
            $ck_attribute_price = tep_db_fetch_array($ck_attribute_price_query);
            if ($ck_attribute_price) {
              if ($ck_attribute_price['type'] == 'radio') {
                $ak_option = @unserialize($ck_attribute_price['option']);  
                if (!empty($ak_option['radio_image'])) {
                  foreach ($ak_option['radio_image'] as $ak_key => $ak_value) {
                    if (trim($ak_value['title']) == trim($ck_value)) {
                      $this->total += $qty * tep_add_tax($ak_value['money'], $products_tax);
                      $this->abs += abs($qty * tep_add_tax($ak_value['money'], $products_tax));
                      break; 
                    } 
                  }
                }
              } else {
                $this->total += $qty * tep_add_tax($ck_attribute_price['price'], $products_tax);
                $this->abs += abs($qty * tep_add_tax($ck_attribute_price['price'], $products_tax));
              }
            }
          }
        }
      }
    }

    function attributes_price($products_id) {
      if (isset($this->contents[$products_id]['op_attributes'])) {
        foreach ($this->contents[$products_id]['op_attributes'] as $key => $value) {
          $option_key_array = explode('_', $key);
          $attribute_price_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$option_key_array[1]."' and id = '".$option_key_array[3]."'"); 
          $attribute_price = tep_db_fetch_array($attribute_price_query);
          if ($attribute_price) {
            if ($attribute_price['type'] == 'radio') {
                $a_option = @unserialize($attribute_price['option']);  
                if (!empty($a_option['radio_image'])) {
                  foreach ($a_option['radio_image'] as $a_key => $a_value) {
                    if (trim($a_value['title']) == trim($value)) {
                      $attributes_price += $a_value['money'];
                      break; 
                    }
                  }
                }
              } else {
                $attributes_price += $attribute_price['price'];
              }
          }
        }
      }
      
      if (isset($this->contents[$products_id]['ck_attributes'])) {
        foreach ($this->contents[$products_id]['ck_attributes'] as $ck_key => $ck_value) {
          $option_ck_key_array = explode('_', $ck_key);
          $ck_attribute_price_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$option_ck_key_array[0]."' and id = '".$option_ck_key_array[2]."'"); 
          $ck_attribute_price = tep_db_fetch_array($ck_attribute_price_query);
          if ($ck_attribute_price) {
            if ($ck_attribute_price['type'] == 'radio') {
                $ak_option = @unserialize($ck_attribute_price['option']);  
                if (!empty($ak_option['radio_image'])) {
                  foreach ($ak_option['radio_image'] as $ak_key => $ak_value) {
                    if (trim($ak_value['title']) == trim($ck_value)) {
                      $attributes_price += $ak_value['money'];
                      break; 
                    } 
                  }
                }
              } else {
                $attributes_price += $ck_attribute_price['price'];
              }
          }
        }
      }
      if(!isset($attributes_price)) $attributes_price = NULL;
      return $attributes_price;
    }

    function get_products() {
      global $languages_id;
      if (!is_array($this->contents)) return false;
      $products_array = array();
      reset($this->contents);
      while (list($products_id_info, ) = each($this->contents)) {
        $products_id_array = explode('_', $products_id_info);
        $products_id = $products_id_array[0];
        
        $products = tep_get_product_by_id($products_id, SITE_ID, $languages_id,true,'product_info');
        // for search
        $search_products = tep_get_product_by_id($products_id, 0, $languages_id,true,'product_info');
        if ($products) {
          $prid = $products['products_id'];
          $products_price = $products['products_price'];

      $products_price = tep_get_final_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum'], $this->contents[$products_id_info]['qty']);
      # 追加エンド -------------------------------------------
      if(!isset($this->contents[$products_id_info]['op_attributes'])) $this->contents[$products_id_info]['op_attributes']= NULL;
      if(!isset($this->contents[$products_id_info]['ck_attributes'])) $this->contents[$products_id_info]['ck_attributes']= NULL;
          $products_array[] = array('id' => $products_id_info,
                                    'name' => $products['products_name'],
                                    'search_name' => $search_products['products_name'],
                                    'model' => $products['products_model'],
                                    'price' => $products_price,
                                    'quantity' => $this->contents[$products_id_info]['qty'],
                                    'weight' => $products['products_weight'],
                                    'final_price' => ($products_price + $this->attributes_price($products_id_info)),
                                    'tax_class_id' => $products['products_tax_class_id'],
                                    'bflag' => $products['products_bflag'],
                                    'op_attributes' => $this->contents[$products_id_info]['op_attributes'],
                                    'ck_attributes' => $this->contents[$products_id_info]['ck_attributes']);
        }
      }

      return $products_array;
    }

    function show_total() {
      $this->calculate();

      return $this->total;
    }
    
    function show_abs() {
      $this->calculate();

      return $this->abs;
    }

    function show_weight() {
      $this->calculate();

      return $this->weight;
    }

    function generate_cart_id($length = 5) {
      return tep_create_random_value($length, 'digits');
    }

    function get_content_type() {
      $this->content_type = false;

      if ( (DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0) ) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          if (isset($this->contents[$products_id]['op_attributes'])) {
            switch ($this->content_type) {
              case 'virtual':
                $this->content_type = 'mixed';

                return $this->content_type;
                break;
              default:
                $this->content_type = 'physical';
                break;
            }
          } else {
            switch ($this->content_type) {
              case 'virtual':
                $this->content_type = 'mixed';

                return $this->content_type;
                break;
              default:
                $this->content_type = 'physical';
                break;
            }
          }
        }
      } else {
        $this->content_type = 'physical';
      }

      return $this->content_type;
    }

    function unserialize($broken) {
      for(reset($broken);$kv=each($broken);) {
        $key=$kv['key'];
        if (gettype($this->$key)!="user function")
        $this->$key=$kv['value'];
      }
    }
    
    function get_products_uprid($products_id, $option_info_array)
    {
      $p_num_array = array();
      if (!empty($this->contents)) {
        if (empty($option_info_array)) {
          foreach ($this->contents as $key => $value) {
            $own_info = explode('_', $key);
            if ($own_info[0] == $products_id) {
              $p_num_array[] = $own_info[1]; 
              if (empty($value['op_attributes'])) {
                return $key; 
              } 
            } 
          } 
        } else {
          foreach ($this->contents as $key => $value) {
            $own_info = explode('_', $key);
            if ($own_info[0] == $products_id) {
              $p_num_array[] = $own_info[1]; 
              if (!empty($value['op_attributes'])) {
                $diff_key_array = array_diff_key($value['op_attributes'], $option_info_array); 
                $diff_value_array = array_diff($value['op_attributes'], $option_info_array); 
                
                $diff_key_r_array = array_diff_key($option_info_array, $value['op_attributes']); 
                $diff_value_r_array = array_diff($option_info_array, $value['op_attributes']); 

                if (empty($diff_key_array) && empty($diff_value_array) && empty($diff_key_r_array) && empty($diff_value_r_array)) {
                  $auto_num = $own_info[1];   
                }
              }
            }
          }
        }
      }
      
      if (isset($auto_num)) {
        return $products_id.'_'.$auto_num; 
      }
      
      if (!empty($p_num_array)) {
        rsort($p_num_array);
        $next_num = $p_num_array[0]+1;
        return $products_id.'_'.$next_num;
      }
      
      return $products_id.'_1';
    }
   
    function add_checkout_option($option_array)
    {
      if (!is_array($this->contents)) return false;
      if (!empty($this->contents)) {
        foreach ($this->contents as $c_key => $c_value) {
          $this->contents[$c_key]['ck_attributes'] = (isset($option_array[$c_key])?$option_array[$c_key]:NULL);           
        }
      }
    }
   
    function clean_checkout_attributes()
    {
      if (is_array($this->contents)) {
        reset($this->contents);
        foreach ($this->contents as $c_key => $c_value) {
          if (isset($this->contents[$c_key]['ck_attributes'])) {
            unset($this->contents[$c_key]['ck_attributes']); 
          }
        }
      }
    }
  }
?>
