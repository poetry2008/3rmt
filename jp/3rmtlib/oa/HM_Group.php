<?php
require_once "HM_Item.php";
class HM_Group extends DbRecord
{
  var $items;
  var $form_id;
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
    返回值: 关联的元素(array) 
------------------------------------ */
  function getItems()
  {
    $sql = "select *,group_id,".$this->form_id ." as form_id from ".TABLE_OA_ITEM." where group_id = ".$this->id  ." order by  ordernumber ,id";
    $items =  $this->getResultObjects($sql,'HM_Item');
    foreach($items as $key=>$item){
      $item->init();
    }
    return $items;
  }

/* -------------------------------------
    功能: 输出组以及其关联的元素信息的html 
    参数: 无   
    返回值: 组以及其关联的元素信息的html(string) 
------------------------------------ */
  function render()
  {
    echo "<tr>";
    echo "<td class='main' width='30%' valign='top' nowrap>";
    echo $this->name;
    echo empty($this->name)?"":':';
    echo "</td>";
    echo "<td class='main'>";
    echo "<table>";
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
