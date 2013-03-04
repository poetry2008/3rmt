<?php
/*
  $Id$
*/

  class currencies {
    var $currencies;

// class constructor
/*--------------------
 功能：查看货币
 参数：无
 返回值：无
 -------------------*/
    function currencies() {
      $this->currencies = array();
      $currencies_query = tep_db_query("
          select code, 
                 title, 
                 symbol_left, 
                 symbol_right, 
                 decimal_point, 
                 thousands_point, 
                 decimal_places, 
                 value 
          from " . TABLE_CURRENCIES
      );
      while ($currencies = tep_db_fetch_array($currencies_query)) {
        $this->currencies[$currencies['code']] = array('title' => $currencies['title'],
                                                       'symbol_left' => $currencies['symbol_left'],
                                                       'symbol_right' => $currencies['symbol_right'],
                                                       'decimal_point' => $currencies['decimal_point'],
                                                       'thousands_point' => $currencies['thousands_point'],
                                                       'decimal_places' => $currencies['decimal_places'],
                                                       'value' => $currencies['value']);
      }
    }

// class methods
/*-----------------------------
 功能：货币价格格式 
 参数；$number(number) 价格
 参数：$calculate_currency_value(boolean) 计算货币价值
 参数：$currency_type(string) 货币类型
 参数：$currency_value(string) 货币的价值
 返回值：货币的价格(string) 
 ----------------------------*/
    function format($number, $calculate_currency_value = true, $currency_type = '', $currency_value = '') {
      $number = abs($number);
      global $currency;
      global $language;

      if (empty($currency_type)) $currency_type = $currency;

      $symbol_right = ($currency_type == 'JPY' && $language != 'japanese')
          ? 'YEN'
          : $this->currencies[$currency_type]['symbol_right'];

      if ($calculate_currency_value == true) {
        $rate = (tep_not_null($currency_value)) ? $currency_value : $this->currencies[$currency_type]['value'];
        $format_string = $this->currencies[$currency_type]['symbol_left'] . number_format($number * $rate, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $symbol_right;
// if the selected currency is in the european euro-conversion and the default currency is euro,
// the currency will displayed in the national currency and euro currency
        if ( (DEFAULT_CURRENCY == 'EUR') && ($currency_type == 'DEM' || $currency_type == 'BEF' || $currency_type == 'LUF' || $currency_type == 'ESP' || $currency_type == 'FRF' || $currency_type == 'IEP' || $currency_type == 'ITL' || $currency_type == 'NLG' || $currency_type == 'ATS' || $currency_type == 'PTE' || $currency_type == 'FIM' || $currency_type == 'GRD') ) {
          $format_string .= ' <small>[' . $this->format($number, true, 'EUR') . ']</small>';
        }
      } else {
        $format_string = $this->currencies[$currency_type]['symbol_left'] . number_format($number, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $symbol_right;
      }

      return $format_string;
    }
    
    // 负数红色
/*-----------------------------
 功能：货币价格负数显示红色 
 参数；$number(number) 价格
 参数：$calculate_currency_value(boolean) 计算货币价值
 参数：$currency_type(string) 货币类型
 参数：$currency_value(string) 货币的价值
 返回值：货币的价格(string) 
 ----------------------------*/
 
    function format_total($number, $calculate_currency_value = true, $currency_type = '', $currency_value = '') {
      $tmp_number = $number; 
      $number = abs($number);
      global $currency;
      global $language;

      if (empty($currency_type)) $currency_type = $currency;

      $symbol_right = ($currency_type == 'JPY' && $language != 'japanese')
          ? 'YEN'
          : $this->currencies[$currency_type]['symbol_right'];

      if ($calculate_currency_value == true) {
        $rate = (tep_not_null($currency_value)) ? $currency_value : $this->currencies[$currency_type]['value'];
        if ($tmp_number < 0) {
          $format_string = $this->currencies[$currency_type]['symbol_left'] . '<font color="#ff0000">'.number_format($number * $rate, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . '</font>'.$symbol_right;
        } else {
          $format_string = $this->currencies[$currency_type]['symbol_left'] . number_format($number * $rate, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $symbol_right;
        }
// if the selected currency is in the european euro-conversion and the default currency is euro,
// the currency will displayed in the national currency and euro currency
        if ( (DEFAULT_CURRENCY == 'EUR') && ($currency_type == 'DEM' || $currency_type == 'BEF' || $currency_type == 'LUF' || $currency_type == 'ESP' || $currency_type == 'FRF' || $currency_type == 'IEP' || $currency_type == 'ITL' || $currency_type == 'NLG' || $currency_type == 'ATS' || $currency_type == 'PTE' || $currency_type == 'FIM' || $currency_type == 'GRD') ) {
          $format_string .= ' <small>[' . $this->format($number, true, 'EUR') . ']</small>';
        }
      } else {
        if ($tmp_number < 0) {
          $format_string = $this->currencies[$currency_type]['symbol_left'] . '<font color="#ff0000">' . number_format($number, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . '</font>'.$symbol_right;
        } else {
          $format_string = $this->currencies[$currency_type]['symbol_left'] .  number_format($number, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $symbol_right;
        }
      }

      return $format_string;
    }
/*-------------------------
 功能：获取价格
 参数：$code(string) 货币值
 返回值：货币价格(string) 
 ------------------------*/
    function get_value($code) {
      return $this->currencies[$code]['value'];
    }
/*------------------------
 功能：获取货币价值的小数位数 
 参数：$code(string) 价值
 返回值：货币价值的小数位数(string) 
 -----------------------*/
    function get_decimal_places($code) {
      return $this->currencies[$code]['decimal_places'];
    }
/*------------------------
 功能: 显示价格
 参数: $products_price(string) 产品价格
 参数: $products_tax(string) 产品税
 参数: $quantity(int)  数量
 返回值: 返回的价格(string)
-------------------------*/
    function display_price($products_price, $products_tax, $quantity = 1) {
      return $this->format(tep_add_tax($products_price, $products_tax) * $quantity);
    }

/*------------------------
 功能：进行金额的零头处理
 参数：$number: 金额
 参数: $round_type: 零头处理的类型  'drop'=取整, 'round'=四舍五入, 'raise'=进位
 参数: $currency_type: 货币代码 (例) 'JPY'
 返回值：返回处理后金额(string)
------------------------*/
    function round_off($number, $round_type = '', $currency_type = '') {
      global $currency;
      $a_comp = array('drop'=>0.00001, 'round'=>0.50001, 'raise'=>0.99999);

      if ($round_type == '') $round_type = TAX_ROUND_OPTION;
      $comp_val = $a_comp[$round_type];
      if (!$comp_val) $comp_val = $a_comp['round'];

      if ($currency_type == '') $currency_type = $currency;

      $power = pow(10.0, doubleval($this->currencies[$currency_type]['decimal_places']));

      return floor($number * $power + $comp_val) / $power;
    }
  }
?>
