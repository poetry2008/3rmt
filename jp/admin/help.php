<?php
/*
   $Id$
*/

require('includes/application_top.php');
if (!empty($_GET)) {
  $get_count = count($_GET);
  if ($get_count == 1) {
    if (isset($_GET[session_name()])) {
      tep_redirect(tep_href_link(FILENAME_DEFAULT));
    }
  }
} else {
  tep_redirect(tep_href_link(FILENAME_DEFAULT));
}
if ($_GET['action'] == 'save_content') {
  //更新内容 
  $help_romaji = urldecode($_POST['h_romaji']); 
  tep_db_query("update `help_info` set `content` = '".addslashes($_POST['help_content'])."', `title` = '".addslashes($_POST['help_title'])."' where romaji = '".$help_romaji."'"); 
  tep_redirect(tep_href_link('help.php', 'info_romaji='.urlencode($help_romaji))); 
}

if (isset($_GET['info_romaji']) && $_GET['info_romaji']) {
  $romaji = $_GET['info_romaji'];
  $info_sql = "select * from help_info where romaji='".$romaji."'";
  $info_query = tep_db_query($info_sql);
  $info_array = tep_db_fetch_array($info_query);
}

$id_array = array();

if (isset($_GET['keyword']) && $_GET['keyword']!="") {
  $keyword_query = tep_db_query("select keyword,id from help_info");
  while ($keyword_array = tep_db_fetch_array($keyword_query)) {
    $keyword_explode_array = explode(',',$keyword_array['keyword']);
    $keyword_all_array[$keyword_array['id']] = $keyword_explode_array;
  }
  foreach ($keyword_all_array as $key=> $val) {
    if(in_array($_GET['keyword'],$val)){
      $id_str .= $key.',';
    }
  }
  $id_str =  substr($id_str,0,-1);
  if ($id_str =="") {
    $keyword = trim($keyword);
    $info_query = tep_db_query("select * from help_info where content like '%".$keyword."%';"); 
  } else {
    $tmp_array = array();
    $keyword = $_GET['keyword'];
    $info_sql = "select * from help_info where id in (".$id_str.")";
    $info_query = tep_db_query($info_sql);
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>
<?php 
if (isset($_GET['info_romaji']) && $_GET['info_romaji']) {
  echo $info_array['title']=="" ? HELP_INFO_NO_INFO : stripslashes($info_array['title']);
} elseif (isset($_GET['keyword']) && $_GET['keyword']!="") {
  echo $keyword.HELP_INFO_SEARCH;
} else {
  echo HELP_INFO_HEADER;
}
?>
</title>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<style>
body {
font-family: "宋体", Osaka, Verdana, Arial, sans-serif;
font-size: 12px;
color: #000000;
padding:0;
margin:0;
}
a img {
border: 0;
vertical-align:middle;
}
.infoBoxContent a img {
vertical-align: top;
}
img {
vertical-align:middle;
}
a{ color:#000000; text-decoration:none;}
a:hover{ color:#666666;}
h2,h3,h4,ul,li{ padding:0; margin:0;}
.content h2,.content_table{
font-size:14px;
font-weight:bold;
background-color:#cccccc;
padding:8px 15px;
margin:5px 8px 0 0;
}
.box_info{ 
margin:0 8px 5px 0;
border-bottom:1px solid #cccccc;
border-left:1px solid #cccccc;
border-right:1px solid #cccccc;
background-color:#EEEEEE;
padding:5px 18px;
}
.box_content{ 
margin:0 8px 5px 0;
border-bottom:1px solid #cccccc;
border-left:1px solid #cccccc;
border-right:1px solid #cccccc;
background-color:#EEEEEE;
padding:5px;
}
.content{
	width:100%;
	min-width:750px;
	overflow:hidden;
	width: expression(document.body.clientWidth < 750? "750px": "100%" );
}
.td_height{
	height:770px;
	height:800px\0;
}
@media all and (-webkit-min-device-pixel-ratio:0){
.td_height{
	height:870px;
}}
@media all and (-webkit-min-device-pixel-ratio:0){
.footer_copyright{
	min-width:750px;
	width:100%;
	width: expression(document.body.clientWidth < 750? "750px": "100%" );
	
}}
.footer_copyright{
	text-align:center;
	font-size:12px;
	border-top:1px solid #000;
	padding:20px 0;
	background-color:#F0F1F1;
	margin-top:10px;
	width:100%;
	min-width:750px;
	overflow:hidden;
	width: expression(document.body.clientWidth < 750? "750px": "100%" );

}
.help_pic{
       margin-top:-5px;
}
.pageHeading {
	font-size: 18px;                                
	color: #727272;                                 
	font-weight: bold;                             
}
</style>
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="lib/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
        mode : "textareas",
        theme : "advanced",
        height: "800", 
        plugins : "imageupload,pagebreak,style,layer,table,advhr,advlink,emotions,iespell,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,inlinepopups",
         
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontsizeselect,forecolor,backcolor,imageupload,|,cut,copy,paste,|,search,replace,|,bullist,numlist,|,undo,redo,|,link,unlink,anchor,|,code",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : false,
        
        skin : "o2k7",
        skin_variant : "silver",
});

<?php //跳转URL?>
function redirect_new_url(new_object)
{
  var url_str = $(new_object).parent().attr('href');
  window.location.href = url_str;
}
</script>
</head>
<body>
<?php if (!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']) {?>
<script language='javascript'>
  one_time_pwd('<?php echo $page_name;?>');
</script>
<?php }?>
<?php 
if ($_GET['action'] == 'modify_content') {
//编辑内容
echo tep_draw_form('m_form', 'help.php', 'action=save_content');
?>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr>
<td width="8">&nbsp;</td> 
<td valign="top"> 
<div class="content_table">
<table width="100%" border="0" cellpadding="2" cellspacing="0">
  <tr>
    <td>
    <?php 
      echo tep_draw_input_field('help_title', stripslashes($info_array['title']), 'size=50');
    ?>
    </td> 
  </tr>
</table>
</div>
<div class="box_content">
<table width="100%" border="0" cellpadding="2" cellspacing="0">
        <tr>
          <td id="emd">
            <textarea id="elm1" class="" cols="207" rows="20" name="help_content" style="width:100%;height:100%;"><?php echo stripslashes($info_array['content']);?></textarea>
          </td>
        </tr>
        <tr>
          <td align="right" id="button_width">
            <input type="hidden" name="h_romaji" value="<?php echo urlencode($info_array['romaji']);?>">   
            <input type="submit" value="<?php echo IMAGE_SAVE;?>">
            <a href="<?php echo tep_href_link('help.php', 'info_romaji='.urlencode($info_array['romaji']));?>"><?php echo tep_html_element_button(IMAGE_BACK);?></a> 
          </td>
        </tr>
      </table>
</div>
</td>
</tr>
</table>
</form>
<?php
} else {
if (isset($_GET['info_romaji']) && $_GET['info_romaji']) {
  if (empty($info_array)) {
?>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr>
<td width="8" class="td_height">&nbsp;</td> 
<td valign="top"> 
<?php
    echo '<div class="content_table">';
    echo '<table width="100%" cellpadding="2" cellspacing="0" border="0">
    <tr>
    <td align="left"><img alt="img" src="images/menu_icon/icon_help_info.gif" class="help_pic">&nbsp;'.$_GET['keyword'].HELP_INFO_SEARCH.'</td>
    <td align="right">
    <form action="help.php" method="get" >
    <input type="text" name="keyword" >
    <input type="submit" value="'.HELP_INFO_SEARCH.'">
    </form>
    </td>
    </tr>
    </table>';
    echo '</div>';
    echo '<div class="box_info">'; 
    echo '<font color="red">';
    echo HELP_INFO_NO_INFO;
    echo '</font>';
    echo '</div>';
?>
</td>
</tr>
</table>
<?php
  } else {
?>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr>
<td width="8" class="td_height">&nbsp;</td> 
<td valign="top"> 
<div class="content">
<?php
echo '<div class="content_table">';
echo '<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td  align="left"><img alt="img" src="images/menu_icon/icon_help_info.gif" class="help_pic">&nbsp;'.stripslashes($info_array['title']);
echo '&nbsp;&nbsp;<a href="'.tep_href_link('help.php', 'action=modify_content&info_romaji='.urlencode($_GET['info_romaji'])).'">'.tep_html_element_button(IMAGE_EDIT).'</a>';
echo '</td>
<td align="right">
<form action="help.php" method="get" >
<input type="text" name="keyword" >
<input type="submit" value="'.HELP_INFO_SEARCH.'">
</form>
</td>
</tr>
</table>';
echo '</div>';
echo '<div class="box_info">';
echo stripslashes($info_array['content']).'<br>';
}
?>
</div>
</div>
</td>
</tr>
</table>
<?php 
}
}
if(isset($_GET['keyword']) && $_GET['keyword']){
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="3" class="td_height">&nbsp;</td> 
    <td valign="top"> 
    <?php
    echo '<div class="content_table">';
    echo '<table width="100%" cellpadding="2" cellspacing="0" border="0">
    <tr>
    <td align="left"><img alt="img" src="images/menu_icon/icon_help_info.gif" class="help_pic">&nbsp;'.$_GET['keyword'].HELP_INFO_SEARCH.'</td>
    <td align="right">
    <form action="help.php" method="get" >
    <input type="text" name="keyword" >
    <input type="submit" value="'.HELP_INFO_SEARCH.'">
    </form>
    </td>
    </tr>
    </table>';
    echo '</div>';
    $num_info = tep_db_num_rows($info_query);
    if ($num_info == 0) {
    echo '<div class="box_info">'; 
    echo '<font color="red">';
    echo HELP_INFO_NO_SEARCH_INFO;
    echo '</font>';
    echo '</div>';
    } else {
      while($info_array = tep_db_fetch_array($info_query)){
        echo '<div class="content">';
        echo '<a href="'.tep_href_link("help.php","info_romaji=".urlencode($info_array['romaji'])).'"><h2><img alt="img" src="images/menu_icon/icon_help_info.gif" class="help_pic">&nbsp;'.stripslashes($info_array['title']).'</h2></a></div>'; 
        echo '<div class="box_info">';
        echo '<a href="'.tep_href_link("help.php","info_romaji=".urlencode($info_array['romaji'])).'">'.mb_substr(strip_tags(stripslashes($info_array['content'])),0,300,'utf-8').'......</a></div>';
      }
    }
    ?>
    </td>
  </tr>
</table>  
<?php
} else {
if (isset($_GET['keyword'])) {
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="3" class="td_height">&nbsp;</td> 
    <td valign="top"> 
    <?php
    echo '<div class="content_table">';
    echo '<table width="100%" cellpadding="2" cellspacing="0" border="0">
    <tr>
    <td align="left"><img alt="img" src="images/menu_icon/icon_help_info.gif" class="help_pic">&nbsp;'.$_GET['keyword'].HELP_INFO_SEARCH.'</td>
    <td align="right">
    <form action="help.php" method="get" >
    <input type="text" name="keyword" >
    <input type="submit" value="'.HELP_INFO_SEARCH.'">
    </form>
    </td>
    </tr>
    </table>';
    echo '</div>';
    echo '<div class="box_info">'; 
    echo '<font color="red">';
    echo HELP_INFO_NO_SEARCH_INFO;
    echo '</font>';
    echo '</div>';
    ?>
    </td>
  </tr>
</table>  
<?php
}
}
?>
<div class="footer_copyright">
<?php echo sprintf(TEXT_SITE_COPYRIGHT,date('Y'));?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
