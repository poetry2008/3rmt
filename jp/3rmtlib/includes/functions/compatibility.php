<?php
/*
  $Id$
*/

/* -------------------------------------
    功能: 把数组里的值添加反斜杠 
    参数: $ar(array) 数组   
    返回值: 无 
------------------------------------ */
  function do_magic_quotes_gpc(&$ar) {
    if (!is_array($ar)) return false;

    while (list($key, $value) = each($ar)) {
      if (is_array($value)) {
        do_magic_quotes_gpc($value);
      } else {
        $ar[$key] = addslashes($value);
      }
    }
  }

// $HTTP_xxx_VARS are always set on php4
  if (!is_array($_GET)) $_GET = array();
  if (!is_array($_POST)) $_POST = array();
  if (!isset($HTTP_COOKIE_VARS)) $HTTP_COOKIE_VARS = array();
  if (!is_array($HTTP_COOKIE_VARS)) $HTTP_COOKIE_VARS = array();

// handle magic_quotes_gpc turned off.
  if (!get_magic_quotes_gpc()) {
    do_magic_quotes_gpc($_GET);
    do_magic_quotes_gpc($_POST);
    do_magic_quotes_gpc($HTTP_COOKIE_VARS);
  }

  if (!function_exists('array_splice')) {
/* -------------------------------------
    功能: 在数组中根据条件删除一段值 
    参数: $array(array) 数组   
    参数: $maximum(int) 取出元素的开始位置   
    返回值: 无
------------------------------------ */
    function array_splice(&$array, $maximum) {
      if (sizeof($array) >= $maximum) {
        for ($i=0; $i<$maximum; $i++) {
          $new_array[$i] = $array[$i];
        }
        $array = $new_array;
      }
    }
  }

  if (!function_exists('in_array')) {
/* -------------------------------------
    功能: 查看元素是否在数组中 
    参数: $lookup_value(string) 查看的元素   
    参数: $lookup_array(array) 数组   
    返回值: 是否在数组中(boolean)
------------------------------------ */
    function in_array($lookup_value, $lookup_array) {
      reset($lookup_array);
      while (list($key, $value) = each($lookup_array)) {
        if ($value == $lookup_value) return true;
      }

      return false;
    }
  }

  if (!function_exists('array_reverse')) {
/* -------------------------------------
    功能: 将数组中的元素顺序翻转 
    参数: $array(array) 数组   
    返回值: 翻转后的数组(array)
------------------------------------ */
    function array_reverse($array) {
      for ($i=0, $n=sizeof($array); $i<$n; $i++) $array_reversed[$i] = $array[($n-$i-1)];

      return $array_reversed;
    }
  }

  if (!function_exists('constant')) {
/* -------------------------------------
    功能: 取得常量的值 
    参数: $constant(string) 常量的名   
    返回值: 常量的值(string)
------------------------------------ */
    function constant($constant) {
      eval("\$temp=$constant;");

      return $temp;
    }
  }

  if (!function_exists('is_null')) {
/* -------------------------------------
    功能: 判断是否为空 
    参数: $value(string) 值   
    返回值: 是否为空(boolean)
------------------------------------ */
    function is_null($value) {
      if (is_array($value)) {
        if (sizeof($value) > 0) {
          return false;
        } else {
          return true;
        }
      } else {
        if (($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0)) {
          return false;
        } else {
          return true;
        }
      }
    }
  }

  if (!function_exists('array_merge')) {
/* -------------------------------------
    功能: 把两个或多个数组合并为一个数组 
    参数: $array1(array) 数组   
    参数: $array2(array) 数组   
    参数: $array3(array) 数组   
    返回值: 合并后的数组(array)
------------------------------------ */
    function array_merge($array1, $array2, $array3 = '') {
      if (empty($array3) && !is_array($array3)) $array3 = array();
      while (list($key, $val) = each($array1)) $array_merged[$key] = $val;
      while (list($key, $val) = each($array2)) $array_merged[$key] = $val;
      if (sizeof($array3) > 0) while (list($key, $val) = each($array3)) $array_merged[$key] = $val;

      return (array) $array_merged;
    }
  }

  if (!function_exists('is_numeric')) {
/* -------------------------------------
    功能: 判断是否为数字 
    参数: $param(string) 值   
    返回值: 是否为数字(boolean)
------------------------------------ */
    function is_numeric($param) {
      return ereg('^[0-9]{1,50}.?[0-9]{0,50}$', $param);
    }
  }

  if (!function_exists('array_slice')) {
/* -------------------------------------
    功能: 在数组中根据条件取出一段值 
    参数: $array(array) 数组   
    参数: $offset(int) 取出元素的开始位置   
    参数: $length(int) 长度   
    返回值: 取出的一段值(array)
------------------------------------ */
    function array_slice($array, $offset, $length = 0) {
      if ($offset < 0 ) {
        $offset = sizeof($array) + $offset;
      }
      $length = ((!$length) ? sizeof($array) : (($length < 0) ? sizeof($array) - $length : $length + $offset));
      for ($i = $offset; $i<$length; $i++) {
        $tmp[] = $array[$i];
      }

      return $tmp;
    }
  }

  if (!function_exists('array_map')) {
/* -------------------------------------
    功能: 用户自定义函数作用后的数组 
    参数: $callback(string) 函数名   
    参数: $array(array) 数组   
    返回值: 处理后的数组(array)
------------------------------------ */
    function array_map($callback, $array) {
      if (is_array($array)) {
        $_new_array = array();
        reset($array);
        while (list($key, $value) = each($array)) {
          $_new_array[$key] = array_map($callback, $array[$key]);
        }
        return $_new_array;
      } else {
        return $callback($array);
      }
    }
  }

  if (!function_exists('str_repeat')) {
/* -------------------------------------
    功能: 把字符串重复指定的次数 
    参数: $string(string) 字符串   
    参数: $number(int) 重复次数   
    返回值: 处理后的字符窜(string)
------------------------------------ */
    function str_repeat($string, $number) {
      $repeat = '';

      for ($i=0; $i<$number; $i++) {
        $repeat .= $string;
      }

      return $repeat;
    }
  }
?>
