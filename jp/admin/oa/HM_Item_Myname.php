<?php
require_once "HM_Item_Basic.php";
class HM_Item_Myname extends HM_Item_Basic
{

  var $hasRequire = true;
  var $hasThename = true;
  var $hasSelect  = true;
  var $hasSubmit = true;
  var $hasFrontText  = true;  
  var $hasBackText  = true;  
  var $hasTheName  = true;

  var $must_comment = '*チェックを入れるとこのパーツは取引完了に必要なものになる';
  var $status_comment = '*設定されたステータスに変わると自動で値が保存される';
  var $project_name_comment = '*○○○○：前方文字 SubmitName 後方文字';
  var $front_comment = '*項目名：○○○○　SubmitName 後方文字';
  var $after_comment = '*項目名： 前方文字 SubmitName ○○○○';
  var $submit_name_comment = '*項目名： 前方文字 ○○○○ 後方文字';
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

    echo $this->beforeInput."<span id='".$this->formname."'type='text' class='".$classrequire." outform'size='".$this->size."' name='".$this->formname."' >".$this->getDefaultValue()."</span >";
    //    if(!$this->loaded){
    echo "<button type='button' id = '".$this->formname.'submit'."' />$this->submitName</button>".$this->afterInput;
    //        }
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
                     beforeSend: function(){$('body').css('cursor','wait');$("#wait").show()},
                     success: function(data){
                               $("#<?php echo $this->formname;?>").text(data);		     
                    		     $("#wait").hide();
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

