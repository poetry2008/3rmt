<?php
/*
 * 地域料金設定 Ajax
 */
require('includes/application_top.php');
$id = tep_db_prepare_input($_POST['id']);
$sort = tep_db_prepare_input($_POST['sort']);
$fid = tep_db_prepare_input($_POST['fid']);
$flag = tep_db_prepare_input($_POST['flag']);

if(isset($id) && $id != 0){
  if(isset($sort) && $sort != ''){
     if($flag == 1){ 
       $area_sort_query = tep_db_query("select count(*) total,max(id) maxid from ". TABLE_AREA_FEE ." where fid=$fid and sort=$sort");
       $area_sort_array = tep_db_fetch_array($area_sort_query);
       if($area_sort_array['total'] > 1){
         if($id < $area_sort_array['maxid']){
           $area_query = tep_db_query("select * from ". TABLE_AREA_FEE ." where fid=$fid and sort=$sort and id>$id order by sort asc,id asc");
         }else{
           $area_query = tep_db_query("select * from ". TABLE_AREA_FEE ." where fid=$fid and sort>$sort order by sort asc limit 0,1");
         }
       }else{
         $area_query = tep_db_query("select * from ". TABLE_AREA_FEE ." where fid=$fid and sort>$sort order by sort asc limit 0,1");
       }
     }elseif($flag == 0){
       $area_sort_query = tep_db_query("select count(*) total,min(id) minid from ". TABLE_AREA_FEE ." where fid=$fid and sort=$sort");
       $area_sort_array = tep_db_fetch_array($area_sort_query);
       if($area_sort_array['total'] > 1){
         if($id > $area_sort_array['minid']){
            
           $area_query = tep_db_query("select * from ". TABLE_AREA_FEE ." where fid=$fid and sort=$sort and id<$id order by sort desc,id desc");
         }else{
           $area_query = tep_db_query("select * from ". TABLE_AREA_FEE ." where fid=$fid and sort<$sort order by sort desc limit 0,1");  
         }
       }else{
         $area_query = tep_db_query("select * from ". TABLE_AREA_FEE ." where fid=$fid and sort<$sort order by sort desc,id desc limit 0,1");
       }
     }
  } 
     if($area_query){
       $area_array = tep_db_fetch_array($area_query);
       $sort_id = $area_array['id'];
     }

     tep_db_free_result($area_query);
     if(isset($flag) && $flag != ''){ 
       $id = $sort_id != '' ? $sort_id : $id;
     }
   
     $address_sort_query = tep_db_query("select max(sort) maxsort,min(sort) minsort from ". TABLE_AREA_FEE ." where fid=$fid");
     $address_sort_array = tep_db_fetch_array($address_sort_query);
     $maxsort = $address_sort_array['maxsort'];
     $minsort = $address_sort_array['minsort'];
     tep_db_free_result($address_sort_query);
     $address_sort_max_query = tep_db_query("select max(id) maxid from ". TABLE_AREA_FEE ." where fid=$fid and sort=$maxsort");
     $address_sort_max_array = tep_db_fetch_array($address_sort_max_query); 
     $maxid = $address_sort_max_array['maxid'];
     tep_db_free_result($address_sort_max_query);
     $address_sort_min_query = tep_db_query("select min(id) minid from ". TABLE_AREA_FEE ." where fid=$fid and sort=$minsort");
     $address_sort_min_array = tep_db_fetch_array($address_sort_min_query); 
     $minid = $address_sort_min_array['minid'];
     tep_db_free_result($address_sort_min_query);     


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

$area_sort = $area_sort == '' ? 0 : $area_sort;
$fid = $area_fee_array['fid'] !='' ? $area_fee_array['fid'] : $fid;
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0" valign="top">
<?php
if($id == 0 || $maxid == $minid){
?>
 <tr><td bgcolor="#000000" class="dataTableHeadingContent" height="35"><?php echo tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO); ?>&nbsp;<?php echo TABLE_NEW.TABLE_TITLE_1;?></td><td bgcolor="#000000" align="right"><a href="javascript:hide_text();"><font color="#FFFFFF">X</font></a></td></tr>
<?php
}else{
  $prev_str = '';
  $next_str = '';
  if($area_sort == $maxsort && $id == $maxid){

    $prev_str = '<a href="javascript:show_text_area('. $id .',\'\','. $fid .','. $area_sort .',0);"><font color="#FFFFFF">'. TABLE_PREV .'</font></a>';

  }elseif($area_sort == $minsort && $id == $minid){

    $next_str = '<a href="javascript:show_text_area('. $id .',\'\','. $fid .','. $area_sort .',1);"><font color="#FFFFFF">'. TABLE_NEXT .'</font></a>';

  }else{

    $prev_str = '<a href="javascript:show_text_area('. $id .',\'\','. $fid .','. $area_sort .',0);"><font color="#FFFFFF">'. TABLE_PREV .'</font></a>';
    $next_str = '<a href="javascript:show_text_area('. $id .',\'\','. $fid .','. $area_sort .',1);"><font color="#FFFFFF">'. TABLE_NEXT .'</font></a>';
  }
?>
  <tr><td bgcolor="#000000" class="dataTableHeadingContent" height="35"><?php echo tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO); ?>&nbsp;<?php echo $title.TABLE_TITLE_1;?></td><td bgcolor="#000000" align="right" class="dataTableHeadingContent" onmouseover="this.style.cursor=\'hand\'"><?php echo $prev_str;?>&nbsp;<?php echo $next_str;?>&nbsp;<a href="javascript:hide_text();"><font color="#FFFFFF">X</font></a></td></tr>
<?php
}
?>
</table>
<form name="form" method="post" id="country_area_form" action="country_area.php">
<table border="0" width="100%" cellspacing="0" cellpadding="0" valign="top" bgcolor="yellow">
<tr><td>&nbsp;</td></tr>
<tr><td width="30%" height="30" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_1;?></td><td><input type="text" name="title" id="title" class="option_text" value="<?php echo $title;?>">&nbsp;<font color="red"><?php echo TABLE_REQUIRED;?></font><br><span id="error_title"></span><input type="hidden" name="cid" value="<?php echo $area_fee_array['id'];?>"><input type="hidden" name="fid" value="<?php echo $fid;?>"></td></tr>
<tr><td width="30%" height="30" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_2;?></td><td><input type="text" name="name" id="name" class="option_text" value="<?php echo $name;?>">&nbsp;<font color="red"><?php echo TABLE_REQUIRED;?></font><br><span id="error_name"></span></td></tr>
<tr><td width="30%" height="30" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_3;?></td><td>
<input type="text" name="free_value" id="free_value" value="<?php echo $free_value;?>" style="text-align: right;">&nbsp;<?php echo TABLE_UNIT;?>
<br><?php echo TABLE_PROMPT_1;?></td></tr>

<tr><td width="30%" height="30" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_4;?></td><td>
<?php
// SHIPPING_BOX_WEIGHT_LIST
// 重量/料金
$weight_fee_array = explode(',',SHIPPING_BOX_WEIGHT_LIST);

$weight_fee_option_array = unserialize($weight_fee);

foreach($weight_fee_array as $weight_fee_value){

  $kg = $weight_fee_value == end($weight_fee_array) ? 'KG' : '';

  echo '<tr><td width="30%" height="30" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. $weight_fee_value. $kg .'</td><td>
<input type="hidden" name="weight_fee_name[]" value="'. $weight_fee_value .'"><input type="text" name="weight_fee[]" value="'. $weight_fee_option_array[$weight_fee_value].'" style="text-align: right;">&nbsp;'. TABLE_UNIT .'</td></tr>';

}
?>

<tr><td width="30%" height="30" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_5;?></td><td>
<input type="text" name="weight_limit" id="weight_limit" value="<?php echo $weight_limit;?>" style="text-align: right;">&nbsp;KG
<br><?php echo TABLE_PROMPT_2;?></td></tr>

<tr><td width="30%" height="30" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_6;?></td><td>
<textarea name="email_comment" id="email_comment" rows="5" cols="30" class="option_text"><?php echo $email_comment; ?></textarea>
<br><?php echo TABLE_PROMPT_3;?></td></tr>

<tr><td width="30%" height="30" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_7;?></td><td><br />
<textarea name="email_comment_1" id="email_comment_1" rows="5" cols="30" class="option_text"><?php echo $email_comment_1; ?></textarea>
<br><?php echo TABLE_PROMPT_4;?></td></tr>

<tr><td width="30%" height="30" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_8;?></td><td>
<input type="text" name="date" id="date" value="<?php echo $area_date;?>" style="text-align: right;">
</td></tr>

<?php
if($id == 0){

  $area_sort = 1000;
}
?>
<tr><td width="30%" height="30" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_9;?></td><td>
<input type="text" name="sort" id="sort" value="<?php echo $area_sort;?>" style="text-align: right;">
</td></tr>



</td></tr>
<tr><td width="30%" height="30"></td><td style="padding-left:20%;">
<?php 
if($id != 0){
?>
<input type="button" name="new" value="<?php echo TABLE_BUTTON_SUBMIT;?>" onclick="show_text_area(0,'');">
<?php
}
?>
&nbsp;<input type="button" name="save" value="<?php echo TABLE_BUTTON_SAVE;?>" onclick="if(check_form()){check_area('save');}else{return check_form();}">&nbsp;
<?php
if($id != 0){
?>
<input type="button" name="del" value="<?php echo TABLE_BUTTON_DEL;?>" onclick="if(confirm('このレコードを削除してもよろしいですか？')){check_area('del');}else{return false;}">
<?php
}else{
?>
<input type="button" name="unset" value="<?php echo TABLE_BUTTON_UNSET;?>" onclick="hide_text();">
<?php
}
?>
&nbsp;</td></tr>
</table>

</form>

