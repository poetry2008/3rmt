<?php
/*
  $Id$

*/

  require('includes/application_top.php');

  $navigation->remove_current_page();

//ccdd
  $present_query = tep_db_query("
      select * 
      from ".TABLE_PRESENT_GOODS." 
      where goods_id = '".(int)$_GET['pID']."' 
        and site_id = '".SITE_ID."'"
  ) ;
  $present = tep_db_fetch_array($present_query) ;
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<title><?php echo $present['title']; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<script language="javascript"><!--
var i=0;
function resize() {
  if (navigator.appName == 'Netscape') i=40;
  if (document.images[0]) window.resizeTo(document.images[0].width +30, document.images[0].height+60-i);
  self.focus();
}
//--></script>
</head>
<body onLoad="resize();">
<?php echo tep_image(DIR_WS_IMAGES . 'present/'.$present['image'], $present['title']); ?>
</body>
</html>
<?php require('includes/application_bottom.php'); ?>
