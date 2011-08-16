<?php
if($_SERVER["HTTP_ACCEPT_LANGUAGE"]){
  $lan_arr = explode(',',$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
  if($lan_arr[0]=='ja'){
    include('includes/languages/japanese/timeout_sql_error.php');
  }
}
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
  echo "<br>";
  echo "<br>";
  echo $_GET['string'];
}
?>
</BODY>
</HTML>
