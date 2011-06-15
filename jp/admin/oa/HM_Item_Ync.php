<?php
require_once "HM_Item_Basic.php";

class HM_Item_Ync extends HM_Item_Basic
{
  function render()
  {
    if ($this->loaded){
      $this->defaultValue = $this->loadedValue;

    }  
    echo "</br>";
    echo "<input id=\"".$this->formname.'value"'." type='hidden' name='".$this->formname."' value='".$this->defaultValue."' >";
    echo "<input id=\"".$this->formname."y\" name='0".$this->formname."' type='radio' value='y'>".$this->firstInput;
    echo "|";
    echo "<input id=\"".$this->formname."n\" name='0".$this->formname."' type='radio' value='n'>".$this->afterInput;
    echo "<span><input id=\"".$this->formname."text\" type='checkbox'
      name='0".$this->formname."' >".$this->defaultText."</span>";

  }
  function renderScript()
  {
    echo "\n";
    echo "<script type='text/javascript'>";
    echo "function $this->formname"."init(){";
    echo "\n";
    echo 'var '.$this->formname.'Values =$("#'.$this->formname.'value'.'").val().split(\'|\');';
    echo "\n";
    echo "if($this->formname"."Values[0]==1){";
    echo "\n";
    echo "$('#".$this->formname.'y'."').attr('checked',true);";
    echo "\n";
    echo "}";
    echo "if($this->formname"."Values[1]==1){";
    echo "\n";
    echo "$('#".$this->formname.'n'."').attr('checked',true);";
    echo "\n";
    echo "}";
    echo "\n";
    echo "if($this->formname"."Values[2]=='Y'){";
    echo "\n";
    echo "$('#".$this->formname.'text'."').attr('checked',true)";
    echo "\n";
    echo "}";
    echo "\n";
    echo "}\n";
    echo "function $this->formname"."Clicked(){";
        echo "if($('#".$this->formname.'text'."').attr('checked')==true)";
        echo "{";
        echo "$('#".$this->formname.'n'."').attr('checked',true)";
        echo "}";    
    echo "}";
    echo "function $this->formname"."Changed(){";

    echo "var y = $('#".$this->formname.'y'."').attr('checked')==true?'1':'0';";
    echo "var n = $('#".$this->formname.'n'."').attr('checked')==true?'1':'0';";
    echo "var final = y+'|'+n+'|'+"."(($('#".$this->formname.'text'."').attr('checked'))==true?'Y':'N');";
    echo '$("#'.$this->formname.'value'.'").val(';
    echo 'final';
    echo '      );';
    echo "\n";
    echo "}";
    echo "\n";
    echo "function $this->formname"."radioClick(ele){";
    echo "\n";
    echo "if ($(this).val()=='y'){ ";
    echo "$('#".$this->formname.'text'."').removeAttr('checked');";
    echo "}else{";
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
    echo "$('#".$this->formname.'text'."').bind('change',".$this->formname."Changed);";
    echo "\n";
    echo "$('#".$this->formname.'text'."').bind('click',".$this->formname."Clicked);";
    echo "$('#".$this->formname.'y'."').change($this->formname"."radioClick);";
    echo "\n";
    //    echo "$('#".$this->formname.'n'."').change($this->formname"."radioClick)";
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
    $formString .= "第一文字<input type='text' name='firstInput'
      value='".(isset($item_value['firstInput'])?$item_value['firstInput']:'有')."'/>";
    $formString .= "</br>\n";
    $formString .= "第二文字<input type='text' name='afterInput'
      value='".(isset($item_value['afterInput'])?$item_value['afterInput']:'無')."'/></br>\n";
    $formString .= "</br>\n";
    $formString .= "ディフォルト<input type='text' name='defaultText'
      value='".(isset($item_value['defaultText'])?$item_value['defautlText']:'展示文字')."'/></br>\n";
    $formString .= "</br>\n";
    return $formString;
  }


}
