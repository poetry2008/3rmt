<?php
/*
 * 住所作成
 */
require('includes/application_top.php');
$action = (isset($_GET['action']) ? $_GET['action'] : '');
switch($_GET['action']){
case 'show_products_manual':
$title_char="";
$cPath=$_GET['cPath'];
$page=$_GET['page'];
$pid=$_GET['pID'];
$site_id = (isset($_GET['site_id']))?$_GET['site_id']:0;
if(isset($_GET['cPath']) && $_GET['cPath']!=''){
if(isset($_GET['pID']) && $_GET['pID']){
if(strpos($_GET['cPath'],"_")!=false){
$categories_id=explode('_',$_GET['cPath']);
$p_categories_id=$categories_id[0];
$s_categories_id=$categories_id[1];
$categories_p_info_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$p_categories_id."' and site_id='".$site_id."'");
$categories_p_info=tep_db_fetch_array($categories_p_info_query);
$categories_s_info_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$s_categories_id."' and site_id='".$site_id."'");
$categories_s_info=tep_db_fetch_array($categories_s_info_query);
}

$products_info_query=tep_db_query("select products_name,p_manual from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$pid."' and site_id='0'");
$products_info_arr=tep_db_fetch_array($products_info_query);
$title_char=$categories_p_info['categories_name'].'/'.$categories_s_info['categories_name'].'/'.$products_info_arr['products_name'].MANUAL_TITLE;
$manual_content=$products_info_arr['p_manual'];
}}
$param_str='cPath='.$cPath.'&pID='.$pid.'&site_id='.$site_id.'&page='.$page.'';
$return_button='<a href="categories.php?'.$param_str.'"><input type="button" value="'.MANUAL_RETURN.'"></a>';

$form_info='<form action="products_manual.php?cPath='.$cPath.'&action=save_products_manual&pID='.$pid.'&site_id='.$site_id.'&page='.$page.'" method="post">';
break;



case 'show_categories_manual':
$title_char="";
$cPath=$_GET['cPath'];
$page=$_GET['page'];
$cid=$_GET['cID'];
$site_id = (!empty($_GET['site_id']))?$_GET['site_id']:0;

if(isset($_GET['cPath']) && $_GET['cPath']==''){
$categories_s_info_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cID']."' and site_id='".$site_id."'");
$categories_s_info=tep_db_fetch_array($categories_s_info_query);
$title_char=$categories_s_info['categories_name'].MANUAL_TITLE;
$manual_content=$categories_s_info['c_manual'];

} else if(isset($_GET['cPath']) && $_GET['cPath']!=''){
if(isset($_GET['cID']) && $_GET['cID']!=''){
$categories_p_info_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cPath']."' and site_id='".$site_id."'");
$categories_p_info=tep_db_fetch_array($categories_p_info_query);
$categories_s_info_query=tep_db_query("select categories_name,c_manual from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$_GET['cID']."' and site_id='".$site_id."'");
$categories_s_info=tep_db_fetch_array($categories_s_info_query);
$title_char=$categories_p_info['categories_name'].'/'.$categories_s_info['categories_name'].MANUAL_TITLE;
$manual_content=$categories_s_info['c_manual'];
}
}
$form_info='<form action="products_manual.php?cPath='.$cPath.'&action=save_categories_manual&cID='.$cid.'&site_id='.$site_id.'&page='.$page.'" method="post">';
$param_str='cPath='.$cPath.'&cID='.$cid.'&site_id='.$site_id.'&page='.$page.'';
$return_button='<a href="categories.php?'.$param_str.'"><input type="button" value="'.MANUAL_RETURN.'"></a>';
break;



case 'save_products_manual':
$cPath=$_GET['cPath'];
$page=$_GET['page'];
$pid=$_GET['pID'];
$site_id = (!empty($_GET['site_id']))?$_GET['site_id']:0;


$products_manual_sql="update ".TABLE_PRODUCTS_DESCRIPTION." set p_manual='".addslashes($_POST['manual'])."' where products_id='".(int)$pid."' and site_id='0'";
tep_db_query($products_manual_sql);
$param_str='cPath='.$cPath.'&pID='.$pid.'&site_id='.$site_id.'&page='.$page.'';
tep_redirect(tep_href_link(FILENAME_CATEGORIES, $param_str)); 

break;




case 'save_categories_manual':
$cPath=$_GET['cPath'];
$page=$_GET['page'];
$cid=$_GET['cID'];
$site_id=0;

$categories_manual_sql="update ".TABLE_CATEGORIES_DESCRIPTION." set c_manual='".addslashes($_POST['manual'])."' where categories_id='".(int)$cid."' and site_id='".$site_id."'";

tep_db_query($categories_manual_sql);
$param_str='cPath='.$cPath.'&cID='.$cid.'&site_id='.$site_id.'&page='.$page.'';
tep_redirect(tep_href_link(FILENAME_CATEGORIES, $param_str)); 

break;
}
$rand_num=time();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script type="text/javascript" src="xheditor1/jquery/jquery-1.4.4.src.js"></script>
<?php
//<script type="text/javascript" src="xheditor1/xheditor-1.1.12-zh-tw.min.js"></script>
?>
<script type="text/javascript" src="xheditor1/xheditor.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script>
$(document).ready(function(){
	$('#elm1').xheditor({upLinkUrl:"upload.php",upLinkExt:"zip,rar,txt",upImgUrl:"upload.php?check=<?php echo $rand_num;?>",upImgExt:"jpg,jpeg,gif,png",upFlashUrl:"upload.php",upFlashExt:"swf",upMediaUrl:"upload.php",upMediaExt:"avi"});
});

$(document).ready(function(){
var tmp_width=$(".xheLayout").attr("style");
var start_pos=tmp_width.indexOf(":");
var end_pos=tmp_width.indexOf(";");
var width=tmp_width.substring(start_pos+1,end_pos);
$("#button_width").attr("width",width);
});
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
    </td></tr></table>
<!-- body_text //-->
<td width="100%" valign = "top" id='categories_right_td'><table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>

<td class="pageHeading"><?php echo $title_char;  ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
</tr>
<tr>
<td>
<?php echo $form_info;?>
<table width="100%">

<tr><td id="emd">
<textarea id="elm1" class="" cols="147" rows="20" name="manual" width="765"><?php echo stripcslashes($manual_content);?></textarea>


<td></tr>
<tr><td align="right" id="button_width">
<input type="submit" value="<?php echo MANUAL_SAVE;?>">
<?php echo $return_button;?>
</td></tr>

</table>


</td>
</tr>
</form>
</table>
<!-- body_eof //-->

   </table> 

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
