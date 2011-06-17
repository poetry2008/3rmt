<?php
require_once "HM_Item_Basic.php";
class HM_Item_Text extends HM_Item_Basic
{
  //  var $hasSubmit = false;
  //  var $hasSelect = false;
  var $hasRequire = true;
  var $hasThename = true;
  //  var $hasSelect  = true;
  //  var $hasSubmit = true;
  var $hasFrontText  = true;  
  var $hasBackText  = true;  
  var $hasDefaultValue  = true;
  var $hasSize  = true;

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
    echo $this->beforeInput."<input type='text' class='".$classrequire."'size='".$this->size."' name='".$this->formname."'
      value='".$this->getDefaultValue()."' />".$this->afterInput;
    echo "</td>";
  }
  function renderScript()
  {
    if($this->require){
      ?>
      <script type='text/javascript' >
       $(document).ready(function (){$("input|[name=<?php echo $this->formname;?>]").change(
      function <?php echo $this->formname;?>Change(ele)
      {
        if($(this).val()==''){
          $('').insertAfter($(this));
        }
      })});
      </script>
      <?php
    }
  }
  static public function prepareForm($item_id = NULL)
  {/*
    $item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $formString  = '';
    $checked = isset($item_value['require'])?'checked="true"':'';
    //    $formString .= "必須<input type='checkbox' name='require' ".$checked."/></br>\n";
    //    $formString .= "项目名<input type='text' name='thename' value='".(isset($item_value['thename'])?$item_value['thename']:'')."'/></br>\n";
    //    $formString .= "前方文字<input type='text' name='beforeInput' value='".(isset($item_value['beforeInput'])?$item_value['beforeInput']:'')."'/></br>\n";
    $formString .= "defaultValue<input type='text' name='defaultValue'
      value='".(isset($item_value['defaultValue'])?$item_value['defaultValue']:'')."'/></br>\n";
    $formString .= "Size<input type='text' name='size' value='".(isset($item_value['size'])?$item_value['size']:'')."'/></br>\n";
    //    $formString .= "後方文字<input type='text' name='afterInput' value='".(isset($item_value['afterInput'])?$item_value['afterInput']:'')."'/></br>\n";
    */
    return $formString;
  }

  
}

