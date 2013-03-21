<?php

class AD_Option_Item_Basic 
{
  var $name;
  var $class;
  var $formname;
  
/*---------------------
 功能：初始化
 参数：$option(string) 选项
 返回值：无
 --------------------*/  
  function init($option)
  {
    $this->parseOption($option);
    $this->formname = $this->name.'_'.$this->group_id.'_'.$this->id; 
  }
/*--------------------
 功能：解析选项
 参数：$option(string) 选项
 返回值：无
 -------------------*/
  function parseOption($option)
  {
    if($optionArray  = unserialize($option)){
      foreach($optionArray as $key=>$value){
        $this->$key = $value;
      }
    }
  }
/*--------------------
 功能：住所地址
 参数：$item_id(number) 项目编号
 返回值：格式字符串文本
 -------------------*/
  public function prepareFormWithParent($item_id){
    $item_raw = tep_db_query("select * from ".TABLE_ADDRESS." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $formString  = '';
   
    if ($this->has_text_comment) {
      $default_text = isset($item_value['icomment'])?$item_value['icomment']:''; 
      $formString .= "<tr><td width='220'>".TEXT_ITEM_TEXT_COMMENT_HEAD."</td><td><input type='text' name='icomment' value='".$default_text."' class='option_text'></td></tr>"; 
    } 
    
    
    if ($this->has_text_check_type) {
      $check_type_array = array(TEXT_ITEM_CHECK_TYPE_ALL, TEXT_ITEM_CHECK_TYPE_JAP, TEXT_ITEM_CHECK_TYPE_ALPHA_NUM, TEXT_ITEM_CHECK_TYPE_ALPHA, TEXT_ITEM_CHECK_TYPE_NUM, 'Email'); 
      $default_text = isset($item_value['ictype'])?$item_value['ictype']:''; 
      $formString .= "<tr><td>".TEXT_ITEM_TEXT_CHECK_TYPE_HEAD."</td><td>";
      $formString .= "<select name='ictype'>"; 
      foreach ($check_type_array as $ckey => $cvalue) {
        $formString .= "<option value='".$ckey."'".(($default_text == $ckey)?" selected":"").">".$cvalue."</option>"; 
      }
      $formString .= "</select>"; 
      $formString .= "<br>".TEXT_ITEM_CHECK_READ; 
      $formString .= "</td></tr>"; 
    } 
    
    if ($this->has_text_max_num) {
      $default_text = isset($item_value['imax_num'])?$item_value['imax_num']:''; 
      $formString .= "<tr><td>".TEXT_ITEM_TEXT_MAX_NUM_HEAD."</td><td><input type='text' name='imax_num' value='".$default_text."' class='option_input'>".TEXT_ITEM_CHARACTER_UNIT."<br>".TEXT_ITEM_INPUT_MAX_READ."</td></tr>"; 
    } 
    
    if ($this->hasRequire){
      $formString .= "<tr><td>".TEXT_MUSTBE."</td><td>";
      if (!isset($item_value['require'])) {
        $formString .= "<input type='radio' name='require' value='1' checked>".TEXT_MUSTBE_INPUT; 
        $formString .= "&nbsp;<input type='radio' name='require' value='0'>".TEXT_MUSTBE_NOT_INPUT; 
      } else {
        if ($item_value['require'] == '1') {
          $formString .= "<input type='radio' name='require' value='1' checked>".TEXT_MUSTBE_INPUT; 
          $formString .= "&nbsp;<input type='radio' name='require' value='0'>".TEXT_MUSTBE_NOT_INPUT; 
        } else {
          $formString .= "<input type='radio' name='require' value='1'>".TEXT_MUSTBE_INPUT; 
          $formString .= "&nbsp;<input type='radio' name='require' value='0' checked>".TEXT_MUSTBE_NOT_INPUT; 
        }
      }
      $formString .= "</td></tr>";      
    }
    
    if ($this->has_text_default) {
      $default_text = isset($item_value['itext'])?$item_value['itext']:''; 
      $formString .= "<tr><td>".TEXT_ITEM_TEXT_HEAD."</td><td><input type='text' name='itext' value='".$default_text."' class='option_text'></td></tr>"; 
    }
    
    if ($this->has_textarea_default) {
      $default_text = isset($item_value['itextarea'])?$item_value['itextarea']:''; 
      $formString .= "<tr><td width='220'>".TEXT_ITEM_TEXTAREA_HEAD."</td><td><textarea name='itextarea' cols='30' rows='10' class='option_text'>".$default_text."</textarea></td></tr>"; 
    }
  
    if ($this->hasSelect) {
      if ($this->hasComment) {
        $formString .= "<tr><td width='220'>".TEXT_ITEM_SELECT_COMMENT."</td><td><input type='text' name='secomment' value='".$item_value['secomment']."'></td></tr>"; 
      }
      if (!isset($item_value['se_option'])) {
        $i = 1;  
        for($i=1; $i<=5; $i++) {
          $formString .= "<tr><td>".TEXT_ITEM_SELECT_HEAD."</td><td><input type='text' name='ad_".$i."' value='' style='width:35%;'>&nbsp;<input type='radio' name='dselect' value ='dp_".$i."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='del_option_select(this);'>".TEXT_ITEM_DEL_LINK."</a></td></tr>"; 
        }
        $formString .= "<tr><td>&nbsp;</td><td id='add_select'><a href='javascript:void(0);' onclick='add_option_select();'>".TEXT_ITEM_ADD_SELECT."</a></td></tr>"; 
      } else {
        $i = 1; 
        if (isset($item_value['se_num'])) {
          $se_num = $item_value['se_num']; 
        }
        
        foreach ($item_value['se_option'] as $ikey => $ivalue) {
          $formString .= "<tr><td>".TEXT_ITEM_SELECT_HEAD."</td><td><input type='text' name='ad_".$i."' value='".$ivalue."' style='width:35%;'>&nbsp;<input type='radio' ".((isset($se_num)) && ($se_num == ($i-1))?'checked ':'')."name='dselect' value ='dp_".$i."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='del_option_select(this);'>".TEXT_ITEM_DEL_LINK."</a></td></tr>"; 
          $i++; 
        }
        $formString .= "<tr><td>&nbsp;</td><td id='add_select'><a href='javascript:void(0);' onclick='add_option_select();'>".TEXT_ITEM_ADD_SELECT."</a></td></tr>"; 
      }
      
    }
    
    return $formString;
  }
}
