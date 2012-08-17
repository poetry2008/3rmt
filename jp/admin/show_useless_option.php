<?php
require('includes/application_top.php');
$action = $_GET['action'];
switch($action){
case 'del':
	$option_group_id=array();
 $option_group_id=$_POST['option_group_id'];
foreach($option_group_id as $id){
 $option_group_sql="delete  from ".TABLE_OPTION_GROUP." where id='".$id."'";
tep_db_query($option_group_sql);
 $option_item_sql="delete  from ".TABLE_OPTION_ITEM." where group_id='".$id."'";
tep_db_query($option_item_sql);
}
header("location:show_useless_option.php");
break;
}
//}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/jquery.autocomplete.css">
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language='javascript'>
  function get_action(action) {
	  var getform=document.getElementById('del_option') ;
	  getform.action="show_useless_option.php?action="+action;
	  getform.submit();
  }
function del_all(){
var get_id=document.getElementsByName('option_group_id[]');
for(i=0;i<get_id.length;i++){
get_id[i].checked=true;
}

}
function del(){
var get_id=document.getElementsByName('option_group_id[]');
var flag=false;
for(i=0;i<get_id.length;i++){
	if(get_id[i].checked==true){
	flag=true;	
	}
}
if(flag==false){
	alert('<?php echo SHOW_USELESS_OPTION_GROUP_ALERT;?>');
}else{
	if(confirm('<?php echo SHOW_USELESS_OPTION_GROUP_CONFIRM;?>')){
get_action('del');
}
}
}
function edit_text(id,pos,end){
$("tr.ajax_show_useless_option1").hide();
	$.ajax({
		url:'ajax_useless_option.php',
		data:{id:id,pos:pos,end:end},	
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






function close_group_info()
{
  $('#show_group_info').html(''); 
  $('#show_group_info').hide(); 
}
 function show_option_group(ele, id, pos , end)
{
  ele = ele.parentNode;
  $.ajax({
    url: 'ajax_useless_option.php',      
    data: {id:id,pos:pos,end:end},
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_group_info').html(data); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if
        (ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight+$('#show_group_info').height() > document.body.scrollHeight) {

          offset =
		  ele.offsetTop+ele.offsetHeight*7 ;
$('#show_group_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight*3;
          $('#show_group_info').css('top', offset).show(); 
        }
      } else {
        if
        (ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight+$('#show_group_info').height() > document.documentElement.clientHeight) {
offset =
		  ele.offsetTop+ele.offsetHeight*7 ;

          $('#show_group_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight*3;
          $('#show_group_info').css('top', offset).show(); 
        }
      }
      $('#show_group_info').show(); 
    }
  });
}	
function show_option_group_ajax( id, pos , end)
{
  $.ajax({
    url: 'ajax_useless_option.php',      
    data: {id:id,pos:pos,end:end},
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_group_info').html(data); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if
        (ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight+$('#show_group_info').height() > document.body.scrollHeight) {
          offset =
          ele.offsetTop+$('#group_list_box').position().top-$('#show_group_info').height()-$('#offsetHeight').height();
          $('#show_group_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight;
          $('#show_group_info').css('top', offset).show(); 
        }
      } else {
        if
        (ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight+$('#show_group_info').height() > document.documentElement.clientHeight) {
          offset = ele.offsetTop+$('#group_list_box').position().top-$('#show_group_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_group_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#group_list_box').position().top+ele.offsetHeight;
          $('#show_group_info').css('top', offset).show(); 
        }
      }
      $('#show_group_info').show(); 
    }
  });
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
    <td width="100%" valign="top"><table border="0" id="group_list_box" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table   border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_USELESS_OPTION_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
<div id="show_group_info" style="display:none"></div>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_GROUP_SELECT;?></td>
		<td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_GROUP_NAME;?></td>
		<td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_GROUP_TITLE;?> </td>
		<td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_GROUP_PREORDER;?></td>
		<td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_GROUP_SORT_NUM;?></td>
 
		 
		<td class="dataTableHeadingContent" align="right"><?php echo SHOW_USELESS_OPTION_GROUP_ACTION;?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
              </tr>

<form name="del_option" method="post" action="show_useless_option.php" id="del_option">
<?php
$use_id=array();
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


$option_group_sql="SELECT *  FROM ".TABLE_OPTION_GROUP." WHERE `id` NOT IN (".$not_in.") ORDER BY sort_num asc,`created_at` DESC";
$group_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $option_group_sql, $num_rows);
$option_group_query=tep_db_query($option_group_sql);
$now_num_row=tep_db_num_rows($option_group_query);
$class_check=0;
while($option_group_array=tep_db_fetch_array($option_group_query)){
  	$option_group_id=$option_group_array['id'];
	if(((!isset($_GET['group_id']) || !$_GET['group_id']) || ($_GET['group_id']==$option_group_id)) && (!isset($selected_item) || !$selected_item)){
$selected_item = $option_group_array;	
	}
	
	if((isset($selected_item) && is_array($selected_item)) && ($option_group_array['id'] == $selected_item['id'])){

echo "<tr class='dataTableRowSelected'>";
	}else{
	?>
<tr class="<?php echo $class_check%2==0 ?'dataTableSecondRow' : 'dataTableRow'?>" onmouseover="this.className='dataTableRowOver'"  onmouseout="this.className='<?php echo $class_check%2==0 ?'dataTableSecondRow' : 'dataTableRow'?>'" >
<?php	
}

?>
<td width="4%"><input type="checkbox" name="option_group_id[]" value="<?php echo $option_group_id;?>"></td>
<td onclick="document.location.href='<?php echo tep_href_link(FILENAME_SHOW_USELESS_OPTION, 'page='.$_GET['page'].'&group_id=' .  $option_group_array['id']);?>'"><a href="<?php echo tep_href_link(FILENAME_SHOW_USELESS_ITEM,'option_group_id='.$option_group_array['id']);?>"><?php echo tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER);?></a>&nbsp;&nbsp;<?php echo $option_group_array['name'];?></td>
<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_SHOW_USELESS_OPTION, 'page='.$_GET['page'].'&group_id=' .  $option_group_array['id']);?>'"><?php echo $option_group_array['title'];?></td>
<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_SHOW_USELESS_OPTION, 'page='.$_GET['page'].'&group_id=' .  $option_group_array['id']);?>'"><?php echo $option_group_array['is_preorder'] == 0 ? SHOW_USELESS_OPTION_GROUP_IS_NOT_PREORDER : SHOW_USELESS_OPTION_GROUP_IS_PREORDER;?></td>
<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_SHOW_USELESS_OPTION, 'page='.$_GET['page'].'&group_id=' .  $option_group_array['id']);?>'"><?php echo $option_group_array['sort_num'];?></td>
<td class="dataTableContent" align="right" ><a onclick="show_option_group(this,'<?php echo $option_group_id;?>','<?php echo $class_check;?>','<?php echo $now_num_row;?>')"><?php echo tep_image(DIR_WS_IMAGES.'icon_info.gif',IMAGE_ICON_INFO);?></a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>
<tr  style="display:none" class="ajax_show_useless_option1">
<td>
<div id="show_<?php echo $option_group_id;?>" class="show_ajax_useless_option"></div>
</td>
</tr>
<?php
$class_check++;
}

?>

</form>

</table>

</td></tr></table></td></tr>
<tr>
<td>
<table width="100%">
<tr>
<td class="smallText" valign="top"><?php echo $group_split->display_count($num_rows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
<td align="right" class="smallText">
<?php
echo $group_split->display_links($num_rows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'group_id')));

?>
&nbsp;&nbsp;&nbsp;
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table width="100%">
<tr><td align="right">
<button onclick="del();"><?php echo SHOW_USELESS_DEL;?></button>&nbsp;&nbsp;&nbsp;<button onclick="del_all();" ><?php echo SHOW_USELESS_ALL_DEL;?></button>
 </td></tr>
</table>
</td>
</tr>
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
