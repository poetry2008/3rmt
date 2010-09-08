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
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents">
      <h1 class="pageHeading"><?php echo '相互リンク'; ?></h1>
        <div class="comment"> 
<div class="content_email01">
  <div id="bgn_content">
    <div id="wrapper_kiyaku">
      <ul>
        <li><a href="http://www.gamemoney.cc">RMTゲームマネー</a></li>
        <li><a href="http://www.iimy.co.jp">RMTジャックポット</a></li>
      </ul>
    </div>
  </div>
  <!-- end of bgn_content -->
</div>
</div> 
    <p class="pageBottom"></p>
    </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </td> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
