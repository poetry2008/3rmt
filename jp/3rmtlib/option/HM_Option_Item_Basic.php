<?php
global $language;
require_once DIR_WS_LANGUAGES . $language . '/option/HM_Option_Item_Basic.php';

class HM_Option_Item_Basic 
{
  var $name;
  var $class;
  var $formname;
  
  
  function init($option)
  {
    $this->parseOption($option);
    $this->formname = $this->name.'_'.$this->group_id.'_'.$this->id; 
  }
  function parseOption($option)
  {
    if($optionArray  = unserialize($option)){
      foreach($optionArray as $key=>$value){
        $this->$key = $value;
      }
    }
  }

  public function prepareFormWithParent($item_id){
    $item_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $formString  = '';
   
    if ($this->has_text_comment) {
      $default_text = isset($item_value['icomment'])?$item_value['icomment']:''; 
      $formString .= "<tr><td width='220'>".TEXT_ITEM_TEXT_COMMENT_HEAD."</td><td><input type='text' name='icomment' value='".$default_text."' class='option_text'></td></tr>"; 
    } 
    
    if ($this->has_text_line) {
      $default_text = isset($item_value['iline'])?$item_value['iline']:'1'; 
      $formString .= "<tr><td>".TEXT_ITEM_TEXT_LINE_HEAD."</td><td><input type='text' name='iline' value='".$default_text."'  class='option_item_input'>&nbsp;".TEXT_ITEM_LINE_UNIT."</td></tr>"; 
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
      $formString .= "<tr><td>".TEXT_ITEM_TEXT_MAX_NUM_HEAD."</td><td><input type='text' name='imax_num' value='".$default_text."' class='option_item_input'>".TEXT_ITEM_CHARACTER_UNIT."<br>".TEXT_ITEM_INPUT_MAX_READ."</td></tr>"; 
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
        $formString .= "<tr><td width='220'>".TEXT_ITEM_SELECT_COMMENT."</td><td><input type='text' name='secomment' value='".$item_value['secomment']."' class='option_text'></td></tr>"; 
      }
      if ($this->has_select_default) {
        $formString .= "<tr><td width='220'>".TEXT_ITEM_SELECT_DEFAULT."</td><td><input type='text' name='sedefault' value='".$item_value['sedefault']."' class='option_text'></td></tr>"; 
      }
      if (!isset($item_value['se_option'])) {
        for($i=1; $i<=5; $i++) {
          $formString .= "<tr><td>".TEXT_ITEM_SELECT_HEAD."</td><td><input type='text' name='op_".$i."' value='' class='option_text'>&nbsp;<input type='button' onclick='del_option_select(this);' value='".TEXT_ITEM_DEL_LINK."'></td></tr>"; 
        }
        $formString .= "<tr><td>&nbsp;</td><td id='add_select'><a href='javascript:void(0);' onclick='add_option_select();'>".TEXT_ITEM_ADD_SELECT."</a></td></tr>"; 
      } else {
        $i = 1; 
        foreach ($item_value['se_option'] as $ikey => $ivalue) {
          $formString .= "<tr><td>".TEXT_ITEM_SELECT_HEAD."</td><td><input type='text' name='op_".$i."' value='".$ivalue."' class='option_text'>&nbsp;<input type='button' onclick='del_option_select(this);' value='".TEXT_ITEM_DEL_LINK."'></a></td></tr>"; 
          $i++; 
        }
        $formString .= "<tr><td>&nbsp;</td><td id='add_select'><a href='javascript:void(0);' onclick='add_option_select();'>".TEXT_ITEM_ADD_SELECT."</a></td></tr>"; 
      }
      
    }
    
    if ($this->has_radio_comment) {
      $formString .= "<tr><td width='220'>".TEXT_ITEM_TEXT_COMMENT_HEAD."</td><td><input type='text' name='racomment' value='".$item_value['racomment']."' class='option_text'><br><span id='rname_error' style='color:#ff0000;'></span></td></tr>"; 
    }
    
    if ($this->has_default) {
      $formString .= "<tr><td width='220'>".TEXT_ITEM_RADIO_DEFAULT_SELECT."</td><td><input type='text' name='default_radio' value='".$item_value['default_radio']."' class='option_text'></td></tr>"; 
    }
    
    if ($this->has_radio) {
      if (!isset($item_value['radio_image'])) {
        for($i=1; $i<=5; $i++) {
          $formString .= "<tr><td>".TEXT_ITEM_SELECT_HEAD."</td><td><input type='text' name='ro_".$i."' value='' style='width:35%;'><a href=\"javascript:void(0);\" onclick=\"delete_radio(this, ".$i.");\">".tep_html_element_button(TEXT_ITEM_DEL_LINK)."</a></td></tr>"; 
          
          $formString .= "<tr><td>&nbsp;&nbsp;".TEXT_ITEM_PIC_NAME."</td><td><input type='file' name='rop_".$i."[]' value=''>&nbsp;<a href=\"javascript:void(0);\" onclick=\"delete_item_pic(this);\">".tep_html_element_button(TEXT_ITEM_DELETE_PIC, 'onclick=""')."</a><a href=\"javascript:void(0);\" onclick=\"add_item_pic(this, ".$i.");\">".tep_html_element_button(TEXT_ITEM_ADD_PIC, 'onclick=""')."</a></td></tr>"; 
          
          $formString .= "<tr><td>&nbsp;&nbsp;".TEXT_ITEM_MONEY_NAME."</td><td><input type='text' name='rom_".$i."' value='' style='width:35%; text-align:right;'>".TEXT_ITEM_MONEY_UNIT."</td></tr>"; 
        }     
        $formString .= "<tr><td>&nbsp;</td><td id='add_radio'><a href='javascript:void(0);' onclick='add_option_radio();'>".TEXT_ITEM_ADD_SELECT."</a></td></tr>"; 
      } else {
        if (!empty($item_value['radio_image'])) {
          $i = 1; 
          foreach ($item_value['radio_image'] as $ri_key => $ri_value) {
            $formString .= "<tr><td>".TEXT_ITEM_SELECT_HEAD."</td><td><input type='text' name='ro_".$i."' value='".$ri_value['title']."' style='width:35%;'><a href=\"javascript:void(0);\" onclick=\"delete_radio(this, ".$i.");\">".tep_html_element_button(TEXT_ITEM_DEL_LINK)."</a></td></tr>"; 
          
            
            if (!empty($ri_value['images'])) {
              foreach ($ri_value['images'] as $pi_key => $pi_value) {
                $formString .= "<tr><td>&nbsp;&nbsp;".TEXT_ITEM_PIC_NAME."</td><td><input type='file' name='rop_".$i."[]' value=''>&nbsp;<a href=\"javascript:void(0);\" onclick=\"delete_item_pic(this);\">".tep_html_element_button(TEXT_ITEM_DELETE_PIC, 'onclick=""')."</a><a href=\"javascript:void(0);\" onclick=\"add_item_pic(this, ".$i.");\">".tep_html_element_button(TEXT_ITEM_ADD_PIC, 'onclick=""')."</a>";
                if (file_exists(DIR_FS_CATALOG_IMAGES.'0/option_image/'.$pi_value) && $pi_value != '') {
                  $formString .= '<br><img src="upload_images/0/option_image/'.$pi_value.'" alt="pic">'; 
                }
                $formString .= '<input type="hidden" name="rou_'.$i.'[]" value="'.$pi_value.'">'; 
              }
            }
            $formString .= "</td></tr>"; 
            
            $formString .= "<tr><td>&nbsp;&nbsp;".TEXT_ITEM_MONEY_NAME."</td><td><input type='text' name='rom_".$i."' value='".$ri_value['money']."' style='width:35%;text-align:right;'>".TEXT_ITEM_MONEY_UNIT."</td></tr>"; 
            $i++; 
          }
          
          $formString .= "<tr><td>&nbsp;</td><td id='add_radio'><a href='javascript:void(0);' onclick='add_option_radio();'>".TEXT_ITEM_ADD_SELECT."</a></td></tr>"; 
        }
      }
    }
    return $formString;
  }
}
