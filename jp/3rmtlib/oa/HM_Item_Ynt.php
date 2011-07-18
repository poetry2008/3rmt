<?php
require_once "HM_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/oa/HM_Item_Ynt.php';

class HM_Item_Ynt extends HM_Item_Basic
{
  function render()
  {
    if ($this->loaded){
      $this->defaultValue = $this->loadedValue;
    }  
    echo "<input id=\"".$this->formname.'value"'." type='hidden' name='".$this->formname."' value='".$this->defaultValue."' >";
    echo "<input id=\"".$this->formname."y\" name='0".$this->formname."' type=radio value='y'>".$this->firstInput;
    echo "|";
    echo "<input id=\"".$this->formname."n\" name='0".$this->formname."' type=radio value='n'>".$this->afterInput;
    echo "<span><input id=\"".$this->formname."text\" type='text'
      name='0".$this->formname."' value= '".$this->defaultText."'></span>";

  }
  function renderScript()
  {
    echo "\n";
    echo "<script type='text/javascript'>";
    echo "function $this->formname"."init(){";
    echo "\n";
    echo 'var '.$this->formname.'Values
      =$("#'.$this->formname.'value'.'").val().split(\'|\');';
    echo "\n";
    echo "if($this->formname"."Values[0]==1){";
    echo "\n";
    echo "$('#".$this->formname.'y'."').attr('checked',true);";
    echo "$('#".$this->formname.'text'."').parent().fadeOut();";
    echo "\n";
    echo "}";
    echo "if($this->formname"."Values[1]==1){";
    echo "\n";
    echo "$('#".$this->formname.'n'."').attr('checked',true);";
    echo "\n";
    echo "}";
    echo "\n";
    echo "if($this->formname"."Values[2]){";
    echo "\n";
    echo "$('#".$this->formname.'text'."').val($this->formname"."Values[2])";
    echo "\n";
    echo "}";
    echo "\n";
    echo "}\n";

    echo "function $this->formname"."Changed(){";
    echo "var y = $('#".$this->formname.'y'."').attr('checked')==true?'1':'0';";
    echo "var n = $('#".$this->formname.'n'."').attr('checked')==true?'1':'0';";
    
    echo "var final = y+'|'+n+'|'+"."$('#".$this->formname.'text'."').val();";
    echo '$("#'.$this->formname.'value'.'").val(';
    echo 'final';
    echo '      );';
    echo "\n";
    echo "}";
    echo "\n";
    echo "function $this->formname"."radioClick(ele){";
    echo "\n";
    echo "if ($(this).val()=='y'){";
    echo "$('#".$this->formname.'text'."').parent().fadeOut();";
    echo "}else{";
    echo "$('#".$this->formname.'text'."').parent().fadeIn();";
    echo "}";
    echo "\n";
    echo "}";
    echo "\n";
    echo "$(document).ready(function()";
    echo "{";
    echo "\n";
    echo "$this->formname"."init();";
    echo "\n";
    echo "$('#".$this->formname.'y'."').bind('change',".$this->formname."Changed);";
    echo "\n";
    echo "$('#".$this->formname.'n'."').bind('change',".$this->formname."Changed);";
    echo "\n";
    echo "$('#".$this->formname.'text'."').bind('change',".$this->formname."Changed)";
    echo "\n";
    echo "$('#".$this->formname.'y'."').change($this->formname"."radioClick)";
    echo "\n";
    echo "$('#".$this->formname.'n'."').change($this->formname"."radioClick)";
    echo "\n";
    echo "});";
    echo "</script>";
    echo "</br>\n";

  }

  static public function prepareForm($item_id = NULL)
  {
    $item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $formString  = '';
    $formString .= TEXT_YNT_FIRST_CHAR."<input type='text' name='firstInput'
      value='".(isset($item_value['firstInput'])?$item_value['firstInput']:TEXT_YNT_HAVE)."'/>";
    $formString .= "</br>\n";
    $formString .= TEXT_YNT_SECOND_CHAR."<input type='text' name='afterInput'
      value='".(isset($item_value['afterInput'])?$item_value['afterInput']:TEXT_YNT_NULL)."'/></br>\n";
    $formString .= "</br>\n";
    $formString .= TEXT_YNT_DEFAULT."<input type='text' name='defaultText'
      value='".(isset($item_value['defaultText'])?$item_value['defautlText']:TEXT_YNT_DEFAULT)."'/></br>\n";
    $formString .= "</br>\n";
    return $formString;
  }


}
