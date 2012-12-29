<?php
/*
 * 设置交易时间指定组 Ajax
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
   $user_added = $product_array['user_added'];
   $date_added = $product_array['date_added'];
   $user_update = $product_array['user_update'];
   $date_update = $product_array['date_update'];
   
   $work_array = unserialize($work);
   tep_db_free_result($product_query);
}

$sort = $sort == '' ? 0 : $sort;
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="campaign_top">
<?php
if($id == 0 || $maxid == $minid){
?>
  <tr><td width="20"><?php echo tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO); ?></td><td><?php echo '<b>'.TABLE_NEW.TABLE_TITLE_1.'</b>';?></td><td align="right"><a href="javascript:hide_text();"><font color="#FFFFFF"><?php echo TEXT_CLOSE;?></font></a></td></tr>
<?php
}else{
  $prev_str = '';
  $next_str = '';
  if($sort == $maxsort && $id == $maxid){

    $prev_str = '<a href="javascript:show_text_products('. $id .',\'\','. $sort .',0);">'. TABLE_PREV .'</a>';

  }elseif($sort == $minsort && $id == $minid){

    $next_str = '<a href="javascript:show_text_products('. $id .',\'\','. $sort .',1);">'. TABLE_NEXT .'</a>';

  }else{
    $prev_str = '<a href="javascript:show_text_products('. $id .',\'\','. $sort .',0);">'. TABLE_PREV .'</a>';
    $next_str = '<a href="javascript:show_text_products('. $id .',\'\','. $sort .',1);">'. TABLE_NEXT .'</a>';
  }
?>
  <tr><td width="20"><?php echo tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO); ?></td><td><?php echo '<b>'.$name.TABLE_TITLE_1.'</b>';?></td><td align="right" onmouseover="this.style.cursor=\'hand\'"><?php echo $prev_str;?>&nbsp;<?php echo $next_str;?>&nbsp;<a href="javascript:hide_text();">X</a></td></tr>
<?php
}
?>
</table>
<form name="form" method="post" id="products_form" action="products_shipping_time.php">
<table border="0" width="100%" cellspacing="0" cellpadding="2" valign="top" bgcolor="yellow" class="campaign_body">
<tr><td width="30%" align="left"><?php echo TABLE_LIST_1;?></td><td><input type="text" name="name" id="name" class="option_text" value="<?php echo $name;?>">&nbsp;<font color="red"><?php echo TABLE_REQUIRED;?></font><br><span id="error_name"></span><input type="hidden" name="cid" value="<?php echo $product_array['id'];?>"></td></tr>

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
    $button_add = '&nbsp;<input type="button" value="'. TABLE_BUTTON_DEL .'" onclick="work_del('. ($w_key+1) .');">';
?>

  <tr id="workid<?php echo $w_key+1;?>"><td width="30%" align="left"><?php echo TABLE_LIST_2.$w_num_str;?></td><td><input type="text" name="work_start_hour[]" size="3" maxlength="2" value="<?php echo $w_start_hour;?>">&nbsp;:&nbsp;<input type="text" name="work_start_min[]" size="3" maxlength="2" value="<?php echo $w_start_min;?>">&nbsp;～&nbsp;<input type="text" name="work_end_hour[]" size="3" maxlength="2" value="<?php echo $w_end_hour;?>">&nbsp;:&nbsp;<input type="text" name="work_end_min[]" size="3" maxlength="2" value="<?php echo $w_end_min;?>"><?php echo $button_add;?><br><span id="work_error<?php echo ($w_key+1);?>"></span></td></tr>

<?php
  }
}else{
?>

  <tr id="workid1"><td width="30%" align="left"><?php echo TABLE_LIST_2?></td><td><input type="text" name="work_start_hour[]" size="3" maxlength="2" value="">&nbsp;:&nbsp;<input type="text" name="work_start_min[]" size="3" maxlength="2" value="">&nbsp;～&nbsp;<input type="text" name="work_end_hour[]" size="3" maxlength="2" value="">&nbsp;:&nbsp;<input type="text" name="work_end_min[]" size="3" maxlength="2" value="">&nbsp;<input type="button" value="<?php echo TABLE_BUTTON_DEL;?>" onclick="work_del(1);"><br><span id="work_error1"></span></td></tr>

<?php 
}

$work_num_value = !empty($work_array) ? count($work_array)+1 : 2;
?>
<tr><td colspan="2">
<table border="0" width="100%" cellspacing="0" cellpadding="2" valign="top" id="work_list">
</table>
</td></tr>
<tr><td width="30%" align="left"><input type="hidden" id="work_num" value="<?php echo $work_num_value;?>"></td><td><input type="button" value="<?php echo TABLE_ADD;?>" onclick="work_add();"></td></tr>

<tr><td width="30%" align="left"><?php echo TABLE_LIST_4;?></td><td><input type="text" name="db_set_day" id="db_set_day" value="<?php echo $db_set_day;?>" style="text-align: right;">&nbsp;<?php echo TABLE_DAY;?>
</td></tr>


<tr><td width="30%" align="left"><?php echo TABLE_LIST_5;?></td><td>
<input type="text" name="shipping_time" id="shipping_time" value="<?php echo $shipping_time;?>" style="text-align: right;">&nbsp;<?php echo TABLE_DAY_END;?>

</td></tr>

<?php
if($id == 0){

  $sort = 1000;
}
?>
<tr><td width="30%" align="left"><?php echo TABLE_LIST_6;?></td><td>
<input type="text" name="sort" id="sort" value="<?php echo $sort;?>" style="text-align: right;">
</td></tr>
<?php 
if($id != 0){
if(tep_not_null($user_added)){?>
<tr>
    <td width="30%" align="left"><?php echo TEXT_USER_ADDED; ?></td><td><?php echo $user_added;?></td>
</tr>
<?php }else{ ?>
<tr>
    <td width="30%" align="left"><?php echo TEXT_USER_ADDED; ?></td><td><?php echo TEXT_UNSET_DATA;?></td>
</tr>
<?php } if(tep_not_null(tep_datetime_short($date_added))){?>
<tr>
    <td width="30%" align="left"><?php echo TEXT_DATE_ADDED; ?></td><td><?php echo $date_added;?></td>
</tr>
<?php }else{ ?> 
<tr>
    <td width="30%" align="left"><?php echo TEXT_DATE_ADDED; ?></td><td><?php echo TEXT_UNSET_DATA;?></td>
</tr> 
<?php } if(tep_not_null($user_update)){?>
<tr>
    <td width="30%" align="left"><?php echo TEXT_USER_UPDATE; ?></td><td><?php echo  $user_update;?></td>
</tr>
<?php }else{ ?> 
<tr>
    <td width="30%" align="left"><?php echo TEXT_USER_UPDATE; ?></td><td><?php echo TEXT_UNSET_DATA;?></td>
</tr> 
<?php }if(tep_not_null(tep_datetime_short($date_update))){?>
<tr>
    <td width="30%" align="left"><?php echo TEXT_DATE_UPDATE; ?></td><td><?php echo $date_update;?></td>
</tr>
<?php }else{ ?>
<tr>
    <td width="30%" align="left"><?php echo TEXT_DATE_UPDATE; ?></td><td><?php echo TEXT_UNSET_DATA;?></td>
</tr>
<?php } } ?>
</td></tr>
<tr><td  colspan="2" align="center">
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
  <input type="button" name="del" value="<?php echo TABLE_BUTTON_DEL;?>" onclick="if(confirm('<?php echo TEXT_WANT_DELETE;?>')){check_products('del');}else{return false;}">
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

