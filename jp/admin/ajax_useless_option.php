<?php
require("includes/application_top.php");
$id=tep_db_prepare_input($_POST['id']);
$pos=$_POST['pos'];
$end=$_POST['end'];




$option_group_sql="select * from ".TABLE_OPTION_GROUP." where id='".$id."'";
$option_group_query=tep_db_query($option_group_sql);
$option_group_array=tep_db_fetch_array($option_group_query);

$use_group_id=array();
$use_id_sql="select belong_to_option from ".TABLE_PRODUCTS;
$use_id_query=tep_db_query($use_id_sql);
while($use_id_array=tep_db_fetch_array($use_id_query)){
	if(!empty($use_id_array['belong_to_option'])){
$use_group_id[]=$use_id_array['belong_to_option'];	
	}
}
$not_in="";
foreach($use_group_id as $val){
$not_in.=$val.",";
}
$not_in=substr($not_in,0,-1);

$option_group_sqla="select * from ".TABLE_OPTION_GROUP." where id not in (".$not_in.") order by created_at desc";

$option_group_querya=tep_db_query($option_group_sqla);
while($option_group_arraya=tep_db_fetch_array($option_group_querya)){
$array_sort[]=$option_group_arraya['id'];
}

$minsort=$array_sort[0];
$maxsort=end($array_sort);
foreach($array_sort as $key=>$val){
	if($id==$array_sort[$key]){
$prev=$array_sort[$key-1]	;
$next=$array_sort[$key+1];
	}
}
if($prev==''){
$prev=$minsort;
}
if($next==''){
$next=$maxsort;
}
?>
<table class="campaign_top" width="100%" cellspacing="0" cellpadding="2" border="0">
<tr>
<td align="left" height="20">
&nbsp;&nbsp;
<?php echo tep_image(DIR_WS_IMAGES.'icon_info.gif',IMAGE_ICON_INFO);?>
&nbsp;&nbsp;&nbsp;<?php echo $option_group_array['name']?>
</td>
<td align="right">
<?php 
if($end==1){
	?>
<a href="javascript:close_group_info();"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_GROUP_CLOSE;?></font></a>
&nbsp;

<?php
}
else if($pos==0){
	?>
<a href="javascript:show_option_group_ajax('<?php echo $next;?>','<?php echo $pos+1;?>','<?php echo $end;?>');"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_GROUP_NEXT;?></font></a>
&nbsp;
<a href="javascript:close_group_info();"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_GROUP_CLOSE;?></font></a>
&nbsp;
<?php
}elseif($pos==($end-1)){
	?>
<a href="javascript:show_option_group_ajax('<?php echo $prev;?>','<?php echo $pos-1;?>','<?php echo $end;?>');"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_GROUP_PREV;?></font></a>
&nbsp;
<a href="javascript:close_group_info();"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_GROUP_CLOSE;?></font></a>
&nbsp;
<?php
}else{
?>
<a href="javascript:show_option_group_ajax('<?php echo $prev;?>','<?php echo $pos-1;?>','<?php echo $end;?>');"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_GROUP_PREV;?></font></a>
&nbsp;
<a href="javascript:show_option_group_ajax('<?php echo $next;?>','<?php echo $pos+1;?>','<?php echo $end;?>');"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_GROUP_NEXT;?></font></a>
&nbsp;
<a href="javascript:close_group_info();"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_GROUP_CLOSE;?></font></a>
&nbsp;
<?php 
}
?>
</td>
</tr>

</table>
<table  style="line-height:200%" class="campaign_body"   border="0" width="100%" cellspacing="0" cellpadding="0" valign="top" >

<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo AJAX_USELESS_OPTION_GROUP_NAME;?>:
</td>
<td>
<?php echo $option_group_array['name']?>
</td>
</tr>

<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo AJAX_USELESS_OPTION_GROUP_TITLE;?>:
</td>
<td>
<?php echo $option_group_array['title']?>
</td>
</tr>

<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo AJAX_USELESS_OPTION_GROUP_PREORDER;?>:
</td>
<td>
<?php 
if($option_group_array['is_preorder']==0){
	echo AJAX_USELESS_OPTION_GROUP_IS_NOT_PREORDER;

}else{
	echo AJAX_USELESS_OPTION_GROUP_IS_PREORDER;
}
?>
</td>
</tr>

<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo AJAX_USELESS_OPTION_GROUP_DESC;?>:
</td>
<td>

<?php 
if(empty($option_group_option['comment'])){
echo "空";
}else{
echo $option_group_option['comment'];
}
?>


</td>
</tr>

<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo AJAX_USELESS_OPTION_GROUP_SORT_NUM;?>:
</td>
<td>
<?php echo $option_group_array['sort_num']?>
</td>
</tr>

<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo TEXT_USER_ADDED;?>
</td>
<td>
<?php echo $option_group_array['user_added']?>
</td>
</tr>

<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo TEXT_DATE_ADDED;?>
</td>
<td>
<?php echo $option_group_array['created_at']?>
</td>
</tr>
<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo TEXT_USER_UPDATE;?>
</td>
<td>
<?php echo $option_group_array['user_update']?>
</td>
</tr>
<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo TEXT_DATE_UPDATE;?>
</td>
<td>
<?php echo $option_group_array['date_update']?>
</td>
</tr>

</table>
