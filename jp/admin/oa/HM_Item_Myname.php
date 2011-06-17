<?php
require_once "HM_Item_Basic.php";
class HM_Item_Myname extends HM_Item_Basic
{

    /*
必須：○　必須

項目名_____ _____　

前方文字___ _______

SubmitName___________　

後方文字__________

ステータス[連動しない▽]（可以跟其他的状态关联）

     */
  var $hasRequire = true;
  var $hasThename = true;
  var $hasSelect  = true;
  var $hasSubmit = true;
  var $hasFrontText  = true;  
  var $hasBackText  = true;  
  var $hasTheName  = true;

  function getDefaultValue()
  {
    if ($this->loaded){
      return $this->loadedValue;
    }else{
      return $this->defaultValue;
    }

  }

  function render()
  {
    if(strlen($this->thename)){
      echo "<td>";
    echo $this->thename.':';
      echo "</td>";
    }
    echo "<td>";
    //如果不允许为空
    if($this->require){
      $classrequire = 'require';
    }else {
      $classrequire = '';
    }

    //    echo $this->beforeInput."<input type='text' class='".$classrequire." outform'size='".$this->size."' name='".$this->formname."'
    echo $this->beforeInput."<span id='".$this->formname."'type='text' class='".$classrequire." outform'size='".$this->size."' name='".$this->formname."' >".$this->getDefaultValue()."<span />".$this->afterInput;
    //    echo "<input type='button' value='",$this->SubmitName,"'>";
    if(!$this->loaded){
    echo "<button type='button' id = '".$this->formname.'submit'."' value='$this->submitName' />$this->submitName";
        }
    echo "</td>";
  }
  function renderScript()
  {
      ?>
      <script type='text/javascript' >

       $(document).ready(function (){
           $("#<?php echo $this->formname;?>submit").click(function(){
               $.ajax({
                 url:'oa_answer_process.php?fix=user&withz=1&oID=<?php echo $_GET["oID"]?>',
                     type:'post',    
                     data:"form_id="+$('input|[name=form_id]').val()+"&<?php echo $this->formname;?>="+$('input|[name=<?php echo $this->formname;?>]').val(),
                     success: function(data){
		     $("#<?php echo $this->formname;?>").text(data);		     
		     $("#<?php echo $this->formname;?>submit").hide();
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

    //    $formString .= "必須<input type='checkbox' name='require' ".$checked."/></br>\n";
    //    $formString .= "项目名<input type='text' name='thename' value='".(isset($item_value['thename'])?$item_value['thename']:'')."'/></br>\n";
    //    $formString .= "前方文字<input type='text' name='beforeInput' value='".(isset($item_value['beforeInput'])?$item_value['beforeInput']:'')."'/></br>\n";
    //    $formString .= "SubmitName<input type='text' name='submitName'      value='".(isset($item_value['submitName'])?$item_value['submitName']:'')."'/></br>\n";

    //    $formString .= "後方文字<input type='text' name='afterInput' value='".(isset($item_value['afterInput'])?$item_value['afterInput']:'')."'/></br>\n";
    //    $formString .= "ステータス<input type='text' name='status' value='".(isset($item_value['status'])?$item_value['status']:'')."'/></br>\n";
    return $formString;
  }

  
}

