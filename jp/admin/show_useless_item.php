<?php
/*
 *未使用オプション削除页面 
 */
require('includes/application_top.php');
$action = $_GET['action'];
$option_group_id=$_GET['option_group_id'];

switch($action){
case 'del':
	$option_group_id=$_GET['option_group_id'];
	$item_id=array();
 $item_id=$_POST['option_item_id'];
foreach($item_id as $id){
$item_sql="delete  from ".TABLE_OPTION_ITEM." where id='".$id."'";
tep_db_query($item_sql);
}
header("location:show_useless_item.php?option_group_id=$option_group_id");
break;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<style type="text/css">
.show_ajax_useless_item {
  width:660px;
  height:400px;
  position:absolute;
  /**
   * filter:alpha(opacity=0);
   * opacity:0.6;
   * **/
margin-top:-20px;
margin-left:200px;
/*background:#ffffff;*/
border:0px #FFF solid;
padding:20px;
font-size:14px;
line-height:170%;
}
.show_ajax_useless_item1{} 
</style>

<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language='javascript'>
  function get_action(action,option_group_id) {
	  var getform=document.getElementById('del_item') ;
	  getform.action="show_useless_item.php?action="+action+"&option_group_id="+option_group_id;
	  getform.submit();
  }
function del_all(){
	if(confirm("全部削除しますか？")){
var get_id=document.getElementsByName('option_item_id[]');
for(i=0;i<get_id.length;i++){
get_id[i].checked=true;
}
}
}
function edit_text(id){
$("tr.ajax_show_useless_option1").hide();
	$.ajax({
		url:'ajax_useless_item.php',
		data:{id:id},	
		type:'POST',
		dataType:'text',
		async:false,
		success:function(data){
			$("div#show_"+id).html(data);
			$("div#show_"+id).parent().parent().show();
		}
	});
}
function hide_text(id){
$("div#show_"+id).parent().parent().hide();
}
  </script>

</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft"><tr><td>
<!-- left_navigation //--> <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> <!-- left_navigation_eof //-->
    </td></tr></table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo SHOW_USELESS_ITEM_HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">

	      <td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_ITEM_NAME;?></td>
	      <td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_ITEM_TITLE;?> </td>
	      <td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_ITEM_TYPE;?></td>
	      <td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_ITEM_REQUIRE;?></td>
	      <td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_ITEM_CONTENT;?></td>
	      <td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_ITEM_PRICE;?> </td>
	      <td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_ITEM_SORT_NUM;?></td>
	      <td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_ITEM_STATUS;?> </td>
	      <td class="dataTableHeadingContent" align="right"><?php echo SHOW_USELESS_OPTION_ITEM_ACTION;?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
              </tr>
<form name="del_item" method="post" action="show_useless_item.php" id="del_item">
<?php
$use_id=array();
$use_id_sql="select belong_to_option from ".TABLE_PRODUCTS;
$use_id_query=tep_db_query($use_id_sql);
while($use_id_array=tep_db_fetch_array($use_id_query)){
	if(!empty($use_id_array['belong_to_option'])){
$use_group_id[]=$use_id_array['belong_to_option'];
	}
}
//print_r($use_group_id);exit;
$not_in="";
foreach($use_group_id as $val){
$not_in.=$val.",";
}
$not_in=substr($not_in,0,-1);
$option_item_sql="SELECT *  FROM ".TABLE_OPTION_ITEM." WHERE `group_id`=".$option_group_id."  ORDER BY `sort_num` ASC";


$option_page=$_GET['option_page'];
$per_page=15;
$first_page=1;
//$option_group_sql="SELECT *  FROM `option_group` WHERE `id` NOT IN (".$not_in.") ORDER BY `sort_num` ASC";
if (!isset($option_page) || !$option_page) {
    $option_page = 1;
  }

$prev_option_page = $option_page - 1;
$next_option_page = $option_page + 1;

$option_item_query=tep_db_query($option_item_sql);

  $option_page_start = ($per_page * $option_page) - $per_page;
  $num_rows = tep_db_num_rows($option_item_query);
if ($num_rows <= $per_page) {
     $num_pages = 1;
  } else if (($num_rows % $per_page) == 0) {
     $num_pages = ($num_rows / $per_page);
  } else {
     $num_pages = ($num_rows / $per_page) + 1;
  }
  $num_pages = (int) $num_pages;
$option_item_sql = $option_item_sql. " LIMIT $option_page_start, $per_page";
$num_now=tep_db_num_rows($option_item_query);


$option_item_query=tep_db_query($option_item_sql);
$num_now=tep_db_num_rows($option_item_query);

$class_check=0;
while($option_item_array=tep_db_fetch_array($option_item_query)){
	$option_item_id=$option_item_array['id']
?>

<tr class="<?php echo $class_check%2==0 ?'dataTableSecondRow' : 'dataTableRow'?>" onmouseover="this.className='dataTableRowOver'"  onmouseout="this.className='<?php echo $class_check%2==0 ?'dataTableSecondRow' : 'dataTableRow'?>'">
<td><input type="checkbox" name="option_item_id[]" value="<?php echo $option_item_id;?>"><?php echo $option_item_array['title'];?></td>
<td class="dataTableContent"><?php echo $option_item_array['front_title'];?></td>
<td class="dataTableContent"><?php 
		if($option_item_array['type']=='select'){
	echo SHOW_USELESS_OPTION_ITEM_TYPE_SELECT;	
		}elseif($option_item_array['type']=='text'){
	echo SHOW_USELESS_OPTION_ITEM_TYPE_TEXT;	
		}elseif($option_item_array['type']=='textarea'){
	echo SHOW_USELESS_OPTION_ITEM_TYPE_TEXTAREA;
		}
		?>

</td>
<td class="dataTableContent"><?php echo $option_item_array['status'];?></td>
<td class="dataTableContent"><?php echo $option_item_array['comment']?></td>
<td class="dataTableContent"><?php echo $option_item_array['price']?>円</td>
<td class="dataTableContent"><?php echo $option_item_array['sort_num']?></td>
<td class="dataTableContent"><?php echo $option_item_array['status']==0 ? SHOW_USELESS_OPTION_ITEM_STATUS_YES : SHOW_USELESS_OPTION_ITEM_STATUS_NO ;?></td>


<td class="dataTableContent" align="right" onclick="edit_text('<?php echo $option_item_array['id'];?>')"><?php echo tep_image(DIR_WS_IMAGES.'icon_info.gif',IMAGE_ICON_INFO);?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>
<tr  style="display:none" class="ajax_show_useless_item1">
<td>
<div id="show_<?php echo $option_item_id;?>" class="show_ajax_useless_item"></div>
</td>
</tr>
<?php
$class_check++;
}

?>

</form>
<tr><td class="smallText">&nbsp;&nbsp;&nbsp;
<?php
echo ($option_page_start+1)."∼".($option_page_start+$num_now)." 番目を表示 (".$num_rows." の顧客のうち)&nbsp;&nbsp;";
?>
</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>

<td align="right" class="smallText">
<?php
if ($prev_option_page) {
    echo '<a class="pageResults" href="' . tep_href_link('show_useless_option.php?option_page=' . $prev_option_page) . '">&lt;&lt;  </a>  ';
  }
for ($i =1; $i <=$num_pages; $i++) {
    if ($i != $option_page) {
      echo '<a  class="pageResults" href="' . tep_href_link('show_useless_option.php?option_page=' . $i) . '">' . $i . '</a>   ';
    } else {
      echo '<b><font color="red">' . $i . '</font></b>';
    }
  }

if ($option_page != $num_pages) {
    echo '<a href="' . tep_href_link('show_useless_option.php?option_page=' . $next_option_page) . '">&gt;&gt; </a>';
  }

?>
&nbsp;&nbsp;&nbsp;
</td></tr>
<tr>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td align="right">
<button onclick="get_action('del','<?php echo $option_group_id;?>')"><?php echo SHOW_USELESS_OPTION_ITEM_DEL_LINK;?></button>&nbsp;<button onclick="del_all();get_action('del','<?php echo $option_group_id;?>')" ><?php echo SHOW_USELESS_OPTION_ITEM_ALL_DEL_LINK;?></button>
</td></tr>

</table>

</td></tr></table></td></tr>
</table></td>
</tr>
</table></td>
<!-- body_text_eof //-->
</tr>

</table>

<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
