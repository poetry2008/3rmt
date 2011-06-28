<?php
require_once "HM_Item_Basic.php";
class HM_Item_Specialbank extends HM_Item_Basic
{

  function parseSbOption($sboption)
  {
    $options = explode("\n",$sboption);
    $radios = array();
    foreach($options as $option){
      $loption = trim($option);
      if (empty($option))continue;
      if (substr($option,0,1)=='('){
	$radios[] = array('text'=>substr($option,strpos($option,')')+1));
	continue;
      }
      if (substr($option,0,1)=='['){
	  $tmp = substr($option,strpos($option,']')+1);
	  $radios[count($radios)-1]['checkboxs'][] =$tmp;
	  continue;
      }else{
	echo substr($option,0,1);
      }

    }
    return $radios;
  }
  function render()
  {
    $this->dataoption=$this->parseSbOption($this->dataoption);
    if ($this->loaded){
      $this->defaultValue = $this->loadedValue;
      $this->defaultValue = explode('|',$this->defaultValue);
    }  
    $this->formnametotal = $this->formname.'total';
    echo "<td>"; 
    echo "<div id='".$this->formnametotal."' >";
    echo "<input type='hidden' id='".$this->formname."' name=".$this->formname." value='".$this->defaultValue."' />";
    echo "<table><tr>";
    $this->radioname = '0'.$this->formname.'radio';
    unset($this->defaultValue[count($this->defaultValue)-1]);
    foreach($this->dataoption as $key=>$radio){
      if(@in_array((string)$key,$this->defaultValue)){
        $checked = 'checked';
      }else{
        $checked = '';
      }
      echo "<td>";
      echo "<input id='".$this->formname.$key."' ".$checked." name='".$this->formname."radio' type='radio'/>";
      echo $radio['text'];      
      if (''==$checked){
      echo "<div class='checkboxs' >";
      }else{
      echo "<div class='checkboxs' >";
      }
      foreach ($radio['checkboxs']  as $key2 => $checkbox){
        if(@in_array((string)$key.'_'.$key2,$this->defaultValue)){
          $checked = 'checked';
        }else{
          $checked = '';
        }
        echo "<input id='".$this->formname.$key.'_'.$key2."' ".$checked." name='".$this->formname.$key."' type='checkbox' />  ";
        echo $checkbox;
        echo "\n</br>";
      }
      echo "</div>";
      echo "</td>";
    }
    echo "</tr>";
    echo "</table>";
	echo "</div>";
    echo "</td>"; 

  }
  function renderScript()
  {
    ?>
    <script type='text/javascript'>
      $(document).ready(function(){
          $("#<?php echo $this->formnametotal;?>").find("input").each(
                                                                      function()
                                                                      {
                                                                        $(this).bind('click',<?php echo $this->formname;?>onItemChanged)});
	});  
    function <?php echo $this->formname;?>onItemChanged()
					    {
                          alert('xcv');
					      $("#<?php echo $this->formname;?>").val('');
					      $('#<?php echo $this->formnametotal;?>').find('input').each(
													  function (){
													    if($(this).attr('type')=='radio')
   {

	if($(this).attr('checked')==false)  {
      //      $(this).parent().parent().find(".checkboxs").hide();
      $(this).next().hide();
        }else{
      $(this).next().show();
      $(this).parent().parent().find(".checkboxs").show();
	}
													  }
   if($(this).attr('checked')){
     $("#<?php echo $this->formname;?>").val($("#<?php echo $this->formname;?>").val()+$(this).attr('id').replace('<?php echo $this->formname;?>','')+'|');
							      
													    }
													  }
													  
                                              );

					    }
    </script>
	<?
	}
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
    echo "</br>";
    echo   '() は radio';
    echo "</br>";
    echo   '[] は checkbox';
    echo "</br>";
    echo   '改行でデータを区切る';    echo "</br>";
    echo   ' 例';    echo "</br>";
    echo   '() ２回目以降';    echo "</br>";
    echo   '[] 常連（以下のチェック必要無）';    echo "</br>";
    echo   '   []   1.過去に本人確認をしている';    echo "</br>";
    echo    '  []   2.決算内容に変更がない';    echo "</br>";
    echo     ' []   3.短期間に高額決算がない';    echo "</br>";
    echo  '    ()初回';    echo "</br>";
    echo    '  [] IP・ホストのチェック';    echo "</br>";
    echo     ' []カード名義・商品名・キャラ名一致';    echo "</br>";
    echo     ' []本人確認日：月日';    echo "</br>";
    echo     ' []信用調査入力';    echo "</br>";
    echo '必ず半角符号を使ってください';    echo "</br>";






  }
}

