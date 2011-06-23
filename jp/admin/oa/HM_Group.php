<?php
require_once "HM_Item.php";
class HM_Group extends DbRecord
{
  var $items;
  var $form_id;
  var $name;
  function __construct()
  {
    $this->items = $this->getItems();
  }
  function getItems()
  {
    $sql = "select *,group_id,".$this->form_id ." as form_id from ".TABLE_OA_ITEM." where group_id = ".$this->id  ." order by  ordernumber ,id";
    $items =  $this->getResultObjects($sql,'HM_Item');
    foreach($items as $key=>$item){
      $item->init();
    }
    return $items;
  }

  function render()
  {
    echo "<tr>";
    echo "<td class='main' valign='top'>";
    echo $this->name;
    echo empty($this->name)?"":':';
    echo "</td>";
    echo "<td class='main' >";
    echo "<table><tr><td>";
    foreach ($this->items as $item){
      echo "<tr>";
      $item->render();
      echo "</tr>";
    }
    echo "</table>";
    echo "</td>";    
    echo "<td class='main' align='right'><img class='clean' src='images/icons/icon_cancel.gif' onclick='cleanthisrow(this)'></td>";
    echo "</tr>";
  }
}
