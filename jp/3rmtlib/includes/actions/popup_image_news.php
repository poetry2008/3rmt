<?php
/*
  $Id$
*/

if(!is_numeric($_GET['nID'])){
  forward404();
}
  $navigation->remove_current_page();

  $latest_news_query = tep_db_query('
      SELECT * 
      from ' . TABLE_NEWS . ' 
      WHERE news_id = ' . $_GET['nID'] . ' 
      and (site_id = '.SITE_ID . ' or site_id=0)'
  );
  $latest_news = tep_db_fetch_array($latest_news_query);
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<title><?php echo $latest_news['headline']; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<script type="text/javascript"><!--
var i=0;
<?php //弹出框所在的位置?>
function resize() {
  if (navigator.appName == 'Netscape') i=40;
  if (document.images[0]) window.resizeTo(document.images[0].width +30, document.images[0].height+60-i);
  self.focus();
}
--></script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>
<body onLoad="resize();">
<?php echo tep_image(DIR_WS_IMAGES . $latest_news['news_image'], $latest_news['headline']); ?>
</body>
</html>
<?php require('includes/application_bottom.php'); ?>
