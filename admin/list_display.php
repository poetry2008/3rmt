<?php 
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$cPath = $_GET['cpath'];
$cID   = $_GET['cid'];  

switch ($_GET['action']){
case update:
  foreach ($_POST['product'] as $key => $pid) {
    if ($pid != 0) {
      if (tep_db_num_rows(tep_db_query("select * from set_menu_list where categories_id ='".$cID."' and products_id='".$pid."'"))) {
        tep_db_perform('set_menu_list', array(
                                              'kakuukosuu' => tep_db_prepare_input($_POST['kakuukosuu'][$key]),
                                              'kakaku' => tep_db_prepare_input($_POST['kakaku'][$key])
                                              ), 'update', 'categories_id=\'' . tep_db_prepare_input($cID) . '\' and products_id = \'' . tep_db_prepare_input($pid) . '\'');
      } else {
        tep_db_perform('set_menu_list', array(
                                              'kakuukosuu' => tep_db_prepare_input($_POST['kakuukosuu'][$key]),
                                              'kakaku' => tep_db_prepare_input($_POST['kakaku'][$key]),
                                              'categories_id' => tep_db_prepare_input(tep_db_prepare_input($cID)),
                                              'products_id' => tep_db_prepare_input(tep_db_prepare_input($pid)),
                                              ));
      }
    }
  }
  /*
  echo "<pre>";
  print_r($_POST);
  echo "</pre>";
  exit;*/
  break;
}
$products = tep_get_products_by_categories_id($cID);
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
<?php foreach ($products as $jk => $jp) {?>
    kakuukosuu[<?php echo $jp['products_id'];?>] = <?php echo tep_get_kakuukosuu_by_products_id($cID,$jp['products_id']);?>;
    kakaku[<?php echo $jp['products_id'];?>]     = <?php echo tep_get_kakaku_by_products_id($cID,$jp['products_id']);?>;
<?php }?>

    function setDefault () {
      i = 1;
      $('.productSelect').each(function(){
        this.selectedIndex = i;
        //$(this).chage();
        $(this).trigger('change');
        i++;
      });
    }

    function exchange_product(x) {
      //alert(x);
      tmp_product = $('#td_product_'+x).html();
      tmp_product_value = $('#td_product_'+x+'>.productSelect').val();
      
      $('#td_product_'+x).html($('#td_product_'+(x-1)).html());
      $('#td_product_'+x+'>.productSelect').val($('#td_product_'+(x-1)+'>.productSelect').val());
      //$('#td_product_'+x+'>.productSelect').trigger('change');
      
      $('#td_product_'+(x-1)).html(tmp_product);
      $('#td_product_'+(x-1)+'>.productSelect').val(tmp_product_value)
      //$('#td_product_'+(x-1)+'>.productSelect').trigger('change');


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
      $('#td_kakuukosuu_'+(x-1)+'>input').val(tmp_kakuukosuu_value)

    }
    function exchange (x, y) {
      tmp = $('#data_'+x+'_'+y).html();
      $('#data_'+x+'_'+y).html($('#data_'+(x-1)+'_'+y).html());
      $('#data_'+(x-1)+'_'+y).html(tmp);
    }
    function selectProduct(index, value) {
      if (value == 0) {
        $('#kakaku_'+index).val('');
        $('#kakuukosuu_'+index).val('');
      } else {
        $('#kakaku_'+index).val(kakaku[value]);
        $('#kakuukosuu_'+index).val(kakuukosuu[value]);
      }
    }
    function productSelected(ele) {
    }
    $(function(){
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
        //alert('focus');
        last_val = $(this).val();
        $(this).children('option').each(function(){
          
          //alert(last_val);
          if (
            $(this).attr('value') != 0
            && $(this).attr('value') != $(this).parent().val()
            && products[$(this).val()] == true
          ) {
            $(this).attr('disabled', true);
            $(this).css('color','999');
          } else {
            $(this).attr('disabled', false);
            $(this).css('color','000');
          }
        });
      });
      setDefault();
    });
  </script>
</head>
<body  onload="">
<?php 
  $cid = $_GET['cpath'];
  $res =tep_db_query('select * from set_oroshi_names son, categories c ,set_oroshi_categories soc where c.categories_id = '.$cid.' and c.categories_id = soc.categories_id and son.oroshi_id = soc.oroshi_id order by soc.oroshi_id desc');
    while($col = tep_db_fetch_array($res)){
      $oroname = $col['oroshi_name'];
      $oroid = $col['oroshi_id'];
    }
?>
<table border="1">
<?php
$lines_arr = array();
$oroname = array();
$cr = array("\r\n", "\r");
$orocnt = tep_db_fetch_array(tep_db_query('select count(distinct(oroshi_id))as cnt from set_oroshi_datas where parent_id = '.$cid));
$res = tep_db_query("select * from set_oroshi_names son, set_oroshi_datas sod where sod.oroshi_id = son.oroshi_id and  parent_id='".$cid."' ORDER BY list_id desc limit ".$orocnt['cnt']);
while($col = tep_db_fetch_array($res)){
    $oroname[] = $col['oroshi_name'];
    $col['datas'] = trim($col['datas']); 
    $col['datas'] = str_replace($cr, "\n",$col['datas']);
    $lines= explode("\n", $col['datas']);
    $count[]=count($lines);
    $lines_arr[]=$lines;
} 

  $cnt = count($count);

for($n=0;$n<$cnt;$n++){
  if($count[0]<=$count[$n]){
    $count[0]=$count[$n];
  }
}
/*
echo "<tr>";  
foreach ($oroname as $value){
  echo "<th>$value</th>";
}
echo "</tr>";

for($i=0;$i < $count[0];$i++){
  echo "<tr id=color>";
  for($j=0;$j<$cnt;$j++){
    echo "<td>".$lines_arr[$j][$i]."</td>";
  }
  echo "</tr>";
}*/
$rows = count($products)>$count[0]?count($products):$count[0];
?>
</table>
<!--------------------->
<form name='listform' method='POST' action="list_display.php?action=update&cid=<?php echo $cID; ?>&cpath=<?php echo $cPath; ?>">
  <table border="1">
  <tr>
<?php foreach ($oroname as $value){?>
    <th><?php echo $value;?></th>
<?php } ?>
    <th>商品選択</th>
    <th>個数/架空</th>
    <th>価格/業者</th>
    <th></th>
  </tr>
<?php for($k = 0; $k < $rows; $k++) {?>
  <tr>
<?php
  for($j=0;$j<$cnt;$j++){
    echo "<td>";
    if (isset($lines_arr[$j][$k])) {
      echo "<span id='data_".$k."_".$j."'>".$lines_arr[$j][$k]."</span>";
    }
    if ($k != 0 && isset($lines_arr[$j][$k])) {
      echo "<a href=\"javascript:void(0)\" onclick=\"exchange(".$k.",".$j.")\">↑</a>";
    }
    echo "</td>";
  }
?>
    <td id="td_product_<?php echo $k;?>">
      <select class="productSelect" name="product[<?php echo $k;?>]" id="product_<?php echo $k;?>">
        <option value='0'>--選択してください--</option>
<?php foreach ($products as $ok => $op) {?>
        <option value='<?php echo $op['products_id'];?>'><?php echo $op['products_name'];?></option>
<?php }?>
      </select>
    </td>
    <td id="td_kakuukosuu_<?php echo $k;?>"><input type="text" name="kakuukosuu[<?php echo $k;?>]" id="kakuukosuu_<?php echo $k;?>" value="" ></td>
    <td id="td_kakaku_<?php echo $k;?>"><input type="text" name="kakaku[<?php echo $k;?>]"     id="kakaku_<?php echo $k;?>"     value="" ></td>
    <td>
<?php if ($k) {?>
        <a href="javascript:void(0)" onclick="exchange_product(<?php echo $k;?>)">↑</a>
<?php }?>
    </td>
  </tr>
<?php }?>
  </table>
  <input type="submit">
  </form>
</body>
</html>
