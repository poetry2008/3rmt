<?php 

require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();




?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; 
charset=<?php echo CHARSET; ?>">
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link media="print" rel="stylesheet" type="text/css" href="includes/print_assets.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<title><?php echo ASSETS_TITLE;?></title>
</head>
<body>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    //one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<div class="assets_title">
<?php echo ASSETS_TITLE;?>
</div>
<?php
set_time_limit(0);
if(isset($pid)&&$pid){ 
//详细页面
echo tep_draw_form('new_product', FILENAME_ASSETS,tep_get_all_get_params(
      array('endY','endM','endD')),'get');
?>
<table class="assets_info_search_bar" width="100%">
<tr><td width="13%">
<table><tr><td>
    <input type="hidden" name="pid" value="<?php echo $pid;?>">
    <input type="hidden" name="site_id" value="<?php echo $_GET['site_id'];?>">
    <select name="endY" size="1">
                    <?php
    if ($endDate) {
      $y = date("Y") - date("Y", $endDate - 60* 60 * 24);
    } else {
      $y = 0;
    }
    for ($i = 10; $i >= 0; $i--) {
?>
   <?php if(isset($_GET['endY'])&&$_GET['endY']){
   ?>
   <option<?php if ($_GET['endY'] == date("Y") - $i) echo " selected"; ?>>
   <?php
   }else{
   ?>
   <option<?php if ($y == $i) echo " selected"; ?>>
   <?php } ?>
   <?php echo date("Y") - $i; ?></option>
<?php
    }
?>
    </select>
    <?php echo ASSETS_YEAR_TEXT;?>
    </td>
    <td><select name="endM" size="1">
<?php
    if ($endDate) {
      $m = date("n", $endDate - 60* 60 * 24);
    } else {
      $m = date("n");
    }
    for ($i = 1; $i < 13; $i++) {
    if(isset($_GET['endM'])&&$_GET['endM']){
    ?>
    <option<?php if ($_GET['endM'] == $i) echo " selected"; ?> value="<?php echo $i; ?>">
    <?php }else{ ?>
    <option<?php if ($m == $i) echo " selected"; ?> value="<?php echo $i; ?>">
    <?php //echo strftime("%B", mktime(0, 0, 0, $i, 1)); ?>
    <?php } ?>
    <?php echo $i;?> 
    </option>
<?php
    }
?>
    </select>
    <?php echo ASSETS_MONTH_TEXT;?>
    </td>
    <td><select name="endD" size="1">
<?php
    if ($endDate) {
      $j = date("j", $endDate - 60* 60 * 24);
    } else {
      $j = date("j");
    }
    for ($i = 1; $i < 32; $i++) {
    if(isset($_GET['endD'])&&$_GET['endD']){
    ?>
    <option<?php if ($_GET['endD'] == $i) echo " selected"; ?>>
<?php
    }else{
?>
    <option<?php if ($j == $i) echo " selected"; ?>>
    <?php } ?>
    <?php echo $i; ?></option>
<?php
    }
?>
    </select>
    <?php echo ASSETS_DAY_TEXT;?>
    </td>
    </tr>
</table>
</td>
<td>
<?php 
echo tep_html_element_submit(TEXT_SEARCH);
?>
</td>
<td align="right" width="50%">
<div class="assets_bottom">
<input type="button" value="<?php echo TEXT_ASSETS_PRINT;?>" onclick="window.print();">
</div>
</td>
</tr>
</table>
</form>
<?php
$all_products_sql = "select o.`date_purchased`,op.`products_name`,
  op.`products_quantity`,op.`final_price`,
  op.products_id from ".TABLE_ORDERS." o,".TABLE_ORDERS_PRODUCTS." op 
  where o.orders_id = op.orders_id and op.products_id ='".$pid."' " ;
if(isset($_GET['endY'])&&$_GET['endY']&&
    isset($_GET['endY'])&&$_GET['endY']&&
    isset($_GET['endY'])&&$_GET['endY'])
{
  $all_products_sql .= " and date_purchased > '".($_GET['endY']-1)."-".$_GET['endM']."-".$_GET['endD']."' ";
  $all_products_sql .= " and date_purchased < '".($_GET['endY'])."-".$_GET['endM']."-".$_GET['endD']."' ";
}else{
  $all_products_sql .= " and date_purchased > DATE_SUB(now(),INTERVAL 1 year) ";
  $all_products_sql .= " and date_purchased < DATE_SUB(now(),INTERVAL 0 year) ";
}
if(isset($_GET['site_id'])&&$_GET['site_id']){
  $all_products_sql .= " and op.site_id = '".$_GET['site_id']."' ";
}
$products_query = tep_db_query($all_products_sql);
?>
<table cellpadding="0" cellspacing="1" border="0" width="99%" class="assets_box">
<tr class="assets_text">
<td  height="28" align="center"><b><?php echo TEXT_ORDER_PRODUCTS_DATE;?></b></td>
<td align="center"><b><?php echo TEXT_ORDER_PRODUCTS_NAME;?></b></td>
<td align="center"><b><?php echo TEXT_ORDER_PRODUCTS_QUANTITY;?></b></td>
<td align="center"><b><?php echo TEXT_ORDER_PRODUCTS_PRICE;?></b></td>
</tr>
<?php
$is_null = true;
while($row = tep_db_fetch_array($products_query)){
  $is_null = false;
  echo "<tr class='asstes_c'>";
  echo "<td height='25' align='left'>"; 
  echo $row['date_purchased'];
  echo "</td>";
  echo "<td align='left'>"; 
  echo $row['products_name'];
  echo "</td>";
  echo "<td align='center'>"; 
  echo $row['products_quantity'];
  echo "</td>";
  echo "<td align='center'>"; 
  echo $currencies->format($row['final_price']);
  echo "</td>";
  echo "</tr>";
}
if($is_null){
  echo "<tr><td colspan='4'>";
  echo TEXT_ASSETS_NO_DATA;
  echo "</td></tr>";
}
?>
</table>
<?php
echo "<div class=\"assets_bottom\"><a href='".
tep_href_link(FILENAME_ASSETS,tep_get_all_get_params(array('pid','endY','endM','endD')))."'>";
echo tep_html_element_button(TEXT_BACK);
echo "</a>";
echo '&nbsp;<input type="button" value="'.TEXT_ASSETS_PRINT.'" onclick="window.print();">';
echo "</div>";
}else{
//简易页面
echo tep_draw_form('new_product', FILENAME_ASSETS,tep_get_all_get_params(
      array('site_id','bflag','product_categories_id')),'get');
echo '<div class="assets_search_bar">';
echo tep_site_pull_down_menu_with_all($_GET['site_id'], false, TEXT_SHOW_ALL);
echo '&nbsp;&nbsp;';
echo tep_draw_pull_down_menu('product_categories_id',tep_get_category_tree(),
    $current_category_id);
?>
&nbsp;&nbsp;
<select name="bflag">
  <option value="1"<?php if($_GET['bflag'] == '1'){?> selected<?php }?>><?php echo TEXT_SELL;?>
</option>
  <option value="2"<?php if($_GET['bflag'] == '2'){?> selected<?php }?>><?php echo TEXT_BUY;?>
</option>
</select>
<select name="sort_order">
  <option value="" <?php
  if(!isset($_GET['sort_order'])||$_GET['sort_order']==''){?>
   selected<?php }?>><?php echo TEXT_SORT_DATE;?>
  </option>
  <option value="price_asc" <?php
  if(isset($_GET['sort_order'])||$_GET['sort_order']=='price_asc'){?>
   selected<?php }?>><?php echo TEXT_SORT_PRICE_ASC;?>
  </option>
  <option value="price_desc" <?php
  if(isset($_GET['sort_order'])||$_GET['sort_order']=='price_desc'){?>
   selected<?php }?>><?php echo TEXT_SORT_PRICE_DESC;?>
  </option>
</select>
&nbsp;&nbsp;
<?php
echo tep_html_element_submit(TEXT_SEARCH);
echo '&nbsp;&nbsp;';
echo "<a href='".
tep_href_link(FILENAME_ASSETS,tep_get_all_get_params(array('action')))."'>";
echo tep_html_element_button(TEXT_REFRESH);
echo "</a>";
?>
<input type="button" class="assets_input" value="<?php echo TEXT_ASSETS_PRINT;?>" onclick="window.print();">
<?php
echo '</div>';
echo "</form>";
$all_products_sql = 'SELECT avg(op.final_price) as final_unit_price, p.`products_id` ,  
  o.torihiki_date ,
  `products_real_quantity` , pd.`products_name`,p.`products_price` 
    FROM  '.TABLE_PRODUCTS.' p,  '.TABLE_PRODUCTS_DESCRIPTION.'
    pd,'.TABLE_ORDERS_PRODUCTS.' op ,
    '.TABLE_ORDERS.' o, '.TABLE_ORDERS_STATUS.' os ';

if (!empty($_GET['product_categories_id'])) {
  $all_products_sql .= ' , `products_to_categories` p2c ';
}
$all_products_sql .= ' WHERE op.orders_id = o.orders_id and o.orders_status =
os.orders_status_id and os.calc_price = 1 and  
p.products_id = op.products_id and p.products_id = pd.products_id and pd.site_id = 0';
if(isset($_GET['site_id'])&&$_GET['site_id']){
  $all_products_sql .= " and op.site_id = '".$_GET['site_id']."' ";
}
if($_GET['bflag'] == 2){
  $all_products_sql .= " and p.products_bflag = 1 ";
}else{
  $all_products_sql .= " and p.products_bflag = 0 ";
}

if (!empty($_GET['product_categories_id'])) {
  $category_path_arr = tep_get_child_category_by_cid($_GET['product_categories_id']);
  $all_products_sql .= ' and p.products_id = p2c.products_id and p2c.categories_id in ('.implode(',', $category_path_arr).')';
}

$all_products_sql .= " group by op.products_id ";


if(isset($_GET['sort_order'])){
  if($_GET['sort_order'] == 'price_asc'){
    $all_products_sql .= 'order by final_unit_price asc ';
  }else if($_GET['sort_order'] == 'price_desc'){
    $all_products_sql .= 'order by final_unit_price desc ';
  }else {
    $all_products_sql .= 'order by torihiki_date desc ';
  }
}else{
  $all_products_sql .= 'order by torihiki_date desc ';
}
$all_products_query = tep_db_query($all_products_sql);
$all_quantity = 0;
$all_price = 0;
$all_price_sum = 0;
?>
<table cellpadding="0" cellspacing="1" border="0" width="99%" class="assets_box">
<tr class="assets_text">
<td height="28" align="center" width="15%"><b><?php echo TEXT_PRODUCTS_DATE;?></b></td>
<td height="28" align="center" width="35%"><b><?php echo TEXT_PRODUCTS_NAME;?></b></td>
<td align="center" width="10%"><b><?php echo TEXT_PRODUCTS_QUANTITY;?></b></td>
<td align="center"><b><?php echo TEXT_PRODUCTS_PRICE;?></b></td>
<td align="center"><b><?php echo TEXT_PRODUCTS_PRICE_SUM;?></b></td>
</tr>
<?php
if(isset($_GET['site_id'])){$site_id=$_GET['site_id'];}else{$site_id=0;}
$is_null = true;
while($row = tep_db_fetch_array($all_products_query)){
  $is_null = false;
  $row_quantity = $row['products_real_quantity'];
  $row_price = $row['final_unit_price'];//tep_get_avg_price_by_order($row['products_id'],$site_id);
  $all_quantity += $row_quantity;
  $all_price += abs($row_price);
  echo "<tr class='asstes_c'>";
  echo "<td height='25' align='left'>";
  echo $row['torihiki_date'];
  echo "</td>";
  echo "<td height='25' align='left'>";
  echo "<span class=\"assets_info_link\"><a href='".  tep_href_link(FILENAME_ASSETS,"pid=".$row['products_id']."&".tep_get_all_get_params())."'>";
  //echo tep_html_element_button(TEXT_INFO);
  echo tep_image(DIR_WS_ICONS.'preview.gif', TEXT_INFO); 
  echo "</a></span>";
  echo "&nbsp;&nbsp;"; 
  echo $row['products_name'];
  echo "</td>";
  echo "<td align='center'>";
  echo $row_quantity;
  echo "</td>";
  echo "<td align='center'>";
  echo $currencies->format($row_price);
  echo "</td>";
  echo "<td align='center'>";
  echo $currencies->format($row_quantity*$row_price);
  echo "</td>";
  echo "</tr>";
}
if($is_null){
  echo "<tr><td colspan='4'>";
  echo TEXT_ASSETS_NO_DATA;
  echo "</td></tr>";
}
?>
</table>
<?php
}
?>
<br>
<div id="print_footer">
<?php
  require(DIR_WS_INCLUDES . 'footer.php');
?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
