<?php
/*
 * お届け時間指定グループの設定 Ajax
 */
require('includes/application_top.php');
$id = tep_db_prepare_input($_POST['id']);
$flag = tep_db_prepare_input($_POST['flag']);

if(isset($id) && $id != 0){

     if($flag == 1){ 
       $product_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME." where id>$id order by id asc limit 0,1");
     }elseif($flag == 0){
       $product_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where id<$id order by id desc limit 0,1"); 
     } 

     if($product_query){
       $product_array = tep_db_fetch_array($product_query);
       $sort_id = $product_array['id'];
     }

     tep_db_free_result($product_query);
     if(isset($flag) && $flag != ''){ 
       $id = $sort_id != '' ? $sort_id : $id;
     }
   
     $address_sort_query = tep_db_query("select max(id) maxsort,min(id) minsort from ". TABLE_PRODUCTS_SHIPPING_TIME);
     $address_sort_array = tep_db_fetch_array($address_sort_query);
     $maxsort = $address_sort_array['maxsort'];
     $minsort = $address_sort_array['minsort'];
     tep_db_free_result($address_sort_query);
   $product_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where id=$id");
   $product_array = tep_db_fetch_array($product_query);
   $cid = $address_array['id'];
   $name = $product_array['name'];
   $work = $product_array['work'];
   $sleep = $product_array['sleep'];
   $db_set_day = $product_array['db_set_day'];
   $shipping_time = $product_array['shipping_time'];
   $sort = $product_array['sort'];

   tep_db_free_result($product_query);
}

$sort = $sort == '' ? 0 : $sort;
?>
<form name="form" method="post" id="products_form" action="products_shipping_time.php">
<table border="0" width="100%" cellspacing="0" cellpadding="0" valign="top" bgcolor="yellow">

<?php
if($id == 0){
?>
 <tr><td bgcolor="#000000" class="dataTableHeadingContent" height="30"><?php echo tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO); ?>&nbsp;<?php echo TABLE_NEW.TABLE_TITLE_1;?></td><td bgcolor="#000000" align="right"><a href="javascript:hide_text();"><font color="#FFFFFF">X</font></a></td></tr>
<?php
}else{
  $prev_str = '';
  if($id > $minsort){

    $prev_str = '<a href="javascript:show_text_products('. $id .',\'\',0);"><font color="#FFFFFF">'. TABLE_PREV .'</font></a>';

  }
  $next_str = '';
  if($id < $maxsort){

    $next_str = '<a href="javascript:show_text_products('. $id .',\'\',1);"><font color="#FFFFFF">'. TABLE_NEXT .'</font></a>';

  }
?>
  <tr><td bgcolor="#000000" class="dataTableHeadingContent" height="30"><?php echo tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO); ?>&nbsp;<?php echo $name.TABLE_TITLE_1;?></td><td bgcolor="#000000" align="right" class="dataTableHeadingContent" onmouseover="this.style.cursor=\'hand\'"><?php echo $prev_str;?>&nbsp;<?php echo $next_str;?>&nbsp;<a href="javascript:hide_text();"><font color="#FFFFFF">X</font></a></td></tr>
<?php
}
?>
<tr><td>&nbsp;</td></tr>
<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_1;?></td><td><input type="text" name="name" id="name" value="<?php echo $name;?>"><span id="error_name"></span><input type="hidden" name="cid" value="<?php echo $product_array['id'];?>"></td></tr>
<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_2;?></td><td><input type="text" name="work" id="work" value="<?php echo $work;?>"></td></tr>
<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_3;?></td><td>
<input type="text" name="sleep" id="sleep" value="<?php echo $sleep;?>" style="text-align: right;">&nbsp;<?php echo TABLE_MIN;?>
</td></tr>

<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_4;?></td><td><input type="text" name="db_set_day" id="db_set_day" value="<?php echo $db_set_day;?>" style="text-align: right;">&nbsp;<?php echo TABLE_DAY;?>
</td></tr>


<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_5;?></td><td>
<input type="text" name="shipping_time" id="shipping_time" value="<?php echo $shipping_time;?>" style="text-align: right;">&nbsp;<?php echo TABLE_DAY;?>

</td></tr>

<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_6;?></td><td>
<input type="text" name="sort" id="sort" value="<?php echo $sort;?>" style="text-align: right;">
</td></tr>

</td></tr>
<tr><td width="30%" height="30" colspan="2" align="right">
<?php
if($id != 0){
?>
<input type="button" name="new" value="<?php echo TABLE_BUTTON_SUBMIT;?>" onclick="show_text_products(0,'');">
<?php
}
?>
&nbsp;<input type="button" name="save" value="<?php echo TABLE_BUTTON_SAVE;?>" onclick="if(check_form_products()){check_products('save');}else{return check_form_products();}">&nbsp;

<?php
if($id != 0){
?>
<input type="button" name="del" value="<?php echo TABLE_BUTTON_DEL;?>" onclick="if(confirm('このレコードを削除してもよろしいですか？')){check_products('del');}else{return false;}">
<?php
}else{
?>
<input type="button" name="new" value="<?php echo TABLE_BUTTON_UNSET;?>" onclick="hide_text();">
<?php
}
?>
&nbsp;</td></tr>
</table>

</form>

