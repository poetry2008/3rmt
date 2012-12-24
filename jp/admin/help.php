<?php
/*
   $Id$
*/

require('includes/application_top.php');

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
  echo $info_array['title']=="" ? HELP_INFO_NO_INFO : $info_array['title'];
} elseif (isset($_GET['keyword']) && $_GET['keyword']!="") {
  echo $keyword.HELP_INFO_SEARCH;
} else {
  echo HELP_INFO_HEADER;
}
?>
</title>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script type="text/javascript" src="xheditor1/jquery/jquery-1.4.4.src.js"></script>
<style>
body {
font-family: "ＭＳ Ｐゴシック", Osaka, Verdana, Arial, sans-serif;
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
margin:5px 8px 0 8px;
}
.box_info{ 
margin:0 8px 5px 8px;
border-top:4px solid #cccccc;
border-bottom:1px solid #cccccc;
border-left:1px solid #cccccc;
border-right:1px solid #cccccc;
background-color:#EEEEEE;
padding:5px 12px;
}
</style>
<script type="text/javascript" src="xheditor1/xheditor.js"></script>
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script>
$(document).ready(function(){
  $('#elm1').xheditor();
});
</script>
</head>
<body>
<?php if (!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']) {?>
<script language='javascript'>
  one_time_pwd('<?php echo $page_name;?>');
</script>
<?php }?>
<?php 
if (isset($_GET['info_romaji']) && $_GET['info_romaji']) {
  if (empty($info_array)) {
    echo HELP_INFO_NO_INFO;
  } else {
?>
<div class="content">
<?php
echo '<div class="content_table">';
echo '<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
<td  align="left"><img alt="img" src="images/icon_help_info.gif">&nbsp;'.$info_array['title'].'</td>
<td align="right">
<form action="help.php" method="get" >
<input type="text" name="keyword" >
<input type="submit" value="'.HELP_INFO_SEARCH.'">
</form>
</td>
</tr>
</table>';
echo '</div>';
echo '</h2>';
echo '<div class="box_info">';
echo $info_array['content'].'<br>';
}
?>
</div>
</div>
<?php 
}
if(isset($_GET['keyword']) && $_GET['keyword']){
?>
<table  width="100%" >
  <tr>
    <td>
    <?php
    $num_info = tep_db_num_rows($info_query);
    if ($num_info == 0) {
    echo '<font color="red"><b>';
    echo HELP_INFO_NO_SEARCH_INFO;
    echo '</b></font>';
    } else {
      while($info_array = tep_db_fetch_array($info_query)){
        echo '<div class="content">';
        echo '<a href="'.tep_href_link("help.php","info_romaji=".urlencode($info_array['romaji'])).'"><h2><img alt="img" src="images/icon_help_info.gif">&nbsp;'.$info_array['title'].'</h2></a>';

        echo '<div class="box_info">';
        echo '<a href="'.tep_href_link("help.php","info_romaji=".urlencode($info_array['romaji'])).'">'.substr(strip_tags($info_array['content']),0,300).'......</a></div>';
      }
    }
    ?>
    </td>
  </tr>
</table>  
<?php
}
?>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
