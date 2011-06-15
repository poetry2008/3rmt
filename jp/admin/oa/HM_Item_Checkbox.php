<?php
require_once "HM_Item_Basic.php";
class HM_Item_Checkbox extends HM_Item_Basic  
{
  function renderScript()
  {
    ?>
    <script type='text/javascript'>
      function <?php echo $this->formname;?>Changed(ele){
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
   
   echo $this->beforeInput;
   echo "<input id='".$this->formname."real' value = '".$this->defaultValue."' type='hidden' name = '".$this->formname."'>";
   foreach($this->radios as $key=>$value){
     if (in_array($value,$loadArray)){
       $check = 'checked';
      }else{
	$check = '';
      }

      echo "<input value = '".$value."' onclick='".$this->formname."Changed(this)' type='checkbox' ".$check." name='0".$this->formname."' />".$value;
	  }
   echo '</br>';
   echo $this->afterInput;
   echo "</td>";
  }

  static public function prepareForm($item_id = NULL)
  {
    $item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $radios = $item_value['radios'];
    $result = '';
    $result .= "必須<input type='checkbox' name='require' ".$checked."/></br>\n";
    $result .= "项目名<input type='text' name='thename' value='".(isset($item_value['thename'])?$item_value['thename']:'')."'/></br>\n";
    $result .= "前方文字<input type='text' name='beforeInput' value='".(isset($item_value['beforeInput'])?$item_value['beforeInput']:'')."'/></br>\n";
    $result .= "後方文字<input type='text' name='afterInput' value='".(isset($item_value['afterInput'])?$item_value['afterInput']:'')."'/></br>\n";
    if(count($radios)){
      foreach($radios as $key=>$radio){
        $result.= "元素<input type='text' name='radios[]' value=$radio />\r\n</br>";
      }
    }else{
        $result.= "元素<input type='text' name='radios[]' />\r\n</br>";
    }
    $result .="<script type='text/javascript' >";
    $result .="function insertAitem(e)";
    $result .="{";
    $result .="$('<p>元素</p><input type=\"text\" name=\"radios[]\" ></br>').insertBefore($(e));";
    $result .="}";
    $result .="</script>";
    $result .="<input value='チェックボックス追加' type='button' onClick='insertAitem(this)' >";
    ?>
<?php 
    return $result;
  }
}

