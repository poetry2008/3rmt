<?php
/*
  $Id$

*/

  require('includes/application_top.php');

  $navigation->remove_current_page();

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ADVANCED_SEARCH);
?>
<?php page_head();?>
</head>
<style type="text/css"><!--
BODY { margin-bottom: 10px; margin-left: 10px; margin-right: 10px; margin-top: 10px; }
//--></style>
<body>

<?php
  $info_box_contents = array();
  $info_box_contents[] = array('text' => HEADING_SEARCH_HELP);

  new infoBoxHeading($info_box_contents, true, true);

  $info_box_contents = array();
  $info_box_contents[] = array('text' => TEXT_SEARCH_HELP);

  new infoBox($info_box_contents);
?>

<p class="smallText" align="right"><?php echo '<a href="javascript:window.close()"><u>ウィンドウを閉じる</u> [x]</a>'; ?></p>

</body>
</html>
<?php require('includes/application_bottom.php'); ?>
