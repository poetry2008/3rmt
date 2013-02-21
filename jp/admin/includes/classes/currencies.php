<?php
/*
  $Id$
*/

////
  class currencies {
    var $currencies;

// class constructor
/*-------------------------------------
 功能: 货币方法
 参数: $decimal_places(string)  小数位数
 返回值: 无
 ------------------------------------*/
    function currencies($decimal_places = null) {
      $this->currencies = array();
      $currencies_query = tep_db_query("select code, title, symbol_left, symbol_right, decimal_point, thousands_point, decimal_places, value from " . TABLE_CURRENCIES);
      while ($currencies = tep_db_fetch_array($currencies_query)) {
        $this->currencies[$currencies['code']] = array('title' => $currencies['title'],
                                                       'symbol_left' => $currencies['symbol_left'],
                                                       'symbol_right' => $currencies['symbol_right'],
                                                       'decimal_point' => $currencies['decimal_point'],
                                                       'thousands_point' => $currencies['thousands_point'],
                                                       'decimal_places' => $decimal_places ? $decimal_places : $currencies['decimal_places'],
                                                       'value' => $currencies['value']);
      }
    }

// class methods
/*------------------------------------
 功能: 格式
 参数: $number(string) 金额
 参数: $calculate_currency_value(bool) 计算货币价值
 参数: $currency_type(string)  货币类型
 参数: $currency_value(string) 货币的价值
 返回值: 格式字符串(string) 
 -----------------------------------*/
    function format($number, $calculate_currency_value = true, $currency_type =
        DEFAULT_CURRENCY, $currency_value = '',$is_abs = true) {
      if($is_abs){
      $number = abs($number);
      }
      $this->currencies[$currency_type]['symbol_right']=TEXT_MONEY_SYMBOL;
      if ($calculate_currency_value) {
        $rate = ($currency_value) ? $currency_value : $this->currencies[$currency_type]['value'];
        $format_string = $this->currencies[$currency_type]['symbol_left'] . number_format($number * $rate, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $this->currencies[$currency_type]['symbol_right'];
// if the selected currency is in the european euro-conversion and the default currency is euro,
// the currency will displayed in the national currency and euro currency
        if ( (DEFAULT_CURRENCY == 'EUR') && ($currency_type == 'DEM' || $currency_type == 'BEF' || $currency_type == 'LUF' || $currency_type == 'ESP' || $currency_type == 'FRF' || $currency_type == 'IEP' || $currency_type == 'ITL' || $currency_type == 'NLG' || $currency_type == 'ATS' || $currency_type == 'PTE' || $currency_type == 'FIM' || $currency_type == 'GRD') ) {
          $format_string .= ' <small>[' . $this->format($number, true, 'EUR') . ']</small>';
        }
      } else {
        $format_string = $this->currencies[$currency_type]['symbol_left'] . number_format($number, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $this->currencies[$currency_type]['symbol_right'];
      }
      $arr = $arr2 = array();
      for($i=0;$i<10;$i++) {
        $arr[] = '.'.(string)$i.'0';
        if ($i == 0) 
          $arr2[] = '';
        else 
          $arr2[] = '.'.(string)$i;
      }
      return str_replace($arr,$arr2,$format_string);
    }
/*---------------------------------
 功能: 总的格式(负数红色)
 参数: $number(string) 金额
 参数: $calculate_currency_value(bool) 计算货币价值
 参数: $currency_type(string)  货币类型
 参数: $currency_value(string) 货币的价值
 返回值: 格式字符串(string) 
 --------------------------------*/
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
        } else {
          $format_string = $this->currencies[$currency_type]['symbol_left'] . '<font color="#ff0000">' . number_format($number, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . '</font>'.$symbol_right;
        }
      }

      return $format_string;
    }
    
// class methods
/*----------------------------------------
 功能: 区分正负的方法 
 参数: $number(string) 金额
 参数: $calculate_currency_value(bool) 计算货币价值
 参数: $currency_type(string)  货币类型
 参数: $currency_value(string) 货币的价值
 返回值: 格式字符串(string)
 ---------------------------------------*/
    function format2($number, $calculate_currency_value = true, $currency_type = DEFAULT_CURRENCY, $currency_value = '') {
      //$number = abs($number);
      if ($calculate_currency_value) {
        $rate = ($currency_value) ? $currency_value : $this->currencies[$currency_type]['value'];
        $format_string = $this->currencies[$currency_type]['symbol_left'] . number_format($number * $rate, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $this->currencies[$currency_type]['symbol_right'];
// if the selected currency is in the european euro-conversion and the default currency is euro,
// the currency will displayed in the national currency and euro currency
        if ( (DEFAULT_CURRENCY == 'EUR') && ($currency_type == 'DEM' || $currency_type == 'BEF' || $currency_type == 'LUF' || $currency_type == 'ESP' || $currency_type == 'FRF' || $currency_type == 'IEP' || $currency_type == 'ITL' || $currency_type == 'NLG' || $currency_type == 'ATS' || $currency_type == 'PTE' || $currency_type == 'FIM' || $currency_type == 'GRD') ) {
          $format_string .= ' <small>[' . $this->format($number, true, 'EUR') . ']</small>';
        }
      } else {
        $format_string = $this->currencies[$currency_type]['symbol_left'] . number_format($number, $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $this->currencies[$currency_type]['symbol_right'];
      }
      $arr = $arr2 = array();
      for($i=0;$i<10;$i++) {
        $arr[] = '.'.(string)$i.'0';
        if ($i == 0) 
          $arr2[] = '';
        else 
          $arr2[] = '.'.(string)$i;
      }
      return str_replace($arr,$arr2,$format_string);
    }
    
/*-------------------------------------
 功能: 全部的格式方法    
 参数: $number(string) 金额
 参数: $calculate_currency_value(bool) 计算货币价值
 参数: $currency_type(string)  货币类型
 参数: $currency_value(string) 货币的价值
 返回值: 格式字符串(string)
 ------------------------------------*/
    function ot_total_format($number, $calculate_currency_value = true, $currency_type = DEFAULT_CURRENCY, $currency_value = '') {
      $number = abs($number);
      if ($calculate_currency_value) {
        $rate = ($currency_value) ? $currency_value : $this->currencies[$currency_type]['value'];
        $format_string = $this->currencies[$currency_type]['symbol_left'] . number_format($number * $rate, 0, $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $this->currencies[$currency_type]['symbol_right'];
        if ( (DEFAULT_CURRENCY == 'EUR') && ($currency_type == 'DEM' || $currency_type == 'BEF' || $currency_type == 'LUF' || $currency_type == 'ESP' || $currency_type == 'FRF' || $currency_type == 'IEP' || $currency_type == 'ITL' || $currency_type == 'NLG' || $currency_type == 'ATS' || $currency_type == 'PTE' || $currency_type == 'FIM' || $currency_type == 'GRD') ) {
          $format_string .= ' <small>[' . $this->format($number, true, 'EUR') . ']</small>';
        }
      } else {
        $format_string = $this->currencies[$currency_type]['symbol_left'] . number_format($number, 0, $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $this->currencies[$currency_type]['symbol_right'];
      }
      return $format_string;
    }
/*-----------------------------------
 功能: 获取的值
 参数: $code(string) 代码
 返回值: 货币代码值
 ----------------------------------*/
    function get_value($code) {
      return $this->currencies[$code]['value'];
    }
/*----------------------------------
 功能: 显示价格
 参数: $products_price(string) 产品价格
 参数: $products_tax(string) 产品税
 参数: $quantity(number)  数量
 返回值: 返回的价格(string)
 ---------------------------------*/
    function display_price($products_price, $products_tax, $quantity = 1) {
      return $this->format(tep_add_tax($products_price, $products_tax) * $quantity);
    }

/*----------------------------------
 功能:进行金额的尾数处理
 参数: $number(number) 金额
 参数: $round_type(string) 尾数处理的类型 
 参数: $currency_type(string) 货币代码 (例) 'JPY'
 返回值: 返回的价格(string)
 ---------------------------------*/
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
