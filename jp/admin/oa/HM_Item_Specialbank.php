<?php
/*
信用調査： 	
金額は一致していますか？Y｜N → お客様へ電話
２回目以降
    常連（以下のチェック必要無）
     1.過去に本人確認をしている
     2.決算内容に変更がない
     3.短期間に高額決算がない
初回	
    IP・ホストのチェック
    カード名義・商品名・キャラ名一致
    本人確認日：月日
    信用調査入力 
*/
require_once "HM_Item_Basic.php";
class HM_Item_Specialbank extends HM_Item_Basic
{
  function render()
  {
    $this->defaultValue = '';//'Y|N-1,2,3,8,4' ;
    if ($this->loaded){
      $this->defaultValue = $this->loadedValue;
    }  
    $this->formname01 = $this->formname.'01';
    $this->formname02 = $this->formname.'02';
    $this->formname03 = $this->formname.'03[]';
    $this->formnametotal = $this->formname."total";

    // Y-x,x,x|N-x,x,x
    echo "<div id='".$this->formnametotal."' >";

    echo "<input type='hidden' id='".$this->formname."' name=".$this->formname." value='".$this->defaultValue."' />";

    echo "<input id='".$this->formname01."' name='01".$this->formname."' type='radio'/>";
    echo "２回目以降";
    echo "<div style='display:none'>";
    echo "<input value = '1' name='".$this->formname03."' type='checkbox' />    常連（以下のチェック必要無）";
    echo "\n</br>";
    echo "<input value = '2' name='".$this->formname03."' type='checkbox' />     1.過去に本人確認をしている";
    echo "\n</br>";
    echo "<input value = '3' name='".$this->formname03."' type='checkbox' />     2.決算内容に変更がない";
    echo "\n</br>";
    echo "<input value = '4' name='".$this->formname03."' type='checkbox' />     3.短期間に高額決算がない";
    echo "</div>";
    echo "</br>";
    echo "<input id='".$this->formname02."' name='01".$this->formname."' type='radio'/>";
    echo "初回	";
    echo "<div style='display:none'>";
    echo "\n</br>";
    echo "<input value = '5' name='".$this->formname03."' type='checkbox' />    IP・ホストのチェック";
    echo "\n</br>";
    echo "<input value = '6' name='".$this->formname03."' type='checkbox' />    カード名義・商品名・キャラ名一致";
    echo "\n</br>";
    echo "<input value = '7' name='".$this->formname03."' type='checkbox' />    本人確認日：月日";
    echo "\n</br>";
    echo "<input value = '8' name='".$this->formname03."' type='checkbox' />    信用調査入力 ";
    echo "\n</br>";
    echo "</div>";
    echo "</div>";
  }
  function renderScript()
  {
    
    ?>
    <script type='text/javascript'>
    $(document).ready(function(){
        <?php echo $this->formname;?>loadDefaultValue();
        $("#<?php echo $this->formnametotal;?> > input").bind('click',<?php echo $this->formname;?>onItemChanged);   
        $("#<?php echo $this->formnametotal;?> > div > input").bind('click',<?php echo $this->formname;?>onItemChanged);   

      });  
    function <?php echo $this->formname;?>loadDefaultValue()
    {
      defulatVal = $("#<?php echo $this->formname;?>").val();
      //      alert(defulatVal);
      if(!defulatVal){
        return '';
      }
      splitArray = defulatVal.split('-');
      if(splitArray.length == 1 ){
        return '';
      }
      YN = splitArray[0].split('|');
      checkGroups = splitArray[1].split(',');
      i = 0;
      while(i<checkGroups.length)
        {
          $("input[name='<?php echo $this->formname03;?>'][value="+checkGroups[i]+"]").attr("checked",true);
          i++;
        }
      if(YN[0] =='Y'){
        $("#<?php echo $this->formname01;?>").attr("checked",true);
        $("#<?php echo $this->formname01;?>").next().show();
        $("#<?php echo $this->formname02;?>").next().hide();
      }
      if(YN[1] =='Y'){
        $("#<?php echo $this->formname02;?>").attr("checked",true);
        $("#<?php echo $this->formname01;?>").next().hide();
        $("#<?php echo $this->formname02;?>").next().show();
      }
    }
    function <?php echo $this->formname;?>onItemChanged()
    {
      if($("#<?php echo $this->formname01;?>").attr('checked') == true){
        $("#<?php echo $this->formname01;?>").next().show();
      }else{
        $("#<?php echo $this->formname01;?>").next().hide();
      }
      if($("#<?php echo $this->formname02;?>").attr('checked') == true){
        $("#<?php echo $this->formname01;?>").next().hide();
        $("#<?php echo $this->formname02;?>").next().show();
      }else{
                $("#<?php echo $this->formname02;?>").next().hide();
      }
      
      var checkvalue='';
      $("input[name=<?php echo $this->formname03;?>][checked=true]").each(function(e){
          checkvalue+=$(this).val()+',';
       });
        $("#<?php echo $this->formname;?>").val(
            (($("#<?php echo $this->formname01;?>").attr('checked') == true)?"Y":"N")
            +'|'
            +(($("#<?php echo $this->formname02;?>").attr('checked') == true)?"Y":"N")
            +'-'
            + checkvalue);
    }
</script>
<?
  }
  static public function prepareForm($item_id = NULL)
  {
    //    echo 'この部品は特殊なタイプで、しばらく定制できません';
  }
}

