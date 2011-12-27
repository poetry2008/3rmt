<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class order_total {
    var $site_id, $modules;

// class constructor
    function order_total($site_id = 0) {
      global $language;

      if (defined('MODULE_ORDER_TOTAL_INSTALLED') && tep_not_null(MODULE_ORDER_TOTAL_INSTALLED)) {
        $this->modules = explode(';', MODULE_ORDER_TOTAL_INSTALLED);

        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          include(DIR_WS_LANGUAGES . $language . '/modules/order_total/' . $value);
          include(DIR_WS_MODULES . 'order_total/' . $value);

          $class = substr($value, 0, strrpos($value, '.'));
          $GLOBALS[$class] = new $class($site_id);
        }
      }
    }

    function process() {
      $order_total_array = array();
      if (is_array($this->modules)) {
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $GLOBALS[$class]->process();

            for ($i=0, $n=sizeof($GLOBALS[$class]->output); $i<$n; $i++) {
              if (tep_not_null($GLOBALS[$class]->output[$i]['title'])) {
                $order_total_array[] = array('code' => $GLOBALS[$class]->code,
                                             'title' => $GLOBALS[$class]->output[$i]['title'],
                                             'text' => "",
                                             'value' => $GLOBALS[$class]->output[$i]['value'],
                                             'sort_order' => $GLOBALS[$class]->sort_order,
                                             'shipping_pid' => isset($GLOBALS[$class]->output[$i]['shipping_pid'])?$GLOBALS[$class]->output[$i]['shipping_pid']:null);
              }
            }
          }
        }
      }

      return $order_total_array;
    }
    
    function pre_process() {
      $order_total_array = array();
      if (is_array($this->modules)) {
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($class == 'ot_codt' || $class == 'ot_conv' || $class == 'ot_loworderfee' || $class == 'ot_tax') {
            continue; 
          }
          if ($GLOBALS[$class]->enabled) {
            $GLOBALS[$class]->pre_process();

            for ($i=0, $n=sizeof($GLOBALS[$class]->output); $i<$n; $i++) {
              if (tep_not_null($GLOBALS[$class]->output[$i]['title'])) {
                $order_total_array[] = array('code' => $GLOBALS[$class]->code,
                                             'title' => $GLOBALS[$class]->output[$i]['title'],
                                             'text' => "",
                                             'value' => $GLOBALS[$class]->output[$i]['value'],
                                             'sort_order' => $GLOBALS[$class]->sort_order);
              }
            }
          }
        }
      }

      return $order_total_array;
    }

    /*
    function output() {
      $output_string = '';
      if (is_array($this->modules)) {
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $size = sizeof($GLOBALS[$class]->output);
            for ($i=0; $i<$size; $i++) {
              $output_string .= '              <tr>' . "\n" .
                                '                <td align="right" class="main">' . $GLOBALS[$class]->output[$i]['title'] . '</td>' . "\n" .
                                '                <td align="right" class="main">' . $GLOBALS[$class]->output[$i]['text'] . '</td>' . "\n" .
                                '              </tr>';
            }
          }
        }
      }

      return $output_string;
    }
    */
    
    function output() {
      global $order;
      global $cart;
      global $payment, $currencies;
      //先使用global 等支付方法修改 完毕 修改成使用POST 
      global $shipping_method_info_arr;
      global $_POST;
      $shipping_cost_all = 0;
      foreach($shipping_method_info_arr as $shipping_key => $shipping_method_info){
        $shipping_cost_all += $shipping_method_info['shipping_cost'];
      }

      $show_handle_fee = 0;

      if(isset($_POST['code_fee'])){
      $show_handle_fee = intval($_POST['code_fee']); 
      }
      $buying_fee = 0; 
      if (isset($cart)) { 
        $bflag_single = $this->ds_count_bflags();
        if ($bflag_single == 'View') {
          $buy_table_fee = split("[:,]", MODULE_PAYMENT_BUYING_COST);
          for ($i = 0; $i < count($buy_table_fee); $i+=2) {
            if ($order->info['total'] <= $buy_table_fee[$i]) {
              $buy_add_fee = $order->info['total'].$buy_table_fee[$i+1];
              @eval("\$buy_add_fee = $buy_add_fee;");
              if (is_numeric($buy_add_fee)) {
                $buying_fee = $buy_add_fee; 
              }
              break; 
            }
          }
        }
      }
      $total_handle_fee = $show_handle_fee + $buying_fee;
      
      $output_string = '';
      if (is_array($this->modules)) {
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $size = sizeof($GLOBALS[$class]->output);
            if($class == 'ot_shipping'){
              continue;
            }
            for ($i=0; $i<$size; $i++) {
              $output_string .= '              <tr>' . "\n" .
                                '                <td align="right" class="main">' . $GLOBALS[$class]->output[$i]['title'] . '</td>' . "\n" .
                                '                <td align="right" class="main">' .
                                $currencies->format_total($GLOBALS[$class]->output[$i]['value']) . '</td>' . "\n" .
                                '              </tr>';
            }
            $_SESSION['mailfee'] = $currencies->format($total_handle_fee);   
            if($class == 'ot_subtotal'){
              if (!empty($shipping_cost_all)) {
                $output_string .= '              <tr>' . "\n" .
                                  '                <td align="right" class="main">'
                                  . TEXT_SHIPPING_COST_ALL_CONFIRMATION . '</td>' . "\n" .
                                  '                <td align="right" class="main">'
                                  . $currencies->format($shipping_cost_all) . '</td>' . "\n" .
                                  '              </tr>';
              }
            }
            if ($class == 'ot_subtotal') {
              if (!empty($total_handle_fee)) {
                $output_string .= '              <tr>' . "\n" .
                                  '                <td align="right" class="main">'
                                  . TEXT_HANDLE_FEE_CONFIRMATION . '</td>' . "\n" .
                                  '                <td align="right" class="main">'
                                  . $currencies->format($total_handle_fee) . '</td>' . "\n" .
                                  '              </tr>';
              }
            }
          }
        }
      }

      return $output_string;
    }
    
    function ds_count_bflags() {
      global $cart;
      $products = $cart->get_products();
      for ($i=0, $n=sizeof($products); $i<$n; $i++) {
        if ($products[$i]['bflag'] == '1') {
          return 'View'; 
        }
      }
      return false; 
    }
  }
?>
