<?php 
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$cPath = $_GET['cpath'];
$cID   = $_GET['cid'];  

$products = tep_get_products_by_categories_id($cID);

switch ($_GET['action']){
case update:
  
  /**
  echo "<pre>";
  print_r($_POST);
  echo "</pre>";
  exit; 
  /**/
  if ($_POST['oroshi_datas'])
  foreach($_POST['oroshi_datas'] as $h_id => $h){
    tep_db_perform('set_oroshi_datas',array(
      'datas' => trim(implode("\n", $h))
    ),'update',"list_id='".$h_id."'");
  }
  if ($_POST['product']){
    for($ii = count($_POST['product'])-1;$ii>=0;$ii--) {
      if($_POST['product'][$ii] == 0){
        unset($_POST['product'][$ii]);
      }else{
        break;
      }
    }
    tep_db_query("delete from set_menu_list where categories_id='".$cID."' ");
  //$set_menu_list_query = tep_db_query("select * from set_menu_list where categories_id='".$cID."' order by set_list_id asc");
    for ($i = 0; $i < count($_POST['product']); $i++) {
      /*if($list = tep_db_fetch_array($set_menu_list_query)){
        tep_db_perform('set_menu_list', array(
          'products_id' => $_POST['product'][$i],
          'kakuukosuu'  => $_POST['kakuukosuu'][$i],
          'kakaku'      => $_POST['kakaku'][$i]
        ), 'update', "set_list_id='".$list['set_list_id']."'");
      } else {*/
        tep_db_perform('set_menu_list', array(
          'products_id'   => $_POST['product'][$i],
          'kakuukosuu'    => $_POST['kakuukosuu'][$i],
          'kakaku'        => $_POST['kakaku'][$i],
          'categories_id' => $cID
        ));
      //}
    }
  }
  break;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
  <title>リスト表示</title>
  <script type="text/javascript" src="includes/javascript/jquery.js"></script>
  <script type="text/javascript">
    // true = disabled, false = enabled
    var products = new Array();
    var last_val = 0;
    var kakaku = new Array();
    var kakuukosuu = new Array();
    var products_order = new Array(31703,31697,31681)
    var products_order = new Array(<?php
    $set_menu_list_query = tep_db_query("select * from set_menu_list where categories_id='".$cID."' order by set_list_id asc");
    $i = 0;
    while($list = tep_db_fetch_array($set_menu_list_query)) {
      if ($i != 0){
        echo ',';
      }
      echo $list['products_id'];
      $set_menu_list[] = $list;
      $i ++;
    }
?>
    );
<?php foreach ($products as $jk => $jp) {?>
    kakuukosuu[<?php echo $jp['products_id'];?>] = <?php echo tep_get_kakuukosuu_by_products_id($cID,$jp['products_id']);?>;
    kakaku[<?php echo $jp['products_id'];?>]     = <?php echo tep_get_kakaku_by_products_id($cID,$jp['products_id']);?>;
<?php }?>

    /*function setDefault () {
      i = 1;
      $('.productSelect').each(function(){
        this.selectedIndex = i;
        $(this).trigger('change');
        i++;
      });
    }*/
    function setDefault () {
      i = 0;
      $('.productSelect').each(function(){
        if (typeof(products_order[i]) != 'undefined') {
          $(this).val(products_order[i]);
          i++;
        } else {
          this.selectedIndex = 0;
        }
        $(this).trigger('change');
      });
    }

    function exchange_product(x) {
      tmp_product = $('#td_product_'+x).html();
      tmp_product_value = $('#td_product_'+x+'>.productSelect').val();
      
      $('#td_product_'+x).html($('#td_product_'+(x-1)).html());
      $('#td_product_'+x+'>.productSelect').val($('#td_product_'+(x-1)+'>.productSelect').val());
      
      $('#td_product_'+(x-1)).html(tmp_product);
      $('#td_product_'+(x-1)+'>.productSelect').val(tmp_product_value)
      

      tmp_kakaku = $('#td_kakaku_'+x).html();
      tmp_kakaku_value = $('#td_kakaku_'+x+'>input').val();
      
      $('#td_kakaku_'+x).html($('#td_kakaku_'+(x-1)).html());
      $('#td_kakaku_'+x+'>input').val($('#td_kakaku_'+(x-1)+'>input').val());
      
      $('#td_kakaku_'+(x-1)).html(tmp_kakaku);
      $('#td_kakaku_'+(x-1)+'>input').val(tmp_kakaku_value)
      
    
      tmp_kakuukosuu = $('#td_kakuukosuu_'+x).html();
      tmp_kakuukosuu_value = $('#td_kakuukosuu_'+x+'>input').val();
      
      $('#td_kakuukosuu_'+x).html($('#td_kakuukosuu_'+(x-1)).html());
      $('#td_kakuukosuu_'+x+'>input').val($('#td_kakuukosuu_'+(x-1)+'>input').val());
      
      $('#td_kakuukosuu_'+(x-1)).html(tmp_kakuukosuu);
      $('#td_kakuukosuu_'+(x-1)+'>input').val(tmp_kakuukosuu_value);
      
      bindActions();

    }
    function exchange (x, y) {
      tmp = $('#data_'+x+'_'+y).html();
      tmp_value = $('#oroshi_datas_'+x+'_'+y).val();
      
      $('#data_'+x+'_'+y).html($('#data_'+(x-1)+'_'+y).html());
      $('#oroshi_datas_'+x+'_'+y).val($('#oroshi_datas_'+(x-1)+'_'+y).val());
      
      $('#data_'+(x-1)+'_'+y).html(tmp);
      $('#oroshi_datas_'+(x-1)+'_'+y).val(tmp_value);
    }
    function selectProduct(index, value) {
      if (value == 0) {
        $('#kakaku_'+index).attr('disabled', true).val('');
        $('#kakuukosuu_'+index).attr('disabled', true).val('');
      } else {
        $('#kakaku_'+index).attr('disabled', false).val(kakaku[value]);
        $('#kakuukosuu_'+index).attr('disabled', false).val(kakuukosuu[value]);
      }
    }
    function bindActions() {
      $('.productSelect').bind('change', function(){
        selectProduct($(this).attr('name').substring($(this).attr('name').indexOf('[')+1,$(this).attr('name').indexOf(']')), $(this).val());
        if ($(this).val() != 0) {
          if (products[$(this).val()] == true) {
            $(this).val(last_val);
          }
          products[last_val] = false;
          products[$(this).val()] = true;
        } else {
          products[last_val] = false;
        }
      });
      $('.productSelect').bind('focus', function(){
        last_val = $(this).val();
        $(this).children('option').each(function(){
          if (
            $(this).attr('value') != 0
            && $(this).attr('value') != $(this).parent().val()
            && products[$(this).val()] == true
          ) {
            $(this).attr('disabled', true).css('color','999');
          } else {
            $(this).attr('disabled', false).css('color','000');
          }
        });
      });
      // for ie bug
      $('.productSelect').bind('mouseover', function(){
        last_val = $(this).val();
        $(this).children('option').each(function(){
          if (
            $(this).attr('value') != 0
            && $(this).attr('value') != $(this).parent().val()
            && products[$(this).val()] == true
          ) {
            $(this).attr('disabled', true).css('color','999');
          } else {
            $(this).attr('disabled', false).css('color','000');
          }
        });
      });
    }
    $(function(){
      bindActions();
      setDefault();
    });
  </script>
</head>
<body  onload="">
<?php 
  $cid = $_GET['cpath'];
  $res =tep_db_query('select * from set_oroshi_names son, categories c
      ,set_oroshi_categories soc where c.categories_id = "'.$cid.'" and c.categories_id = soc.categories_id and son.oroshi_id = soc.oroshi_id order by soc.oroshi_id desc');
    while($col = tep_db_fetch_array($res)){
      $oroname = $col['oroshi_name'];
      $oroid = $col['oroshi_id'];
    }
?>
<table border="1">
<?php
$lines_arr = array();
$oroname = array();
$cr = array("\r\n", "\r");   // 改行コード置換用配
$orocnt = tep_db_query('select distinct(oroshi_id) from set_oroshi_datas where
    parent_id = "'.$cid.'" order by oroshi_id');
while($testcol = tep_db_fetch_array($orocnt)){
  $oroids[] = $testcol['oroshi_id'];
}
//print_r($oroids);
if($oroids)
foreach($oroids as $key=>$value){
  $res = tep_db_query("select * from set_oroshi_names son, set_oroshi_datas sod
      where sod.oroshi_id ='". $value."' and sod.oroshi_id = son.oroshi_id and  parent_id='".$cid."' ORDER BY sod.list_id desc limit 1");
  $col = tep_db_fetch_array($res);
  $cols[]=$col;
  //print_r($col);
}

if($cols)
foreach($cols as $col){
    $oroname[] = $col['oroshi_name'];
    $orotime[] = $col['set_date'];
    $datas_id[] = $col['list_id'];
    $lines = spliteOroData($col['datas']);
    $count[]=count($lines);
    $lines_arr[]=$lines;
} 
                                
  $cnt = count($count);

for($n=0;$n<$cnt;$n++){
  if($count[0]<=$count[$n]){
    $count[0]=$count[$n];
  }
}
$rows = count($set_menu_list)>$count[0]?count($set_menu_list):$count[0];
?>
</table>
<!--------------------->
<form name='listform' method='POST' action="?action=update&cid=<?php echo $cID; ?>&cpath=<?php echo $cPath; ?>">
  <table border="1">
  <tr>
<?php if($orotime)foreach ($orotime as $k => $value){?>
    <td><?php echo $value;?></td>
<?php } ?>
    <td colspan="3"></td>
  </tr>
  <tr>
<?php foreach ($oroname as $k => $value){?>
    <th><?php echo $value;?></th>
<?php } ?>
    <th>商品選択</th>
    <th>個数/架空</th>
    <th>価格/業者</th>
    <!--<th></th>-->
  </tr>
<?php for($k = 0; $k < $rows; $k++) {?>
  <tr>
<?php
  for($j=0;$j<$cnt;$j++){
    echo "<td>";
    if (isset($lines_arr[$j][$k])) {
      echo "<span style='float:left' id='data_".$k."_".$j."'>".$lines_arr[$j][$k]."</span>";
    }
    if ($k != 0 && isset($lines_arr[$j][$k])) {
      echo "<span style='float:right'><a href=\"javascript:void(0)\" onclick=\"exchange(".$k.",".$j.")\" >↑</a></span>";
    }
    echo "<input type='hidden' name='oroshi_datas[".$datas_id[$j]."][]' id='oroshi_datas_".$k."_".$j."' value='".$lines_arr[$j][$k]."'>\n";
    echo "</td>";
  }
?>
    <td id="td_product_<?php echo $k;?>">
<?php //if($k<count($products)) {?>
      <select class="productSelect" name="product[<?php echo $k;?>]" id="product_<?php echo $k;?>">
        <option value='0'>--選択してください--</option>
<?php foreach ($products as $ok => $op) {?>
        <option value='<?php echo $op['products_id'];?>'><?php echo $op['products_name'];?></option>
<?php }?>
      </select>
<?php //}?>
    </td>
    <td id="td_kakuukosuu_<?php echo $k;?>">
<?php //if($k<count($products)) {?>
      <input type="text" size='10' name="kakuukosuu[<?php echo $k;?>]" id="kakuukosuu_<?php echo $k;?>" value="" disabled>
<?php //}?>
      </td>
    <td id="td_kakaku_<?php echo $k;?>">
<?php //if($k<count($products)) {?>
      <input type="text" size='10' name="kakaku[<?php echo $k;?>]" id="kakaku_<?php echo $k;?>" value="" disabled>
<?php //}?>
      </td>
    <!--<td>
<?php if ($k) {?>
        <input type='button' onclick="exchange_product(<?php echo $k;?>)" value='↑'>
<?php }?>
    </td>-->
  </tr>
<?php }?>
  </table>
  <input type="submit" value="決定">
  </form>
</body>
</html>
