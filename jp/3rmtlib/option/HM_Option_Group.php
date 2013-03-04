<?php
require_once "HM_Option_Item.php";
class HM_Option_Group extends Option_DbRecord
{
  var $items;
  var $name;

/* -------------------------------------
    功能: 构造函数 
    参数: 无   
    返回值: 无 
------------------------------------ */
  function __construct()
  {
    $this->items = $this->getItems();
  }

/* -------------------------------------
    功能: 获得该组关联的元素 
    参数: 无   
    返回值: 该组关联的元素(array) 
------------------------------------ */
  function getItems()
  {
    $sql = "select * from ".TABLE_OPTION_ITEM." where group_id = ".$this->id  ." and status = '1' order by sort_num,title";
    $items =  $this->getResultObjects($sql,'HM_Option_Item');
    foreach($items as $key=>$item){
      $item->init();
    }
    return $items;
  }

/* -------------------------------------
    功能: 输出该组关联的元素的html 
    参数: $option_error_array(array) 错误信息   
    参数: $is_product_info(int) 是否在商品信息页   
    参数: $pre_item_str(string) 变量名的前缀   
    参数: $cart_obj(obj) 购物车对象   
    参数: $ptype(boolean) 是否是预约转正式页   
    参数: $cflag(int) 标识   
    返回值: 元素的html(string) 
------------------------------------ */
  function render($option_error_array, $is_product_info = 0, $pre_item_str = '', $cart_obj = '', $ptype = false, $cflag)
  {
    $pro_pos  = strpos($_SERVER['PHP_SELF'], 'product_info.php');
    if ($pro_pos !== false) {
      echo "<table class='option_table' cellspacing='1' cellpadding='3' border='0'>";
    } else {
      if(NEW_STYLE_WEB===true){
      echo "<table class='option_table' border='0' cellspacing='0' cellpadding='0'>";
      }else{
      echo "<table class='option_table'>";
      }
    }
    foreach ($this->items as $item){
      if ($is_product_info == 1) {
        if ($item->place_type == 0) {
          continue; 
        }
      } else if ($is_product_info == 0) {
        if ($item->place_type == 1) {
          continue; 
        }
      }
      echo "<tr>";
      $item->render($option_error_array, $pre_item_str, $cart_obj, $ptype);
      echo "</tr>";
    }
    echo "</table>";
  }
}
