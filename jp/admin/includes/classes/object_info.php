<?php
/*
  $Id$

*/

  class objectInfo {

// class constructor
/*-------------------------------
 功能: 对象信息
 参数: $object_array(array) 对象数组
 返回值: 无
 ------------------------------*/
    function objectInfo($object_array) {
      reset($object_array);
      while (list($key, $value) = each($object_array)) {
        $this->$key = tep_db_prepare_input($value);
      }
    }
  }
?>
