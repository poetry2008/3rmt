<?php
global $language;
require_once "HM_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/oa/HM_Item_Specialbank.php';
class HM_Item_Specialbank extends HM_Item_Basic
{

/* -------------------------------------
    功能: 过滤信息 
    参数: $sboption(string) 信息   
    返回值: 处理后的信息(array) 
------------------------------------ */
  function parseSbOption($sboption)
  {
    $options = preg_split("/(\n|\[|\()/",$sboption);
    $radios = array();
    foreach($options as $option){
      $loption = trim($option);
      if (empty($option))continue;
      if (substr($option,0,1)==')'){
        $radios[] = array('text'=>substr($option,strpos($option,')')+1));
        continue;
      }
      if (substr($option,0,1)==']'){
        $tmp = substr($option,strpos($option,']')+1);
        $radios[count($radios)-1]['checkboxs'][] =$tmp;
        continue;
      }else{
        echo substr($option,0,1);
      }

    }
    return $radios;
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
    $this->dataoption=$this->parseSbOption($this->dataoption);
    if ($this->loaded){
      $this->defaultValue = $this->loadedValue;
      $this->defaultValue = explode('|',$this->defaultValue);
    }  

    $this->formnametotal = $this->formname.'total';
    echo "<td>"; 
    echo "<div id='".$this->formnametotal."' >";
    echo "\n";
    echo "<input type='hidden' id='".$this->formname."' name='".$this->formname."' value='".@join('|',$this->defaultValue)."' />";
    echo "\n";
    echo "<table>";
    echo "\n";
    echo "<tr>";
    echo "\n";
    $this->radioname = '0'.$this->formname.'radio';
    unset($this->defaultValue[count($this->defaultValue)-1]);
    foreach($this->dataoption as $key=>$radio){
      if(@in_array((string)$key,$this->defaultValue)){
        $checked = 'checked';
      }else{
        $checked = '';
      }

    echo "<td valign='top'>";
    echo "\n";
      if (count($radio['checkboxs'])==1){
	$float = 'float:left;';
      }else{
	$float = '';
      }
      echo "<div style='".$float."' ><input id='".$this->formname.$key."' ".$checked." name='0".$this->formname."radio' type='radio'/>";
      echo $radio['text'];      
      echo "</div>";
      if (''==$checked){
        echo "<div class='checkboxs ".$this->formname.$key."' style='display:none;".$float."' >";
      }else{
        echo "<div class='checkboxs ".$this->formname.$key."' style='".$float."' >";
      }
    echo "\n";
      if (count($radio['checkboxs'])){
        foreach ($radio['checkboxs']  as $key2 => $checkbox){
          if(@in_array((string)$key.'_'.$key2,$this->defaultValue)){
            $checked = 'checked';
          }else{
            $checked = '';
          }
          echo "<input id='".$this->formname.$key.'_'.$key2."' ".$checked." name='0".$this->formname.$key."' type='checkbox' />  ";
    echo "\n";
          echo $checkbox;
          echo "\n</br>";
    echo "\n";
        }
      }
      echo "</div>";
    echo "\n";
      echo "</td>";
    echo "\n";
    }
      echo "</tr>";
    echo "\n";

    echo "</table>";
    echo "\n";
	echo "</div>";
    echo "\n";    echo "\n";
    echo "</td>"; 
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
      $(document).ready(function(){
          $("#<?php echo $this->formnametotal;?>").find("input").each(
                                                                      function()
                                                                      {
                                                                        $(this).change(<?php echo $this->formname;?>onItemChanged);
                                                                        $(this).click(<?php echo $this->formname;?>onItemChanged);
                                                                      }
                                                                      );

        });  
    function <?php echo $this->formname;?>onItemChanged()
                                            {
                                              if($(this).attr('type')=='radio'){
                                                if($(this).attr('checked')){
                                                  $(".checkboxs").hide();
                                                  $("."+$(this).attr('id')).show();
                                                }
                                              }
					      $("#<?php echo $this->formname;?>").val('');
					      $("#<?php echo $this->formnametotal;?>").find("input").each(function(){
					 
                                              if($(this).attr('checked')){
                                                $("#<?php echo $this->formname;?>").val($("#<?php echo $this->formname;?>").val()+$(this).attr('id').replace('<?php echo $this->formname;?>','')+'|');
                                                
                                              }
					      });
                                            }
    
</script>
  <?
}

/* -------------------------------------
    功能: 输出构成元素的html 
    参数: $item_id(int) 元素id   
    返回值: 无 
------------------------------------ */
static public function prepareForm($item_id = NULL)
{
  $item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".(int)$item_id."'"); 
  $item_res = tep_db_fetch_object($item_raw); 
  if ($item_res) {
    $item_value = unserialize($item_res->option); 
  }
  echo "<textarea name='dataoption' style='width: 600px; height:400px;'>";
  echo  $item_value['dataoption'];
  echo "</textarea>";
  echo TEXT_SPECIALBAN_INFO;






}
}


