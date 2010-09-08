<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  $breadcrumb->add('相互リンク', tep_href_link('domain.php'));
?>
<?php page_head();?>
</head>
<body>
<div align="center">
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<table class="side_border" width="900" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="left_colum_border" width="171" align="left" valign="top">
<!-- body //--> 
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</td>
<td id="contents" valign="top">
<!-- left_navigation_eof //-->
<!-- body_text //-->
<h1 class="pageHeading"><?php echo '相互リンク'; ?></h1>
<div>
      <ul>
        <li><a href="http://rmt.worldmoney.jp">RMTワールドマネー</a></li>
        <li><a href="http://www.gamemoney.cc">RMTゲームマネー</a></li>
      </ul>
<!-- right_navigation //--> 
</div>
</td>
<td class="right_colum_border" width="171" valign="top">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</td>
</tr>
</table>
<!-- right_navigation_eof //-->
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div> <!-- end of -->
</body>
</html>

