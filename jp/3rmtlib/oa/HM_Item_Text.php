<?php
global $language;
require_once "HM_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/oa/HM_Item_Text.php';
class HM_Item_Text extends HM_Item_Basic
{
  var $hasRequire = true;
  var $hasThename = true;
  var $hasFrontText  = true;  
  var $hasBackText  = true;  
  var $hasDefaultValue  = true;
  var $hasSize  = true;
  
  var $front_comment = TEXT_TEXT_FRONT_COMMENT;
  var $after_comment = TEXT_TEXT_AFTER_COMMENT;
  var $default_value_comment = TEXT_TEXT_DEFAULT_VALUE_COMMENT;
  var $size_comment = TEXT_TEXT_SIZE_COMMENT;
  var $must_comment = TEXT_TEXT_MUST_COMMENT;
 
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
    if($this->require){
    //如果不允许为空
      $classrequire = 'require';
    }else {
      $classrequire = '';
    }
    $input_size = $this->size > 28 ? 28 : $this->size;
    echo $this->beforeInput."<input type='text' class='".$classrequire."'size='".$input_size."' name='".$this->formname."'
      value='".$this->getDefaultValue()."' /><input type='hidden' id='size_".$this->formname."' value='".$this->size."'>"."<div>".$this->afterInput."</div>";
    echo "</td>";
  }

/* -------------------------------------
    功能: 输出javascript 
    参数: 无   
    返回值: 无 
------------------------------------ */
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

/* -------------------------------------
    功能: 输出构成元素的html 
    参数: $item_id(int) 元素id   
    返回值: 构成元素的html(string) 
------------------------------------ */
  static public function prepareForm($item_id = NULL)
  {
    return $formString;
  }

  
}

