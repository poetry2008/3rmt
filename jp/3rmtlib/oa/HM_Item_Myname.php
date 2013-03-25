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

/* -------------------------------------
    功能: 获得默认值 
    参数: 无   
    返回值: 获得默认值(string) 
------------------------------------ */
  function getDefaultValue()
  {
    if ($this->loaded){
      return $this->loadedValue;
    }else{
      return $this->defaultValue;
    }

  }

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
    global $ocertify;
    $user_info = tep_get_user_info($ocertify->auth_user);
    $value =$user_info['name'];
    return $this->updateValue($order_id,$form_id,$group_id,$item_id,$value);

  }

/* -------------------------------------
    功能: 输出元素的html 
    参数: $m(boolean) 标识   
    返回值: 无 
------------------------------------ */
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

    if($this->require){
    //如果不允许为空
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

/* -------------------------------------
    功能: 输出javascript 
    参数: 无   
    返回值: 无 
------------------------------------ */
  function renderScript($m)
  {
      ?>
      <script type='text/javascript' >

       $(document).ready(function (){
           $("#<?php echo $this->formname;?>submit").click(function(){
 $.ajax({
                 url:'oa_answer_process.php?fix=user&withz=1&oID=<?php echo $_GET["oID"]?><?php if($m){echo "&fake=1";}?>',
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
                               }
                          }
                 });
             });
      });
      </script>
      <?php

  }

/* -------------------------------------
    功能: 输出构成元素的html 
    参数: $item_id(int) 元素id   
    返回值: 构成元素的html(string) 
------------------------------------ */
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

