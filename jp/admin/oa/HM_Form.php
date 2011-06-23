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
    $sql .=" order by ordernumber ";
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
    if (tep_orders_finished($_GET['oID'])) {
      echo "<button disabled  id='canEnd' >取引完了</button>";
    } else { 
      echo "<button disabled  id='canEnd' >保存</button>";
    }

    echo "</td><td>&nbsp;</td></tr>";
    echo '</from>';
    echo "</div>";
    ?>
    <script type='text/javascript'>
    <?php 

       if (tep_orders_finished($order->info['orders_id'])) {
	 echo "var finished = true;";
       }else {
	 echo "var finished = false;";
       }
    ?>
  　var canEnd = true;
    function checkLockOrder()
       {
	 if (finished==true){
	   return false;
	 }
         canEnd = true;
         $('.require').each(function(){
             if(canEnd){
               canEnd = eval($(this).attr('tagName')+$(this).attr('type')+'Require(this)');
             }
           });
	 if (canEnd == true){
	   $("#canEnd").removeAttr('disabled');
	 }else{
	   $("#canEnd").attr('disabled',true);
	 }
         return false;
       }
    function SPANRequire(ele)
    {
      return $(ele).text().trim().length>0;
    }
    function SPANtextRequire(ele)
    {
      return SPANRequire(ele);
    }
    function INPUTtextRequire(ele)
    {
     return INPUThiddenRequire(ele);
    }
    function INPUThiddenRequire(ele)
    {
      return $(ele).val().trim().length>0;
    }
    function cleanthisrow(ele){
      $(ele).parent().parent().children().find('input').each(
            function(){
              if($(this).attr("type")=='text' || $(this).attr("type") =='hidden'){
                $(this).val('');
              }
              if($(this).attr('checked')!='undefined'){
                $(this).removeAttr('checked');
              }
            });
      $(ele).parent().parent().children().find('span').each(
                                                            function (){
                                                              $(this).text('');
                                                            }
                                                            );
      $("#qa_form").ajaxSubmit();

    }
    function INPUTtext(e){
      return jQuery.trim(e.val()).length > 0;
    }
       $(document).ready(
                         function()
                         {
	                   //bind size fonction 
	$("#qa_form").find("input[type=text]").each(function(){
            if($(this).attr('size')){
	$(this).bind('keypress',function(){
	      if( $(this).val().length >$(this).attr('size')){
               	$(this).val($(this).val().substr(0,$(this).attr('size')));
              }
              });
            }
});
			   checkLockOrder();
                           $("#qa_form").find("input").each(function (){
     
                               $(this).change(function(){
                                   checkLockOrder();
                                   $("#qa_form").submit();
                                     });
                             });
                           $("#qa_form").submit(function(){
                               $('body').css('cursor','wait');
                               $('#wait').show();

                               $(this).find('.outform').each(function(){
                                   if($(this).attr('name').substr(0,1)!='0'){
                                     $(this).attr('name','0'+$(this).attr('name'));}});
                               $(this).ajaxSubmit(function(){ $('#wait').hide();});

                               $(this).find('.outform').each(function(){
                                   if($(this).attr('name').substr(0,1)=='0'){
                                     $(this).attr('name',$(this).attr('name').substr(1));}});
                                 return false;
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
