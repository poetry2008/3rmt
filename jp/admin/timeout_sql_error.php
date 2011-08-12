<?php
  require('includes/application_top.php');
?>
<HTML>
<HEAD>
<!-- locale-sensitive -->
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<TITLE><?php echo ERROR_TEXT;?></TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" >
<?php echo TEXT_ERROR_PAGE_SHOW;?>
<?php
echo "<br>";
echo "<a href='".$_SERVER['HTTP_REFERER']."'>".TEXT_ERROR_LINK_BACK."</a>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<a href='index.php'>".TEXT_ERROR_LINK_INDEX."</a>";
if(isset($_GET['string'])&&$_GET['string']){
  echo $_GET['string'];
}
?>
</BODY>
</HTML>
