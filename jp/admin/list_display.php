<?php 
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$cPath = $_GET['cpath'];
$cID   = $_GET['cid'];  

if ($ocertify->npermission>7) {
  $products = tep_get_products_by_categories_id($cID);
} else {
  $products = tep_get_products_by_categories_id($cID, 1);
}

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
          'kakuukosuu'    => SBC2DBC($_POST['kakuukosuu'][$i]),
          'kakaku'        => SBC2DBC($_POST['kakaku'][$i]),
          'categories_id' => $cID,
          'last_modified' => 'now()'
        ));
      //}
    }
  }
  //tep_redirect(tep_href_link('list_display.php','cid='.$cid.'&cpath='.$cpath));
  tep_redirect('categories_admin.php?cPath='.$_POST['fullpath']);
  break;
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
  <title>リスト表示</title>
  <style>
.dataTableHeadingRow {
background-color: #808080;
}

.dataTableHeadingContent {
color: #ffffff;
font-weight: bold;
}

.dataTableRow {
background-color: #F0F1F1;

}

.dataTableSecondRow {
background-color: #E0E0E0;

}
.dataTableRowOver {
background-color: #FFCC99;

}

.dataTableContent {
color: #000000;
/*font-size:11px;*/
/*white-space:nowrap;*/
}
  </style>
  <script type="text/javascript" src="includes/javascript/jquery.js"></script>
  <script type="text/javascript" src="includes/javascript/udlr.js"></script>
  <script type="text/javascript">
    // true = disabled, false = enabled
    var products = new Array();
    var last_val = 0;
    var kakaku = new Array();
    var kakuukosuu = new Array();
    //var products_order = new Array(31703,31697,31681)
    var products_order = new Array(<?php
    $set_menu_list_query = tep_db_query("select * from set_menu_list where categories_id='".$cID."' order by set_list_id asc");
    $i = 0;
    while($list = tep_db_fetch_array($set_menu_list_query)) {
      if ($i != 0){
        echo ',';
      }else{
        echo '"",';
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
      i = 1;
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
    function exchange_down (x, y) {
      tmp = $('#data_'+x+'_'+y).html();
      tmp_value = $('#oroshi_datas_'+x+'_'+y).val();
      
      $('#data_'+x+'_'+y).html($('#data_'+(x+1)+'_'+y).html());
      $('#oroshi_datas_'+x+'_'+y).val($('#oroshi_datas_'+(x+1)+'_'+y).val());
      
      $('#data_'+(x+1)+'_'+y).html(tmp);
      $('#oroshi_datas_'+(x+1)+'_'+y).val(tmp_value);
    }
    function deleteHistory(x,y){
      $('#data_'+x+'_'+y).html(' ');
      $('#oroshi_datas_'+x+'_'+y).val(' ');
      dhi = 1;
      while($('#data_'+(x+dhi)+'_'+y).length){
        exchange(x+dhi,y);
        dhi++;
      }
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
      $(".udlr").udlr();
      bindActions();
      setDefault();
    });
    function reset_page(){
       products = new Array();
      $('.kakuukosuu_input').attr('disabled', true);
      $('.kakaku_input').attr('disabled', true);
      setDefault();
      for(i in default_value){
        if(default_value[i].length) {
          for(j in default_value[i]){
            $('#data_'+i+'_'+j).html(default_value[i][j]);
            $('#oroshi_datas_'+i+'_'+j).val(default_value[i][j]);
          }
        }
      }
    }
    function clear_page(){
       products = new Array();
      $('.kakuukosuu_input').attr('disabled', true);
      $('.kakaku_input').attr('disabled', true);

      $('.productSelect').each(function(){
        this.selectedIndex = 0;
        $(this).trigger('change');
      });

      for(i in default_value){
        if(default_value[i].length) {
          for(j in default_value[i]){
            $('#data_'+i+'_'+j).html(default_value[i][j]);
            $('#oroshi_datas_'+i+'_'+j).val(default_value[i][j]);
          }
        }
      }
    }
  </script>
</head>
<body  onload="">
<?php 
  $cid = $_GET['cpath'];
  
  $res =tep_db_query('
    select * 
    from set_oroshi_names son, categories c,set_oroshi_categories soc
    where c.categories_id = "'.$cid.'" 
      and c.categories_id = soc.categories_id 
      and son.oroshi_id = soc.oroshi_id 
    order by soc.oroshi_id asc
  ');
  while($col = tep_db_fetch_array($res)){
    //$oroname2[] = $col['oroshi_name'];
    $oroids[]   = $col['oroshi_id'];
  }
  
$lines_arr = array();
$oroname   = array();
$cr        = array("\r\n", "\r");   // 改行コード置換用配
/*
$orocnt = tep_db_query('
  select distinct(oroshi_id) 
  from set_oroshi_datas 
  where parent_id = "'.$cid.'" 
  order by oroshi_id
');
while($testcol = tep_db_fetch_array($orocnt)){
  $oroids[] = $testcol['oroshi_id'];
}*/
if($oroids)
  foreach($oroids as $key=>$value){
    $res = tep_db_query("
      select * 
      from set_oroshi_names son, set_oroshi_datas sod
      where sod.oroshi_id ='". $value."' 
        and sod.oroshi_id = son.oroshi_id 
        and parent_id='".$cid."' 
      ORDER BY sod.list_id desc 
      limit 1
    ");
    $col    = tep_db_fetch_array($res);
    $cols[] = $col;
  }

if($cols)
foreach($cols as $col){
    $oroname[]   = $col['oroshi_name'];
    $orotime[]   = date('Y/m/d H:i:s',strtotime($col['set_date']));
    $datas_id[]  = $col['list_id'];
    $lines       = spliteOroData($col['datas']);
    $count[]     = count($lines);
    $lines_arr[] = $lines;
} 

                                
  $cnt = count($count);

for($n=0;$n<$cnt;$n++){
  if($count[0]<=$count[$n]){
    $count[0]=$count[$n];
  }
}
//$rows = count($set_menu_list)>$count[0]?count($set_menu_list):$count[0];
$rows = $count[0]>count($products)?$count[0]:count($products);
?>
<script>
  default_value=new Array();
<?php for($k = 0; $k < $rows; $k++) {
  echo "default_value[".$k."]=new Array();\n";
  for($j=0;$j<$cnt;$j++){
    if (isset($lines_arr[$j][$k])) {
      echo "default_value[".$k."][".$j."]='".$lines_arr[$j][$k]."';\n";
    }
  }
  }?>
</script>
<!--------------------->
<form name='listform' method='POST' action="?action=update&cid=<?php echo $cID; ?>&cpath=<?php echo $cPath; ?>">
  <table border="0" cellspacing="1" cellpadding="2">
  <tr class="dataTableRow">
<?php if($orotime)foreach ($orotime as $k => $value){?>
    <td class="dataTableContent"><?php echo $value;?></td>
<?php } ?>
    <td colspan="3" class="dataTableContent"></td>
  </tr>
  <tr class="dataTableHeadingRow">
<?php foreach ($oroname as $k => $value){?>
    <th class="dataTableHeadingContent"><?php echo $value;?></th>
<?php } ?>
    <th class="dataTableHeadingContent" >商品選択</th>
    <th class="dataTableHeadingContent" >架空数量</th>
    <th class="dataTableHeadingContent" >業者単価</th>
    <!--<th></th>-->
  </tr>
<?php for($k = 0; $k < $rows; $k++) {?>
  <tr class="<?php echo $k%2==1?'dataTableRow':'dataTableSecondRow';?>" onmouseover="this.className='dataTableRowOver'" onmouseout="this.className='<?php echo $k%2==1?'dataTableRow':'dataTableSecondRow';?>'">
<?php
  for($j=0;$j<$cnt;$j++){
    echo "<td class=\"dataTableContent\" valign='top'>";
    if (isset($lines_arr[$j][$k])) {
      echo "<span style='float:left' class='oroshi_data' id='data_".$k."_".$j."'>".$lines_arr[$j][$k]."</span>";
    }
    echo "<span style='float:right'>";
    if ($k != 0 && isset($lines_arr[$j][$k])) {
      echo "<a href=\"javascript:void(0)\" onclick=\"exchange(".$k.",".$j.")\" >↑</a>";
    }
    if ($k != ($count[0]-1) && isset($lines_arr[$j][$k])) {
      echo "<a href=\"javascript:void(0)\" onclick=\"exchange_down(".$k.",".$j.")\" >↓</a>";
    } else if ($k == ($count[0]-1) && isset($lines_arr[$j][$k])) {
      echo "↓";
    }
    if (isset($lines_arr[$j][$k])) {
      echo "  <a href=\"javascript:void(0)\" onclick=\"deleteHistory(".$k.",".$j.")\" >X</a>";
    }
    echo "</span>";
    echo "<input type='hidden' name='oroshi_datas[".$datas_id[$j]."][]' id='oroshi_datas_".$k."_".$j."' value='".$lines_arr[$j][$k]."'>\n";
    echo "</td>";
  }
?>
    <td class="dataTableContent" id="td_product_<?php echo $k;?>">
<?php //if($k<count($products)) {?>
      <select class="productSelect" name="product[<?php echo $k;?>]" id="product_<?php echo $k;?>">
        <option value='0'>--選択してください--</option>
<?php foreach ($products as $ok => $op) {?>
        <option value='<?php echo $op['products_id'];?>'><?php echo $op['products_name'];?></option>
<?php }?>
      </select>
<?php //}?>
    </td>
    <td class="dataTableContent" id="td_kakuukosuu_<?php echo $k;?>">
<?php //if($k<count($products)) {?>
      <input pos="<?php echo $k;?>_0" class="udlr kakuukosuu_input" type="text" size='10' name="kakuukosuu[<?php echo $k;?>]" id="kakuukosuu_<?php echo $k;?>" value="" disabled>
<?php //}?>
      <!--<a href="javascript:void(0)" onclick="$('.kakuukosuu_input').val($('#kakuukosuu_<?php echo $k;?>').val())">統一</a>-->
      </td>
    <td class="dataTableContent" id="td_kakaku_<?php echo $k;?>">
<?php //if($k<count($products)) {?>
      <input pos="<?php echo $k;?>_1" class="udlr kakaku_input" type="text" size='10' name="kakaku[<?php echo $k;?>]" id="kakaku_<?php echo $k;?>" value="" disabled>
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
    <input type="hidden" name="fullpath" value="<?php echo $_GET['fullpath']?>">
    <input type="submit" value="決定">
    <input type="button" value="リセット" onclick="clear_page()">
    <!--<input type="button" value="リセット" onclick="reset_page()">-->
  </form>
</body>
</html>
