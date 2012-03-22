<?php
/*
 * お届け時間指定グループの設定 Ajax
 */
require('includes/application_top.php');
$id = tep_db_prepare_input($_POST['id']);
$sort_sid = tep_db_prepare_input($_POST['sort']);
$flag = tep_db_prepare_input($_POST['flag']);

if(isset($id) && $id != 0){
  if(isset($sort_sid) && $sort_sid != ''){
     if($flag == 1){ 
       $product_sort_query = tep_db_query("select count(*) total,max(id) maxid from ". TABLE_PRODUCTS_SHIPPING_TIME ." where sort=$sort_sid");
       $product_sort_array = tep_db_fetch_array($product_sort_query);
       if($product_sort_array['total'] > 1){
         if($id < $product_sort_array['maxid']){
           $products_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where sort=$sort_sid and id>$id order by sort asc,id asc");
         }else{
           $products_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where sort>$sort_sid order by sort asc limit 0,1");
         }
       }else{
         $products_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where sort>$sort_sid order by sort asc limit 0,1");
       }
     }elseif($flag == 0){
       $product_sort_query = tep_db_query("select count(*) total,min(id) minid from ". TABLE_PRODUCTS_SHIPPING_TIME ." where sort=$sort_sid");
       $product_sort_array = tep_db_fetch_array($product_sort_query);
       if($product_sort_array['total'] > 1){
         if($id > $product_sort_array['minid']){
            
           $products_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where sort=$sort_sid and id<$id order by sort desc,id desc");
         }else{
           $products_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where sort<$sort_sid order by sort desc limit 0,1");  
         }
       }else{
         $products_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where sort<$sort_sid order by sort desc,id desc limit 0,1");
       }
     } 
  }

     if($products_query){
       $products_array = tep_db_fetch_array($products_query);
       $sort_id = $products_array['id'];
     }

     tep_db_free_result($products_query);
      
     $address_sort_query = tep_db_query("select max(sort) maxsort,min(sort) minsort from ". TABLE_PRODUCTS_SHIPPING_TIME);
     $address_sort_array = tep_db_fetch_array($address_sort_query);
     $maxsort = $address_sort_array['maxsort'];
     $minsort = $address_sort_array['minsort'];
     tep_db_free_result($address_sort_query);
     $address_sort_max_query = tep_db_query("select max(id) maxid from ". TABLE_PRODUCTS_SHIPPING_TIME ." where sort=$maxsort");
     $address_sort_max_array = tep_db_fetch_array($address_sort_max_query); 
     $maxid = $address_sort_max_array['maxid'];
     tep_db_free_result($address_sort_max_query);
     $address_sort_min_query = tep_db_query("select min(id) minid from ". TABLE_PRODUCTS_SHIPPING_TIME ." where sort=$minsort");
     $address_sort_min_array = tep_db_fetch_array($address_sort_min_query); 
     $minid = $address_sort_min_array['minid'];
     tep_db_free_result($address_sort_min_query);

     if(isset($flag) && $flag != ''){
       $id = $sort_id != '' ? $sort_id : $id;
     } 
       
          
   $product_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where id=$id");
   $product_array = tep_db_fetch_array($product_query);
   $cid = $address_array['id'];
   $name = $product_array['name'];
   $work = $product_array['work'];
   $db_set_day = $product_array['db_set_day'];
   $shipping_time = $product_array['shipping_time'];
   $sort = $product_array['sort'];
   
   $work_array = unserialize($work);
   tep_db_free_result($product_query);
}

$sort = $sort == '' ? 0 : $sort;
?>
<form name="form" method="post" id="products_form" action="products_shipping_time.php">
<table border="0" width="100%" cellspacing="0" cellpadding="0" valign="top" bgcolor="yellow">

<?php
if($id == 0 || $maxid == $minid){
?>
 <tr><td bgcolor="#000000" class="dataTableHeadingContent" height="30"><?php echo tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO); ?>&nbsp;<?php echo TABLE_NEW.TABLE_TITLE_1;?></td><td bgcolor="#000000" align="right"><a href="javascript:hide_text();"><font color="#FFFFFF">X</font></a></td></tr>
<?php
}else{
  $prev_str = '';
  $next_str = '';
  if($sort == $maxsort && $id == $maxid){

    $prev_str = '<a href="javascript:show_text_products('. $id .',\'\','. $sort .',0);"><font color="#FFFFFF">'. TABLE_PREV .'</font></a>';

  }elseif($sort == $minsort && $id == $minid){

    $next_str = '<a href="javascript:show_text_products('. $id .',\'\','. $sort .',1);"><font color="#FFFFFF">'. TABLE_NEXT .'</font></a>';

  }else{
    $prev_str = '<a href="javascript:show_text_products('. $id .',\'\','. $sort .',0);"><font color="#FFFFFF">'. TABLE_PREV .'</font></a>';
    $next_str = '<a href="javascript:show_text_products('. $id .',\'\','. $sort .',1);"><font color="#FFFFFF">'. TABLE_NEXT .'</font></a>';
  }
?>
  <tr><td bgcolor="#000000" class="dataTableHeadingContent" height="30"><?php echo tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO); ?>&nbsp;<?php echo $name.TABLE_TITLE_1;?></td><td bgcolor="#000000" align="right" class="dataTableHeadingContent" onmouseover="this.style.cursor=\'hand\'"><?php echo $prev_str;?>&nbsp;<?php echo $next_str;?>&nbsp;<a href="javascript:hide_text();"><font color="#FFFFFF">X</font></a></td></tr>
<?php
}
?>
<tr><td>&nbsp;</td></tr>
<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_1;?></td><td><input type="text" name="name" id="name" class="option_text" value="<?php echo $name;?>">&nbsp;<font color="red"><?php echo TABLE_REQUIRED;?></font><br><span id="error_name"></span><input type="hidden" name="cid" value="<?php echo $product_array['id'];?>"></td></tr>

<?php 
if(!empty($work_array)){
  foreach($work_array as $w_key=>$w_value){
    $w_start_array = explode(':',$w_value[0]);
    $w_end_array = explode(':',$w_value[1]);
    $w_start_hour = $w_start_array[0];
    $w_start_min = $w_start_array[1];
    $w_end_hour = $w_end_array[0];
    $w_end_min = $w_end_array[1];
    $w_num_str = $w_key+1 == 1 ? '' : $w_key+1;
    $button_add = $w_key == 0 ? '&nbsp;<input type="button" value="'. TABLE_ADD .'" onclick="work_add();"><input type="hidden" id="work_num" value="'. (count($work_array)+1).'">' : '&nbsp;<input type="button" value="'. TABLE_BUTTON_DEL .'" onclick="work_del('. ($w_key+1) .');">';
?>

  <tr id="workid<?php echo $w_key+1;?>"><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_2.$w_num_str;?></td><td><input type="text" name="work_start_hour[]" size="3" maxlength="2" value="<?php echo $w_start_hour;?>">&nbsp;:&nbsp;<input type="text" name="work_start_min[]" size="3" maxlength="2" value="<?php echo $w_start_min;?>">&nbsp;～&nbsp;<input type="text" name="work_end_hour[]" size="3" maxlength="2" value="<?php echo $w_end_hour;?>">&nbsp;:&nbsp;<input type="text" name="work_end_min[]" size="3" maxlength="2" value="<?php echo $w_end_min;?>"><?php echo $button_add;?><br><span id="work_error<?php echo ($w_key+1);?>"></span></td></tr>

<?php
  }
}else{
?>

<tr id="workid1"><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_2?></td><td><input type="text" name="work_start_hour[]" size="3" maxlength="2" value="">&nbsp;:&nbsp;<input type="text" name="work_start_min[]" size="3" maxlength="2" value="">&nbsp;～&nbsp;<input type="text" name="work_end_hour[]" size="3" maxlength="2" value="">&nbsp;:&nbsp;<input type="text" name="work_end_min[]" size="3" maxlength="2" value="">&nbsp;<input type="button" value="<?php echo TABLE_ADD;?>" onclick="work_add();"><input type="hidden" id="work_num" value="2"><br><span id="work_error1"></span></td></tr>

<?php 
}
?>
<tr><td colspan="2">
<table border="0" width="100%" cellspacing="0" cellpadding="0" valign="top" id="work_list">
</table>
</td></tr>

<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_4;?></td><td><input type="text" name="db_set_day" id="db_set_day" value="<?php echo $db_set_day;?>" style="text-align: right;">&nbsp;<?php echo TABLE_DAY;?>
</td></tr>


<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_5;?></td><td>
<input type="text" name="shipping_time" id="shipping_time" value="<?php echo $shipping_time;?>" style="text-align: right;">&nbsp;<?php echo TABLE_DAY;?>

</td></tr>

<?php
if($id == 0){

  $sort = 1000;
}
?>
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
&nbsp;<input type="button" name="save" value="<?php echo TABLE_BUTTON_SAVE;?>" onclick="if(check_form_products() && work_check()){check_products('save');}else{return check_form_products();}">&nbsp;

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

