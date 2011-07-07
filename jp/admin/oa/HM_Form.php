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
    $this->orders_id = $orders_id;

    foreach ($this->groups as $gk=>$group){
      foreach ($this->groups[$gk]->items as $ikey=>$item){
        $this->groups[$gk]->items[$ikey]->loadDefaultValue($orders_id,$this->id,$this->groups[$gk]->id);
      }
    }
    $sql = 'select * from '.TABLE_OA_FORMVALUE." where form_id = '".$this->id.'" and orders_id ="'.$orders_id.'"';
  }
  function getGroups()
  {
    $sql = "select g.*,$this->id as form_id ";
    $sql .=" from ".TABLE_OA_FORM_GROUP." fg,".TABLE_OA_GROUP." g ";
    $sql .=" where fg.form_id = ".$this->id;
    $sql .=" and fg.group_id= g.id ";
    $sql .=" order by fg.ordernumber ,fg.id ";
    $groups =  $this->getResultObjects($sql,'HM_Group');
    return $groups;
  }
  function render()
  {
    echo "<div id='orders_answer'>";
    echo "<form id='qa_form' action='".$this->action."' method='post'>";
    echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'>";
    foreach ($this->groups as $group){
      $group->render();
    }
    echo "<tr><td class='main' colspan='3' align='right'>&nbsp;"; 
    echo "<input type='hidden' name='form_id' value='".$this->id."' /><div id='canEndDiv'><button onclick='finishTheOrder()'  id='canEnd' >取引完了</button></div>";
    echo "</td>";
    // if(!tep_orders_finishqa($this->orders_id)) {
    //echo "<button onclick='finishTheOrder()'  id='canEnd' >取引完了</button>";
    //    }
    echo "</tr>";
    echo '</from>';
    echo "</div>";
    ?>
      <script type='text/javascript'>
      <?php 

      if (tep_orders_finishqa($this->orders_id)) {
        echo "var finished = true;";
      }else {
        echo "var finished = false;";
      }
    ?>
  　var canEnd = false;
    function checkLockOrder()
    {
      if (finished==true){
        return false;
      }
      canEnd = true;
      if($('.require').length ==0 ){
        canEnd = true;
      }else{
      $('.require').each(function(ele){
          if(canEnd == true){
          canEnd = eval($(this).tagName+$(this).attr('type')+'Require(this)');
          }
          });
      }
      if (canEnd == true){
        //	   $("#canEnd").removeAttr('disabled');
        $("#canEndDiv").show();
      }else{
        //	   $("#canEnd").attr('disabled',true);
        $("#canEndDiv").hide();
      }
      //     alert('check');
      return false;

    }
    function SPANRequire(ele)
    {
      return $(ele).text().length>0;
    }
    function SPANtextRequire(ele)
    {
      return SPANRequire(ele);
    }
    function INPUTtextRequire(ele)
    {
      return INPUThiddenRequire(ele);
    }
    function undefinedtextRequire(ele)
    {
             return INPUThiddenRequire(ele);
    }

    function undefinedhiddenRequire(ele)
    {
             return INPUThiddenRequire(ele);
    }
    function INPUThiddenRequire(ele)
    {
      return $(ele).val().length>0;
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
      checkLockOrder();
      $("#qa_form").ajaxSubmit();

    }
    function INPUTtext(e){
      return jQuery.trim($(e).val()).length > 0;
    }
    function disableQA()
    {
      $("#qa_form").find('input').each(function(){
          $(this).attr('disabled',true);
          });
      $("#qa_form").find('button').each(function(){
          $(this).attr('disabled',true);
          });
      $("#qa_form").find('.clean').each(function(){
          $(this).hide();
          });


    }
    function finishTheOrder()
    {
      $.ajax({
url:'oa_ajax.php?action=finish&oID=<?php echo $_GET["oID"]?>',
type:'post',    
beforeSend: function(){$('body').css('cursor','wait');$("#wait").show()},
async : false,
success: function(data){
$("#canEndDiv").hide();
$("#wait").hide();
$('body').css('cursor','');
disableQA();
window.location.href='orders.php';
}
}
);
      }
  $(document).ready(
      function()
      {
      <?php 
      if(tep_orders_finishqa($this->orders_id)) {
      ?>
      disableQA();
      return 0;
      <?
      }
      ?>

      //bind size fonction 
      $("#qa_form").find("input[type=text]").each(function(){
        if($(this).attr('size')){
        $(this).bind('keyup',function(){
          checkLockOrder();
          if( $(this).val().length >$(this).attr('size')){
          //               	$(this).val($(this).val().substr(0,$(this).attr('size')));
          $(this).parent().parent().find('.alertmsg').remove();
          $("<span class='alertmsg'> 文字の最大入力は"+$(this).attr('size')+"です。"+$(this).attr('size')+"以内にしてください。</span>").insertAfter($(this).next());
          }else{
          $(this).parent().parent().find('.alertmsg').remove();
          }
          checkLockOrder();

          });
        }
        });
  checkLockOrder();
  $("#qa_form").find("input").each(function (){
      $(this).keyup(function(){
        checkLockOrder();
        });
      $(this).click(function(ele){
        checkLockOrder();
        $("#qa_form").submit();
        });
      $(this).change(function(ele){
        checkLockOrder();
        //                                   alert($("input|[name=dfossrrfwwkvomzw_6_1_107]").val());
        $("#qa_form").submit();
        });
      });
  $("#qa_form").submit(function(){

        $('body').css('cursor','wait');
        $('#wait').show();

        $(this).find('.outform').each(function(){
          if($(this).attr('name').substr(0,1)!='0'){
          $(this).attr('name','0'+$(this).attr('name'));}});
        $(this).ajaxSubmit(function(){ $('#wait').hide();           $('body').css('cursor','');});

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
