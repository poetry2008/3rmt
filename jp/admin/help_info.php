<?php
/*
  $Id$
 */

require('includes/application_top.php');
switch ($_GET['action']){
case 'info_save':
	$title   = tep_db_prepare_input(addslashes($_POST['title']));
	$keyword = tep_db_prepare_input(addslashes($_POST['keyword']));
	$content = tep_db_prepare_input(addslashes($_POST['content']));
	$opa = $_POST['opa'];
  $romaji  = tep_db_prepare_input(addslashes($_POST['romaji']));

  if($_POST['romaji'] == "modules"){
	  if(preg_match('#set=order_total#',$_POST['opa']))   $romaji .= ".php?set=order_total";
    if(preg_match('#set=payment#',$_POST['opa']))   $romaji .= ".php?set=payment";
    if(preg_match('#set=metaseo#',$_POST['opa']))   $romaji .= ".php?set=metaseo";
  }

	$res = tep_db_query("insert into help_info(id,title,keyword,content,romaji) values ('','".$title."','".$keyword."','".$content."','".$romaji."')");
        
	if($res){
		tep_redirect($romaji);
	}

	break;
case 'info_update':
  $title   = tep_db_prepare_input(addslashes($_POST['title']));
	$keyword = tep_db_prepare_input(addslashes($_POST['keyword']));
	$content = tep_db_prepare_input(addslashes($_POST['content']));
	$id      = tep_db_prepare_input(addslashes($_POST['id']));
	$romaji  = tep_db_prepare_input(addslashes($_POST['romaji']));
	$opa = $_POST['opa'];
	
	if($_POST['romaji'] == "modules"){
	  if(preg_match('#set=order_total#',$_POST['opa']))   $romaji .= ".php?set=order_total";
    if(preg_match('#set=payment#',$_POST['opa']))       $romaji .= ".php?set=payment";
    if(preg_match('#set=metaseo#',$_POST['opa']))       $romaji .= ".php?set=metaseo";
  }
	
	$res = tep_db_query("update help_info set title='".$title."',keyword='".$keyword."',content='".$content."' where romaji='".$romaji."'");
	if($res){
          tep_redirect('help_info.php?'.substr($_POST['opa'], 0, -1));
	}
break;
case 'info_del':
	$info_id_array = $_POST['ck'];
	foreach($info_id_array as $val){
		tep_db_query("delete from help_info where id='".$val."'");
	}
	tep_redirect(FILENAME_HELP_INFO);
	break;
default:	
	$info_list_sql = "select * from help_info";
	if(isset($_GET['keyword']) && $_GET['keyword']!=""){
		$info_key_sql = $info_list_sql." where keyword like '%".$_GET['keyword']."%'";
		if(tep_db_num_rows(tep_db_query($info_key_sql)) ==0){
			$info_con_sql = $info_list_sql ." where content like '%".$_GET['keyword']."%'";
		}
		if(!isset($info_con_sql)){
			$info_list_sql = $info_key_sql;
		}else{
			$info_list_sql = $info_con_sql;	
		}
	}
        $info_list_sql .= " order by romaji"; 
        $info_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $info_list_sql, $num_rows); 
	$info_list_query = tep_db_query($info_list_sql);
	
  $info_sql   = "select * from help_info";

  if(isset($_GET['keyword']) && $_GET['keyword']!=""){
		$tmp_array  = array();
		$keyword    =  $_GET['keyword'];
		$info_sql  .= "select * from help_info where keyword like '%".$keyword."%'";
		$info_query = tep_db_query($info_sql);
    while($info_array = tep_db_fetch_array($info_query)){
	    $key_array  = explode(',',$info_array["keyword"]);
      if(in_array($keyword,$key_array)){
	      $tmp_array[] = $info_array;
	    }
    }
    if(empty($tmp_array)){
	    $info_sql  .= "select * from help_info where content like '%".$keyword."%'";
	    $info_query = tep_db_query($info_sql);
      while($info_array = tep_db_fetch_array($info_query)){
	      $tmp_array[] = $info_array;
	    }
    }
  }
  $info_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $info_sql, $num_rows);
	break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HELP_INFO_HEADER; ?></title>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript">
function checkform(){
	var title = document.getElementById('title').value;
	var keyword = document.getElementById('keyword').value;
	var content = document.getElementById('content').value;
	if(title == ""){
		alert("<?php echo HELP_INFO_JS_TITLE_ERROR;?>");
		return false;
	}
	if(keyword == ""){
	  alert("<?php echo HELP_INFO_JS_KEYWORD_ERROR;?>")
	  return false;
	}
	if(content == ""){
	  alert("<?php echo HELP_INFO_JS_CONTENT_ERROR;?>")
	  return false;
	}
}

function checkall(){
  var ckall= document.getElementById("ckall");

  var ele = document.getElementsByName("ck[]");

  for (var i=0;i<ele.length;i++){
    var e = ele[i];
    if(ckall.checked==true){
      e.checked = true; 
    }else{
      e.checked = false; 
    }
  }
}

function get_action(action) {
  var getform=document.getElementById('info_del') ;
	getform.action="show_useless_option.php?action="+action;
	getform.submit();
}
</script>
<script type="text/javascript" src="xheditor1/jquery/jquery-1.4.4.src.js"></script>
<script type="text/javascript" src="xheditor1/xheditor.js"></script>
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript">
$(document).ready(function(){
	$('#elm1').xheditor({upLinkUrl:"upload.php",upLinkExt:"zip,rar,txt",upImgUrl:"upload.php?check=<?php echo $rand_num;?>",upImgExt:"jpg,jpeg,gif,png",upFlashUrl:"upload.php",upFlashExt:"swf",upMediaUrl:"upload.php",upMediaExt:"avi"});

});
</script>

<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
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
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>
	 <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
	   <tr>
	     <td class="pageHeading"><?php echo HELP_INFO_HEADER; ?></td>
	     <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
	   </tr>
	</table></td>
       </tr>
<?php 
//info_list=================================================================
if(!isset($_GET['action'])){
?>
<tr>

	<td><table border="0" width="100%" cellspacing="0" cellpadding="4">
<tr class="dataTableHeadingRow">
		 <td class="dataTableHeadingContent"><?php echo HELP_INFO_TABLE_TITLE;?></td>
		 <td class="dataTableHeadingContent"><?php echo HELP_INFO_TABLE_KEYWORD;?></td>
     <td class="dataTableHeadingContent"><?php echo HELP_INFO_TABLE_BELONG;?></td>
		 <td class="dataTableHeadingContent"><?php echo HELP_INFO_TABLE_CONTENT;?> </td>
     <td class="dataTableHeadingContent" align="right"><?php echo HELP_INFO_TABLE_ACTION;?></td>
</tr>
<form id="info_del" method="post" action="<?php echo tep_href_link(FILENAME_HELP_INFO,'action=info_del');?>">
<?php
	$now_css = 'dataTableRow';
  $select_row = $_GET['select_row'];
	while($info_list_array = tep_db_fetch_array($info_list_query)){
    if(!isset($select_row)){
      $select_row = $info_list_array['id'];
	  }

		if($now_css == 'dataTableRow'){
			$now_css = 'dataTableSecondRow'	;
		}else{
			$now_css = 'dataTableRow';
		}
		if($select_row == $info_list_array['id']){
			echo "<tr class =\"dataTableRowSelected\" >";
		}else{
      echo "<tr class=\"".$now_css."\" onmouseover=\"this.className='dataTableRowOver'\"  onmouseout=\"this.className='".$now_css."'\">";
		}
    $select_url = tep_href_link('help_info.php',tep_get_all_get_params(array('select_row')).'select_row='.$info_list_array['id'],'NONSSL');
?>
<!--<td><input type="checkbox" name="ck[]" value="<?php echo $info_list_array['id'];?>"></td>-->
<td onclick="window.location.href='<?php echo $select_url ?>'"><?php echo $info_list_array['title']?></td>
<td onclick="window.location.href='<?php echo $select_url ?>'"><?php echo $info_list_array['keyword']?></td>
<td onclick="window.location.href='<?php echo $select_url ?>'"><?php echo $info_list_array['romaji']?></td>
<td onclick="window.location.href='<?php echo $select_url ?>'"><?php echo mb_substr(strip_tags($info_list_array['content']),0,70,'utf-8');?></td>
<td onclick="window.location.href='<?php echo $select_url ?>'" align="right"><a href="<?php echo tep_href_link(FILENAME_HELP_INFO,'action=info_edit&info_romaji='.$info_list_array['romaji'].'&opa='.urlencode(tep_get_all_get_params()),'NONSSL')?>"><?php echo tep_image(DIR_WS_IMAGES.'icon_info.gif',IMAGE_ICON_INFO);?></a></td>
</tr>
<?php
	}
?>
</table>
</td>
</tr>
<tr><td>
<table width="100%">
<tr>
<td class="smallText" valign="top"><?php echo $info_split->display_count($num_rows,MAX_DISPLAY_SEARCH_RESULTS,$_GET['page'],TEXT_DISPLAY_NUMBER_OF_HELP_INFO);?></td>
<td align="right" class="smallText">
<?php
	echo $info_split->display_links($num_rows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']    , tep_get_all_get_params(array('page', 'info', 'x', 'y')));
?>
&nbsp;&nbsp;&nbsp;
</td>
</tr>
</table>
</td></tr>
</form>
<?php
}elseif(isset($_GET['action']) && $_GET['action'] == "info_edit"){
  $info_romaji = $_GET['info_romaji'];
	if(isset($_GET['info_romaji']) && $_GET['info_romaji'] !=""){
		if($_GET['info_romaji'] == "modules"){
	    if(preg_match('#set=order_total#',$_GET['opa']))   $info_romaji .= ".php?set=order_total";
      if(preg_match('#set=payment#',$_GET['opa']))   $info_romaji .= ".php?set=payment";
      if(preg_match('#set=metaseo#',$_GET['opa']))   $info_romaji .= ".php?set=metaseo";
		}
    $info_query = tep_db_query("select * from help_info where romaji='".$info_romaji."'");
    $info_array = tep_db_fetch_array($info_query);
	}
//new_edit==========================================================================================
?>
<tr>
<td>
<form action="help_info.php?action=<?php echo empty($info_array) ? "info_save" : "info_update"?>" id="info_form" method="post">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr><td><?php echo HELP_INFO_TITLE;?></td><td><input type="hidden" name="opa" value="<?php echo $_GET['opa']?>"><input type="hidden" name="romaji" value="<?php echo $_GET['info_romaji']?>"><input type="hidden" name="id" value="<?php echo $_GET["info_id"];?>"><input type="text" name="title" id="title" value="<?php echo $info_array['title'];?>"></td></tr>
	<tr><td><?php echo HELP_INFO_KEYWORD;?></td><td><input type="text" name="keyword" id="keyword" value="<?php echo $info_array['keyword'];?>"></td></tr>
	<tr><td valign="top"> <?php echo HELP_INFO_CONTENT;?></td><td><textarea id="elm1" name="content"   id="content" cols="100" rows="20"><?php echo $info_array['content']?></textarea></td></tr>
	<tr><td></td><td ><input type="submit" value="<?php echo HELP_INFO_SAVE?>" onClick="return checkform()"><input type="button" value="<?php echo HELP_INFO_RETURN;?>" onclick="window.location.href='<?php echo $_SESSION['last_page'].'?'.$_GET['opa']?>';"></td></tr>
</table>
</form>
</td>
</tr>
<?php }?>
</table>
</div>
</td>
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
