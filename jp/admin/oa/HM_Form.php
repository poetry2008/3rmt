<?php
require_once "DbRecord.php";
class HM_Form extends DbRecord
{
  var $id;
  var $groups;

  function __construct()
  {
    //    $this->id = $option['id'];
    $this->groups = $this->getGroups();
  }
  function loadOrderValue($orders_id)
  {
    $id  = $this->id;

    foreach ($this->groups as $gk=>$group){
      foreach ($this->groups[$gk]->items as $ikey=>$item){
        $this->groups[$gk]->items[$ikey]->loadDefaultValue($orders_id,$this->id,$this->groups[$gk]->id);
      }
    }
    $sql = 'select * from '.TABLE_OA_FORMVALUE."where form_id = '".$this->id.'" and orders_id ="'.$orders_id.'"';
  }
  function getGroups()
  {
    $sql = "select g.*,$this->id as form_id ";
    $sql .=" from ".TABLE_OA_FORM_GROUP." fg,".TABLE_OA_GROUP." g ";
    $sql .=" where fg.form_id = ".$this->id;
    $sql .=" and fg.group_id= g.id ";
    $groups =  $this->getResultObjects($sql,'HM_Group');
    return $groups;
  }
  function render()
  {
    echo "<form action='".$this->action."' method='post'>";
    echo "<input type='hidden' name='form_id' value='".$this->id."' />";
    foreach ($this->groups as $group){
      $group->render();
    }
    echo "<input type='submit'/>";
    echo "</form>";
  }
  function setAction($actionPage)
  {
    $this->action = $actionPage;
  }
}
