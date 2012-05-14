<?php
require_once "HM_Option_Item.php";
class HM_Option_Group extends Option_DbRecord
{
  var $items;
  var $name;
  function __construct()
  {
    $this->items = $this->getItems();
  }
  function getItems()
  {
    $sql = "select * from ".TABLE_OPTION_ITEM." where group_id = ".$this->id  ." and status = '1' order by sort_num";
    $items =  $this->getResultObjects($sql,'HM_Option_Item');
    foreach($items as $key=>$item){
      $item->init();
    }
    return $items;
  }

  function render($option_error_array, $is_product_info = 0, $pre_item_str = '', $cart_obj = '', $ptype = false)
  {
    echo "<table class='option_table'>";
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
 
  function get_product_option()
  {
    $return_array = array(); 
    foreach ($this->items as $item){
      if ($item->place_type == 0) {
        $return_array[] = $item->name.'_'.$item->group_id.'_'.$item->id; 
      }
    }
    return $return_array; 
  }
}
