<?php
global $language;
require_once "HM_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/oa/HM_Item_Myname.php';
class HM_Item_Myname extends HM_Item_Basic
{

  var $hasRequire = true;
  var $hasThename = true;
  var $hasSelect  = true;
  var $hasSubmit = true;
  var $hasFrontText  = true;  
  var $hasBackText  = true;  
  var $hasTheName  = true;

  var $must_comment = TEXT_MYNAME_MUST_COMMENT;
  var $status_comment = TEXT_MYNAME_STATUS_COMMENT;
  var $project_name_comment = TEXT_MYNAME_P_NAME_COMMENT;
  var $front_comment = TEXT_MYNAME_FRONT_COMMENT;
  var $after_comment = TEXT_MYNAME_AFTER_COMMENT;
  var $submit_name_comment = TEXT_MYNAME_SUBMIT_NAME_COMMENT; 
  function getDefaultValue()
  {
    if ($this->loaded){
      return $this->loadedValue;
    }else{
      return $this->defaultValue;
    }

  }
  function statusChange($order_id,$form_id,$group_id,$item_id)
  {
    global $ocertify;
    $user_info = tep_get_user_info($ocertify->auth_user);
    $value =$user_info['name'];
    return $this->updateValue($order_id,$form_id,$group_id,$item_id,$value);

  }

  function render($m)
  {
    if(!$m){
    if(strlen($this->thename)){
      echo "<td>";
    echo $this->thename.':';
      echo "</td>";
    }
    }
    echo "<td>";

    //如果不允许为空
    if($this->require){
      $classrequire = 'require';
    }else {
      $classrequire = '';
    }
    if ($m){
      echo "<input id='hidden".$this->formname."' type='hidden' name='".$this->formname."'>";
    }
    echo $this->beforeInput."<span id='".$this->formname."'type='text' class='".$classrequire." outform'size='".$this->size."' name='".$this->formname."' >".$this->getDefaultValue()."</span >";
    echo "<button type='button' id = '".$this->formname.'submit'."' >$this->submitName</button>".$this->afterInput;
    echo "</td>";
  }
  function renderScript($m)
  {
      ?>
      <script type='text/javascript' >

       $(document).ready(function (){
           $("#<?php echo $this->formname;?>submit").click(function(){
 $.ajax({
                 url:'pre_oa_answer_process.php?fix=user&withz=1&oID=<?php echo $_GET["oID"]?><?php if($m){echo "&fake=1";}?>',
                     type:'post',    
                     data:"form_id="+$('input|[name=form_id]').val()+"&<?php echo $this->formname;?>="+$('input|[name=<?php echo $this->formname;?>]').val(),
                     beforeSend: function(){$('body').css('cursor','wait');$("#wait").show()},
                     async : false,
                     success: function(data){
                               $("#<?php echo $this->formname;?>").text(data);		     
                               <?php 
                               if($m){
                                 ?>
                                 $("#hidden<?php echo $this->formname;?>").attr("value",data);		     
                                 <?php 
                               }
                               ?>
                               $("#wait").hide();
                               $('body').css('cursor','');
                                 checkLockOrder();
                     //                     $("#<?php echo $this->formname;?>submit").show();
                          }
                 });
             });
      });
      </script>
      <?php

  }
  static public function prepareForm($item_id = NULL)
  {

    $item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $formString  = '';
    return $formString;
  }

  
}

