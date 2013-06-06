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
/* -----------------------------------------------------
   case 'update' 更新批发商的信息以及商品的数量    
------------------------------------------------------*/
case 'update':
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

    for ($i = 0; $i < count($_POST['product']); $i++) {
        tep_db_perform('set_menu_list', array(
          'products_id'   => $_POST['product'][$i],
          'kakaku'        => SBC2DBC($_POST['kakaku'][$i]),
          'categories_id' => $cID,
          'last_modified' => 'now()'
        ));
        tep_db_perform('products', array('products_virtual_quantity' => SBC2DBC($_POST['kakuukosuu'][$i])), 'update', "products_id='".$_POST['product'][$i]."'");
    }
  }
  tep_redirect('categories.php?cPath='.$_POST['fullpath']);
  break;
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
  <title><?php echo TEXT_LIST_RISUTOHYOUZI;?></title>
  <link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />
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
}
.show_menu{
	height:auto;
	min-height:650px;
}
.show_menu p{
	min-width:750px;
	margin-left:8px;
}
  </style>
  <script type="text/javascript" src="includes/javascript/jquery.js"></script>
  <script type="text/javascript" src="includes/javascript/udlr.js"></script>
  <script language="javascript" src="includes/javascript/jquery_include.js"></script>
  <script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
  <script type="text/javascript">
    var products = new Array();
    var last_val = 0;
    var kakaku = new Array();
    var kakuukosuu = new Array();
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
    kakuukosuu[<?php echo $jp['products_id'];?>] = <?php echo tep_get_kakuukosuu_by_products_id($jp['products_id']);?>;
    kakaku[<?php echo $jp['products_id'];?>]     = <?php echo tep_get_kakaku_by_products_id($cID,$jp['products_id']);?>;
<?php }?>

    <?php //设置默认值?>
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
    <?php //交换产品数据?>
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
    <?php //向上交换数据?> 
    function exchange (x, y) {
      tmp = $('#data_'+x+'_'+y).html();
      tmp_value = $('#oroshi_datas_'+x+'_'+y).val();
      
      $('#data_'+x+'_'+y).html($('#data_'+(x-1)+'_'+y).html());
      $('#oroshi_datas_'+x+'_'+y).val($('#oroshi_datas_'+(x-1)+'_'+y).val());
      
      $('#data_'+(x-1)+'_'+y).html(tmp);
      $('#oroshi_datas_'+(x-1)+'_'+y).val(tmp_value);
    }
    <?php //向下交换数据?> 
    function exchange_down (x, y) {
      tmp = $('#data_'+x+'_'+y).html();
      tmp_value = $('#oroshi_datas_'+x+'_'+y).val();
      
      $('#data_'+x+'_'+y).html($('#data_'+(x+1)+'_'+y).html());
      $('#oroshi_datas_'+x+'_'+y).val($('#oroshi_datas_'+(x+1)+'_'+y).val());
      
      $('#data_'+(x+1)+'_'+y).html(tmp);
      $('#oroshi_datas_'+(x+1)+'_'+y).val(tmp_value);
    }
    <?php //删除历史数据?> 
    function deleteHistory(x,y){
      $('#data_'+x+'_'+y).html(' ');
      $('#oroshi_datas_'+x+'_'+y).val(' ');
      dhi = 1;
      while($('#data_'+(x+dhi)+'_'+y).length){
        exchange(x+dhi,y);
        dhi++;
      }
    }
    <?php //选择商品是否可以编辑?> 
    function selectProduct(index, value) {
      if (value == 0) {
        $('#kakaku_'+index).attr('disabled', true).val('');
        $('#kakuukosuu_'+index).attr('disabled', true).val('');
      } else {
        $('#kakaku_'+index).attr('disabled', false).val(kakaku[value]);
        $('#kakuukosuu_'+index).attr('disabled', false).val(kakuukosuu[value]);
      }
    }
    <?php //绑定选择框动作?> 
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
    <?php //恢复默认?> 
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
    <?php //清除数据?> 
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
    <?php //设置输入框允许输入的最大长度?> 
    function check_input(pos){
	    var value = pos.value;
	    var arr = new Array();
	    var len = value.length;
	    arr = value.split(".");
	    if(arr.length == 2){
		    if(arr[1].length == 2){
                         $(pos).attr("maxlength",len);

		    }

	    }
    }
<?php //执行动作?>
function toggle_list_display_form(c_permission)
{
  if (c_permission == 31) {
    document.forms.listform.submit(); 
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          document.forms.listform.submit(); 
        } else {
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.listform.action),
              async: false,
              success: function(msg_info) {
                document.forms.listform.submit(); 
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
          }
        }
      }
    });
  }
}
</script>
</head>
<body  onload="">
<?php
if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
    <script language='javascript'>
          one_time_pwd('<?php echo $page_name;?>');
    </script>
<?php }?>
<?php 
  $cid = $_GET['cpath'];
  
  $res =tep_db_query('
    select * 
    from set_oroshi_names son, categories c,set_oroshi_categories soc
    where c.categories_id = "'.$cid.'" 
      and c.categories_id = soc.categories_id 
      and son.oroshi_id = soc.oroshi_id 
    order by son.sort_order asc
  ');
  while($col = tep_db_fetch_array($res)){
    $oroids[]   = $col['oroshi_id'];
  }
  
$lines_arr = array();
$oroname   = array();
$cr        = array("\r\n", "\r");   // 用于换行代码替换
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
  if($col['set_date']){
    $oroname[]   = $col['oroshi_name'];
    $orotime[]   = date('Y/m/d H:i:s',strtotime($col['set_date']));
    $datas_id[]  = $col['list_id'];
    $lines       = spliteOroData($col['datas']);
    $count[]     = count($lines);
    $lines_arr[] = $lines;
  }
} 

if($count){
                                
  $cnt = count($count);

for($n=0;$n<$cnt;$n++){
  if($count[0]<=$count[$n]){
    $count[0]=$count[$n];
  }
}
$rows = $count[0]>count($products)?$count[0]:count($products);
}
?>
<script>
  default_value=new Array();
<?php 
 if($count){
 for($k = 0; $k < $rows; $k++) {
  echo "default_value[".$k."]=new Array();\n";
  for($j=0;$j<$cnt;$j++){
    if (isset($lines_arr[$j][$k])) {
      echo "default_value[".$k."][".$j."]='".$lines_arr[$j][$k]."';\n";
    }
  }
  }
 }
 ?>
</script>
<form name='listform' method='POST' action="?action=update&cid=<?php echo $cID; ?>&cpath=<?php echo $cPath; ?>">
<div class="show_menu">
  <table border="0" cellspacing="1" cellpadding="2" style="margin-left:8px; min-width:750px;">
  <tr class="dataTableRow">
<?php if($orotime)foreach ($orotime as $k => $value){?>
    <td class="dataTableContent"><?php echo $value;?></td>
<?php } ?>
    <td colspan="3" class="dataTableContent"></td>
  </tr>
  <tr class="dataTableHeadingRow">
<?php 
  if($oroname){
    foreach ($oroname as $k => $value){?>
    <th class="dataTableHeadingContent"><?php echo $value;?></th>
<?php }} ?>
    <th class="dataTableHeadingContent" ><?php echo LIST_DISPLAY_PRODUCT_SELECT;?></th>
    <th class="dataTableHeadingContent" ><?php echo LIST_DISPLAY_JIAKONGZAIKU;?></th>
    <th class="dataTableHeadingContent" ><?php echo LIST_DISPLAY_YEZHE_PRICE;?></th>
  </tr>
<?php 
 if($count){
  for($k = 0; $k < $rows; $k++) {?>
  <tr class="<?php echo $k%2==1?'dataTableRow':'dataTableSecondRow';?>" onmouseover="this.className='dataTableRowOver'" onmouseout="this.className='<?php echo $k%2==1?'dataTableRow':'dataTableSecondRow';?>'">
<?php
  for($j=0;$j<$cnt;$j++){
    echo "<td class=\"dataTableContent\">";
    if (isset($lines_arr[$j][$k])) {
      echo "<span style='float:left' class='oroshi_data' id='data_".$k."_".$j."'>".$lines_arr[$j][$k]."</span>";
    }
    echo "<span style='float:right'>";
    echo '<table border="0" cellspacing="0" cellpadding="0">';
    echo '<tr>';
    if ($k != 0 && isset($lines_arr[$j][$k])) {
      echo "<td width='12'><a href=\"javascript:void(0)\" onclick=\"exchange(".$k.",".$j.")\" >↑</a></td>";
    }else{
      echo '<td width="12">&nbsp;</td>'; 
    }
    if ($k != ($count[0]-1) && isset($lines_arr[$j][$k]) && count($lines_arr[$j]) > 1) {
      echo "<td width='12'><a href=\"javascript:void(0)\" onclick=\"exchange_down(".$k.",".$j.")\" >↓</a></td>";
    }else{
      echo '<td width="12">&nbsp;</td>'; 
    }
    if (isset($lines_arr[$j][$k])) {
      echo "<td width='13' align='center'><a href=\"javascript:void(0)\" onclick=\"deleteHistory(".$k.",".$j.")\" >X</a></td>";
    }
    echo '</tr>';
    echo '</table>';
    echo "</span>";
    echo "<input type='hidden' name='oroshi_datas[".$datas_id[$j]."][]' id='oroshi_datas_".$k."_".$j."' value='".$lines_arr[$j][$k]."'>\n";
    echo "</td>";
  }
?>
    <td class="dataTableContent" id="td_product_<?php echo $k;?>">
      <select class="productSelect" name="product[<?php echo $k;?>]" id="product_<?php echo $k;?>">
        <option value='0'><?php echo TEXT_LIST_ENTAKUSHITEKUDASAI;?></option>
<?php foreach ($products as $ok => $op) {?>
        <option value='<?php echo $op['products_id'];?>'><?php echo $op['products_name'];?></option>
<?php }?>
      </select>
    </td>
    <td class="dataTableContent" id="td_kakuukosuu_<?php echo $k;?>">
      <input pos="<?php echo $k;?>_0" class="udlr kakuukosuu_input" type="text" size='10' name="kakuukosuu[<?php echo $k;?>]" id="kakuukosuu_<?php echo $k;?>" value="" disabled>
    </td>
    <td class="dataTableContent" id="td_kakaku_<?php echo $k;?>">
      <input pos="<?php echo $k;?>_1" onkeyup="check_input(this);" class="udlr kakaku_input" type="text" size='10' name="kakaku[<?php echo $k;?>]" id="kakaku_<?php echo $k;?>" value="" disabled>
    </td>
  </tr>
<?php }}?>
  </table>
    <input type="hidden" name="fullpath" value="<?php echo $_GET['fullpath']?>">
    <p><a href="javascript:void(0);"><?php echo tep_html_element_button(TEXT_LIST_KETTEI, 'onclick="toggle_list_display_form(\''.$ocertify->npermission.'\');"');?></a>
    <input type="button" value="<?php echo TEXT_LIST_RISETTO;?>" onclick="clear_page()"></p>
    </div>
  </form>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</body>
</html>
