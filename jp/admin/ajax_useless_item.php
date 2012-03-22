<?php
require("includes/application_top.php");
$id=tep_db_prepare_input($_POST['id']);
$group_id=tep_db_prepare_input($_POST['group_id']);
$pos=$_POST['pos'];
$end=$_POST['end'];

$option_item_sql="select * from ".TABLE_OPTION_ITEM." where id='".$id."'";
$option_item_query=tep_db_query($option_item_sql);
$option_item_array=tep_db_fetch_array($option_item_query);
$option_item_option=unserialize($option_item_array['option']);


$group_to_item_query=tep_db_query("select id from ".TABLE_OPTION_ITEM." where group_id='".$group_id."' order by created_at desc");
while($item_sort_array=tep_db_fetch_array($group_to_item_query)){
$array_sort[]=$item_sort_array['id'];
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
<table style="font-size:14px;line-height:200%" border="0" width="100%" cellspacing="0" cellpadding="0" valign="top" bgcolor="yellow">
<tr bgcolor="#000000">
<td class="dataTableHeadingContent">
&nbsp;&nbsp;
<?php echo tep_image(DIR_WS_IMAGES.'icon_info.gif',IMAGE_ICON_INFO);?>
&nbsp;&nbsp;&nbsp;<?php echo $option_item_array['title']?>
</td>
<td align="right" class ="dataTableHeadingContent">
<?php 
if($end==1){
	?>
<a href="javascript:close_item_info();"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_ITEM_CLOSE;?></font></a>
&nbsp;

<?php
}
else if($pos==0){
	?>
<a href="javascript:show_option_item_ajax('<?php echo $next;?>','<?php echo $group_id;?>','<?php echo $pos+1;?>','<?php echo $end;?>');"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_ITEM_NEXT;?></font></a>
&nbsp;
<a href="javascript:close_item_info();"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_ITEM_CLOSE;?></font></a>
&nbsp;
<?php
}elseif($pos==($end-1)){
	?>
<a href="javascript:show_option_item_ajax('<?php echo $prev;?>','<?php echo $group_id;?>','<?php echo $pos-1;?>','<?php echo $end;?>');"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_ITEM_PREV;?></font></a>
&nbsp;
<a href="javascript:close_item_info();"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_ITEM_CLOSE;?></font></a>
&nbsp;
<?php
}else{
?>
<a href="javascript:show_option_item_ajax('<?php echo $prev;?>','<?php echo $group_id;?>','<?php echo $pos-1;?>','<?php echo $end;?>');"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_ITEM_PREV;?></font></a>
&nbsp;
<a href="javascript:show_option_item_ajax('<?php echo $next;?>','<?php echo $group_id;?>','<?php echo $pos+1;?>','<?php echo $end;?>');"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_ITEM_NEXT;?></font></a>
&nbsp;
<a href="javascript:close_item_info('<?php echo $id;?>');"><font color="#FFFFFF"><?php echo AJAX_USELESS_OPTION_ITEM_CLOSE;?></font></a>
&nbsp;
<?php 
}
?>
</td>
</tr>

<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo AJAX_USELESS_OPTION_ITEM_NAME;?>:
</td>
<td>
<?php echo $option_item_array['title']?>
</td>
</tr>

<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo AJAX_USELESS_OPTION_ITEM_TITLE;?>:
</td>
<td>
<?php echo $option_item_array['front_title']?>
</td>
</tr>

<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo AJAX_USELESS_OPTION_ITEM_TYPE;?>:
</td>
<td>
<?php 
if($option_item_array['type']=='select'){
echo AJAX_USELESS_OPTION_ITEM_TYPE_SELECT;
}elseif($option_item_array['type']=='text'){
echo AJAX_USELESS_OPTION_ITEM_TYPE_TEXT;
}elseif($option_item_array['type']=='textarea'){
echo AJAX_USELESS_OPTION_ITEM_TYPE_TEXTAREA;
}
?>
</td>
</tr>

<?php 
if($option_item_array['type']=='select'){

?>
<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo AJAX_USELESS_OPTION_ITEM_FIRST_SELECT;?>:
</td>
<td>

<?php 
if(empty($option_item_option['secomment'])){
echo $option_item_option['se_option'][0];
}else{
echo $option_item_option['secomment'];
}
?>
</td>
</tr>

<?php
}
for ($i=0;$i<count($option_item_option['se_option']);$i++){
?>
<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo AJAX_USELESS_OPTION_ITEM_SELECT;?>:
</td>
<td>
<?php echo $option_item_option['se_option'][$i]?>
</td>
</tr>
<?php
}
?>
<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo AJAX_USELESS_OPTION_ITEM_PRICE;?>:
</td>
<td>
<?php echo $option_item_array['price']?>
</td>
</tr>
<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo AJAX_USELESS_OPTION_ITEM_SORT_NUM;?>:
</td>
<td>
<?php echo $option_item_array['sort_num']?>
</td>
</tr>

<tr>
<td>
&nbsp;&nbsp;&nbsp;<?php echo AJAX_USELESS_OPTION_ITEM_TIME;?>:
</td>
<td>
<?php echo $option_item_array['created_at']?>
</td>
</tr>

</table>
