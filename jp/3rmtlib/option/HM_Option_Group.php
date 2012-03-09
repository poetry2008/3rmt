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

  function render($option_error_array)
  {
    echo "<table class='option_table'>";
    foreach ($this->items as $item){
      echo "<tr>";
      $item->render($option_error_array);
      echo "</tr>";
    }
    echo "</table>";
  }
}
