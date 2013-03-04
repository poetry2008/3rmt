<?php
global $language;
require_once "HM_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/oa/HM_Item_Date.php';
class HM_Item_Date extends HM_Item_Basic
{

  var $hasRequire = true;
  var $hasTheName = true;
  var $hasSelect  = true;
  var $hasSubmit = true;
  var $hasFrontText  = true;  
  var $hasBackText  = true;  
  //  var $hasDefaultValue  = true;
  //  var $hasSize  = true;
  
  var $must_comment = TEXT_DATE_MUST_COMMENT; 
  var $status_comment = TEXT_DATE_STATUS_COMMENT;
  var $project_name_comment = TEXT_DATE_P_NAME_COMMENT;
  var $front_comment = TEXT_DATE_FRONT_COMMENT;
  var $submit_name_comment = TEXT_DATE_SUBMIT_NAME_COMMENT;
  var $after_comment = TEXT_DATE_AFTER_COMMENT;
  
/* -------------------------------------
    功能: 更新数据 
    参数: $order_id(string) 订单id   
    参数: $form_id(int) 表单id   
    参数: $group_id(int) 组id   
    参数: $item_id(int) 元素id   
    返回值: 是否更新成功(boolean) 
------------------------------------ */
  function statusChange($order_id,$form_id,$group_id,$item_id)
  {
    $value =date('Y/m/d H:i',time());
    return $this->updateValue($order_id,$form_id,$group_id,$item_id,$value);

  }

/* -------------------------------------
    功能: 输出元素的html 
    参数: 无   
    返回值: 无 
------------------------------------ */
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
    echo "<span id='".$this->formname."showvalue' >".str_replace('-', '/', $thevalue)."</span>";
    echo "<button type='button' id = '".$this->formname.'submit'."' >".$this->submitName."</button>";
    //    }
    echo $this->afterInput;
    echo "<script type='text/javascript'>";
?>
       $(document).ready(function (){
           $("#<?php echo $this->formname;?>submit").click(function(){
               $.ajax({
                 url:'oa_answer_process.php?withz=1&fix=date&oID=<?php echo $_GET["oID"]?>',
                     type:'post',    
                     data:"form_id="+$('input|[name=form_id]').val()+"&<?php echo $this->formname;?>="+$('input|[name=<?php echo $this->formname;?>]').val()+"&eof=eof",
                     beforeSend: function(){$('body').css('cursor','wait');$("#wait").show()},
                     <?php //如果请求失败，弹出相应的出错信息?>
                     success: function(data){

                              if(data == 'eof_error'){
                                $("#wait").hide();
                                $('body').css('cursor','');  
                                show_error_message();
                                $("#popup_info").show();
                                $("#popup_box").show();
                              }else{
		                $("#<?php echo $this->formname;?>showvalue").text(data);
			        $("#<?php echo $this->formname;?>").val(data);
			        $("#wait").hide();
			        $('body').css('cursor','');
                                checkLockOrder();
                              }
                   }
                 });
             });
      });
<?php 
    echo "</script>";
    echo "</br>\n";
      echo "</td>";
  }

/* -------------------------------------
    功能: 初始化默认值 
    参数: $order_id(string) 订单id   
    参数: $form_id(int) 表单id   
    参数: $group_id(int) 组id   
    返回值: 无 
------------------------------------ */
  function initDefaultValue($order_id,$form_id,$group_id)
  {
  }


/* -------------------------------------
    功能: 输出构成元素的html 
    参数: $item_id(int) 元素id   
    返回值: 构成元素的html(string) 
------------------------------------ */
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
