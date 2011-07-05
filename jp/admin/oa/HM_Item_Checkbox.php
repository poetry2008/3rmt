<?php
require_once "HM_Item_Basic.php";
class HM_Item_Checkbox extends HM_Item_Basic  
{

  var $hasRequire = true;
  var $hasThename = true;
  var $hasSelect  = true;
  //  var $hasSubmit = true;
  var $hasFrontText  = true;  
  //  var $hasBackText  = true;  
  //  var $hasDefaultValue  = true;
  //  var $hasSize  = true;
  
  var $must_comment = '*チェックを入れるとこのパーツは取引完了に必要なものになる';
  var $status_comment = '*設定されたステータスに変わると自動でチェックが入る保存される'; 
  var $front_comment = '* ○○○○ チェックボックス 文字列';
  var $html_form_end = true;

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
      $result.='_'.$value;
    }
    return $this->updateValue($order_id,$form_id,$group_id,$item_id,$result);

  }
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
    //    $result .= "必須<input type='checkbox' name='require' ".$checked."/></br>\n";
    //    $result .= "项目名<input type='text' name='thename' value='".(isset($item_value['thename'])?$item_value['thename']:'')."'/></br>\n";
    //    $result .= "前方文字<input type='text' name='beforeInput' value='".(isset($item_value['beforeInput'])?$item_value['beforeInput']:'')."'/></br>\n";
    //    $result .= "後方文字<input type='text' name='afterInput' value='".(isset($item_value['afterInput'])?$item_value['afterInput']:'')."'/></br>\n";
    $_result ="<script type='text/javascript' >";
    $_result .="function insertAitem(e)";
    $_result .="{";
    $_result .="$('</br><input type=\"text\" name=\"radios[]\" size=\"40\">').insertBefore($(e));";
    $_result .="}";
    $_result .="</script>";
    $_result .="<input value='チェックボックス追加' type='button'
      onClick='insertAitem(this)' >";
    if(count($radios)){
      foreach($radios as $key=>$radio){
        //$result.= "<table width='100%' border='0'><tr><td width='5%' valign='top'>Checkbox</td><td width='15%' class='checkbox_item'><input type='text' size='40' name='radios[]' value=$radio />".$_result."</td><td valign='top'><font size='2' color='#ff0000'>*前方文字 チェックボックス ○○○○ </font>";
        $result.= "<tr><td width='5%' valign='top'>Checkbox</td><td width='15%' class='checkbox_item'><input type='text' size='40' name='radios[]' value=$radio />".$_result."</td><td valign='top'><font size='2' color='#ff0000'>*前方文字 チェックボックス ○○○○ </font>";
      }
    }else{
        $result.= "<tr><td width='5%' valign='top'>Checkbox</td><td width='15%' class='checkbox_item'><input type='text' size='40' name='radios[]' />".$_result."</td><td valign='top'><font size='2' color='#ff0000'>*前方文字 チェックボックス ○○○○ </font>";
    }
    ?>
<?php 
    return $result.'</table>';
  }
}

