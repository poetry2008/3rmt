<?php
global $language;
require_once "HM_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/oa/HM_Item_Checkbox.php';
class HM_Item_Checkbox extends HM_Item_Basic  
{

  var $hasRequire = true;
  var $hasThename = true;
  var $hasSelect  = true;
  var $hasFrontText  = true;  
  
  var $must_comment = TEXT_CHECKBOX_MUST_COMMENT;
  var $status_comment = TEXT_CHECKBOX_STATUS_COMMENT;
  var $front_comment = TEXT_CHECKBOX_FRONT_COMMENT;
  var $html_form_end = true;

/* -------------------------------------
    功能: 更新数据 
    参数: $order_id(string) 预约订单id   
    参数: $form_id(int) 表单id   
    参数: $group_id(int) 组id   
    参数: $item_id(int) 元素id   
    返回值: 是否更新成功(boolean) 
------------------------------------ */
  function statusChange($order_id,$form_id,$group_id,$item_id)
  {
    $item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $radios = $item_value['radios'];
    $result = '';
    foreach($radios as $key=>$value){
      $result.='_'.$key;
    }
    return $this->updateValue($order_id,$form_id,$group_id,$item_id,$result);

  }

/* -------------------------------------
    功能: 输出javascript 
    参数: 无   
    返回值: 无 
------------------------------------ */
  function renderScript()
  {
    ?>
    <script type='text/javascript'>
      function <?php echo $this->formname;?>Changed(ele){
      $('#<?php echo $this->formname;?>real').val('');
      var <?php echo $this->formname;?>val ='';
      $("input|[name=0<?php echo $this->formname;?>]").each(function(){
	  if($(this).attr('checked')){
	    <?php echo $this->formname;?>val += '_'+$(this).val();
	  }
	});
      $('#<?php echo $this->formname;?>real').val( <?php echo $this->formname;?>val);
    }
    </script>
<?php
  }

/* -------------------------------------
    功能: 输出元素的html 
    参数: 无   
    返回值: 无 
------------------------------------ */
  function render()
  {
    if(strlen($this->thename)){
      echo "<td>";
    echo $this->thename.':';
      echo "</td>";
    }
    echo "<td>";
    

   if ($this->loaded){
    $this->defaultValue = $this->loadedValue;
   }  
   $loadArray = explode('_',$this->defaultValue);

   if($this->require){
     $classrequire = 'require';
   }else {
     $classrequire = '';
   }   
   echo $this->beforeInput;
   echo "<input class='".$classrequire."' id='".$this->formname."real' value =
     '".$this->defaultValue."' type='hidden' name = '".$this->formname."'>";
   foreach($this->radios as $key=>$value){
     if(empty($value))
       {
         continue;
       }
     if (in_array((string)$key,$loadArray)){
       $check = 'checked';
      }else{
	$check = '';
      }
      echo "<input value = '".$key."' onclick='".$this->formname."Changed(this)' type='checkbox' ".$check." name='0".$this->formname."' />".$value;
	  }
   echo '</br>';
   echo $this->afterInput;
   echo "</td>";
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
    $radios = $item_value['radios'];
    $result = '';
    $_result ="<script type='text/javascript' >";
    $_result .="function insertAitem(e)";
    $_result .="{";
    $_result .="$('</br><input type=\"text\" name=\"radios[]\" size=\"40\">').insertBefore($(e));";
    $_result .="}";
    $_result .="</script>";
    $_result .="<input value='".TEXT_CHECKBOX_ADD."' type='button'
      onClick='insertAitem(this)' >";
    if(count($radios)){
      foreach($radios as $key=>$radio){
        $result.= "<tr><td width='5%' valign='top'>Checkbox</td><td width='15%'
          class='checkbox_item'><input type='text' size='40' name='radios[]'
          value=$radio />".$_result."</td><td valign='top'><font size='2'
          color='#ff0000'>".TEXT_CHECKBOX_FRONT_TEXT."</font>";
      }
    }else{
        $result.= "<tr><td width='5%' valign='top'>Checkbox</td><td width='15%'
          class='checkbox_item'><input type='text' size='40' name='radios[]'
          />".$_result."</td><td valign='top'><font size='2'
          color='#ff0000'>".TEXT_CHECKBOX_FRONT_TEXT."</font>";
    }
    ?>
<?php 
    return $result.'</table>';
  }
}

