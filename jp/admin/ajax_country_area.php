<?php
/*
 * 地域料金設定 Ajax
 */
require('includes/application_top.php');
$id = tep_db_prepare_input($_POST['id']);
$fid = tep_db_prepare_input($_POST['fid']);
$flag = tep_db_prepare_input($_POST['flag']);

if(isset($id) && $id != 0){

     if($flag == 1){ 
       $area_query = tep_db_query("select * from ". TABLE_AREA_FEE ." where fid=$fid and id>$id order by id asc limit 0,1");
     }elseif($flag == 0){
       $area_query = tep_db_query("select * from ". TABLE_AREA_FEE ." where fid=$fid and id<$id order by id desc limit 0,1"); 
     } 

     if($area_query){
       $area_array = tep_db_fetch_array($area_query);
       $sort_id = $area_array['id'];
     }

     tep_db_free_result($area_query);
     if(isset($flag) && $flag != ''){ 
       $id = $sort_id != '' ? $sort_id : $id;
     }

   $area_fee_query = tep_db_query("select * from ". TABLE_AREA_FEE ." where id=$id");
   $area_fee_array = tep_db_fetch_array($area_fee_query);
   $cid = $address_array['id'];
   $title = $area_fee_array['title'];
   $name = $area_fee_array['name'];
   $free_value = $area_fee_array['free_value'];
   $weight_fee = $area_fee_array['weight_fee'];
   $weight_limit = $area_fee_array['weight_limit'];
   $email_comment = $area_fee_array['email_comment'];
   $email_comment_1 = $area_fee_array['email_comment_1'];
   $area_date = $area_fee_array['date'];
   $area_sort = $area_fee_array['sort'];

   tep_db_free_result($area_fee_query);
}

$fid = $area_fee_array['fid'] !='' ? $area_fee_array['fid'] : $fid;
?>
<form name="form" method="post" id="country_area_form" action="country_area.php">
<table border="0" width="100%" cellspacing="0" cellpadding="0" valign="top" bgcolor="yellow">

<?php
if($id == 0){
?>
 <tr><td bgcolor="#000000" class="dataTableHeadingContent">&nbsp;&nbsp;<?php echo TABLE_NEW.TABLE_TITLE_1;?></td><td bgcolor="#000000" align="right"><a href="javascript:hide_text();"><font color="#FFFFFF">X</font></a></td></tr>
<?php
}else{
?>
  <tr><td bgcolor="#000000" class="dataTableHeadingContent">&nbsp;&nbsp;<?php echo $title.TABLE_TITLE_1;?></td><td bgcolor="#000000" align="right" class="dataTableHeadingContent" onmouseover="this.style.cursor=\'hand\'"><a href="javascript:show_text_area(<?php echo $id;?>,'',<?php echo $fid;?>,0);"><font color="#FFFFFF"><?php echo TABLE_PREV;?></font></a>&nbsp;<a href="javascript:show_text_area(<?php echo $id;?>,'',<?php echo $fid;?>,1);"><font color="#FFFFFF"><?php echo TABLE_NEXT;?></font></a>&nbsp;<a href="javascript:hide_text();"><font color="#FFFFFF">X</font></a></td></tr>
<?php
}
?>
<tr><td>&nbsp;</td></tr>
<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_1;?></td><td><input type="text" name="title" id="title" value="<?php echo $title;?>"><span id="error_title"><font color="red">*</font></span><input type="hidden" name="cid" value="<?php echo $area_fee_array['id'];?>"><input type="hidden" name="fid" value="<?php echo $fid;?>"></td></tr>
<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_2;?></td><td><input type="text" name="name" id="name" value="<?php echo $name;?>"><span id="error_name"><font color="red">*</font></span></td></tr>
<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_3;?></td><td>
<input type="text" name="free_value" id="free_value" value="<?php echo $free_value;?>">&nbsp;<?php echo TABLE_UNIT;?>
</td></tr>

<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_4;?></td><td>
<?php
// SHIPPING_BOX_WEIGHT_LIST
// 重量/料金
$weight_fee_array = explode(',',SHIPPING_BOX_WEIGHT_LIST);

$weight_fee_option_array = unserialize($weight_fee);

foreach($weight_fee_array as $weight_fee_value){

  $kg = $weight_fee_value == end($weight_fee_array) ? 'KG' : '';

  echo '<tr><td width="30%" height="30" align="left">&nbsp;'. $weight_fee_value. $kg .'</td><td>
<input type="hidden" name="weight_fee_name[]" value="'. $weight_fee_value .'"><input type="text" name="weight_fee[]" value="'. $weight_fee_option_array[$weight_fee_value].'">&nbsp;'. TABLE_UNIT .'</td></tr>';

}
?>

<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_5;?></td><td>
<input type="text" name="weight_limit" id="weight_limit" value="<?php echo $weight_limit;?>">&nbsp;KG
</td></tr>

<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_6;?></td><td>
<textarea name="email_comment" id="email_comment" rows="5" cols="30"><?php echo $email_comment; ?></textarea>
</td></tr>

<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_7;?></td><td><br />
<textarea name="email_comment_1" id="email_comment_1" rows="5" cols="30"><?php echo $email_comment_1; ?></textarea>
</td></tr>

<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_8;?></td><td>
<input type="text" name="date" id="date" value="<?php echo $area_date;?>">
</td></tr>

<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_9;?></td><td>
<input type="text" name="sort" id="sort" value="<?php echo $area_sort;?>">
</td></tr>



</td></tr>
<tr><td width="30%" height="30" colspan="2" align="right"><input type="button" name="new" value="<?php echo TABLE_BUTTON_SUBMIT;?>" onclick="show_text_area(0,'');">&nbsp;<input type="button" name="save" value="<?php echo TABLE_BUTTON_SAVE;?>" onclick="if(check_form()){check_area('save');}else{return check_form();}">&nbsp;

<?php
if($id != 0){
?>
<input type="button" name="del" value="<?php echo TABLE_BUTTON_DEL;?>" onclick="if(confirm('このレコードを削除してもよろしいですか？')){check_area('del');}else{return false;}">
<?php
}
?>
&nbsp;</td></tr>
</table>

</form>

