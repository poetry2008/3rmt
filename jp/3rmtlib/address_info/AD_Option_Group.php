<?php
require_once "AD_Option_Item.php";
class AD_Option_Group extends AD_Option_DbRecord
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
    功能: 获得该组关联的所有元素 
    参数: 无   
    返回值: 元素信息(array) 
------------------------------------ */
  function getItems()
  {
    $sql = "select * from ".TABLE_ADDRESS." where id = ".$this->id  ." and status = '0' order by sort";
    $items =  $this->getResultObjects($sql,'AD_Option_Item');
    foreach($items as $key=>$item){
      $item->init();
    }
    return $items;
  }

/* -------------------------------------
    功能: 输出该组关联的元素的html 
    参数: $option_error_array(array) 错误信息   
    参数: $is_space(boolean) 是否出现空行   
    返回值: 元素的html(string) 
------------------------------------ */
  function render($option_error_array,$is_space = false)
  {
    //echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">';
    foreach ($this->items as $item){
      echo '<tr id="td_'.$item->name_flag.'">';
      $item->render($option_error_array,$is_space);
      echo "</tr>";
    }
    //echo '</table>';
  }
}
