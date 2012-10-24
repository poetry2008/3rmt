<?php
/*
 *未使用オプション削除页面 
 */
require('includes/application_top.php');
require(DIR_WS_CLASSES.'currencies.php');
$currencies = new currencies(2);
$action = $_GET['action'];

switch($action){
case 'del':

	$item_id=array();
 $item_id=$_POST['option_item_id'];
foreach($item_id as $id){
$item_sql="delete  from ".TABLE_OPTION_ITEM." where id='".$id."'";
$option_group_id = $_POST['option_group_id'];
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

<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language='javascript'>
  function get_action(action,option_group_id) {
	  var getform=document.getElementById('del_item') ;
	  getform.action="show_useless_item.php?action="+action+"&option_group_id="+option_group_id;
	  getform.submit();
  }
function del_all(){
var get_id=document.getElementsByName('option_item_id[]');
for(i=0;i<get_id.length;i++){
get_id[i].checked=true;
}
}
function del(){
var get_id=document.getElementsByName('option_item_id[]');
var flag=false;
for(i=0;i<get_id.length;i++){
	if(get_id[i].checked==true){
	flag=true;	
	}
}
if(flag==false){
	alert('<?php echo SHOW_USELESS_OPTION_ITEM_ALERT;?>');
}else{
	if(confirm('<?php echo SHOW_USELESS_OPTION_ITEM_CONFIRM;?>')){
get_action('del');
}
}
}
function edit_text(id,group_id,pos,end){
$("tr.ajax_show_useless_item1").hide();
	$.ajax({
		url:'ajax_useless_item.php',
		data:{id:id,group_id:group_id,pos:pos,end:end},	
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

function close_item_info()
{
  $('#show_item_info').html(''); 
  $('#show_item_info').hide(); 
}

 function show_option_item(ele,id,group_id,pos , end)
{
  ele = ele.parentNode;
  $.ajax({
    url: 'ajax_useless_item.php',      
    data: {id:id,group_id:group_id,pos:pos,end:end},
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_item_info').html(data); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if
        (ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight+$('#show_item_info').height() > document.body.scrollHeight) {
          offset =
          ele.offsetTop+$('#item_list_box').position().top-$('#show_item_info').height()-$('#offsetHeight').height();
          $('#show_item_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight;
          $('#show_item_info').css('top', offset).show(); 
        }
      } else {
        if
        (ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight+$('#show_item_info').height() > document.documentElement.clientHeight) {
          offset = ele.offsetTop+$('#item_list_box').position().top-$('#show_item_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_item_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight;
          $('#show_item_info').css('top', offset).show(); 
        }
      }
      $('#show_item_info').show(); 
    }
  });
}	
function show_option_item_ajax( id,group_id, pos , end)
{
  $.ajax({
    url: 'ajax_useless_item.php',      
    data: {id:id,group_id:group_id,pos:pos,end:end},
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_item_info').html(data); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if
        (ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight+$('#show_item_info').height() > document.body.scrollHeight) {
          offset =
          ele.offsetTop+$('#item_list_box').position().top-$('#show_item_info').height()-$('#offsetHeight').height();
          $('#show_item_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight;
          $('#show_item_info').css('top', offset).show(); 
        }
      } else {
        if
        (ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight+$('#show_item_info').height() > document.documentElement.clientHeight) {
          offset = ele.offsetTop+$('#item_list_box').position().top-$('#show_item_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_item_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#item_list_box').position().top+ele.offsetHeight;
          $('#show_item_info').css('top', offset).show(); 
        }
      }
      $('#show_item_info').show(); 
    }
  });
}
  </script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/option_group_id=[^&]+/',$belong,$belong_array);
if($belong_array[0][0] != ''){

  $belong = $href_url.'?'.$belong_array[0][0];
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
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
    <td width="100%" valign="top"><?php echo $notes;?><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo SHOW_USELESS_ITEM_HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
<div id="show_item_info" style="display:none">
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table id="item_list_box" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent"><?php echo SHOW_USELESS_OPTION_ITEM_SELECTED;?></td>
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
$not_in="";
foreach($use_group_id as $val){
$not_in.=$val.",";
}
$not_in=substr($not_in,0,-1);
$option_group_id=$_GET['option_group_id'];
$option_item_sql="SELECT *  FROM ".TABLE_OPTION_ITEM." WHERE `group_id`=".$_GET['option_group_id']."  ORDER BY sort_num,title asc";

//$num_rows=tep_db_num_rows($option_item_query);

$item_split = new splitPageResults($_GET['page'],MAX_DISPLAY_SEARCH_RESULTS,$option_item_sql,$num_rows);
$option_item_query=tep_db_query($option_item_sql);
$now_num_row=tep_db_num_rows($option_item_query);
$class_check=0;
while($option_item_array=tep_db_fetch_array($option_item_query)){
	$option_item_id=$option_item_array['id'];
		if(((!isset($_GET['item_id']) || !$_GET['item_id']) || ($_GET['item_id'] == $option_item_id)) && (!isset($selected) || !$selected)){
	$selected= $option_item_array;	
		}
	if((isset($selected) && is_array($selected)) && ($option_item_array['id'] == $selected['id'])){
echo "<tr class='dataTableRowSelected'>";
	}else{
?>

<tr class="<?php echo $class_check%2==0 ?'dataTableSecondRow' : 'dataTableRow'?>" onmouseover="this.className='dataTableRowOver'"  onmouseout="this.className='<?php echo $class_check%2==0 ?'dataTableSecondRow' : 'dataTableRow'?>'">
<?php 
	}
?>
<td><input type="checkbox" name="option_item_id[]" value="<?php echo $option_item_id;?>">
<input type='hidden' name='option_group_id' value="<?php echo $_GET['option_group_id']?>">
</td>
<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_SHOW_USELESS_ITEM, 'option_group_id='.$option_group_id.'&page='.$_GET['page'].'&item_id='.$option_item_array['id']);?>'"><?php echo $option_item_array['title'];?></td>
<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_SHOW_USELESS_ITEM, 'option_group_id='.$option_group_id.'&page='.$_GET['page'].'&item_id='.$option_item_array['id']);?>'"><?php echo $option_item_array['front_title'];?></td>
<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_SHOW_USELESS_ITEM, 'option_group_id='.$option_group_id.'&page='.$_GET['page'].'&item_id='.$option_item_array['id']);?>'"><?php 
		if($option_item_array['type']=='select'){
	echo SHOW_USELESS_OPTION_ITEM_TYPE_SELECT;	
		}elseif($option_item_array['type']=='text'){
	echo SHOW_USELESS_OPTION_ITEM_TYPE_TEXT;	
		}elseif($option_item_array['type']=='textarea'){
	echo SHOW_USELESS_OPTION_ITEM_TYPE_TEXTAREA;
		}
		?>

</td>
<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_SHOW_USELESS_ITEM, 'option_group_id='.$option_group_id.'&page='.$_GET['page'].'&item_id='.$option_item_array['id']);?>'"><?php 
$option_item_option = @unserialize($option_item_array['option']);
if(isset($option_item_option['require']) && $option_item_option['require']==1)	{
echo SHOW_USELESS_OPTION_ITEM_IS_REQUIRE;
}else{
echo SHOW_USELESS_OPTION_ITEM_IS_NOT_REQUIRE;
}


?></td>
<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_SHOW_USELESS_ITEM, 'option_group_id='.$option_group_id.'&page='.$_GET['page'].'&item_id='.$option_item_array['id']);?>'"><?php 
if(isset($option_item_option['itext'])){
echo $option_item_option['itext'];
} elseif(isset($option_item_option['itextarea'])){
echo $option_item_option['itextarea'];
} elseif(isset($option_item_opton['se_option'])) {
	if(is_array($option_item_option['se_option'])){
		if(!empty($option_item_option['se_option']))	{
			foreach($option_item_option['se_option'] as $key=>$val)	{
		echo $val.'&nbsp;';
			}
		}
	}
}

?>
</td>
<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_SHOW_USELESS_ITEM, 'option_group_id='.$option_group_id.'&page='.$_GET['page'].'&item_id='.$option_item_array['id']);?>'"><?php echo $currencies->format($option_item_array['price']);?></td>
<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_SHOW_USELESS_ITEM, 'option_group_id='.$option_group_id.'&page='.$_GET['page'].'&item_id='.$option_item_array['id']);?>'"><?php echo $option_item_array['sort_num']?></td>
<td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_SHOW_USELESS_ITEM, 'option_group_id='.$option_group_id.'&page='.$_GET['page'].'&item_id='.$option_item_array['id']);?>'"><?php echo $option_item_array['status']==0 ? SHOW_USELESS_OPTION_ITEM_STATUS_NO : SHOW_USELESS_OPTION_ITEM_STATUS_YES ;?></td>


<td class="dataTableContent" align="right" onclick="show_option_item(this,'<?php echo $option_item_array['id'];?>','<?php echo $option_group_id;?>','<?php echo $class_check;?>','<?php echo $now_num_row;?>')"><?php echo tep_image(DIR_WS_IMAGES.'icon_info.gif',IMAGE_ICON_INFO);?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
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

</table>
<tr>
<td>
<table width="100%">
<tr>
<td class="smallText">&nbsp;&nbsp;&nbsp;
<?php echo $item_split->display_count($num_rows, MAX_DISPLAY_SEARCH_RESULTS,
    $_GET['page'], TEXT_DISPLAY_NUMBER_OF_USELESS_ITEM);?>
</td>
<td align="right" class="smallText">
<?php
echo $item_split->display_links($num_rows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'item_id')));

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
<tr>


</tr>
</table>
</td>
</tr>
<td align="right">
<button onclick="del();"><?php echo SHOW_USELESS_OPTION_ITEM_DEL_LINK;?></button>&nbsp;<button onclick="del_all();" ><?php echo SHOW_USELESS_OPTION_ITEM_ALL_DEL_LINK;?></button>
</td>
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
