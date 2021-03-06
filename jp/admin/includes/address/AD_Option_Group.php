<?php
require_once "AD_Option_Item.php";
class AD_Option_Group extends AD_Option_DbRecord
{
  var $items;
  var $name;
  function __construct()
  {
    $this->items = $this->getItems();
  }
/*------------------------------
 功能：获得的元素
 参数：无
 返回值：返回的元素
 -----------------------------*/
  function getItems()
  {
    $sql = "select * from ".TABLE_ADDRESS." where id = ".$this->id  ." and status = '0' order by sort";
    $items =  $this->getResultObjects($sql,'AD_Option_Item');
    foreach($items as $key=>$item){
      $item->init();
    }
    return $items;
  }
/*------------------------------
 功能：显示输出
 参数：$option_error_array(array) 错误选项
 返回值：无
 -----------------------------*/
  function render($option_error_array)
  {
    foreach ($this->items as $item){
      echo '<tr id="td_'. $item->name_flag .'">';
      $item->render($option_error_array);
      echo "</tr>";
    }
  }
}
