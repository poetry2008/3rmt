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
    echo "<div id='orders_answer'>";
    echo "<form id='qa_form' action='".$this->action."' method='post'>";
    echo "<table width='100%' >";

    foreach ($this->groups as $group){
      $group->render();
    }

    echo "<tr><td class='main'>&nbsp;"; 
    echo "<input type='hidden' name='form_id' value='".$this->id."' />";
    echo "</td><td>";
    echo "<input type='submit' value='保存'  />";
    echo "</td><td>&nbsp;</td></tr>";
    echo '</from>';
    echo "</div>";
    ?>
    <script type='text/javascript'>
    function INPUTtext(e){
      return jQuery.trim(e.val()).length > 0;
    }
       $(document).ready(
                         function()
                         {
                           $("#qa_form").submit(function(){
                               var couldPost = true;
                               $(".require").each(function(){
                                 userfun = $(this).attr("tagName")+$(this).attr('type');
                                 if(couldPost){
                                   couldPost = eval(userfun+'($(this))');
                                 }
                                 });
                               return couldPost;
                             });

                         }

                         );

    </script>
    <?php
    
    
  }
  function setAction($actionPage)
  {
    $this->action = $actionPage;
  }
}
