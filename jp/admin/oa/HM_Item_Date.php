<?php
require_once "HM_Item_Basic.php";
class HM_Item_Date extends HM_Item_Basic
{
  /*
ステータス[連動しない▽]（可以跟其他的状态关联）

必須：○　必須

項目名_____ _____　

前方文字___ _______

SubmitName __________(确认钮)

後方文字__________  
  */
  var $hasRequire = true;
  var $hasTheName = true;
  //  var $hasSelect  = true;
  var $hasSubmit = true;
  var $hasFrontText  = true;  
  var $hasBackText  = true;  
  //  var $hasDefaultValue  = true;
  //  var $hasSize  = true;
  function render()
  {

   if ($this->loaded){
    $this->defaultValue = $this->loadedValue;
   }  
    if(strlen($this->thename)){
      echo "<td>";
    echo $this->thename.':';
      echo "</td>";
    }
    echo "<td>";
    echo $this->beforeInput;
    if($this->require){
      $classrequire = 'require';
    }else {
      $classrequire = '';
    }
    echo "<input class='outform ".$classrequire."' id = '".$this->formname."' type='hidden' name='".$this->formname."' value='".$this->defaultValue."' />";
    $thevalue = $this->loaded?$this->defaultValue:"";
    echo "<span id='".$this->formname."showvalue' >".$thevalue."</span>";
    if(empty($thevalue)){
    echo "<button type='button' id = '".$this->formname.'submit'."' value='$this->submitName' />";
    }
    echo $this->afterInput;
    echo "<script type='text/javascript'>";
?>
       $(document).ready(function (){
           $("#<?php echo $this->formname;?>submit").click(function(){
               $.ajax({
                 url:'oa_answer_process.php?withz=1&fix=date&oID=<?php echo $_GET["oID"]?>',
                     type:'post',    
                     data:"form_id="+$('input|[name=form_id]').val()+"&<?php echo $this->formname;?>="+$('input|[name=<?php echo $this->formname;?>]').val(),
                     success: function(data){
                     alert(data);
                     $("#<?php echo $this->formname;?>showvalue").text(data);
                     $("#<?php echo $this->formname;?>").val(data);
                     $("#<?php echo $this->formname;?>submit").hide();
                     $(this).attr('disable',true);
                   }
                 });
             });
      });
<?php 
    echo "</script>";
    echo "</br>\n";
      echo "</td>";
  }

  function initDefaultValue($order_id,$form_id,$group_id)
  {
    //    $sql = 'select '.$this->datetype.' dp from orders where orders_id = "'.$order_id.'"';
    //    $result = tep_db_fetch_array(tep_db_query($sql));
    //    $theDate = $result['dp'];
    //    $theDate = time();
    //    $this->defaultValue = date('Y-m-d',($theDate));
    //    $this->m= date('m',strtotime($theDate));
    //    $this->d= date('d',strtotime($theDate));
    //    $this->y= date('Y',strtotime($theDate));
  }


  static public function prepareForm($item_id = NULL)
  {
    /*
    $item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $formString  = '';
    */
    //    $checked = isset($item_value['require'])?'checked="true"':'';
    //    $formString .= "必須<input type='checkbox' name='require' ".$checked."/></br>\n";
    //    $formString .= "项目名<input type='text' name='thename' value='".(isset($item_value['thename'])?$item_value['thename']:'')."'/></br>\n";
    //    $formString .= "前方文字<input type='text' name='beforeInput' value='".(isset($item_value['beforeInput'])?$item_value['beforeInput']:'')."'/></br>\n";
    //    $formString .= "SubmitName<input type='text' name='submitName'
    //      value='".(isset($item_value['submitName'])?$item_value['submitName']:'')."'/></br>\n";
    //    $formString .= "後方文字<input type='text' name='afterInput' value='".(isset($item_value['afterInput'])?$item_value['afterInput']:'')."'/></br>\n";

    return $formString;
  }
}
