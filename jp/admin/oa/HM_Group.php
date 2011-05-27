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
//    var_dump($this->group_id);
    $sql = "select *,group_id,".$this->form_id ." as form_id from ".TABLE_OA_ITEM." where group_id = ".$this->id;
    $items =  $this->getResultObjects($sql,'HM_Item');
    foreach($items as $key=>$item){
      $item->init();
    }
    return $items;
  }

  function render()
  {
    echo $this->name;
    echo empty($this->name)?"":':';
    foreach ($this->items as $item){
      $item->render();
    }
  }
}
