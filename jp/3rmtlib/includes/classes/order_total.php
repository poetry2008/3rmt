<?php
/*
  $Id$
*/

  class order_total {
    var $site_id, $modules;

// class constructor
/*------------------------
 功能：构造函数(订单总量)
 参数：$site_id(string) SITE_ID 值
 返回值：无
 -----------------------*/
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
/*-------------------------
 功能：订单总量 
 参数：无
 返回值：订单总量数组(array)
 ------------------------*/
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
                                             'sort_order' => $GLOBALS[$class]->sort_order);
              }
            }
          }
        }
      }

      return $order_total_array;
    }
/*---------------------------
 功能：订单总量的处理流程 
 参数：无
 返回值：订单总量数组(array)
 --------------------------*/ 
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

/*-------------------------------
 功能：订单的总量的配送费用
 参数：无
 返回值：配送费送的字符串(string)
 ------------------------------*/ 
    function output() {
      global $order;
      global $cart;
      global $payment, $currencies;
      //先使用global 等支付方法修改 完毕 修改成使用POST 
      global $_POST;

      $show_handle_fee = 0;

      if(isset($_SESSION['h_code_fee'])){
      $show_handle_fee = intval($_SESSION['h_code_fee']); 
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
            for ($i=0; $i<$size; $i++) {
              if ($class == 'ot_point') {
                if (isset($_SESSION['campaign_fee'])) {
                  if ($_SESSION['campaign_fee'] == 0) {
                    continue; 
                  }
                } else {
                  if ($GLOBALS[$class]->output[$i]['value'] == 0) {
                    continue; 
                  }
                }
              }
              if($class == 'ot_subtotal'){

                $sub_total = $GLOBALS[$class]->output[$i]['value'];
              }
              $colspan = NEW_STYLE_WEB===true ? ' colspan="2"' : ''; 
              $output_string .= '              <tr>' . "\n" .
                                '                <td align="right" class="main"
                                '.$colspan.'>' . $GLOBALS[$class]->output[$i]['title'] . '</td>' . "\n" .
                                '                <td align="right" class="main">';
              if ($class == 'ot_point') {
                if (isset($_SESSION['campaign_fee'])) {
                 
                  $output_string .= '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format_total(abs($_SESSION['campaign_fee']))) .  '</font>'.JPMONEY_UNIT_TEXT.'</td>' . "\n" .  '              </tr>';
                } else {
                  $output_string .= '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format_total($GLOBALS[$class]->output[$i]['value'])) . '</font>'.JPMONEY_UNIT_TEXT.'</td>' . "\n" .  '              </tr>';
                }
              } else {
                $output_string .= $currencies->format_total($GLOBALS[$class]->output[$i]['value']) . '</td>' . "\n" .  '              </tr>';
              }
            }
            $_SESSION['mailfee'] = $currencies->format($total_handle_fee);   
            if ($class == 'ot_point') {
              //配送费用
              if (isset($_SESSION['free_value'])) {
                if (!empty($_SESSION['weight_fee'])) {
                  $shipping_fee = $sub_total-$_SESSION['h_point'] > $_SESSION['free_value'] ? TEXT_SHIPPING_FREE : $currencies->format($_SESSION['weight_fee']);
                  $output_string .= '              <tr>' . "\n" .
                                  '                <td align="right" class="main" '.$colspan.'>'
                                  . TEXT_SHIPPING_FEE . '</td>' . "\n" .
                                  '                <td align="right" class="main">'
                                  . $shipping_fee . '</td>' . "\n" .
                                  '              </tr>';
                }
              } 
              if (!empty($total_handle_fee)) {
                $output_string .= '              <tr>' . "\n" .
                                  '                <td align="right" class="main" '.$colspan.'>'
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
/*-----------------------------
 功能：订单标志 
 参数：无
 返回值：判断是否返回View(string)
 ----------------------------*/
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
