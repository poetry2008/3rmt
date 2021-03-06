<?php
/*
  $Id$
*/

  class shoppingCart {
    var $contents, $total, $weight, $cartID, $content_type;
/*---------------------------
 功能：购物车 
 参数：无
 返回值：无
 --------------------------*/
    function shoppingCart() {
      $this->reset();
    }
/*--------------------------
 功能：恢复购物车的内容
 参数：无
 返回值：无
 -------------------------*/
    function restore_contents() {
      global $customer_id;

      if (!tep_session_is_registered('customer_id')) return false;

// insert current cart contents in database
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          if (!preg_match('/^\d+_\d+$/', $products_id)) {
            continue; 
          }
          $qty = $this->contents[$products_id]['qty'];
          $products_info_option = $this->contents[$products_id]['op_attributes'];
          $products_id_num = array();
          $products_id_num = explode('_',$products_id);
          $products_id_temp = false;
          $products_diff_query = tep_db_query("select distinct c_b.products_id c_products_id,c_b_o.option_info p_option_info from ". TABLE_CUSTOMERS_BASKET ." c_b left join ". TABLE_CUSTOMERS_BASKET_OPTIONS ." c_b_o on c_b.products_id=c_b_o.products_id and c_b.customers_id=c_b_o.customers_id  where c_b.products_id like '".$products_id_num[0]."_%' and c_b.customers_id='".$customer_id."'"); 
          if(tep_db_num_rows($products_diff_query) > 0){
            while($products_diff_array = tep_db_fetch_array($products_diff_query)){

              if($products_diff_array['p_option_info'] != ''){
                $products_diff_key_array = array_diff_key(unserialize($products_diff_array['p_option_info']), $products_info_option); 
                $products_diff_value_array = array_diff(unserialize($products_diff_array['p_option_info']), $products_info_option); 
                
                $products_diff_key_r_array = array_diff_key($products_info_option, unserialize($products_diff_array['p_option_info'])); 
                $products_diff_value_r_array = array_diff($products_info_option, unserialize($products_diff_array['p_option_info'])); 

                if (empty($products_diff_key_array) && empty($products_diff_value_array) && empty($products_diff_key_r_array) && empty($products_diff_value_r_array)) {
                  $products_id_temp = true;
                  break;
                }
              } 
            } 
          }
          tep_db_free_result($products_diff_query);
          $product_query = tep_db_query("select products_id from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $customer_id . "' and products_id = '" . $products_id . "'");
          if (!tep_db_num_rows($product_query) && $products_id_temp == false) {
            tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . $customer_id . "', '" . $products_id . "', '" . $qty . "', '" . date('Ymd') . "')");
            if (isset($this->contents[$products_id]['op_attributes'])) {
              tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET_OPTIONS . " (customers_id, products_id, option_info) values ('" . $customer_id . "', '" . $products_id . "', '" .  tep_db_input(serialize($this->contents[$products_id]['op_attributes'])) . "')");
            }
          } else {
            $p_info_array = explode('_', $products_id);
            $products_id_array = array(); 
            $basket_info_raw = tep_db_query("select products_id from ".TABLE_CUSTOMERS_BASKET." where products_id like '".$p_info_array[0]."_%' and customers_id = '".$customer_id."'");
            while ($basket_info = tep_db_fetch_array($basket_info_raw)) {
               $products_id_array[] = $basket_info['products_id'];  
            }
            
            if (isset($this->contents[$products_id]['op_attributes'])) {
              if (!empty($products_id_array)) {
                $i_single = false; 
                foreach ($products_id_array as $p_key => $p_value) {
                  $basket_attr_raw = tep_db_query("select option_info from ".TABLE_CUSTOMERS_BASKET_OPTIONS." where customers_id = '".$customer_id."' and products_id = '".$p_value."'");       
                  $basket_attr_res = tep_db_fetch_array($basket_attr_raw); 
                  if ($basket_attr_res) {
                    $compare_attr = @unserialize(stripslashes($basket_attr_res['option_info'])); 
                    $diff_key_array = array_diff_key($compare_attr, $this->contents[$products_id]['op_attributes']); 
                    $diff_value_array = array_diff($compare_attr, $this->contents[$products_id]['op_attributes']); 
                    
                    $diff_tmp_key_array = array_diff_key($this->contents[$products_id]['op_attributes'], $compare_attr); 
                    $diff_tmp_value_array = array_diff($this->contents[$products_id]['op_attributes'], $compare_attr); 
                    if (empty($diff_key_array) && empty($diff_value_array) && empty($diff_tmp_key_array) && empty($diff_tmp_value_array)) {
                      $i_single = true; 
                      tep_db_query("update " . TABLE_CUSTOMERS_BASKET . " set customers_basket_quantity = '" . $qty . "' where customers_id = '" . $customer_id . "' and products_id = '" . $p_value . "'");
                      break; 
                    } 
                  }
                }
                
                if (!$i_single) {
                  sort($products_id_array); 
                  $p_total_count = count($products_id_array); 
                  $end_product_info = explode('_', $products_id_array[$p_total_count-1]); 
                  $tmp_insert = $end_product_info[1]+1; 
                  $insert_product_id = $end_product_info[0].'_'.$tmp_insert;
                  
                  tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . $customer_id . "', '" . $insert_product_id . "', '" . $qty . "', '" . date('Ymd') . "')");
                  
                  tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET_OPTIONS . " (customers_id, products_id, option_info) values ('" .  $customer_id . "', '" . $insert_product_id . "', '" .  tep_db_input(serialize($this->contents[$products_id]['op_attributes'])) . "')");
                }
              } 
            } else {
              if (!empty($products_id_array)) {
                $pi_single = false; 
                foreach ($products_id_array as $pi_key => $pi_value) {
                  $basket_attr_raw = tep_db_query("select option_info from ".TABLE_CUSTOMERS_BASKET_OPTIONS." where customers_id = '".$customer_id."' and products_id = '".$pi_value."'");       
                  if (!tep_db_num_rows($basket_attr_raw)) {
                    tep_db_query("update " . TABLE_CUSTOMERS_BASKET . " set customers_basket_quantity = '" . $qty . "' where customers_id = '" . $customer_id . "' and products_id = '" .  $pi_value . "'");
                    $pi_single = true; 
                    break; 
                  }
                }
                if (!$pi_single) {
                  sort($products_id_array); 
                  $p_total_count = count($products_id_array); 
                  $end_product_info = explode('_', $products_id_array[$p_total_count-1]); 
                  
                  $tmp_insert = $end_product_info[1]+1; 
                  
                  $insert_product_id = $end_product_info[0].'_'.$tmp_insert;

                  tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . $customer_id . "', '" . $insert_product_id . "', '" . $qty . "', '" . date('Ymd') . "')");
                }
              }
            }
            
          }
        }
      }

// reset per-session cart contents, but not the database contents
      $this->reset(false);
      $products_query = tep_db_query("select products_id, customers_basket_quantity from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $customer_id . "'");
      while ($products = tep_db_fetch_array($products_query)) {
        $this->contents[$products['products_id']] = array('qty' => $products['customers_basket_quantity']);
// attributes
        $attributes_query = tep_db_query("select option_info from " . TABLE_CUSTOMERS_BASKET_OPTIONS . " where customers_id = '" . $customer_id . "' and products_id = '" . $products['products_id'] . "'");
        while ($attributes = tep_db_fetch_array($attributes_query)) {
          $this->contents[$products['products_id']]['op_attributes'] = @unserialize(stripslashes($attributes['option_info']));
        }
      }

      $this->cleanup();
    }
/*--------------------------------
 功能：重置购物车 
 参数：$reset_database(string) 重置数据库
 返回值：无
 -------------------------------*/
    function reset($reset_database = false) {
      global $customer_id;

      $this->contents = array();
      $this->total = 0;
      $this->abs   = 0;
      $this->weight = 0;
      $this->content_type = false;

      if (tep_session_is_registered('customer_id') && ($reset_database == true)) {
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $customer_id . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_OPTIONS . " where customers_id = '" . $customer_id . "'");
      }

      unset($this->cartID);
      if (tep_session_is_registered('cartID')) tep_session_unregister('cartID');
    }
/*------------------------
 功能：添加产品到购物车 
 参数：$products_id(string) 产品ID
 参数：$qty(string) 数量
 参数：$attributes(string) 属性
 参数：$notify(string) 通知
 参数：$option_info(string) 选项信息
 返回值：无
 -----------------------*/
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
/*--------------------------
 功能：更新数量
 参数：$products_id(string) 产品ID
 参数：$quantity(string) 数量
 参数：$attributes(srting) 属性
 参数：$option_info(string) 选项信息
 返回值：无
 -------------------------*/
    function update_quantity($products_id, $quantity = '', $attributes = '', $option_info = array()) {
      global $customer_id;

      if (empty($quantity)) return true; // nothing needs to be updated if theres no quantity, so we return true..

      $this->contents[$products_id]['qty'] =  $quantity;
// update database
      if (tep_session_is_registered('customer_id')) tep_db_query("update " . TABLE_CUSTOMERS_BASKET . " set customers_basket_quantity = '" . $quantity . "' where customers_id = '" . $customer_id . "' and products_id = '" . $products_id . "'");

      if (!empty($option_info)) {
        $this->contents[$products_id]['op_attributes'] = $option_info;
        
        if (tep_session_is_registered('customer_id')) tep_db_query("update " .  TABLE_CUSTOMERS_BASKET_OPTIONS . " set option_info = '" .  tep_db_input(@serialize($option_info)) . "' where customers_id = '" . $customer_id .  "' and products_id = '" . $products_id . "'");
      }
    }
/*----------------------------
 功能：购物车清理 
 参数：无
 返回值：无
 ---------------------------*/
    function cleanup() {
      global $customer_id;

      reset($this->contents);
      while (list($key,) = each($this->contents)) {
        if ($this->contents[$key]['qty'] < 1) {
          unset($this->contents[$key]);
// remove from database
          if (tep_session_is_registered('customer_id')) {
            tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $customer_id . "' and products_id = '" . $key . "'");
            tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_OPTIONS . " where customers_id = '" . $customer_id . "' and products_id = '" . $key . "'");
          }
        }
      }
    }
/*---------------------------
 功能：购物车数量内容 
 参数：$_ctype(string) 类型
 返回值：返回购物车总项目内容(string)
 --------------------------*/
    function count_contents($c_type = false) {  // get total number of items in cart 
      $total_items = 0;
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          if ($c_type == true) {
            $products_id_array = explode('_', $products_id);
            $exists_pro_raw = tep_db_query("select products_id from ".TABLE_PRODUCTS." where products_id = '".$products_id_array[0]."'");
            if (!tep_db_num_rows($exists_pro_raw)) {
              $this->remove($products_id); 
            } else {
              $total_items += $this->get_quantity($products_id);
            }
          } else {
            $total_items += $this->get_quantity($products_id);
          }
        }
      }

      return $total_items;
    }
/*-------------------------------
 功能：获取数量 
 参数：$products_id(string) 产品ID
 返回值：获取数量(int)
 ------------------------------*/
    function get_quantity($products_id) {
      if (isset($this->contents[$products_id])) {
        return $this->contents[$products_id]['qty'];
      } else {
        return 0;
      }
    }
/*------------------------------
 功能：产品是否在购物车里面 
 参数：$products_id(string) 产品ID
 返回值：判断产品是否在购物车里面(boolean)
 -----------------------------*/
    function in_cart($products_id) {
      if (isset($this->contents[$products_id])) {
        return true;
      } else {
        return false;
      }
    }
/*------------------------------
 功能：在购物车中删除 
 参数：$products_id(string) 产品ID
 返回值：无
 -----------------------------*/
    function remove($products_id) {
      global $customer_id;
      unset($this->contents[$products_id]);
// remove from database
      if (tep_session_is_registered('customer_id')) {
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $customer_id . "' and products_id = '" . $products_id . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_OPTIONS . " where customers_id = '" . $customer_id . "' and products_id = '" . $products_id . "'");
      }

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }
/*-----------------------------
 功能：全部删除 
 参数：无
 返回值：无
 ----------------------------*/
    function remove_all() {
      $this->reset();
    }
/*-----------------------------
 功能：获取产品编号列表 
 参数：无
 返回值：产品编号列表(string)
 ----------------------------*/
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
/*-----------------------------
 功能：购物车计算金额 
 参数：无
 返回值：无
 ----------------------------*/
    function calculate() {
      // 支付金额
      $this->total = 0;
      // 交易金额
      $this->abs = 0;
      $this->weight = 0;
      if (!is_array($this->contents)) return 0;

      reset($this->contents);
      $check_products_option = $_SESSION['change_option_id']; 
      while (list($products_id, ) = each($this->contents)) {
        if(in_array($products_id,$check_products_option)){continue;}
        $qty = $this->contents[$products_id]['qty'];

// products price
        $product_query = tep_db_query("select products_id, products_price,
            products_price_offset, products_tax_class_id, products_weight,
            products_small_sum,price_type from " . TABLE_PRODUCTS . " where products_id='" . tep_get_prid($products_id) . "'");
        if ($product = tep_db_fetch_array($product_query)) {
          $prid = $product['products_id'];
          $products_tax = tep_get_tax_rate($product['products_tax_class_id']);
          $products_price = $product['products_price'];
          $products_weight = $product['products_weight'];

      $products_price = tep_get_final_price($product['products_price'],
          $product['products_price_offset'], $product['products_small_sum'], $qty,
          $product['price_type']);
      # 添加结束 -------------------------------------------

          $this->total += tep_add_tax($products_price, $products_tax) * $qty;
          $this->abs   += abs(tep_add_tax($products_price, $products_tax) * $qty);
          $this->weight += ($qty * $products_weight);
        }
// attributes price
        $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>");
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
                    if (trim(str_replace($replace_arr, '', nl2br(stripslashes($a_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($value))))) {
                      $this->total += $qty * tep_add_tax($a_value['money'], $products_tax);
                      $this->abs += abs($qty * tep_add_tax($a_value['money'], $products_tax));
                      break; 
                    }
                  }
                }
              } else if ($attribute_price['type'] == 'textarea') {
                $t_option = @unserialize($attribute_price['option']);  
                $tmp_o_single = false; 
                if ($t_option['require'] == '0') {
                  if ($value == MSG_TEXT_NULL) {
                    $tmp_o_single = true; 
                  }
                }
                if ($tmp_o_single) {
                  $this->total += $qty * tep_add_tax('0', $products_tax);
                  $this->abs += abs($qty * tep_add_tax('0', $products_tax));
                } else {
                  $this->total += $qty * tep_add_tax($attribute_price['price'], $products_tax);
                  $this->abs += abs($qty * tep_add_tax($attribute_price['price'], $products_tax));
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
                    if (trim(str_replace($replace_arr, '', nl2br(stripslashes($ak_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($ck_value))))) {
                      $this->total += $qty * tep_add_tax($ak_value['money'], $products_tax);
                      $this->abs += abs($qty * tep_add_tax($ak_value['money'], $products_tax));
                      break; 
                    } 
                  }
                }
              } else if ($ck_attribute_price['type'] == 'textarea') {
                $tk_option = @unserialize($ck_attribute_price['option']);  
                $tk_o_single = false; 
                if ($tk_option['require'] == '0') {
                  if ($ck_value == MSG_TEXT_NULL) {
                    $tk_o_single = true; 
                  }
                }
                if ($tk_o_single) {
                  $this->total += $qty * tep_add_tax('0', $products_tax);
                  $this->abs += abs($qty * tep_add_tax('0', $products_tax));
                } else {
                  $this->total += $qty * tep_add_tax($ck_attribute_price['price'], $products_tax);
                  $this->abs += abs($qty * tep_add_tax($ck_attribute_price['price'], $products_tax));
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
/*-----------------------------------
 功能：购物车属性价格 
 参数：$products_id(string) 产品ID
 返回值：属性价格(string)
 ----------------------------------*/
    function attributes_price($products_id) {
      $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>");
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
                    if (trim(str_replace($replace_arr, '', nl2br(stripslashes($a_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($value))))) {
                      $attributes_price += $a_value['money'];
                      break; 
                    }
                  }
                }
              } else if ($attribute_price['type'] == 'textarea') { 
                $t_option = @unserialize($attribute_price['option']);  
                $tmp_o_single = false; 
                if ($t_option['require'] == '0') {
                  if ($value == MSG_TEXT_NULL) {
                    $tmp_o_single = true; 
                  }
                }
                if ($tmp_o_single) {
                  $attributes_price += 0;
                } else {
                  $attributes_price += $attribute_price['price'];
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
                    if (trim(str_replace($replace_arr, '', nl2br(stripslashes($ak_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($ck_value))))) {
                      $attributes_price += $ak_value['money'];
                      break; 
                    } 
                  }
                }
              } else if ($ck_attribute_price['type'] == 'textarea') {
                $tk_option = @unserialize($ck_attribute_price['option']);  
                $tk_o_single = false; 
                if ($tk_option['require'] == '0') {
                  if ($ck_value == MSG_TEXT_NULL) {
                    $tk_o_single = true; 
                  }
                }
                if ($tk_o_single) {
                  $attributes_price += 0;
                } else {
                  $attributes_price += $ck_attribute_price['price'];
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
/*---------------------------------
 功能：获取产品 
 参数：无
 返回值：返回产品数组(array)
 --------------------------------*/
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

      $products_price = tep_get_final_price($products['products_price'],
          $products['products_price_offset'], $products['products_small_sum'],
          $this->contents[$products_id_info]['qty'],$products['price_type']);
      # 添加结束 -------------------------------------------
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
/*------------------------------
 功能：全部显示 
 参数：无
 返回值：显示全部(string)
 -----------------------------*/
    function show_total() {
      $this->calculate();

      return $this->total;
    }
/*------------------------------
 功能：显示ABS 
 参数：无
 返回值：ABS(string)
 -----------------------------*/    
    function show_abs() {
      $this->calculate();

      return $this->abs;
    }
/*------------------------------
 功能：显示重量 
 参数：无
 返回值：重量(string)
 -----------------------------*/
    function show_weight() {
      $this->calculate();

      return $this->weight;
    }
/*-----------------------------
 功能：生成购物车ID 
 参数：$length(string) 长度
 返回值：创建购物车的随机ID值(string)
 ----------------------------*/
    function generate_cart_id($length = 5) {
      return tep_create_random_value($length, 'digits');
    }
/*----------------------------
 功能：获取内容的类型 
 参数：无
 返回值: 返回内容的类型(string)
 ---------------------------*/
    function get_content_type() {
      $this->content_type = false;

      if ( (DOWNLOAD_ENABLED == 'true') && ($this->count_contents(true) > 0) ) {
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
/*---------------------------
 功能：购物车的反序列化 
 参数：$broken(string) 字符串
 返回值：无
 --------------------------*/
    function unserialize($broken) {
      for(reset($broken);$kv=each($broken);) {
        $key=$kv['key'];
        if (gettype($this->$key)!="user function")
        $this->$key=$kv['value'];
      }
    }
/*--------------------------
 功能：获取产品ID 
 参数：$products_id(string) 产品ID
 参数：$option_info_array(string) 选项信息数组
 返回值：产品ID(string)
 -------------------------*/    
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
/*---------------------------------
 功能：添加结账选项 
 参数：$option_array(string) 选项数组
 返回值：无
 --------------------------------*/   
    function add_checkout_option($option_array)
    {
      if (!is_array($this->contents)) return false;
      if (!empty($this->contents)) {
        foreach ($this->contents as $c_key => $c_value) {
          $this->contents[$c_key]['ck_attributes'] = (isset($option_array[$c_key])?$option_array[$c_key]:NULL);           
        }
      }
    }
/*---------------------------------
 功能：清除结账属性  
 参数：无
 返回值：无
 --------------------------------*/   
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
