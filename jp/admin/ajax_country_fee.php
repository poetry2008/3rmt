<?php
/*
 * 设置费用 Ajax
 */
require('includes/application_top.php');
$id = tep_db_prepare_input($_POST['id']);
$flag = tep_db_prepare_input($_POST['flag']);

if(isset($id) && $id != 0){
     
     if($flag == 1){ 
       $country_query = tep_db_query("select * from ". TABLE_COUNTRY_FEE ." where id>$id order by id asc limit 0,1");
     }elseif($flag == 0){
       $country_query = tep_db_query("select * from ". TABLE_COUNTRY_FEE ." where id<$id order by id desc limit 0,1"); 
     }

     if($country_query){
       $country_array = tep_db_fetch_array($country_query);
       $sort_id = $country_array['id'];
     }

     tep_db_free_result($country_query);
     if(isset($flag) && $flag != ''){ 
       $id = $sort_id != '' ? $sort_id : $id;
     }
   
     $address_sort_query = tep_db_query("select max(id) maxsort,min(id) minsort from ". TABLE_COUNTRY_FEE);
     $address_sort_array = tep_db_fetch_array($address_sort_query);
     $maxsort = $address_sort_array['maxsort'];
     $minsort = $address_sort_array['minsort'];
     tep_db_free_result($address_sort_query);
   $country_fee_query = tep_db_query("select * from ". TABLE_COUNTRY_FEE ." where id=$id");
   $country_fee_array = tep_db_fetch_array($country_fee_query);
   $cid = $address_array['id'];
   $title = $country_fee_array['title'];
   $name = $country_fee_array['name'];
   $free_value = $country_fee_array['free_value'];
   $weight_fee = $country_fee_array['weight_fee'];
   $weight_limit = $country_fee_array['weight_limit'];
   $email_comment = $country_fee_array['email_comment'];
   $email_comment_1 = $country_fee_array['email_comment_1'];

   tep_db_free_result($country_fee_query);
}


?>
<table border="0" width="100%" cellspacing="0" cellpadding="2" valign="top" class="campaign_top">
<?php
if($id == 0){
?>
  <tr><td><?php echo tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO); ?>&nbsp;<?php echo TABLE_NEW.TABLE_TITLE_1;?></td><td align="right"><a href="javascript:hide_text();"><font color="#FFFFFF"><?php echo TEXT_CLOSE;?></font></a></td></tr>
<?php
}else{
  $prev_str = '';
  if($id > $minsort){

    $prev_str = '<a href="javascript:show_text_fee('. $id .',\'\',0);"><font color="#FFFFFF">'. TABLE_PREV .'</font></a>';

  }
  $next_str = '';
  if($id < $maxsort){

    $next_str = '<a href="javascript:show_text_fee('. $id .',\'\',1);"><font color="#FFFFFF">'. TABLE_NEXT .'</font></a>';

  }
?>
  <tr><td><?php echo tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO); ?>&nbsp;<?php echo $title.TABLE_TITLE_1;?></td><td align="right" onmouseover="this.style.cursor=\'hand\'"><?php echo $prev_str;?>&nbsp;<?php echo $next_str;?>&nbsp;<a href="javascript:hide_text();"><font color="#FFFFFF">X</font></a></td></tr>
<?php
}
?>
</table>
<form name="country_fee_form" method="post" id="country_fee_form" action="country_fee.php">
<table border="0" width="100%" cellspacing="0" cellpadding="2" valign="top" bgcolor="yellow" class="campaign_body">
<tr><td width="30%">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_1;?></td><td><input type="text" name="title" id="title" class="option_text" value="<?php echo $title;?>">&nbsp;<font color="red"><?php echo TABLE_REQUIRED;?></font><br><span id="error_title"></span><input type="hidden" name="cid" value="<?php echo $country_fee_array['id'];?>"></td></tr>
<tr><td width="30%">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_2;?></td><td><input type="text" name="name" id="name" class="option_text" value="<?php echo $name;?>">&nbsp;<font color="red"><?php echo TABLE_REQUIRED;?></font><br><span id="error_name"></span></td></tr>
<tr><td width="30%">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_3;?></td><td>
<input type="text" name="free_value" id="free_value" value="<?php echo $free_value;?>" style="text-align: right;">&nbsp;<?php echo TABLE_UNIT;?>
<br><?php echo TABLE_PROMPT_1;?></td></tr>

<tr><td width="30%">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_4;?></td><td>
<?php
// SHIPPING_BOX_WEIGHT_LIST
// 重量/费用
$weight_fee_array = explode(',',SHIPPING_BOX_WEIGHT_LIST);

$weight_fee_option_array = unserialize($weight_fee);

foreach($weight_fee_array as $weight_fee_value){

  $kg = $weight_fee_value == end($weight_fee_array) ? 'KG' : '';

  echo '<tr><td width="30%">&nbsp;&nbsp;&nbsp;&nbsp;'. $weight_fee_value. $kg .'</td><td>
<input type="hidden" name="weight_fee_name[]" value="'. $weight_fee_value .'"><input type="text" name="weight_fee[]" value="'. $weight_fee_option_array[$weight_fee_value].'" style="text-align: right;">&nbsp;'. TABLE_UNIT .'</td></tr>';

}
?>

<tr><td width="30%">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_5;?></td><td>
<input type="text" name="weight_limit" id="weight_limit" value="<?php echo $weight_limit;?>" style="text-align: right;">&nbsp;KG
<br><?php echo TABLE_PROMPT_2;?></td></tr>

<tr><td width="30%" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_6;?></td><td>
<textarea name="email_comment" id="email_comment" rows="5" cols="30" class="option_text"><?php echo $email_comment; ?></textarea>
<br><?php echo TABLE_PROMPT_3;?></td></tr>

<tr><td width="30%" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_7;?></td><td>
<textarea name="email_comment_1" id="email_comment_1" rows="5" cols="30" class="option_text"><?php echo $email_comment_1; ?></textarea>
<br><?php echo TABLE_PROMPT_4;?></td></tr>

<?php 
if(!empty($country_fee_array['id'])){
if(tep_not_null($country_fee_array['user_added'])){ ?>
<tr><td width="30%" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_USER_ADDED;?></td><td><?php echo $country_fee_array['user_added'];?></td></tr>
<?php }else{ ?>
<tr><td width="30%" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_USER_ADDED;?></td><td><?php echo TEXT_UNSET_DATA;?></td></tr>
<?php } if(tep_not_null(tep_datetime_short($country_fee_array['date_added']))){ ?>
<tr><td width="30%" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_DATE_ADDED;?></td><td><?php echo $country_fee_array['date_added'];?></td></tr>
<?php }else{ ?> 
<tr><td width="30%" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_DATE_ADDED;?></td><td><?php echo TEXT_UNSET_DATA;?></td></tr>
<?php }if(tep_not_null($country_fee_array['user_update'])){ ?>
<tr><td width="30%" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_USER_UPDATE;?></td><td><?php echo $country_fee_array['user_update'];?></td></tr>
<?php } else { ?>
<tr><td width="30%" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_USER_UPDATE;?></td><td><?php echo TEXT_UNSET_DATA;?></td></tr>
<?php } if(tep_not_null(tep_datetime_short($country_fee_array['date_update']))){ ?>
<tr><td width="30%" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_DATE_UPDATE;?></td><td><?php echo $country_fee_array['date_update'];?></td></tr>
<?php }else{ ?>
<tr><td width="30%" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TEXT_DATE_UPDATE;?></td><td><?php echo TEXT_UNSET_DATA;?></td></tr>
<?php } } ?>
</td></tr>
<tr><td  align="center" colspan="2"><input type="button" name="new" value="<?php echo TABLE_BUTTON_SUBMIT;?>" onclick="show_text_fee(0,'');">&nbsp;<input type="button" name="save" value="<?php echo TABLE_BUTTON_SAVE;?>" onclick="if(check_form()){check_fee('save');}else{return check_form();}">&nbsp;

<?php
if($id != 0){
?>
  <input type="button" name="del" value="<?php echo TABLE_BUTTON_DEL;?>" onclick="if(confirm('<?php echo TEXT_WANT_DELETE;?>')){check_fee('del');}else{return false;}">
<?php
}
?>
&nbsp;</td></tr>
</table>

</form>

